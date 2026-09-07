<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_log_in_and_access_admin_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'admin@icp.edu.np',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();

        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Admissions Control Center');
        $dashboardResponse->assertSee('Staff Account Management');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function active_staff_can_log_in_and_access_staff_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'aarav@icp.edu.np',
            'password' => '12345678',
        ]);

        $response->assertRedirect('/staff/dashboard');
        $this->assertAuthenticated();

        $dashboardResponse = $this->get('/staff/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Aarav Shrestha');
        $dashboardResponse->assertSee('Assigned Enquiries');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function inactive_staff_login_is_rejected_with_error_message()
    {
        $response = $this->post('/login', [
            'email' => 'suman@icp.edu.np',
            'password' => '12345678',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $errors = session('errors')->get('email');
        $this->assertContains('Your account is currently inactive. Please contact the administrator.', $errors);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function staff_cannot_access_admin_dashboard()
    {
        $staff = User::where('email', 'aarav@icp.edu.np')->first();

        $response = $this->actingAs($staff)->get('/admin/dashboard');
        $response->assertRedirect('/staff/dashboard');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_create_new_staff_member()
    {
        $admin = User::where('email', 'admin@icp.edu.np')->first();

        $response = $this->actingAs($admin)->post('/admin/staff', [
            'name' => 'Kiran Devkota',
            'email' => 'kiran@icp.edu.np',
            'position' => 'Counselor',
            'password' => '12345678',
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'kiran@icp.edu.np',
            'role' => 'staff',
            'status' => 'active',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_toggle_staff_status()
    {
        $admin = User::where('email', 'admin@icp.edu.np')->first();
        $staff = User::where('email', 'aarav@icp.edu.np')->first();

        $this->assertEquals('active', $staff->status);

        // Deactivate staff
        $response = $this->actingAs($admin)->post("/admin/staff/{$staff->id}/toggle-status");
        $response->assertRedirect('/admin/dashboard');

        $staff->refresh();
        $this->assertEquals('inactive', $staff->status);

        // Logout admin first so guest middleware allows /login POST request
        $this->post('/logout');

        // Verify deactivated staff can no longer log in
        $loginAttempt = $this->post('/login', [
            'email' => 'aarav@icp.edu.np',
            'password' => '12345678',
        ]);
        $loginAttempt->assertSessionHasErrors('email');
    }
}
