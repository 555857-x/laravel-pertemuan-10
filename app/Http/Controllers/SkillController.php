<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        return view('posts.skills.index', [
            'skills' => Skill::orderByDesc('level')->get(),
        ]);
    }

    public function edit(Skill $skill)
    {
        return view('posts.skills.edit', compact('skill'));
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'level' => 'required|integer|min:0|max:100',
        ], [
            'name.required'  => 'Nama skill wajib diisi.',
            'level.required' => 'Level wajib diisi.',
            'level.integer'  => 'Level harus angka.',
            'level.min'      => 'Level minimal 0.',
            'level.max'      => 'Level maksimal 100.',
        ]);

        $skill->update($validated);

        return redirect()->route('post.skills.index')->with('success', 'Skill berhasil diperbarui.');
    }
}
