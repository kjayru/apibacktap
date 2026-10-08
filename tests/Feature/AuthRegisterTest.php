<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * El panel lista a los alumnos por el rol `usuario`, así que un registro sin rol no
 * aparece nunca en "User who is taking or has taken course(s)" (#1825).
 */
class AuthRegisterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createTables();
        Role::create(['name' => 'usuario', 'guard_name' => 'web']);
    }

    public function test_registering_from_the_front_gives_the_student_role(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Tania',
            'lastname' => 'Martínez',
            'email' => 'alumna@example.com',
            'password' => 'Secreta-2026',
            'password_confirmation' => 'Secreta-2026',
        ])->assertCreated();

        $user = User::where('email', 'alumna@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('usuario'));
    }

    private function createTables(): void
    {
        Schema::create('users', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->string('lastname')->nullable();
            $t->string('email');
            $t->string('password');
            $t->timestamps();
        });
        Schema::create('profiles', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->timestamps();
        });
        Schema::create('personal_access_tokens', function (Blueprint $t): void {
            $t->id();
            $t->morphs('tokenable');
            $t->string('name');
            $t->string('token', 64)->unique();
            $t->text('abilities')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamp('last_used_at')->nullable();
            $t->timestamps();
        });
        Schema::create('roles', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->string('guard_name');
            $t->timestamps();
        });
        Schema::create('model_has_roles', function (Blueprint $t): void {
            $t->unsignedBigInteger('role_id');
            $t->string('model_type');
            $t->unsignedBigInteger('model_id');
            $t->primary(['role_id', 'model_id', 'model_type']);
        });
    }
}
