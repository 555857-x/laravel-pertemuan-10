<?php

namespace App\Observers;

use App\Models\Project;
use Illuminate\Support\Facades\Log;

class ProjectObserver
{
    // Dijalankan sebelum project disimpan pertama kali.
    public function creating(Project $project): void
    {
        $project->name = trim($project->name);
        $project->status = $project->status ?: 'draft';
    }

    public function created(Project $project): void
    {
        Log::info("Project #{$project->id} dibuat: {$project->name}");
    }

    public function deleted(Project $project): void
    {
        Log::info("Project #{$project->id} dipindah ke trash.");
    }

    public function restored(Project $project): void
    {
        Log::info("Project #{$project->id} dipulihkan.");
    }

    public function forceDeleted(Project $project): void
    {
        Log::info("Project #{$project->id} dihapus permanen.");
    }
}
