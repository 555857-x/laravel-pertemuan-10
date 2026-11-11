<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modul P10 memakai kolom name dan category_id di tabel projects.
     * Kolom title lama diganti nama jadi name, lalu ditambah category_id.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
        });

        Schema::table('projects', function (Blueprint $table) {
            // Panjang disamakan dengan validasi modul: max:255.
            $table->string('name', 255)->change();

            // nullable karena project lama belum punya kategori.
            $table->foreignId('category_id')
                ->nullable()
                ->after('name')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('name', 200)->change();
            $table->renameColumn('name', 'title');
        });
    }
};
