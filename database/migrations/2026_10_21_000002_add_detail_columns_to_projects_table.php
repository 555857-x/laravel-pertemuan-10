<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambah kolom status, tanggal mulai, dan anggaran.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
                $table->string('short_description')->nullable()->after('slug');
                $table->text('description')->nullable()->after('short_description');
                $table->string('status')->default('draft')->after('description');
                $table->date('project_date')->nullable();
                $table->boolean('is_featured')->default(false);
                $table->string('project_url')->nullable();
                $table->string('repository_url')->nullable();
        });
    }

    /**
     * Menghapus kolom status, tanggal mulai, dan anggaran.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['status', 'started_at', 'budget']);
        });
    }
};
