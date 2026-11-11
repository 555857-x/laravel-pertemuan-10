@extends('layouts.app')

@section('title', 'Tambah Project')

@section('content')
    <section style="max-width: 800px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
        <h1>Tambah Project</h1>
        <p class="text-muted">Masukkan data project baru.</p>

        @include('partials.flash')

        {{-- Daftar semua error validasi. --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Data belum dapat disimpan.</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('post.projects.store') }}" method="POST" enctype="multipart/form-data" class="card card-body">
            @csrf

            @include('posts.projects.partials.form')

            <div>
                <button type="submit" class="btn btn-primary">Simpan Project</button>
                <a href="{{ route('post.projects.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
