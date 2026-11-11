@extends('layouts.app')

@section('title', 'Trash Project')

@section('content')
    <section style="max-width: 1000px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Trash</h1>
                <p class="text-muted mb-0">Project yang sudah dihapus sementara (soft delete).</p>
            </div>
            <a href="{{ route('post.projects.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        @include('partials.flash')

        @if ($projects->isEmpty())
            <div class="alert alert-info">Trash kosong.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered align-middle bg-white">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Dihapus Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $projects->firstItem() + $loop->index }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->deleted_at->format('d/m/Y H:i') }}</td>
                                <td class="text-nowrap">
                                    <form action="{{ route('post.projects.restore', $project->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">Restore</button>
                                    </form>

                                    <form
                                        action="{{ route('post.projects.forceDelete', $project->id) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Hapus permanen? Data tidak bisa dikembalikan.')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus Permanen</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $projects->links() }}
            </div>
        @endif
    </section>
@endsection
