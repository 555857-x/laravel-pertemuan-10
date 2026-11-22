<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TechnologyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// File ini dimuat karena baris api: sudah didaftarkan di bootstrap/app.php.
// Semua route di sini otomatis berprefix /api.

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route API publik, dibatasi 60 request per menit per IP (named limiter portfolio-api).
Route::middleware('throttle:portfolio-api')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project:slug}', [ProjectController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/technologies', [TechnologyController::class, 'index']);
});
