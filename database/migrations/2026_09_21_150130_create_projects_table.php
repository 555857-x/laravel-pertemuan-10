<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel projects.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->mediumText('description');
            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel projects.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};