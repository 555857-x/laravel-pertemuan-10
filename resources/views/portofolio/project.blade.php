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
    <p class="text-muted">Kategori: {{ $project->category?->name ?? '-' }}</p>
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
