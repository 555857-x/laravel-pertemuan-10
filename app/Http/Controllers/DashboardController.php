<?php

namespace App\Http\Controllers;

use App\Models\{Category, Project, Skill, Technology};

class DashboardController extends Controller
{
    public function index()
    {
        return view('posts.dashboard', [
            'totalProjects'     => Project::count(),
            'totalCategories'   => Category::count(),
            'totalTechnologies' => Technology::count(),
            'totalSkills'       => Skill::count(),
            'latestProjects'    => Project::with('category')->latest()->take(5)->get(),

            // Latihan mandiri: Published/Draft, Skill Teratas, jumlah project per kategori.
            'totalPublished'    => Project::published()->count(),
            'totalDraft'        => Project::draft()->count(),
            'topSkill'          => Skill::orderByDesc('level')->first(),
            'categories'        => Category::withCount('projects')->orderBy('name')->get(),
        ]);
    }
}
