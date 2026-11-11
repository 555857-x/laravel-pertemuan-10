@extends('layouts.app')

@section('title', 'Edit Skill')

@section('content')
<section style="max-width: 600px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
    <h1>Edit Skill</h1>

    <form action="{{ route('post.skills.update', $skill) }}" method="POST" class="card card-body">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Nama Skill</label>
            <input type="text" id="name" name="name" value="{{ old('name', $skill->name) }}" class="form-control @error('name') is-invalid @enderror">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="level" class="form-label fw-bold">Level (0 - 100)</label>
            <input type="number" id="level" name="level" min="0" max="100" value="{{ old('level', $skill->level) }}" class="form-control @error('level') is-invalid @enderror">
            @error('level')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('post.skills.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</section>
@endsection
