<?php

use App\Http\Controllers\ContactAdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectGalleryController;
use App\Http\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

// Halaman publik, bisa dibuka tanpa login.
Route::view('/', 'home')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/education', 'education')->name('education');

// P11: daftar project di /projects (search, filter, sort, pagination).
Route::get('/projects', [PortfolioController::class, 'projects'])
    ->name('projects');

// sebelum : /projects/3
// sesudah : /projects/laravel-blog  ({project:slug} = route model binding lewat kolom slug)
Route::get('/projects/{project:slug}', [PortfolioController::class, 'show'])
    ->name('projects.show');

// P12: form contact. GET menampilkan form, POST dibatasi 3 pesan per menit per IP
// lewat named limiter contact (lihat AppServiceProvider).
Route::get('/contact', [ContactController::class, 'create'])->name('contact');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.store');

// Bawaan Breeze: dashboard dan pengaturan profil.
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Latihan P9: halaman khusus user login (admin maupun bukan).
    Route::view('/post/profile-saya', 'profile-saya')->name('profile.saya');
});

// Grup /post: nama route diawali post. (post.dashboard.index, post.projects.index, ...).
// Middleware admin dari P9 tetap dipasang setelah auth.
Route::prefix('post')->name('post.')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('dashboard', DashboardController::class);

    // Route tambahan harus di atas resource agar tidak dianggap projects/{project}.
    Route::get('/projects/search', [ProjectController::class, 'search'])->name('projects.search');
    Route::get('/projects/trash', [ProjectController::class, 'trash'])->name('projects.trash');
    Route::patch('/projects/{id}/restore', [ProjectController::class, 'restore'])->name('projects.restore');
    Route::delete('/projects/{id}/force-delete', [ProjectController::class, 'forceDelete'])->name('projects.forceDelete');

    Route::resource('projects', ProjectController::class);

    // Gallery project: upload banyak gambar, edit caption, hapus gambar.
    Route::post('/projects/{project}/images', [ProjectGalleryController::class, 'store'])->name('projects.images.store');
    Route::put('/projects/{project}/images/{image}', [ProjectGalleryController::class, 'update'])->name('projects.images.update');
    Route::delete('/projects/{project}/images/{image}', [ProjectGalleryController::class, 'destroy'])->name('projects.images.destroy');

    // P12 Latihan 3: pesan masuk dari form contact, hanya untuk admin.
    Route::get('/contacts', [ContactAdminController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{contact}', [ContactAdminController::class, 'show'])->name('contacts.show');

    // Latihan: edit skill.
    Route::resource('skills', SkillController::class)->only(['index', 'edit', 'update']);
});

require __DIR__.'/auth.php';
