<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class P12ApiRateLimitTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@example.com',
            'password' => Hash::make('password123'), 'role' => 'admin',
        ]);
    }

    private function base(): array
    {
        $web = Category::create(['name' => 'Web', 'slug' => 'web']);
        $mob = Category::create(['name' => 'Mobile', 'slug' => 'mobile']);
        $lar = Technology::create(['name' => 'Laravel', 'slug' => 'laravel']);

        return compact('web', 'mob', 'lar');
    }

    private function make(string $name, $cat, array $techs = [], string $status = 'published'): Project
    {
        $p = Project::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'category_id' => $cat->id,
            'short_description' => "Ringkasan $name",
            'status' => $status,
        ]);
        $p->technologies()->attach(collect($techs)->pluck('id'));

        return $p;
    }

    public function test_api_projects_returns_resource_json_only_for_published(): void
    {
        $b = $this->base();
        $this->make('Laravel Blog', $b['web'], [$b['lar']]);
        $this->make('Masih Draft', $b['web'], [$b['lar']], 'draft');

        $res = $this->getJson('/api/projects');

        $res->assertOk()
            ->assertJsonStructure(['data' => [['title', 'slug', 'summary', 'thumbnail', 'category', 'technologies']], 'links', 'meta'])
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('meta.per_page', 9)
            ->assertJsonPath('data.0.title', 'Laravel Blog')
            ->assertJsonPath('data.0.category', 'Web')
            ->assertJsonPath('data.0.technologies.0', 'Laravel');

        // Kolom internal tidak boleh bocor lewat Resource.
        $res->assertJsonMissing(['status' => 'published']);
        $this->assertArrayNotHasKey('budget', $res->json('data.0'));
    }

    public function test_api_projects_supports_search_and_category_with_query_string(): void
    {
        $b = $this->base();
        foreach (range(1, 10) as $i) {
            $this->make("Project Web Laravel $i", $b['web'], [$b['lar']]);
        }
        $this->make('Aplikasi Mobile Catatan', $b['mob']);

        $this->getJson('/api/projects?search=mobile')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'Aplikasi Mobile Catatan');

        $res = $this->getJson('/api/projects?search=laravel&category=web');
        $res->assertOk()->assertJsonPath('meta.total', 10);

        // withQueryString(): filter ikut terbawa ke link halaman berikutnya.
        $this->assertStringContainsString('search=laravel', $res->json('links.next'));
        $this->assertStringContainsString('category=web', $res->json('links.next'));

        // Tanpa hasil tetap 200 dengan data kosong, bukan 404,
        // dan bentuk JSON-nya tetap data + links + meta.
        $kosong = $this->getJson('/api/projects?search=flutter');
        $kosong->assertOk()->assertJsonPath('meta.total', 0);
        $this->assertSame([], $kosong->json('data'));
        $this->assertSame(['data', 'links', 'meta'], array_keys($kosong->json()));
    }

    public function test_api_project_detail_by_slug_and_404_for_unknown_or_draft(): void
    {
        $b = $this->base();
        $p = $this->make('Laravel Blog', $b['web'], [$b['lar']]);
        $draft = $this->make('Masih Draft', $b['web'], [], 'draft');

        $this->getJson('/api/projects/laravel-blog')
            ->assertOk()
            ->assertJsonPath('data.title', 'Laravel Blog')
            ->assertJsonPath('data.slug', 'laravel-blog');

        // ID bukan slug, jadi tetap 404.
        $this->getJson('/api/projects/' . $p->id)->assertNotFound();
        $this->getJson('/api/projects/tidak-ada')->assertNotFound();
        $this->getJson('/api/projects/' . $draft->slug)->assertNotFound();
    }

    public function test_api_error_is_json_even_without_accept_header(): void
    {
        // shouldRenderJsonWhen: /api/* selalu JSON, walau header Accept bukan JSON.
        $res = $this->get('/api/projects/tidak-ada');

        $res->assertNotFound();
        $this->assertStringContainsString('application/json', $res->headers->get('content-type'));
    }

    public function test_api_categories_returns_projects_count(): void
    {
        $b = $this->base();
        $this->make('Web Satu', $b['web']);
        $this->make('Web Dua', $b['web'], [], 'draft');
        $this->make('Mobile Satu', $b['mob']);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'slug', 'projects_count']]])
            ->assertJsonPath('data.0.slug', 'mobile')
            ->assertJsonPath('data.0.projects_count', 1)
            ->assertJsonPath('data.1.slug', 'web')
            ->assertJsonPath('data.1.projects_count', 2);
    }

    public function test_api_response_sends_rate_limit_headers_and_429_after_limit(): void
    {
        $b = $this->base();
        $this->make('Laravel Blog', $b['web']);

        $first = $this->getJson('/api/projects');
        $first->assertOk();
        $this->assertSame('60', $first->headers->get('X-RateLimit-Limit'));
        $this->assertSame('59', $first->headers->get('X-RateLimit-Remaining'));

        // Request ke-2 sampai ke-60 masih lolos.
        foreach (range(2, 60) as $i) {
            $this->getJson('/api/projects')->assertOk();
        }

        // Request ke-61 kena batas.
        $limited = $this->getJson('/api/projects');
        $limited->assertStatus(429);
        $this->assertNotNull($limited->headers->get('Retry-After'));
        $this->assertSame('0', $limited->headers->get('X-RateLimit-Remaining'));
    }

    public function test_contact_form_stores_message_and_validates_input(): void
    {
        $this->get('/contact')->assertOk()->assertSee('Hubungi Saya');

        $this->post('/contact', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'subject' => 'Tanya project Laravel',
            'message' => 'Halo, saya ingin bertanya soal project Laravel Blog.',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('contacts', [
            'email' => 'budi@example.com',
            'is_read' => false,
        ]);

        // Validasi gagal: email wajib, message minimal 10 karakter.
        $this->postJson('/contact', ['name' => 'Tanpa Email', 'message' => 'pendek'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'message']);

        $this->assertSame(1, Contact::count());
    }

    public function test_contact_form_is_throttled_to_three_per_minute(): void
    {
        $payload = fn (int $i) => [
            'name' => "Pengirim $i",
            'email' => "pengirim$i@example.com",
            'subject' => "Pesan $i",
            'message' => "Ini pesan percobaan nomor $i untuk menguji throttle.",
        ];

        foreach (range(1, 3) as $i) {
            $this->post('/contact', $payload($i))->assertRedirect();
        }

        $this->post('/contact', $payload(4))->assertStatus(429);
        $this->assertSame(3, Contact::count());
    }

    public function test_admin_contacts_page_is_protected_and_marks_message_read(): void
    {
        $contact = Contact::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'subject' => 'Tanya project Laravel',
            'message' => 'Halo, saya ingin bertanya soal project Laravel Blog.',
        ]);

        // Tamu diarahkan ke /login.
        $this->get('/post/contacts')->assertRedirect('/login');

        $admin = $this->admin();
        $this->actingAs($admin)->get('/post/contacts')
            ->assertOk()
            ->assertSee('Belum dibaca')
            ->assertSee('budi@example.com');

        $this->actingAs($admin)->get('/post/contacts/' . $contact->id)
            ->assertOk()
            ->assertSee('Tanya project Laravel');

        $this->assertTrue($contact->fresh()->is_read);

        $this->actingAs($admin)->get('/post/contacts')
            ->assertOk()
            ->assertSee('Sudah dibaca');
    }

    public function test_dashboard_shows_messages_and_unread_count(): void
    {
        Contact::create(['name' => 'A', 'email' => 'a@example.com', 'message' => 'Pesan pertama yang cukup panjang.']);
        Contact::create(['name' => 'B', 'email' => 'b@example.com', 'message' => 'Pesan kedua yang cukup panjang.', 'is_read' => true]);

        $this->actingAs($this->admin())->get('/post/dashboard')
            ->assertOk()
            ->assertViewHas('totalMessages', 2)
            ->assertViewHas('unreadMessages', 1)
            ->assertSee('Messages')
            ->assertSee('Belum Dibaca');
    }
}
