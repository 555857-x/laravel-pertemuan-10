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
            $table->enum('status', ['draft', 'published'])->default('draft')->after('description');
            $table->date('started_at')->nullable()->after('status');
            $table->unsignedBigInteger('budget')->nullable()->after('started_at');
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
