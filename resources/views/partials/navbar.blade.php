<nav style="
    padding: 18px 24px;
    background-color: white;
    border-bottom: 1px solid #dddddd;
">
    <div style="
        max-width: 900px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    ">
        <a
            href="{{ route('home') }}"
            style="
                color: #111827;
                font-size: 20px;
                font-weight: bold;
                text-decoration: none;
            "
        >
            Portofolio Saya
        </a>

        <div style="
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
        ">
            <a
                href="{{ route('home') }}"
                style="color: #2563eb; text-decoration: none;"
            >
                Home
            </a>

            <a
                href="{{ route('about') }}"
                style="color: #2563eb; text-decoration: none;"
            >
                About
            </a>

            <a
                href="{{ route('education') }}"
                style="color: #2563eb; text-decoration: none;"
            >
                Education
            </a>

            <a
                href="{{ route('projects') }}"
                style="color: #2563eb; text-decoration: none;"
            >
                Projects
            </a>

            {{-- Bagian ini berubah sesuai status login. --}}
            @auth
                {{-- Link dashboard hanya untuk admin, user biasa akan dapat 403. --}}
                @if (Auth::user()->role === 'admin')
                    <a
                        href="{{ route('post.dashboard.index') }}"
                        style="color: #2563eb; text-decoration: none;"
                    >
                        Dashboard
                    </a>
                @endif

                <a
                    href="{{ route('profile.saya') }}"
                    style="color: #111827; font-weight: bold; text-decoration: none;"
                >
                    {{ Auth::user()->name }}
                </a>

                {{-- Logout wajib POST + CSRF, jadi pakai form, bukan link biasa. --}}
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-sm btn-outline-danger"
                    >
                        Logout
                    </button>
                </form>
            @endauth

            @guest
                <a
                    href="{{ route('login') }}"
                    class="btn btn-sm btn-primary"
                >
                    Login
                </a>
            @endguest
        </div>
    </div>
</nav>
