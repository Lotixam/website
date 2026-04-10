<x-me.layout title="Changer le mot de passe">

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 me-dark:text-zinc-50">Changer le mot de passe</h1>
            <p class="text-sm text-zinc-500 me-dark:text-zinc-400 mt-1">Pour votre sécurité, utilisez un mot de passe long et unique.</p>
        </div>

        <form method="POST" action="{{ route('me.password.update') }}" class="bg-white me-dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 me-dark:border-zinc-700 divide-y divide-zinc-100 me-dark:divide-zinc-800">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-zinc-700 me-dark:text-zinc-200 mb-1">Mot de passe actuel</label>
                    <input type="password" name="current_password" id="current_password" required
                           class="w-full rounded-lg border-zinc-300 me-dark:border-zinc-600 me-dark:bg-zinc-800 me-dark:text-zinc-100 shadow-sm focus:border-[#b1e90e] focus:ring-[#b1e90e] text-sm">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-zinc-700 me-dark:text-zinc-200 mb-1">Nouveau mot de passe</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-lg border-zinc-300 me-dark:border-zinc-600 me-dark:bg-zinc-800 me-dark:text-zinc-100 shadow-sm focus:border-[#b1e90e] focus:ring-[#b1e90e] text-sm">
                    <p class="mt-1 text-xs text-zinc-400 me-dark:text-zinc-500">Minimum 8 caractères</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-zinc-700 me-dark:text-zinc-200 mb-1">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full rounded-lg border-zinc-300 me-dark:border-zinc-600 me-dark:bg-zinc-800 me-dark:text-zinc-100 shadow-sm focus:border-[#b1e90e] focus:ring-[#b1e90e] text-sm">
                </div>
            </div>

            <div class="px-6 py-4 bg-zinc-50 me-dark:bg-zinc-800/60 flex items-center justify-between">
                <a href="{{ route('me.index') }}" class="text-sm text-zinc-500 me-dark:text-zinc-400 hover:text-zinc-700 me-dark:hover:text-zinc-200 transition">Annuler</a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#b1e90e] text-[#2b2b2b] text-sm font-semibold rounded-lg hover:bg-[#9fd00c] transition shadow-sm">
                    Modifier le mot de passe
                </button>
            </div>
        </form>
    </div>

</x-me.layout>
