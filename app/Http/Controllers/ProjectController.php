<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    // Pilihan jumlah data per halaman.
    private const PER_PAGE_OPTIONS = [5, 10, 25];

    public function index(Request $request)
    {
        $perPage = $this->perPage($request);
        $status = $request->query('status', '');

        // Filter status memakai local scope sebelum paginate.
        $query = Project::with('category');

        if ($status === 'published') {
            $query->published();
        } elseif ($status === 'draft') {
            $query->draft();
        }

        $projects = $query->latest()->paginate($perPage)->withQueryString();

        // Retrieving aggregates: hasil berupa angka, bukan model.
        $totalProjects = Project::count();
        $totalPublished = Project::published()->count();
        $totalToday = Project::today()->count();
        $totalTrash = Project::onlyTrashed()->count();
        $totalBudget = Project::sum('budget');
        $maxId = Project::max('id');
        $latestProject = Project::latest()->first();

        return view('posts.projects.index', compact(
            'projects',
            'status',
            'perPage',
            'totalProjects',
            'totalPublished',
            'totalToday',
            'totalTrash',
            'totalBudget',
            'maxId',
            'latestProject',
        ));
    }

    public function search(Request $request)
    {
        $cari = trim((string) $request->query('kata', ''));

        if ($cari === '') {
            return redirect()->route('post.projects.index');
        }

        $perPage = $this->perPage($request);

        // withQueryString menjaga kata kunci saat pindah halaman.
        $projects = Project::with('category')->search($cari)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $jumlahProject = $projects->total();

        return view('posts.projects.search', compact('projects', 'cari', 'jumlahProject', 'perPage'));
    }

    public function create()
    {
        return view('posts.projects.create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('projects/thumbnails', 'public');
        }

        Project::create($validated);

        return redirect()->route('post.projects.create')
            ->with('success', 'Project berhasil ditambahkan.');
    }

    public function show(Project $project)
    {
        // Route model binding otomatis memberi 404 jika data tidak ada.
        $project->load(['category', 'images']);

        return view('posts.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('posts.projects.edit', [
            'project' => $project,
            'categories' => Category::all(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        if ($request->hasFile('thumbnail')) {
            if ($project->thumbnail) {
                Storage::disk('public')->delete($project->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('projects/thumbnails', 'public');
        } else {
            // Tidak upload file baru: thumbnail lama jangan ditimpa null.
            unset($validated['thumbnail']);
        }

        $project->fill($validated);

        // isDirty mengecek apakah ada kolom yang benar-benar berubah.
        if (! $project->isDirty()) {
            return redirect()
                ->route('post.projects.show', $project->id)
                ->with('info', 'Tidak ada perubahan data.');
        }

        $project->save();

        return redirect()
            ->route('post.projects.index')
            ->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        // Soft delete (P8): data hanya ditandai deleted_at, masih bisa dipulihkan,
        // jadi file thumbnail dan gallery JANGAN dihapus dulu di sini.
        $project->delete();

        return redirect()
            ->route('post.projects.index')
            ->with('success', 'Project dipindahkan ke trash.');
    }

    public function trash()
    {
        $projects = Project::onlyTrashed()
            ->latest('deleted_at')
            ->paginate(10);

        return view('posts.projects.trash', compact('projects'));
    }

    public function restore($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $project->restore();

        return redirect()
            ->route('post.projects.trash')
            ->with('success', 'Project berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);

        // Hapus permanen: thumbnail dan semua gambar gallery ikut dihapus dari storage.
        if ($project->thumbnail) {
            Storage::disk('public')->delete($project->thumbnail);
        }
        foreach ($project->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $project->forceDelete();   // baris project_images ikut hilang (cascadeOnDelete)

        return redirect()
            ->route('post.projects.trash')
            ->with('success', 'Project dihapus permanen.');
    }

    // Aturan validasi untuk store dan update.
    private function rules(): array
    {
        return [
            'name'        => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp'
                           . '|max:2048|dimensions:min_width=400,min_height=300',
            'description' => 'required|string|min:10',
            'status' => 'required|in:draft,published',
            'started_at' => 'nullable|date',
            'budget' => 'nullable|integer|min:0',
        ];
    }

    // Pesan validasi dalam Bahasa Indonesia.
    private function messages(): array
    {
        return [
            'name.required' => 'Nama project wajib diisi.',
            'name.max' => 'Nama project maksimal 255 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'thumbnail.image' => 'Thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Thumbnail harus jpg, jpeg, png, atau webp.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2 MB.',
            'thumbnail.dimensions' => 'Thumbnail minimal 400 x 300 px.',
            'description.required' => 'Deskripsi project wajib diisi.',
            'description.min' => 'Deskripsi project minimal 10 karakter.',
            'status.required' => 'Status project wajib dipilih.',
            'status.in' => 'Status hanya boleh draft atau published.',
            'started_at.date' => 'Tanggal mulai tidak valid.',
            'budget.integer' => 'Anggaran harus berupa angka.',
            'budget.min' => 'Anggaran tidak boleh negatif.',
        ];
    }

    // Jumlah data per halaman dari ?per_page, hanya 5, 10, atau 25.
    private function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 10);

        return in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 10;
    }
}
