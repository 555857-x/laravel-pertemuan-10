<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    // P11: halaman publik daftar project dengan search, filter, sorting, pagination.
    // Query dibangun bertahap; when() hanya menjalankan closure jika nilainya terisi.
    public function projects(Request $request)
    {
        $query = Project::with('category', 'technologies')
            ->where('status', 'published')
            ->when($request->search, fn ($q, $s) =>
                $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")
                    ->orWhere('short_description', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")))
            ->when($request->category, fn ($q, $c) =>
                $q->whereHas('category', fn ($w) => $w->where('slug', $c)))
            // Latihan: filter technology lewat relasi many-to-many.
            ->when($request->technology, fn ($q, $t) =>
                $q->whereHas('technologies', fn ($w) => $w->where('slug', $t)));

        switch ($request->query('sort')) {
            case 'terlama':
                $query->oldest();
                break;
            case 'az':
            case 'judul': // slide Pengujian memakai sort=judul
                $query->orderBy('name');
                break;
            case 'tanggal': // Latihan: sort berdasarkan project_date
                $query->orderByDesc('project_date');
                break;
            default:
                $query->latest();
        }

        $projects = $query->paginate(9)->withQueryString();   // 9 item / halaman

        $categories = Category::orderBy('name')->get();
        $technologies = Technology::orderBy('name')->get();

        return view('projects', compact('projects', 'categories', 'technologies'));
    }

    // Detail lewat slug. Project draft dibuka lewat URL harus 404.
    public function show(Project $project)
    {
        abort_if($project->status !== 'published', 404);
        $project->load('category', 'technologies', 'images');
        return view('portofolio.project', compact('project'));
    }
}
