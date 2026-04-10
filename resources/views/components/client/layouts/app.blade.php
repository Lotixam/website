<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Mon espace' }} — Lotixam</title>
    <link href="/img/logo.png" rel="icon" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 text-zinc-800 min-h-screen flex flex-col">

    <header class="bg-[#2b2b2b] text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('client.dashboard') }}" class="flex items-center gap-3">
                    <img src="/img/logo.png" alt="Lotixam" class="h-10 w-10">
                    <span class="text-lg font-bold tracking-wide">LOTIXAM</span>
                </a>

                <nav class="hidden sm:flex items-center gap-6 text-sm" aria-label="Navigation principale">
                    <a href="{{ route('home') }}"
                       class="transition hover:text-[#b1e90e] {{ request()->routeIs('home') ? 'text-[#b1e90e] font-semibold' : 'text-zinc-300' }}">
                        Accueil site
                    </a>
                    <a href="{{ route('client.dashboard') }}"
                       class="transition hover:text-[#b1e90e] {{ request()->routeIs('client.dashboard') ? 'text-[#b1e90e] font-semibold' : 'text-zinc-300' }}">
                        Mes projets
                    </a>
                    <a href="{{ route('me.index') }}"
                       class="transition hover:text-[#b1e90e] {{ request()->routeIs('me.*') ? 'text-[#b1e90e] font-semibold' : 'text-zinc-300' }}">
                        Mon profil
                    </a>
                </nav>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2">
                        <x-user-avatar :user="auth()->user()" size="h-8 w-8" text="text-xs" class="ring-1 ring-white/20" />
                        <span class="text-sm text-zinc-400">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-zinc-400 hover:text-white transition">Déconnexion</button>
                    </form>
                </div>
            </div>
            <nav class="sm:hidden flex flex-wrap items-center gap-x-4 gap-y-2 px-1 pb-3 text-xs border-t border-white/10 pt-2"
                 aria-label="Navigation">
                <a href="{{ route('home') }}" class="text-zinc-300 hover:text-[#b1e90e] transition">Accueil site</a>
                <a href="{{ route('client.dashboard') }}" class="text-zinc-300 hover:text-[#b1e90e] transition">Mes projets</a>
                <a href="{{ route('me.index') }}" class="text-zinc-300 hover:text-[#b1e90e] transition">Mon profil</a>
            </nav>
        </div>
    </header>

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    @php
        $homeUrl = \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/');
        $legalsUrl = \Illuminate\Support\Facades\Route::has('vitrine.legals') ? route('vitrine.legals') : url('/mentions-legales');
        $cookiesUrl = \Illuminate\Support\Facades\Route::has('vitrine.cookies') ? route('vitrine.cookies') : url('/politique-cookies');
    @endphp
    <footer class="bg-[#2b2b2b] text-zinc-500 text-xs text-center py-4 mt-auto px-4">
        <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2">
            <span>&copy; {{ date('Y') }} Lotixam SAS — Tous droits réservés</span>
            <span class="hidden sm:inline text-zinc-600" aria-hidden="true">|</span>
            <a href="{{ $homeUrl }}" class="text-zinc-400 hover:text-[#b1e90e] transition">Accueil Lotixam</a>
            <span class="text-zinc-600" aria-hidden="true">|</span>
            <a href="{{ $legalsUrl }}" class="text-zinc-400 hover:text-[#b1e90e] transition">Mentions légales</a>
            <span class="text-zinc-600" aria-hidden="true">|</span>
            <a href="{{ $cookiesUrl }}" class="text-zinc-400 hover:text-[#b1e90e] transition">Politique de cookies</a>
        </div>
    </footer>

</body>
</html>
