@extends('layouts.app')

@section('title', 'Messages')

@section('content')
    <section style="max-width: 1000px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="mb-1">Messages</h1>
                <p class="text-muted mb-0">Pesan masuk dari form /contact.</p>
            </div>
            <span class="badge text-bg-warning fs-6">{{ $totalUnread }} belum dibaca</span>
        </div>

        <table class="table table-bordered align-middle bg-white">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Subjek</th>
                    <th>Waktu Kirim</th>
                    <th>Status</th>
                    <th style="width: 90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contacts as $contact)
                    <tr>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->subject ?? '-' }}</td>
                        <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            {{-- Badge diambil dari kolom is_read. --}}
                            @if ($contact->is_read)
                                <span class="badge text-bg-secondary">Sudah dibaca</span>
                            @else
                                <span class="badge text-bg-warning">Belum dibaca</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('post.contacts.show', $contact) }}" class="btn btn-sm btn-outline-primary">
                                Buka
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada pesan masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $contacts->links() }}
    </section>
@endsection
