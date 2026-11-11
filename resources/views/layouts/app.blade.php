<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>@yield('title', 'Portofolio Saya')</title>

    {{-- Bootstrap 5 untuk tabel, alert, form, dan pagination. --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- Tempat CSS tambahan per halaman (dashboard memuat Tailwind di sini). --}}
    @stack('styles')
</head>

<body>
    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
