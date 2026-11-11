@extends('layouts.app')

@section('title', 'Skills')

@section('content')
<section style="max-width: 800px; min-height: 500px; margin: 40px auto; padding: 0 20px;">
    <h1>Skills</h1>

    @include('partials.flash')

    <table class="table table-bordered bg-white">
        <thead class="table-light">
            <tr>
                <th>Nama</th>
                <th>Level</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($skills as $skill)
                <tr>
                    <td>{{ $skill->name }}</td>
                    <td>{{ $skill->level }}%</td>
                    <td><a href="{{ route('post.skills.edit', $skill) }}" class="btn btn-sm btn-primary">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('post.dashboard.index') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
</section>
@endsection
