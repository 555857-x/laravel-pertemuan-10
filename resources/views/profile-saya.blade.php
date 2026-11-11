@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <section style="max-width: 1100px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
        <h1 class="h3 mb-4">Profil Saya</h1>

        {{-- Auth::user() berisi data user yang sedang login. --}}
        @php($user = Auth::user())

        <table class="table table-bordered bg-white" style="max-width: 600px;">
            <tr>
                <th style="width: 180px;">Nama</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Role</th>
                <td>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Terdaftar sejak</th>
                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        @if ($user->role === 'admin')
            <a href="{{ route('post.projects.index') }}" class="btn btn-primary">Kelola Project</a>
        @else
            <p class="text-muted">Akun ini bukan admin, jadi tidak bisa membuka halaman kelola project.</p>
        @endif
    </section>
@endsection
