<x-me.layout title="Mon profil">

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 me-dark:text-zinc-50">Mon profil</h1>
            <p class="text-sm text-zinc-500 me-dark:text-zinc-400 mt-1">Consultez et gérez vos informations personnelles.</p>
        </div>

        <div class="bg-white me-dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 me-dark:border-zinc-700 overflow-hidden">
            <div class="bg-gradient-to-r from-[#2b2b2b] to-zinc-700 px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5">
                <x-user-avatar :user="$user" size="h-20 w-20" text="text-3xl" class="ring-2 ring-white/20" />
                <div class="min-w-0">
                    <h2 class="text-xl font-semibold text-white break-words">{{ $user->name }}</h2>
                    <p class="text-zinc-400 text-sm break-all">{{ '@' . $user->username }}</p>
                    @php
                        $meRole = $user->getRoleNames()->first();
                        $meRoleLabel = match ($meRole) {
                            'admin' => 'Administrateur',
                            'collaborator' => 'Collaborateur',
                            'client' => 'Client',
                            'seller' => 'Vendeur',
                            default => $meRole ? ucfirst((string) $meRole) : 'Membre',
                        };
                    @endphp
                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full
                        {{ $user->hasRole('admin') ? 'bg-amber-400/20 text-amber-300' : '' }}
                        {{ $user->hasRole('collaborator') ? 'bg-sky-400/20 text-sky-300' : '' }}
                        {{ $user->hasRole('client') ? 'bg-emerald-400/20 text-emerald-300' : '' }}
                        {{ $user->hasRole('seller') ? 'bg-amber-400/20 text-amber-200' : '' }}">
                        {{ $meRoleLabel }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 divide-y xl:divide-y-0 xl:divide-x divide-zinc-100 me-dark:divide-zinc-800">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div>
                        <dt class="text-xs font-medium text-zinc-500 me-dark:text-zinc-400 uppercase tracking-wider">Nom complet</dt>
                        <dd class="mt-0.5 text-zinc-900 me-dark:text-zinc-100 break-words">{{ $user->name }}</dd>
                    </div>
                </div>
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div>
                        <dt class="text-xs font-medium text-zinc-500 me-dark:text-zinc-400 uppercase tracking-wider">Nom d'utilisateur</dt>
                        <dd class="mt-0.5 text-zinc-900 me-dark:text-zinc-100 break-all">{{ $user->username }}</dd>
                    </div>
                </div>
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div>
                        <dt class="text-xs font-medium text-zinc-500 me-dark:text-zinc-400 uppercase tracking-wider">Adresse e-mail</dt>
                        <dd class="mt-0.5 text-zinc-900 me-dark:text-zinc-100 break-all">{{ $user->email ?? '—' }}</dd>
                    </div>
                </div>
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div>
                        <dt class="text-xs font-medium text-zinc-500 me-dark:text-zinc-400 uppercase tracking-wider">Membre depuis</dt>
                        <dd class="mt-0.5 text-zinc-900 me-dark:text-zinc-100">{{ $user->created_at?->translatedFormat('d F Y') ?? '—' }}</dd>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 py-4 bg-zinc-50 me-dark:bg-zinc-800/60 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('me.edit') }}" class="inline-flex w-full sm:w-auto justify-center items-center gap-2 px-4 py-2 bg-[#2b2b2b] me-dark:bg-zinc-950 text-white text-sm font-medium rounded-lg hover:bg-zinc-700 me-dark:hover:bg-zinc-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Modifier mes informations (photo incluse)
                </a>
                <a href="{{ route('me.password') }}" class="inline-flex w-full sm:w-auto justify-center items-center gap-2 px-4 py-2 border border-zinc-300 me-dark:border-zinc-600 text-zinc-700 me-dark:text-zinc-200 text-sm font-medium rounded-lg hover:bg-zinc-100 me-dark:hover:bg-zinc-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Changer le mot de passe
                </a>
            </div>
        </div>
    </div>

</x-me.layout>
