<?php

namespace Tests\Feature;

use App\Models\Archivo;
use App\Models\Information;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * La tabla de solicitudes viene del sitio anterior y varias columnas no admiten nulos.
 * Un campo condicional que no aplica llegaba vacío y la solicitud se perdía con "Server
 * Error" (#1774).
 */
class EmploymentApplicationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createLegacyTables();

        // Sin secreto configurado el controlador no exige captcha (ver phpunit.xml).
        config(['services.recaptcha.secret' => null]);
    }

    public function test_an_application_without_the_conditional_fields_is_saved(): void
    {
        $this->postJson('/api/v1/employment', [
            'lastname' => 'Prueba',
            'firstname' => 'QA',
            'mi' => 'X',
            'date' => '2026-10-08',
            'address' => '123 Test St',
            'city' => 'San Antonio',
            'state' => 'TX',
            'zipcode' => '78216',
            'phone' => '2103991116',
            'email' => 'qa@example.com',
            'birthday' => '1990-05-10',
            'socialnumber' => '123456789',
            'placebirth' => 'San Antonio',
            'appliedpay' => 'Security Officer',
            'shifts' => ['Morning', 'Weekends'],
            'citizen' => 'yes',
            // 'authorized' no se manda: sólo aplica si no es ciudadano.
            'worked' => 'no',
            'convicted' => 'no',
            'indictment' => 'no',
            'days' => ['2', '3'],
        ])->assertCreated();

        $information = Information::firstOrFail();

        $this->assertSame('', $information->authorized);
        $this->assertSame('QA', $information->firstname);
    }

    public function test_an_attachment_keeps_the_name_the_applicant_used(): void
    {
        Storage::fake('public');

        $this->post('/api/v1/employment', [
            'lastname' => 'Prueba',
            'firstname' => 'QA',
            'address' => '123 Test St',
            'city' => 'San Antonio',
            'state' => 'TX',
            'zipcode' => '78216',
            'phone' => '2103991116',
            'email' => 'qa@example.com',
            'fileid' => [UploadedFile::fake()->create('licencia de conducir.pdf', 10, 'application/pdf')],
        ])->assertCreated();

        $archivo = Archivo::firstOrFail();

        $this->assertSame('licencia de conducir.pdf', $archivo->original_name);
    }

    /**
     * El aspirante puede marcar varios turnos (#1888). Antes sólo cabía uno, y quien
     * estaba disponible mañana y fin de semana tenía que elegir.
     */
    public function test_an_applicant_can_choose_several_shifts(): void
    {
        $this->postJson('/api/v1/employment', $this->solicitud())->assertCreated();

        $guardado = \App\Models\Information::first()->whichshift;

        $this->assertSame(['Morning', 'Weekends'], unserialize($guardado, ['allowed_classes' => false]));
        $this->assertSame(
            'Morning, Weekends',
            \App\Filament\Resources\Information\Schemas\InformationInfolist::appliedShifts($guardado),
        );
    }

    /**
     * Las 1 138 solicitudes anteriores guardaron el turno como texto suelto y no se
     * tocaron: tienen que seguir leyéndose tal cual en la ficha y en la impresión.
     */
    public function test_older_applications_keep_showing_their_single_shift(): void
    {
        $this->assertSame(
            '1st Shift',
            \App\Filament\Resources\Information\Schemas\InformationInfolist::appliedShifts('1st Shift'),
        );
    }

    /** El cuerpo mínimo de una solicitud válida, el mismo que usa la primera prueba. */
    private function solicitud(): array
    {
        return [
            'lastname' => 'Prueba',
            'firstname' => 'QA',
            'mi' => 'X',
            'date' => '2026-10-08',
            'address' => '123 Test St',
            'city' => 'San Antonio',
            'state' => 'TX',
            'zipcode' => '78216',
            'phone' => '2103991116',
            'email' => 'qa@example.com',
            'birthday' => '1990-05-10',
            'socialnumber' => '123456789',
            'placebirth' => 'San Antonio',
            'appliedpay' => 'Security Officer',
            'shifts' => ['Morning', 'Weekends'],
            'citizen' => 'yes',
            // 'authorized' no se manda: sólo aplica si no es ciudadano.
            'worked' => 'no',
            'convicted' => 'no',
            'indictment' => 'no',
            'days' => ['2', '3'],
        ];
    }

    private function createLegacyTables(): void
    {
        Schema::create('informations', function (Blueprint $t): void {
            foreach (['lastname', 'firstname', 'mi', 'date', 'address', 'city', 'state', 'zipcode', 'phone', 'email', 'birthday', 'socialnumber', 'placebirth', 'appliedpay', 'whichshift', 'whichday', 'citizen', 'authorized', 'worked', 'convicted', 'indictment'] as $column) {
                $t->string($column);
            }
            foreach (['apartment', 'when', 'explain1', 'explain2'] as $column) {
                $t->string($column)->nullable();
            }
            $t->id();
            $t->timestamps();
        });
        Schema::create('educations', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('information_id');
            foreach (['graduatehigh', 'hightschool', 'highfrom', 'hightto', 'graduatecollage', 'collaganame', 'collagefrom', 'collageto', 'whatmayor', 'completed', 'activecard', 'officer', 'firearm', 'holster', 'others'] as $column) {
                $t->string($column)->nullable();
            }
            $t->timestamps();
        });
        $extras = [
            'references' => ['fullname', 'relationship', 'companyref', 'phoneref', 'addressreference'],
            'employments' => ['company', 'phoneemp', 'addressempl', 'supervisor', 'jobtitle', 'starting', 'ending', 'from', 'to', 'reason', 'references'],
            'militaries' => ['branch', 'from', 'to', 'rank', 'type', 'explain'],
            'disclaimers' => ['signature', 'fileid', 'datedisclamer'],
            'archivos' => ['file', 'original_name', 'name'],
        ];

        foreach ($extras as $table => $columns) {
            Schema::create($table, function (Blueprint $t) use ($columns): void {
                $t->id();
                $t->unsignedBigInteger('information_id')->nullable();
                $t->unsignedBigInteger('disclaimer_id')->nullable();
                foreach ($columns as $column) {
                    $t->string($column)->nullable();
                }
                $t->timestamps();
            });
        }
    }
}
