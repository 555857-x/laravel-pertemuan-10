@extends('layouts.app')

@section('title', 'Dashboard')

{{-- Komponen stat-card memakai class Tailwind, jadi CSS Tailwind (breeze.css) dimuat di halaman ini. --}}
@push('styles')
    @vite('resources/css/breeze.css')
@endpush

@section('content')
<section class="max-w-6xl mx-auto px-5 py-8">
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <x-stat-card label="Projects"     :value="$totalProjects" />
    <x-stat-card label="Categories"   :value="$totalCategories" color="text-green-600" />
    <x-stat-card label="Technologies" :value="$totalTechnologies" color="text-purple-600" />
    <x-stat-card label="Skills"       :value="$totalSkills" color="text-orange-600" />
</div>

    {{-- Latihan mandiri: Published + Draft = Total Projects. --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
        <x-stat-card label="Published"     :value="$totalPublished" color="text-green-600" />
        <x-stat-card label="Draft"         :value="$totalDraft" color="text-gray-600" />
        <x-stat-card label="Skill Teratas" :value="$topSkill ? $topSkill->name . ' (' . $topSkill->level . '%)' : '-'" color="text-red-600" />
    </div>

    <div class="grid md:grid-cols-2 gap-6 mt-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold mb-4">5 Project Terbaru</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2">Nama</th>
                        <th class="py-2">Kategori</th>
                        <th class="py-2">Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestProjects as $project)
                        <tr class="border-b">
                            <td class="py-2">
                                <a href="{{ route('post.projects.show', $project) }}" class="text-blue-600">{{ $project->name }}</a>
                            </td>
                            <td class="py-2">{{ $project->category?->name ?? '-' }}</td>
                            <td class="py-2">{{ $project->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-2 text-gray-500">Belum ada project.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Latihan mandiri: withCount() per kategori. --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold mb-4">Project per Kategori</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2">Kategori</th>
                        <th class="py-2">Jumlah Project</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr class="border-b">
                            <td class="py-2">{{ $category->name }}</td>
                            <td class="py-2">{{ $category->projects_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 mt-8">
        <a href="{{ route('post.projects.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Kelola Project</a>
        <a href="{{ route('post.skills.index') }}" class="bg-orange-600 text-white px-4 py-2 rounded">Kelola Skill</a>
        <a href="{{ route('projects') }}" class="bg-gray-700 text-white px-4 py-2 rounded">Lihat Halaman Publik</a>
    </div>
</section>
@endsection
