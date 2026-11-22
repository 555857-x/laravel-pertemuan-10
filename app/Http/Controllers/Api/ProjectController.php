<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * GET /api/projects
     * Latihan 1: endpoint menerima ?search dan ?category seperti halaman publik P11.
     */
    public function index(Request $request)
    {
        $projects = Project::with(['category', 'technologies'])
            ->where('status', 'published')
            // search: cocokkan ke nama atau ringkasan project.
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('short_description', 'like', '%' . $request->search . '%');
                });
            })
            // category: difilter lewat slug kategori, bukan id.
            ->when($request->category, function ($query) use ($request) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
            })
            ->latest()
            ->paginate(9)
            // withQueryString() menjaga ?search dan ?category tetap ada di links.next.
            ->withQueryString();

        return ProjectResource::collection($projects);
    }

    /**
     * GET /api/projects/{slug}
     * Slug tidak ada -> 404, status bukan published -> 404 juga.
     */
    public function show(Project $project)
    {
        abort_if($project->status !== 'published', 404);

        return new ProjectResource($project->load(['category', 'technologies']));
    }
}
