<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class P9AuthTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password123'), 'role' => 'admin']);
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get('/post/projects')->assertRedirect('/login');
        $this->get('/post/projects/create')->assertRedirect('/login');
        $this->get('/post/profile-saya')->assertRedirect('/login');
    }

    public function test_public_pages_open(): void
    {
        $this->get('/')->assertOk()->assertSee('Login');
        $this->get('/about')->assertOk();
        $this->get('/login')->assertOk();
    }

    public function test_admin_login_redirects_to_projects(): void
    {
        $this->admin();
        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'password123'])
            ->assertRedirect('/post/projects');
        $this->assertAuthenticated();
        $this->get('/post/projects')->assertOk()->assertSee('Admin')->assertSee('Logout');
    }

    public function test_intended_redirect(): void
    {
        $this->admin();
        $this->get('/post/projects/create');
        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'password123'])
            ->assertRedirect('/post/projects/create');
    }

    public function test_wrong_password(): void
    {
        $this->admin();
        $this->from('/login')->post('/login', ['email' => 'admin@example.com', 'password' => 'salah'])
            ->assertRedirect('/login')->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_closes_post(): void
    {
        $this->actingAs($this->admin());
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        $this->get('/post/projects')->assertRedirect('/login');
    }

    public function test_non_admin_forbidden(): void
    {
        $user = User::create(['name' => 'Budi', 'email' => 'budi@example.com', 'password' => Hash::make('password')]);
        $this->assertSame('user', $user->fresh()->role);
        $this->actingAs($user)->get('/post/projects')->assertForbidden();
        $this->actingAs($user)->get('/post/profile-saya')->assertOk()->assertSee('Budi')->assertSee('bukan admin');
    }

    public function test_register_closed(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', ['name' => 'x', 'email' => 'x@x.com', 'password' => 'password', 'password_confirmation' => 'password'])->assertNotFound();
    }

    public function test_seeder_hashes_and_crud_still_works(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertStringStartsWith('$2y$', $admin->password);
        $this->actingAs($admin)->get('/post/projects')->assertOk();
        $this->actingAs($admin)->get('/post/projects/trash')->assertOk();
        $this->actingAs($admin)->get('/post/projects/search?kata=Judul')->assertOk();
        $this->actingAs($admin)->get('/dashboard')->assertOk();
    }
}
