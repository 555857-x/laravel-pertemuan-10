@extends('layouts.app')

@section('title', $project->name)

@section('content')
<section style="max-width: 900px; margin: 40px auto; padding: 0 20px;">
    <a href="{{ route('projects') }}" class="btn btn-sm btn-secondary mb-3">&larr; Semua Project</a>

@if ($project->thumbnail)
    <img src="{{ asset('storage/' . $project->thumbnail) }}"
         alt="{{ $project->name }}" class="img-fluid rounded mb-3">
@endif

    <h1>{{ $project->name }}</h1>
    <p class="text-muted mb-1">Kategori: {{ $project->category?->name ?? '-' }}
        @if ($project->project_date) · {{ $project->project_date->format('d M Y') }} @endif
    </p>
    @if ($project->short_description)
        <p class="lead">{{ $project->short_description }}</p>
    @endif
    <p>
        @foreach ($project->technologies as $tech)
            <span class="badge bg-light text-dark border">{{ $tech->name }}</span>
        @endforeach
    </p>
    {{-- P11: tombol project_url dan repository_url. --}}
    <div class="d-flex gap-2 mb-3">
        @if ($project->project_url)
            <a href="{{ $project->project_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary">Lihat Project</a>
        @endif
        @if ($project->repository_url)
            <a href="{{ $project->repository_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-dark">Repository</a>
        @endif
    </div>
    <div style="line-height: 1.8;">{!! nl2br(e($project->description)) !!}</div>

    <h2 class="h4 mt-4">Gallery</h2>
    <div class="row g-3">
@foreach ($project->images as $img)
        <figure class="col-6 col-md-4 m-0">
    <img src="{{ asset('storage/' . $img->image) }}"
         alt="{{ $img->caption ?: $project->name }}" class="img-fluid rounded" style="height: 180px; width: 100%; object-fit: cover;">
            @if ($img->caption)
                <figcaption class="small text-muted mt-1">{{ $img->caption }}</figcaption>
            @endif
        </figure>
@endforeach
    </div>

    @if ($project->images->isEmpty())
        <p class="text-muted">Belum ada gambar gallery.</p>
    @endif
</section>
@endsection
