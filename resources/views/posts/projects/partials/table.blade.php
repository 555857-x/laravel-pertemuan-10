{{-- Tabel project dipakai di halaman index dan search. --}}
<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle bg-white">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Thumbnail</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Tgl. Mulai</th>
                <th>Anggaran</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    {{-- Nomor urut berlanjut antar halaman, halaman 2 mulai dari 11. --}}
                    <td>{{ $projects->firstItem() + $loop->index }}</td>
                    <td>
                        @if ($project->thumbnail)
                            <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->name }}" style="width: 80px; height: 60px; object-fit: cover;" class="rounded">
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('post.projects.show', $project->id) }}" class="text-decoration-none">
                            {{ $project->name }}
                        </a>
                    </td>
                    <td>{{ $project->category?->name ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $project->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </td>
                    <td>{{ $project->started_at?->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        {{ $project->budget !== null ? 'Rp ' . number_format($project->budget, 2, ',', '.') : '-' }}
                    </td>
                    <td>{{ $project->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('post.projects.show', $project->id) }}" class="btn btn-sm btn-info text-white">Detail</a>
                        <a href="{{ route('post.projects.edit', $project->id) }}" class="btn btn-sm btn-primary">Edit</a>

                        <form
                            action="{{ route('post.projects.destroy', $project->id) }}"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Pindahkan project ini ke trash?')"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-3 small text-muted">
    Halaman : {{ $projects->currentPage() }} <br>
    Jumlah Data : {{ $projects->total() }} <br>
    Data Per Halaman : {{ $projects->perPage() }}
</div>

<div class="mt-3">
    {{ $projects->links() }}
</div>
