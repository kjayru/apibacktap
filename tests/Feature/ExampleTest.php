<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * El dominio sólo sirve al panel: la raíz lleva al acceso, no a una página propia.
     */
    public function test_the_root_redirects_to_the_admin_login(): void
    {
        $this->get('/')->assertRedirect(route('filament.admin.auth.login'));
    }
}
