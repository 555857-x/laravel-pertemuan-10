@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
    <section style="max-width: 800px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
        <h1>Edit Project</h1>
        <p class="text-muted">Ubah data project.</p>

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

        {{-- Form HTML mengirim POST; Laravel membacanya sebagai PUT. --}}
        <form action="{{ route('post.projects.update', $project->id) }}" method="POST" enctype="multipart/form-data" class="card card-body">
            @csrf
            @method('PUT')

            @include('posts.projects.partials.form', ['project' => $project])

            <div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('post.projects.show', $project->id) }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
