@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<section style="max-width: 1000px; margin: 40px auto; padding: 0 20px;">
    <h1>Projects</h1>

    <div class="row g-4 mt-1">
        @forelse ($projects as $project)
            <div class="col-md-4">
                <div class="card h-100">
                    @if ($project->thumbnail)
                        <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->name }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <h2 class="h5">{{ $project->name }}</h2>
                        <p class="small text-muted">{{ \Illuminate\Support\Str::limit($project->description, 80) }}</p>
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-primary">Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Belum ada project.</p>
        @endforelse
    </div>
</section>
@endsection
