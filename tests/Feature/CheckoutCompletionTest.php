<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\User;
use App\Models\UserCourse;
use App\Services\CheckoutCompletion;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Completar una compra cobrada: marcar la orden, abrir la matrícula y avisar.
 *
 * Esto vivía dentro del webhook de Stripe, así que una compra sólo se completaba si ese
 * aviso llegaba. Del 21 al 23 de octubre de 2026 no llegó ninguno y tres alumnos se
 * quedaron pagados y sin curso. Ahora el mismo camino lo recorre también la tarea que
 * repasa las compras pendientes, y estas pruebas fijan que hace lo mismo y que no se puede
 * ejecutar dos veces.
 */
class CheckoutCompletionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['mail.default' => 'array', 'mail.contact' => 'avisos@tap.test']);

        $this->crearTablas();
    }

    /** Una sesión de Stripe como la que llega en el aviso o se consulta por su id. */
    private function sesion(string $id, int $courseId, int $userId): object
    {
        return (object) [
            'id' => $id,
            'payment_intent' => 'pi_prueba',
            'payment_status' => 'paid',
            'metadata' => (object) ['course_id' => (string) $courseId, 'user_id' => (string) $userId],
        ];
    }

    /** @return array{0: User, 1: Course, 2: CourseOrder} */
    private function compraPendiente(string $sesion = 'cs_prueba'): array
    {
        $user = User::create(['name' => 'Howell', 'email' => 'alumno@tap.test', 'password' => 'x']);
        $course = Course::create(['titulo' => 'Private Security Level II', 'slug' => 'level-ii', 'tiempovalido' => 30]);

        $order = CourseOrder::create([
            'user_id' => $user->id,
            'course' => json_encode(['course_id' => $course->id, 'slug' => $course->slug, 'title' => $course->titulo]),
            'email' => $user->email,
            'amount' => 0.5,
            'currency' => 'usd',
            'order_id' => '1792771013',
            'checkout_session_id' => $sesion,
            'payment_status' => 'pending',
        ]);

        return [$user, $course, $order];
    }

    public function test_a_paid_purchase_opens_the_enrollment(): void
    {
        [$user, $course, $order] = $this->compraPendiente();

        $completada = app(CheckoutCompletion::class)->completar($this->sesion('cs_prueba', $course->id, $user->id));

        $this->assertTrue($completada);
        $this->assertSame('succeeded', $order->fresh()->payment_status);
        $this->assertSame('pi_prueba', $order->fresh()->txn_id);

        $matricula = UserCourse::where('user_id', $user->id)->first();
        $this->assertNotNull($matricula, 'La compra no abrió ninguna matrícula.');
        $this->assertSame($course->id, (int) $matricula->course_id);
        $this->assertSame(0, (int) $matricula->caducado);
        $this->assertSame(30, (int) $matricula->dias_activo);
    }

    /**
     * El aviso de Stripe y la tarea pueden llegar a la vez a la misma compra: la segunda
     * no debe abrir una matrícula de más ni volver a mandar los correos.
     */
    public function test_completing_twice_changes_nothing(): void
    {
        [$user, $course] = $this->compraPendiente();
        $servicio = app(CheckoutCompletion::class);
        $sesion = $this->sesion('cs_prueba', $course->id, $user->id);

        $this->assertTrue($servicio->completar($sesion));
        $this->assertFalse($servicio->completar($sesion), 'La segunda llamada volvió a completarla.');

        $this->assertSame(1, UserCourse::where('user_id', $user->id)->count());
    }

    public function test_it_tells_the_company_and_the_student(): void
    {
        [$user, $course] = $this->compraPendiente();

        app(CheckoutCompletion::class)->completar($this->sesion('cs_prueba', $course->id, $user->id));

        $destinatarios = collect(Mail::getSymfonyTransport()->messages())
            ->map(fn ($m) => $m->getOriginalMessage()->getTo()[0]->getAddress())
            ->all();

        $this->assertContains('avisos@tap.test', $destinatarios);
        $this->assertContains('alumno@tap.test', $destinatarios);
    }

    /** Una sesión cuya orden ya no existe no debe reventar: sólo no hace nada. */
    public function test_an_unknown_session_is_ignored(): void
    {
        [$user, $course] = $this->compraPendiente();

        $this->assertFalse(
            app(CheckoutCompletion::class)->completar($this->sesion('cs_que_no_existe', $course->id, $user->id)),
        );
        $this->assertSame(0, UserCourse::count());
    }

    private function crearTablas(): void
    {
        Schema::create('users', function (Blueprint $t): void {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->string('password')->nullable();
            $t->timestamps();
        });

        Schema::create('courses', function (Blueprint $t): void {
            $t->id();
            $t->string('titulo');
            $t->string('slug');
            $t->integer('tiempovalido')->nullable();
            $t->timestamps();
        });

        // La plantilla del correo al alumno cuenta los capítulos del curso.
        Schema::create('chapters', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('course_id');
            $t->timestamps();
        });

        Schema::create('course_orders', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->text('course')->nullable();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->decimal('amount', 10, 2)->nullable();
            $t->string('currency')->nullable();
            $t->string('order_id')->nullable();
            $t->string('txn_id')->nullable();
            $t->string('checkout_session_id')->nullable();
            $t->string('payment_status')->nullable();
            $t->string('cupon')->nullable();
            $t->timestamps();
        });

        Schema::create('user_courses', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->unsignedBigInteger('course_id');
            $t->date('fecha_inicio')->nullable();
            $t->integer('dias_activo')->nullable();
            $t->integer('aprobado')->default(0);
            $t->integer('intentos')->default(0);
            $t->integer('caducado')->default(0);
            $t->integer('finalizado')->default(0);
            $t->timestamps();
        });
    }
}
