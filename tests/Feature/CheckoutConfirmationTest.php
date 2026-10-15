<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * La pantalla de "compra realizada" muestra la ficha del curso recién comprado. Salía
 * vacía porque la orden guarda el curso bajo la clave `course_id` y se buscaba por `id`,
 * así que el curso nunca se encontraba (#1799, #1856).
 */
class CheckoutConfirmationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createLegacyTables();
    }

    public function test_the_confirmation_shows_the_purchased_course(): void
    {
        [$user, $course] = $this->makeOrderOwner();

        CourseOrder::create([
            'user_id' => $user->id,
            'course' => json_encode(['course_id' => $course->id, 'slug' => $course->slug, 'title' => $course->titulo]),
            'name' => $user->name,
            'email' => $user->email,
            'price' => 95,
            'amount' => 95,
            'currency' => 'usd',
            'order_id' => '1',
            'checkout_session_id' => 'cs_test_ok',
            'payment_status' => 'succeeded',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/orders/by-session?session_id=cs_test_ok')
            ->assertOk()
            ->assertJsonPath('data.course.slug', $course->slug)
            ->assertJsonPath('data.course.title', $course->titulo);
    }

    /** Si el curso se borró después de comprarlo, la pantalla no revienta. */
    public function test_the_confirmation_survives_a_deleted_course(): void
    {
        [$user] = $this->makeOrderOwner();

        CourseOrder::create([
            'user_id' => $user->id,
            'course' => json_encode(['course_id' => 999999, 'slug' => 'gone', 'title' => 'Gone']),
            'name' => $user->name,
            'email' => $user->email,
            'price' => 95,
            'amount' => 95,
            'currency' => 'usd',
            'order_id' => '2',
            'checkout_session_id' => 'cs_test_gone',
            'payment_status' => 'succeeded',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/orders/by-session?session_id=cs_test_gone')
            ->assertOk()
            ->assertJsonPath('data.course', null)
            ->assertJsonPath('data.course_title', 'Gone');
    }

    /** @return array{0: User, 1: Course} */
    private function makeOrderOwner(): array
    {
        $user = User::create(['name' => 'Alumno', 'email' => 'alumno@tap.test', 'password' => bcrypt('secret123')]);

        $course = Course::create([
            'titulo' => 'Level II',
            'slug' => 'level-ii',
            'resumen' => 'Resumen',
            'contenido' => '<p>Contenido</p>',
            'precio' => 95,
            'tiempovalido' => 30,
            'estado' => 1,
        ]);

        return [$user, $course];
    }

    /** El esquema es el heredado del sitio anterior, así que se crea a mano lo mínimo. */
    private function createLegacyTables(): void
    {
        Schema::create('users', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->rememberToken();
            $t->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $t): void {
            $t->id();
            $t->morphs('tokenable');
            $t->string('name');
            $t->string('token', 64)->unique();
            $t->text('abilities')->nullable();
            $t->timestamp('last_used_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });

        Schema::create('courses', function (Blueprint $t): void {
            $t->id();
            $t->string('titulo');
            $t->string('slug');
            $t->text('resumen')->nullable();
            $t->longText('contenido')->nullable();
            $t->string('banner')->nullable();
            $t->string('responsable')->nullable();
            $t->string('disponible')->nullable();
            $t->string('audio')->nullable();
            $t->string('nivel')->nullable();
            $t->decimal('precio', 10, 2)->nullable();
            $t->integer('tiempovalido')->nullable();
            $t->integer('estado')->nullable();
            $t->timestamps();
        });

        Schema::create('chapters', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('course_id');
            $t->string('titulo')->nullable();
            $t->string('slug')->nullable();
            $t->integer('order')->nullable();
            $t->timestamps();
        });

        Schema::create('course_orders', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->text('course')->nullable();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->decimal('price', 10, 2)->nullable();
            $t->decimal('amount', 10, 2)->nullable();
            $t->string('currency')->nullable();
            $t->string('order_id')->nullable();
            $t->string('txn_id')->nullable();
            $t->string('checkout_session_id')->nullable();
            $t->string('payment_status')->nullable();
            $t->string('cupon')->nullable();
            $t->timestamps();
        });
    }
}
