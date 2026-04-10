<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Mon espace' }} — Lotixam</title>
    <link href="/img/logo.png" rel="icon" type="image/x-icon">
    <script>
        (function () {
            try {
                var k = 'lotixam-me-theme';
                var t = localStorage.getItem(k) || 'system';
                var d = document.documentElement;
                d.classList.remove('me-dark');
                if (t === 'dark') d.classList.add('me-dark');
                else if (t !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches) d.classList.add('me-dark');
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-me-theme-page class="bg-zinc-100 me-dark:bg-zinc-950 text-zinc-800 me-dark:text-zinc-100 min-h-screen flex flex-col transition-colors duration-200">

    <header class="bg-[#2b2b2b] shadow-md">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10 2xl:px-12">
            <div class="flex items-center justify-between h-16 gap-2">
                <a href="{{ route('me.index') }}" class="flex items-center gap-3 min-w-0">
                    <img src="/img/logo.png" alt="Lotixam" class="h-10 w-10 shrink-0">
                    <span class="text-lg font-bold tracking-wide text-white truncate">LOTIXAM</span>
                </a>

                <nav class="hidden sm:flex items-center gap-1 text-sm flex-wrap justify-end" aria-label="Navigation principale">
                    <a href="{{ route('home') }}"
                       class="px-3 py-2 rounded-lg transition {{ request()->routeIs('home') ? 'bg-white/10 text-[#b1e90e] font-semibold' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
                        Accueil site
                    </a>
                    <a href="{{ route('me.index') }}"
                       class="px-3 py-2 rounded-lg transition {{ request()->routeIs('me.index') ? 'bg-white/10 text-[#b1e90e] font-semibold' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
                        Mon profil
                    </a>
                    <a href="{{ route('me.password') }}"
                       class="px-3 py-2 rounded-lg transition {{ request()->routeIs('me.password') ? 'bg-white/10 text-[#b1e90e] font-semibold' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
                        Mot de passe
                    </a>

                    <span class="mx-1 w-px h-5 bg-zinc-600 shrink-0" aria-hidden="true"></span>

                    <div class="flex items-center gap-0.5 rounded-lg bg-black/20 p-0.5" role="group" aria-label="Thème d'affichage">
                        <button type="button" data-me-theme="light" aria-pressed="false"
                                class="px-2 py-1 rounded-md text-xs text-zinc-300 hover:text-white hover:bg-white/10 transition ring-offset-2 ring-offset-[#2b2b2b]">
                            Clair
                        </button>
                        <button type="button" data-me-theme="dark" aria-pressed="false"
                                class="px-2 py-1 rounded-md text-xs text-zinc-300 hover:text-white hover:bg-white/10 transition ring-offset-2 ring-offset-[#2b2b2b]">
                            Sombre
                        </button>
                        <button type="button" data-me-theme="system" aria-pressed="false"
                                class="px-2 py-1 rounded-md text-xs text-zinc-300 hover:text-white hover:bg-white/10 transition ring-offset-2 ring-offset-[#2b2b2b]">
                            Système
                        </button>
                    </div>

                    <span class="mx-1 w-px h-5 bg-zinc-600 shrink-0" aria-hidden="true"></span>

                    @php $user = auth()->user(); @endphp
                    @if($user->hasRole('admin') || $user->hasRole('collaborator'))
                        <a href="/admin" class="px-3 py-2 rounded-lg text-zinc-400 hover:text-white hover:bg-white/5 transition text-xs uppercase tracking-wider">
                            Administration
                        </a>
                    @endif
                    @if($user->hasRole('client'))
                        <a href="{{ route('client.dashboard') }}" class="px-3 py-2 rounded-lg text-zinc-400 hover:text-white hover:bg-white/5 transition text-xs uppercase tracking-wider">
                            Mes projets
                        </a>
                    @endif
                </nav>

                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <div class="flex sm:hidden items-center gap-0.5 rounded-lg bg-black/20 p-0.5" role="group" aria-label="Thème d'affichage">
                        <button type="button" data-me-theme="light" title="Thème clair" aria-pressed="false"
                                class="p-1.5 rounded-md text-zinc-300 hover:text-white hover:bg-white/10 transition ring-offset-2 ring-offset-[#2b2b2b]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>
                        <button type="button" data-me-theme="dark" title="Thème sombre" aria-pressed="false"
                                class="p-1.5 rounded-md text-zinc-300 hover:text-white hover:bg-white/10 transition ring-offset-2 ring-offset-[#2b2b2b]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        </button>
                        <button type="button" data-me-theme="system" title="Thème système" aria-pressed="false"
                                class="p-1.5 rounded-md text-zinc-300 hover:text-white hover:bg-white/10 transition ring-offset-2 ring-offset-[#2b2b2b]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                    <div class="hidden md:flex items-center gap-2">
                        <x-user-avatar :user="auth()->user()" size="h-8 w-8" text="text-xs" class="ring-1 ring-white/20" />
                        <span class="text-sm text-zinc-400 max-w-[8rem] truncate">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-zinc-400 hover:text-red-400 transition whitespace-nowrap">Déconnexion</button>
                    </form>
                </div>
            </div>
            <nav class="sm:hidden flex flex-wrap items-center gap-x-2 gap-y-2 px-1 pb-3 text-xs border-t border-white/10 pt-2"
                 aria-label="Navigation">
                <a href="{{ route('home') }}" class="px-2 py-1.5 rounded-lg text-zinc-300 hover:text-white hover:bg-white/5 transition">Accueil site</a>
                <a href="{{ route('me.index') }}" class="px-2 py-1.5 rounded-lg text-zinc-300 hover:text-white hover:bg-white/5 transition">Profil</a>
                <a href="{{ route('me.password') }}" class="px-2 py-1.5 rounded-lg text-zinc-300 hover:text-white hover:bg-white/5 transition">Mot de passe</a>
                @if(auth()->user()->hasRole('client'))
                    <a href="{{ route('client.dashboard') }}" class="px-2 py-1.5 rounded-lg text-zinc-300 hover:text-white hover:bg-white/5 transition">Mes projets</a>
                @endif
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('collaborator'))
                    <a href="/admin" class="px-2 py-1.5 rounded-lg text-zinc-300 hover:text-white hover:bg-white/5 transition">Admin</a>
                @endif
            </nav>
        </div>
    </header>

    @if (session('success'))
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10 2xl:px-12 mt-6">
            <div class="rounded-xl bg-emerald-50 me-dark:bg-emerald-950/40 border border-emerald-200 me-dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 me-dark:text-emerald-300 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 me-dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10 2xl:px-12 mt-6">
            <div class="rounded-xl bg-red-50 me-dark:bg-red-950/40 border border-red-200 me-dark:border-red-800 px-4 py-3 text-sm text-red-700 me-dark:text-red-300">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <main class="flex-1 max-w-screen-2xl mx-auto w-full px-4 sm:px-6 lg:px-10 2xl:px-12 py-6 sm:py-8">
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
