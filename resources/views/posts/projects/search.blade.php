@extends('layouts.app')

@section('title', 'Cari Project')

@section('content')
    <section style="max-width: 1100px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
        <h1 class="mb-3">Hasil Pencarian</h1>

        <form action="{{ route('post.projects.search') }}" method="GET" class="d-flex gap-2 mb-4">
            <input
                type="text"
                name="kata"
                value="{{ $cari }}"
                class="form-control"
                placeholder="Cari judul atau deskripsi ..."
                aria-label="Cari project"
            >
            <select name="per_page" class="form-select w-auto" aria-label="Data per halaman">
                @foreach ([5, 10, 25] as $option)
                    <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        @include('partials.flash')

        @if ($jumlahProject)
            <div class="alert alert-success">
                Ditemukan <strong>{{ $jumlahProject }}</strong> data dengan kata:
                <strong>{{ $cari }}</strong>
            </div>

            @include('posts.projects.partials.table')

            <a href="{{ route('post.projects.index') }}" class="btn btn-secondary mt-3">Kembali</a>
        @else
            <div class="alert alert-warning">
                <h4>Data {{ $cari }} tidak ditemukan</h4>
                <a href="{{ route('post.projects.index') }}" class="btn btn-warning">Kembali</a>
            </div>
        @endif
    </section>
@endsection
