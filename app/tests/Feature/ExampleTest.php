<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_superadmin_can_authenticate(): void
    {
        $user = \App\Models\User::where('email', 'superadmin@ocei.local')->first();
        if (!$user) {
            $user = \App\Models\User::create([
                'user_id' => 'SA-0001',
                'full_name' => 'Super Admin',
                'email' => 'superadmin@ocei.local',
                'mobile_no' => '01700000001',
                'admin_type' => 'super_admin',
                'password' => bcrypt('password'),
                'otp_status' => 'verified',
            ]);
        }

        $response = $this->post('/login', [
            'login_id' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
