<?php

namespace Tests\Feature;

use App\Models\Form;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * El aviso del Form 8850 (#1874): el cliente pedía recibir el formulario completo, que
 * hasta ahora obligaba a entrar al panel porque el correo sólo llevaba el nombre.
 */
class Form8850Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // El transporte de pruebas guarda el mensaje tal cual sale, que es lo que hay que
        // mirar: el aviso se manda con Mail::raw y no hay Mailable que interceptar.
        config([
            'services.recaptcha.secret' => null,
            'mail.contact' => 'avisos@tap.test',
            'mail.default' => 'array',
        ]);

        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            foreach (['yourname', 'socialnumber', 'address', 'citystate', 'country', 'telephone', 'birthday'] as $columna) {
                $table->string($columna)->nullable();
            }
            $table->text('condicional')->nullable();
            $table->timestamps();
        });
    }

    /** @return array<string, mixed> */
    private function solicitud(array $cambios = []): array
    {
        return array_merge([
            'yourname' => 'John Smith',
            'socialnumber' => '123-45-6789',
            'address' => '123 Main St',
            'citystate' => 'San Antonio, TX',
            'country' => 'US',
            'telephone' => '210-555-0100',
            'birthday' => '1990-03-14',
            'condicional' => [2, 5],
        ], $cambios);
    }

    public function test_the_notice_carries_the_whole_form(): void
    {
        $this->postJson('/api/v1/form8850', $this->solicitud())->assertCreated();

        $cuerpo = $this->ultimoCorreo();

        foreach (['John Smith', '123 Main St', 'San Antonio, TX', '210-555-0100', '1990-03-14'] as $dato) {
            $this->assertStringContainsString($dato, $cuerpo);
        }

        // Los enunciados marcados, con el número del impreso oficial (el 7 es la 6ª).
        $this->assertStringContainsString('2. Any of the statements of the second group', $cuerpo);
        $this->assertStringContainsString('5. Veteran entitled to compensation', $cuerpo);
    }

    /**
     * El correo no viaja cifrado y se queda en el buzón de quien lo reciba, así que el
     * número de la seguridad social va enmascarado; el completo se ve en el panel.
     */
    public function test_the_social_security_number_is_masked(): void
    {
        $this->postJson('/api/v1/form8850', $this->solicitud())->assertCreated();

        $cuerpo = $this->ultimoCorreo();

        $this->assertStringContainsString('***-**-6789', $cuerpo);
        $this->assertStringNotContainsString('123-45-6789', $cuerpo);
        $this->assertStringNotContainsString('123456789', $cuerpo);

        // Y en la base sí se guarda entero: el panel tiene que poder enseñarlo.
        $this->assertSame('123-45-6789', Form::first()->socialnumber);
    }

    public function test_a_form_without_checked_statements_says_so(): void
    {
        $this->postJson('/api/v1/form8850', $this->solicitud(['condicional' => []]))->assertCreated();

        $this->assertStringContainsString('Checked statements: none.', $this->ultimoCorreo());
    }

    /**
     * El cuerpo del último correo que salió por el transporte de pruebas, ya decodificado:
     * el mensaje viaja en quoted-printable y parte las líneas largas con `=`, así que
     * buscar texto en el crudo falla por donde cae el corte.
     */
    private function ultimoCorreo(): string
    {
        $mensajes = Mail::getSymfonyTransport()->messages();

        $this->assertNotEmpty($mensajes, 'No salió ningún correo.');

        return $mensajes->last()->getOriginalMessage()->getTextBody();
    }
}
