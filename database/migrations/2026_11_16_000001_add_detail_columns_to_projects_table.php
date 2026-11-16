<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * P11: kolom detail project. Migration baru, migration lama tidak diubah.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('short_description')->nullable()->after('slug');

            // description dan status sudah ada sejak P7-P8, jadi tidak dibuat ulang
            // (bisa error "Duplicate column"). Tipenya disamakan dengan modul lewat change().
            $table->text('description')->nullable()->after('short_description')->change();
            $table->string('status')->default('draft')->after('description')->change();

            $table->date('project_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('project_url')->nullable();
            $table->string('repository_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Index unique dihapus dulu sebelum kolomnya.
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'slug',
                'short_description',
                'project_date',
                'is_featured',
                'project_url',
                'repository_url',
            ]);

            // Kembalikan tipe kolom lama dari P7-P8.
            $table->mediumText('description')->nullable(false)->change();
            $table->enum('status', ['draft', 'published'])->default('draft')->change();
        });
    }
};
