<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectGalleryController extends Controller
{
    // Batas gambar gallery per project (latihan mandiri).
    private const MAX_IMAGES = 10;

    public function store(Request $request, Project $project)
    {
        // Sisa slot: kalau sudah ada 8 gambar, hanya boleh upload 2 lagi.
        $sisa = self::MAX_IMAGES - $project->images()->count();

        $request->validate([
            'images'   => 'required|array|max:' . max($sisa, 0),
            // Latihan: lebar minimal 800 px.
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096|dimensions:min_width=800',
        ], [
            'images.max' => 'Maksimal ' . self::MAX_IMAGES . ' gambar per project. Sisa slot: ' . max($sisa, 0) . '.',
            'images.*.dimensions' => 'Lebar gambar gallery minimal 800 px.',
        ]);

        $urutan = (int) $project->images()->max('sort_order');

        foreach ($request->file('images') as $file) {
            $project->images()->create([
                'image' => $file->store('projects/gallery', 'public'),
                'sort_order' => ++$urutan,
            ]);
        }

        return back()->with('success', 'Gambar gallery berhasil diupload.');
    }

    // Latihan: edit caption gambar (route PUT).
    public function update(Request $request, Project $project, ProjectImage $image)
    {
        abort_if($image->project_id !== $project->id, 404);

        $validated = $request->validate([
            'caption' => 'nullable|string|max:255',
        ]);

        $image->update($validated);

        return back()->with('success', 'Caption diperbarui.');
    }

    public function destroy(Project $project, ProjectImage $image)
    {
        abort_if($image->project_id !== $project->id, 404);

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return back()->with('success', 'Gambar dihapus.');
    }
}
