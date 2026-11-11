<?php

namespace App\Http\Controllers;

use App\Models\Project;

class PortfolioController extends Controller
{
    // Halaman publik daftar project.
    public function projects()
    {
        return view('projects', ['projects' => Project::latest()->get()]);
    }

    // Halaman publik detail project + gallery.
    public function show(Project $project)
    {
        $project->load(['category', 'images']);
        return view('portofolio.project', compact('project'));
    }
}
