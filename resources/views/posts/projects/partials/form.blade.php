{{-- Field form dipakai bersama oleh create dan edit. $project kosong saat create. --}}
@php
    $project = $project ?? null;
@endphp

<div class="mb-3">
    <label for="name" class="form-label fw-bold">Nama Project</label>
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $project?->name) }}"
        placeholder="Masukkan nama project"
        class="form-control @error('name') is-invalid @enderror"
    >
    @error('name')
        <p class="text-red-500 text-sm invalid-feedback">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    {{-- Dropdown kategori dari tabel categories, $categories dikirim controller. --}}
    <label for="category_id" class="form-label fw-bold">Kategori</label>
    <select
        id="category_id"
        name="category_id"
        class="form-select @error('category_id') is-invalid @enderror"
    >
        <option value="">Pilih kategori</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((string) old('category_id', $project?->category_id) === (string) $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <p class="text-red-500 text-sm invalid-feedback">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label for="thumbnail" class="form-label fw-bold">Thumbnail</label>

    {{-- Saat edit, tampilkan thumbnail lama. --}}
    @if ($project?->thumbnail)
        <div class="mb-2">
            <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->name }}" style="max-width: 200px;" class="rounded border">
            <div class="form-text">Upload file baru untuk mengganti. File lama otomatis dihapus.</div>
        </div>
    @endif

    <input
        type="file"
        id="thumbnail"
        name="thumbnail"
        accept="image/*"
        class="form-control @error('thumbnail') is-invalid @enderror"
    >
    <div class="form-text">jpg, jpeg, png, webp. Maks 2 MB. Minimal 400 x 300 px.</div>
    @error('thumbnail')
        <p class="text-red-500 text-sm invalid-feedback">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label fw-bold">Deskripsi Project</label>
    <textarea
        id="description"
        name="description"
        rows="6"
        placeholder="Masukkan deskripsi project"
        class="form-control @error('description') is-invalid @enderror"
    >{{ old('description', $project?->description) }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="status" class="form-label fw-bold">Status</label>
        <select
            id="status"
            name="status"
            class="form-select @error('status') is-invalid @enderror"
        >
            @foreach (['draft' => 'Draft', 'published' => 'Published'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $project?->status ?? 'draft') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        {{-- Datepicker bawaan browser; nilai terkirim dalam format database Y-m-d. --}}
        <label for="started_at" class="form-label fw-bold">Tanggal Mulai</label>
        <input
            type="date"
            id="started_at"
            name="started_at"
            value="{{ old('started_at', $project?->started_at?->format('Y-m-d')) }}"
            class="form-control @error('started_at') is-invalid @enderror"
        >
        @error('started_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="budget" class="form-label fw-bold">Anggaran (Rp)</label>
        <input
            type="number"
            id="budget"
            name="budget"
            min="0"
            value="{{ old('budget', $project?->budget) }}"
            placeholder="contoh: 1500000"
            class="form-control @error('budget') is-invalid @enderror"
        >
        @error('budget')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
