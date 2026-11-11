@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <section style="max-width: 800px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
        @include('partials.flash')

        <article class="card card-body">
            @if ($project->thumbnail)
                <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->name }}" class="img-fluid rounded mb-3" style="max-height: 320px; object-fit: cover;">
            @endif

            <h1 class="mt-0">{{ $project->name }}</h1>

            <p class="mb-1">
                <span class="badge {{ $project->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($project->status) }}
                </span>
            </p>

            <table class="table table-sm w-auto mt-2">
                <tr>
                    <th>Kategori</th>
                    <td>{{ $project->category?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Mulai</th>
                    <td>{{ $project->started_at?->format('d/m/Y') ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Anggaran</th>
                    <td>{{ $project->budget !== null ? 'Rp ' . number_format($project->budget, 2, ',', '.') : '-' }}</td>
                </tr>
                <tr>
                    <th>Dibuat</th>
                    <td>{{ $project->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Diubah</th>
                    <td>{{ $project->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>

            {{-- Deskripsi di-escape lebih dulu, lalu baris barunya ditampilkan. --}}
            <div style="line-height: 1.8;">
                {!! nl2br(e($project->description)) !!}
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <a href="{{ route('post.projects.index') }}" class="btn btn-secondary">Kembali</a>
                <a href="{{ route('post.projects.edit', $project->id) }}" class="btn btn-primary">Edit Project</a>

                <form
                    action="{{ route('post.projects.destroy', $project->id) }}"
                    method="POST"
                    onsubmit="return confirm('Pindahkan project ini ke trash?')"
                    class="m-0"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Project</button>
                </form>
            </div>
        </article>
        {{-- Gallery project: upload banyak gambar sekaligus. --}}
        <section class="card card-body mt-4">
            <h2 class="h4">Gallery ({{ $project->images->count() }}/10)</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('post.projects.images.store', $project) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-wrap gap-2 mb-4">
                @csrf
                <input type="file" name="images[]" accept="image/*" multiple class="form-control" style="max-width: 400px;">
                <button type="submit" class="btn btn-success">Upload Gambar</button>
                <div class="form-text w-100">Boleh pilih beberapa file. jpg, jpeg, png, webp. Maks 4 MB per file, lebar minimal 800 px, maksimal 10 gambar per project.</div>
            </form>

            <div class="row g-3">
                @forelse ($project->images as $img)
                    <div class="col-6 col-md-4">
                        <div class="card h-100">
                            <img src="{{ asset('storage/' . $img->image) }}" alt="{{ $img->caption ?: $project->name }}" class="card-img-top" style="height: 160px; object-fit: cover;">
                            <div class="card-body p-2">
                                {{-- Edit caption (route PUT). --}}
                                <form action="{{ route('post.projects.images.update', [$project, $img]) }}" method="POST" class="d-flex gap-1 mb-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="caption" value="{{ $img->caption }}" placeholder="Caption" class="form-control form-control-sm">
                                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                </form>

                                <form action="{{ route('post.projects.images.destroy', [$project, $img]) }}" method="POST" onsubmit="return confirm('Hapus gambar ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger w-100">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Belum ada gambar gallery.</p>
                @endforelse
            </div>
        </section>
    </section>
@endsection
