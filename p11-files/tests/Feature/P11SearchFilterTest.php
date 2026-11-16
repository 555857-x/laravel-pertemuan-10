<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class P11SearchFilterTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password123'), 'role' => 'admin']);
    }

    private function base(): array
    {
        $web = Category::create(['name' => 'Web Application', 'slug' => 'web-application']);
        $mob = Category::create(['name' => 'Mobile', 'slug' => 'mobile']);
        $lar = Technology::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $flt = Technology::create(['name' => 'Flutter', 'slug' => 'flutter']);
        return compact('web', 'mob', 'lar', 'flt');
    }

    private function make(string $name, $cat, array $techs = [], string $status = 'published', array $extra = []): Project
    {
        $p = Project::create(array_merge([
            'name' => $name, 'slug' => \Str::slug($name), 'category_id' => $cat->id,
            'short_description' => "Ringkasan $name", 'status' => $status,
        ], $extra));
        $p->technologies()->attach(collect($techs)->pluck('id'));
        return $p;
    }

    public function test_store_generates_slug_and_attaches_technologies(): void
    {
        $b = $this->base();
        $this->actingAs($this->admin())->post('/post/projects', [
            'name' => 'Laravel Blog', 'category_id' => $b['web']->id, 'status' => 'published',
            'short_description' => 'Blog sederhana', 'technologies' => [$b['lar']->id, $b['flt']->id],
            'project_url' => 'https://example.com', 'is_featured' => '1',
        ])->assertRedirect(route('post.projects.create'));

        $p = Project::first();
        $this->assertSame('laravel-blog', $p->slug);
        $this->assertTrue($p->is_featured);
        $this->assertCount(2, $p->technologies);

        // name unik, url harus valid, status hanya draft/published
        $this->post('/post/projects', ['name' => 'Laravel Blog', 'category_id' => $b['web']->id, 'status' => 'x', 'project_url' => 'bukan-url'])
            ->assertSessionHasErrors(['name', 'status', 'project_url']);
    }

    public function test_update_syncs_technologies_and_ignores_own_name(): void
    {
        $b = $this->base();
        $p = $this->make('Laravel Blog', $b['web'], [$b['lar']]);
        $this->actingAs($this->admin())->put("/post/projects/{$p->id}", [
            'name' => 'Laravel Blog', 'category_id' => $b['web']->id, 'status' => 'published',
            'technologies' => [$b['flt']->id],
        ])->assertRedirect(route('post.projects.index'));
        $this->assertSame(['Flutter'], $p->fresh()->technologies->pluck('name')->all());
    }

    public function test_public_list_search_filter_sort_pagination(): void
    {
        $b = $this->base();
        for ($i = 1; $i <= 11; $i++) {
            $this->make("Laravel App $i", $b['web'], [$b['lar']]);
        }
        $this->make('Flutter Kasir', $b['mob'], [$b['flt']]);
        $this->make('Laravel Draft', $b['web'], [$b['lar']], 'draft');

        $this->get('/projects')->assertOk()->assertViewHas('projects', fn ($p) => $p->total() === 12 && $p->count() === 9)
            ->assertDontSee('Laravel Draft');
        $this->get('/projects?search=flutter')->assertViewHas('projects', fn ($p) => $p->total() === 1);
        $this->get('/projects?category=mobile')->assertViewHas('projects', fn ($p) => $p->total() === 1);
        $this->get('/projects?technology=laravel')->assertViewHas('projects', fn ($p) => $p->total() === 11);
        $this->get('/projects?search=laravel&category=web-application&sort=az')
            ->assertViewHas('projects', fn ($p) => $p->total() === 11 && $p->first()->name === 'Laravel App 1')
            ->assertSee('search=laravel&amp;category=web-application&amp;sort=az&amp;page=2', false);
        $this->get('/projects?search=tidakada')->assertSee('Tidak ada project yang cocok.');
    }

    public function test_detail_by_slug_and_draft_404(): void
    {
        $b = $this->base();
        $p = $this->make('Laravel Blog', $b['web'], [$b['lar']], 'published', ['repository_url' => 'https://github.com/x/y']);
        $d = $this->make('Rahasia', $b['web'], [], 'draft');
        $this->get('/projects/laravel-blog')->assertOk()->assertSee('Laravel Blog')->assertSee('Repository');
        $this->get("/projects/{$p->id}")->assertNotFound();
        $this->get('/projects/rahasia')->assertNotFound();
        $this->assertSame(url('/projects/laravel-blog'), route('projects.show', $p));
    }

    public function test_no_n_plus_one_on_list(): void
    {
        $b = $this->base();
        for ($i = 1; $i <= 9; $i++) {
            $this->make("App $i", $b['web'], [$b['lar'], $b['flt']]);
        }
        DB::enableQueryLog();
        $this->get('/projects')->assertOk();
        $this->assertLessThan(10, count(DB::getQueryLog()));
    }

    public function test_admin_search_with_status_filter(): void
    {
        $b = $this->base();
        $this->make('Laravel Satu', $b['web'], [], 'published');
        $this->make('Laravel Dua', $b['web'], [], 'draft');
        $this->make('Flutter Tiga', $b['web'], [], 'draft');
        $this->actingAs($this->admin())->get('/post/projects?search=laravel&status=draft')
            ->assertOk()->assertViewHas('projects', fn ($p) => $p->total() === 1 && $p->first()->name === 'Laravel Dua')
            ->assertSee('1 hasil ditemukan');
    }
}
