<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_login_page(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk untuk memulai aktivitas')->assertSee('Pasang aplikasi di HP');
    }

    public function test_install_button_is_not_rendered_after_login(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);
        $this->actingAs($employee)->get(route('dashboard'))->assertOk()->assertDontSee('Pasang aplikasi di HP');
    }

    public function test_active_employee_can_login(): void
    {
        $user = User::factory()->create(['password' => 'rahasia', 'is_active' => true]);

        $this->post('/login', ['email' => $user->email, 'password' => 'rahasia'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_employee_cannot_login(): void
    {
        $user = User::factory()->create(['password' => 'rahasia', 'is_active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'rahasia'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
