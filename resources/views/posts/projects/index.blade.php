@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <section style="max-width: 1100px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="mb-1">Projects</h1>
                <p class="text-muted mb-0">Beberapa project yang pernah saya kerjakan.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('post.projects.trash') }}" class="btn btn-outline-secondary">
                    Trash ({{ $totalTrash }})
                </a>
                <a href="{{ route('post.projects.create') }}" class="btn btn-primary">
                    Tambah Project
                </a>
            </div>
        </div>

        {{-- Ringkasan dari retrieving aggregates di controller. --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="small text-muted">Total Project</div>
                    <div class="fs-4 fw-bold">{{ $totalProjects }}</div>
                </div></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="small text-muted">Published</div>
                    <div class="fs-4 fw-bold">{{ $totalPublished }}</div>
                </div></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="small text-muted">Dibuat Hari Ini</div>
                    <div class="fs-4 fw-bold">{{ $totalToday }}</div>
                </div></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100"><div class="card-body">
                    <div class="small text-muted">Total Anggaran</div>
                    <div class="fs-6 fw-bold">Rp {{ number_format($totalBudget, 2, ',', '.') }}</div>
                </div></div>
            </div>
        </div>

        @if ($latestProject)
            <p class="small text-muted">
                Project terbaru: <strong>{{ $latestProject->name }}</strong>
                · ID terbesar: {{ $maxId }}
            </p>
        @endif

        {{-- Search box mengarah ke route projects.search. --}}
        <form action="{{ route('post.projects.search') }}" method="GET" class="d-flex gap-2 mb-3">
            <input
                type="text"
                name="kata"
                class="form-control"
                placeholder="Cari judul atau deskripsi ..."
                aria-label="Cari project"
            >
            <input type="hidden" name="per_page" value="{{ $perPage }}">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        {{-- Filter status dan jumlah data per halaman. --}}
        <form action="{{ route('post.projects.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <label for="status" class="small">Status</label>
            <select id="status" name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="" @selected($status === '')>Semua</option>
                <option value="published" @selected($status === 'published')>Published</option>
                <option value="draft" @selected($status === 'draft')>Draft</option>
            </select>

            <label for="per_page" class="small ms-2">Per halaman</label>
            <select id="per_page" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                @foreach ([5, 10, 25] as $option)
                    <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                @endforeach
            </select>

            <noscript><button type="submit" class="btn btn-sm btn-secondary">Terapkan</button></noscript>
        </form>

        @include('partials.flash')

        @if ($projects->isEmpty())
            <div class="alert alert-warning">
                Belum ada project. Tambahkan lewat tombol Tambah Project.
            </div>
        @else
            @include('posts.projects.partials.table')
        @endif
    </section>
@endsection
