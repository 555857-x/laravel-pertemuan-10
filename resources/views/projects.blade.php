@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<section style="max-width: 1000px; margin: 40px auto; padding: 0 20px;">
    <h1>Projects</h1>

    {{-- Form GET: parameter masuk ke URL. Nilai lama dipertahankan dengan request(). --}}
    <form method="GET" action="{{ route('projects') }}" class="row g-2 align-items-end mt-2 mb-3">
        <div class="col-md-4">
            <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari project ...">
        </div>
        <div class="col-md-2">
            <select name="category" class="form-select">
                <option value="">Semua kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->slug }}" @selected(request('category') === $c->slug)>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        {{-- Latihan: dropdown filter technology. --}}
        <div class="col-md-2">
            <select name="technology" class="form-select">
                <option value="">Semua technology</option>
                @foreach ($technologies as $t)
                    <option value="{{ $t->slug }}" @selected(request('technology') === $t->slug)>
                        {{ $t->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="sort" class="form-select">
                <option value="terbaru" @selected(request('sort', 'terbaru') === 'terbaru')>Terbaru</option>
                <option value="terlama" @selected(request('sort') === 'terlama')>Terlama</option>
                <option value="az" @selected(in_array(request('sort'), ['az', 'judul']))>Judul A-Z</option>
                <option value="tanggal" @selected(request('sort') === 'tanggal')>Tanggal Project</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary">Cari</button>
            <a href="{{ route('projects') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    {{-- Latihan: jumlah hasil dari total() paginator. --}}
    <p class="text-muted small">{{ $projects->total() }} hasil ditemukan</p>

    <div class="row g-4">
        @forelse ($projects as $project)
            <div class="col-md-4">
                <div class="card h-100">
                    @if ($project->thumbnail)
                        <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->name }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <h2 class="h5">
                            <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">{{ $project->name }}</a>
                        </h2>
                        <p class="small text-muted mb-2">{{ $project->short_description }}</p>
                        <p class="small mb-2">
                            <span class="badge bg-primary">{{ $project->category?->name }}</span>
                            @foreach ($project->technologies as $tech)
                                <span class="badge bg-light text-dark border">{{ $tech->name }}</span>
                            @endforeach
                        </p>
                        @if ($project->project_date)
                            <p class="small text-muted mb-0">{{ $project->project_date->format('d M Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Tidak ada project yang cocok.</p>
        @endforelse
    </div>

    {{-- withQueryString() di controller membuat link halaman 2 tetap membawa search/filter. --}}
    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</section>
@endsection
