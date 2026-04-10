<x-me.layout title="Modifier mon profil">

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 me-dark:text-zinc-50">Modifier mon profil</h1>
            <p class="text-sm text-zinc-500 me-dark:text-zinc-400 mt-1">Mettez à jour vos informations personnelles.</p>
        </div>

        <div class="bg-white me-dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 me-dark:border-zinc-700 overflow-hidden">
            <div class="p-6 border-b border-zinc-100 me-dark:border-zinc-800">
                <span class="block text-sm font-medium text-zinc-700 me-dark:text-zinc-200 mb-3">Photo de profil</span>
                <div class="flex flex-wrap items-start gap-4">
                    <x-user-avatar :user="$user" size="h-20 w-20" text="text-3xl" class="ring-2 ring-zinc-200 me-dark:ring-zinc-600" />
                    <div class="space-y-2 min-w-0 flex-1">
                        @if ($user->avatar)
                            <form method="POST" action="{{ route('me.avatar.destroy') }}" class="inline" onsubmit="return confirm('Supprimer votre photo de profil ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 me-dark:text-red-400 hover:text-red-800 me-dark:hover:text-red-300 underline">Supprimer la photo actuelle</button>
                            </form>
                        @endif
                        <p class="text-xs text-zinc-500 me-dark:text-zinc-400">Pour changer la photo, choisissez un fichier dans le formulaire ci-dessous puis enregistrez.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('me.update') }}" enctype="multipart/form-data" class="divide-y divide-zinc-100 me-dark:divide-zinc-800">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">
                <div>
                    <label for="avatar" class="block text-sm font-medium text-zinc-700 me-dark:text-zinc-200 mb-1">Nouvelle photo</label>
                    <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/gif,image/webp"
                           class="block w-full text-sm text-zinc-600 me-dark:text-zinc-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-800 hover:file:bg-zinc-200 me-dark:file:bg-zinc-800 me-dark:file:text-zinc-100 me-dark:hover:file:bg-zinc-700">
                    <p class="mt-1 text-xs text-zinc-500 me-dark:text-zinc-400">JPEG, PNG, GIF ou WebP — 2 Mo max.</p>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-zinc-700 me-dark:text-zinc-200 mb-1">Nom complet</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-lg border-zinc-300 me-dark:border-zinc-600 me-dark:bg-zinc-800 me-dark:text-zinc-100 shadow-sm focus:border-[#b1e90e] focus:ring-[#b1e90e] text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-zinc-700 me-dark:text-zinc-200 mb-1">Nom d'utilisateur</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-400 me-dark:text-zinc-500 text-sm">@</span>
                        <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required
                               class="w-full pl-8 rounded-lg border-zinc-300 me-dark:border-zinc-600 me-dark:bg-zinc-800 me-dark:text-zinc-100 shadow-sm focus:border-[#b1e90e] focus:ring-[#b1e90e] text-sm">
                    </div>
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-zinc-700 me-dark:text-zinc-200 mb-1">Adresse e-mail</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-lg border-zinc-300 me-dark:border-zinc-600 me-dark:bg-zinc-800 me-dark:text-zinc-100 shadow-sm focus:border-[#b1e90e] focus:ring-[#b1e90e] text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-4 bg-zinc-50 me-dark:bg-zinc-800/60 flex items-center justify-between">
                <a href="{{ route('me.index') }}" class="text-sm text-zinc-500 me-dark:text-zinc-400 hover:text-zinc-700 me-dark:hover:text-zinc-200 transition">Annuler</a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#b1e90e] text-[#2b2b2b] text-sm font-semibold rounded-lg hover:bg-[#9fd00c] transition shadow-sm">
                    Enregistrer les modifications
                </button>
            </div>
            </form>
        </div>
    </div>

</x-me.layout>
