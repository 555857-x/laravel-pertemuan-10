<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class P10DashboardUploadTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password123'), 'role' => 'admin']);
    }

    private function project(array $extra = []): Project
    {
        $cat = Category::firstOrCreate(['name' => 'Web']);
        return Project::create(array_merge([
            'name' => 'Portofolio Laravel', 'category_id' => $cat->id,
            'description' => 'Deskripsi project panjang', 'status' => 'published',
        ], $extra));
    }

    public function test_dashboard_counts(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@example.com')->first();
        $res = $this->actingAs($admin)->get('/post/dashboard')->assertOk()
            ->assertViewHas('totalProjects', 35)
            ->assertViewHas('totalCategories', 3)
            ->assertViewHas('totalTechnologies', 5)
            ->assertViewHas('totalSkills', 4)
            ->assertSee('Skill Teratas')->assertSee('HTML &amp; CSS (90%)', false);
        $this->assertSame(35, $res->viewData('totalPublished') + $res->viewData('totalDraft'));
        $this->assertCount(5, $res->viewData('latestProjects'));
        $this->assertSame('post.dashboard.index', app('router')->getRoutes()->match(request()->create('/post/dashboard'))->getName());
    }

    public function test_dashboard_guarded(): void
    {
        $this->get('/post/dashboard')->assertRedirect('/login');
        $user = User::create(['name' => 'B', 'email' => 'b@x.com', 'password' => Hash::make('password')]);
        $this->actingAs($user)->get('/post/dashboard')->assertForbidden();
    }

    public function test_create_form_has_categories_and_enctype(): void
    {
        Category::create(['name' => 'Web']);
        $this->actingAs($this->admin())->get('/post/projects/create')->assertOk()
            ->assertSee('multipart/form-data')->assertSee('Pilih kategori')->assertSee('Web');
    }

    public function test_store_with_thumbnail(): void
    {
        Storage::fake('public');
        $cat = Category::create(['name' => 'Web']);
        $this->actingAs($this->admin())->post('/post/projects', [
            'name' => 'Project Baru', 'category_id' => $cat->id, 'description' => 'Deskripsi sepuluh karakter',
            'status' => 'published', 'thumbnail' => UploadedFile::fake()->image('thumb.jpg', 800, 600),
        ])->assertRedirect('/post/projects/create')->assertSessionHas('success');

        $p = Project::first();
        $this->assertStringStartsWith('projects/thumbnails/', $p->thumbnail);
        Storage::disk('public')->assertExists($p->thumbnail);
        $this->get('/projects/' . $p->id)->assertOk()->assertSee('storage/' . $p->thumbnail);
        $this->get('/projects')->assertOk()->assertSee('Project Baru');
    }

    public function test_thumbnail_validation(): void
    {
        Storage::fake('public');
        $cat = Category::create(['name' => 'Web']);
        $base = ['name' => 'X', 'category_id' => $cat->id, 'description' => 'Deskripsi sepuluh karakter', 'status' => 'draft'];
        $admin = $this->admin();
        $this->actingAs($admin)->post('/post/projects', $base + ['thumbnail' => UploadedFile::fake()->create('a.pdf', 100, 'application/pdf')])
            ->assertSessionHasErrors('thumbnail');
        $this->actingAs($admin)->post('/post/projects', $base + ['thumbnail' => UploadedFile::fake()->image('a.jpg', 200, 200)])
            ->assertSessionHasErrors('thumbnail');
        $this->actingAs($admin)->post('/post/projects', array_merge($base, ['category_id' => 999]))
            ->assertSessionHasErrors('category_id');
        $this->assertSame(0, Project::count());
    }

    public function test_update_replaces_and_deletes_old_thumbnail(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $old = UploadedFile::fake()->image('old.jpg', 800, 600)->store('projects/thumbnails', 'public');
        $p = $this->project(['thumbnail' => $old]);
        $data = ['name' => $p->name, 'category_id' => $p->category_id, 'description' => $p->description, 'status' => 'published'];

        // Tanpa file baru: thumbnail lama tetap.
        $this->actingAs($admin)->put("/post/projects/{$p->id}", array_merge($data, ['name' => 'Nama Diubah']))->assertRedirect();
        $this->assertSame($old, $p->fresh()->thumbnail);

        $this->actingAs($admin)->put("/post/projects/{$p->id}", $data + ['thumbnail' => UploadedFile::fake()->image('new.jpg', 800, 600)])
            ->assertRedirect('/post/projects');
        $new = $p->fresh()->thumbnail;
        $this->assertNotSame($old, $new);
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($new);
    }

    public function test_gallery_upload_caption_delete(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $p = $this->project();

        $this->actingAs($admin)->from("/post/projects/{$p->id}")->post("/post/projects/{$p->id}/images", ['images' => [
            UploadedFile::fake()->image('1.jpg', 800, 600),
            UploadedFile::fake()->image('2.jpg', 900, 600),
            UploadedFile::fake()->image('3.png', 1000, 600),
        ]])->assertRedirect("/post/projects/{$p->id}");
        $this->assertCount(3, $p->images()->get());
        $this->assertSame([1, 2, 3], $p->images()->pluck('sort_order')->all());

        $img = $p->images()->first();
        $this->actingAs($admin)->put("/post/projects/{$p->id}/images/{$img->id}", ['caption' => 'Halaman login'])->assertRedirect();
        $this->get("/projects/{$p->id}")->assertSee('Halaman login');

        $this->actingAs($admin)->delete("/post/projects/{$p->id}/images/{$img->id}")->assertRedirect();
        Storage::disk('public')->assertMissing($img->image);
        $this->assertCount(2, $p->images()->get());

        // Gambar milik project lain: 404.
        $other = $this->project(['name' => 'Lain']);
        $img2 = $p->images()->first();
        $this->actingAs($admin)->delete("/post/projects/{$other->id}/images/{$img2->id}")->assertNotFound();
    }

    public function test_gallery_validation_and_limit(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $p = $this->project();
        $url = "/post/projects/{$p->id}/images";

        $this->actingAs($admin)->post($url, ['images' => [UploadedFile::fake()->image('kecil.jpg', 500, 400)]])->assertSessionHasErrors('images.0');
        $this->actingAs($admin)->post($url, ['images' => [UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')]])->assertSessionHasErrors('images.0');
        $this->actingAs($admin)->post($url, [])->assertSessionHasErrors('images');

        $ten = array_map(fn ($i) => UploadedFile::fake()->image("$i.jpg", 800, 600), range(1, 10));
        $this->actingAs($admin)->post($url, ['images' => $ten])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post($url, ['images' => [UploadedFile::fake()->image('11.jpg', 800, 600)]])->assertSessionHasErrors('images');
        $this->assertSame(10, $p->images()->count());
    }

    public function test_soft_delete_keeps_files_force_delete_removes(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $thumb = UploadedFile::fake()->image('t.jpg', 800, 600)->store('projects/thumbnails', 'public');
        $p = $this->project(['thumbnail' => $thumb]);
        $gal = UploadedFile::fake()->image('g.jpg', 800, 600)->store('projects/gallery', 'public');
        $p->images()->create(['image' => $gal]);

        $this->actingAs($admin)->delete("/post/projects/{$p->id}")->assertRedirect('/post/projects');
        Storage::disk('public')->assertExists($thumb);
        $this->get("/projects/{$p->id}")->assertNotFound();

        $this->actingAs($admin)->delete("/post/projects/{$p->id}/force-delete")->assertRedirect('/post/projects/trash');
        Storage::disk('public')->assertMissing($thumb);
        Storage::disk('public')->assertMissing($gal);
        $this->assertDatabaseCount('project_images', 0);
    }

    public function test_skill_edit_update(): void
    {
        $admin = $this->admin();
        $s = Skill::create(['name' => 'PHP', 'level' => 50]);
        $this->actingAs($admin)->get('/post/skills')->assertOk()->assertSee('PHP');
        $this->actingAs($admin)->get("/post/skills/{$s->id}/edit")->assertOk();
        $this->actingAs($admin)->put("/post/skills/{$s->id}", ['name' => '', 'level' => 150])->assertSessionHasErrors(['name', 'level']);
        $this->actingAs($admin)->put("/post/skills/{$s->id}", ['name' => 'PHP 8', 'level' => 95])->assertRedirect('/post/skills');
        $this->assertSame(95, (int) $s->fresh()->level);
    }

    public function test_admin_pages_render(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@example.com')->first();
        $p = Project::first();
        $this->actingAs($admin)->get('/post/projects')->assertOk()->assertSee('Kategori');
        $this->actingAs($admin)->get("/post/projects/{$p->id}")->assertOk()->assertSee('Gallery');
        $this->actingAs($admin)->get("/post/projects/{$p->id}/edit")->assertOk()->assertSee('multipart/form-data');
        $this->actingAs($admin)->get('/')->assertSee('Dashboard');
    }
}
