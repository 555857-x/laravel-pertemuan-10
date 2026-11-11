<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun admin. Password wajib di-hash, kalau tidak login selalu gagal.
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Akun biasa untuk menguji EnsureAdmin (harus dapat 403 di /post).
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        // Seeder dijalankan berurutan. Kategori dulu karena project butuh category_id.
        $this->call([
            CategorySeeder::class,
            TechnologySeeder::class,
            SkillSeeder::class,
            ProjectTableSeeder::class,
            RandomProjectSeeder::class,
        ]);
    }
}
