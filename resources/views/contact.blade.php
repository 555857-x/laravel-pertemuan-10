@extends('layouts.app')

@section('title', 'Kontak')

{{-- Form ini memakai class Tailwind seperti di modul, jadi breeze.css ikut dimuat
     di halaman ini (pola yang sama dipakai posts/dashboard.blade.php). --}}
@push('styles')
    @vite('resources/css/breeze.css')
@endpush

@section('content')
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow-sm p-6 border border-gray-200" style="margin: 40px auto;">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Hubungi Saya</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-semibold mb-1">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-semibold mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="subject" class="block text-sm font-semibold mb-1">Subjek</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="w-full border rounded px-3 py-2">
                @error('subject')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="message" class="block text-sm font-semibold mb-1">Pesan</label>
                <textarea name="message" id="message" rows="5" class="w-full border rounded px-3 py-2">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Kirim Pesan</button>
        </form>
    </div>
@endsection
