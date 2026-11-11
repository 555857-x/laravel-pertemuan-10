<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom thumbnail menyimpan PATH file, bukan file-nya.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('thumbnail')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
};
