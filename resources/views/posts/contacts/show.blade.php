@extends('layouts.app')

@section('title', 'Detail Pesan')

@section('content')
    <section style="max-width: 800px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
        <a href="{{ route('post.contacts.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
            &larr; Kembali ke Messages
        </a>

        <div class="card bg-white">
            <div class="card-body">
                <h1 class="h4 mb-1">{{ $contact->subject ?? '(tanpa subjek)' }}</h1>
                <p class="text-muted mb-3">
                    {{ $contact->name }} &lt;{{ $contact->email }}&gt;
                    &middot; {{ $contact->created_at->format('d/m/Y H:i') }}
                    &middot; <span class="badge text-bg-secondary">Sudah dibaca</span>
                </p>
                <p style="white-space: pre-line;" class="mb-0">{{ $contact->message }}</p>
            </div>
        </div>
    </section>
@endsection
