<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    public function test_admin_panel_requires_authentication(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }
}
