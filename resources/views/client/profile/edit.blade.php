<x-client.layouts.app>
    @section('title', 'Mon profil')

    <div class="max-w-xl">
        <h1 class="text-2xl font-bold text-zinc-900 mb-6">Mon profil</h1>

        <form method="POST" action="{{ route('client.profile.update') }}" class="bg-white rounded-xl border border-zinc-200 p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-zinc-700 mb-1">Prénom</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->profile?->first_name) }}"
                           class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:outline-none focus:border-[#b1e90e] focus:ring-1 focus:ring-[#b1e90e]/50">
                    @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-zinc-700 mb-1">Nom</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->profile?->last_name) }}"
                           class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:outline-none focus:border-[#b1e90e] focus:ring-1 focus:ring-[#b1e90e]/50">
                    @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="gender" class="block text-sm font-medium text-zinc-700 mb-1">Genre</label>
                <select name="gender" id="gender"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:outline-none focus:border-[#b1e90e] focus:ring-1 focus:ring-[#b1e90e]/50">
                    <option value="">—</option>
                    @foreach (\App\Enums\Gender::cases() as $gender)
                        <option value="{{ $gender->value }}" @selected(old('gender', $user->profile?->gender?->value) === $gender->value)>{{ $gender->getLabel() }}</option>
                    @endforeach
                </select>
                @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-zinc-700 mb-1">Téléphone</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->profile?->phone) }}"
                       class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:outline-none focus:border-[#b1e90e] focus:ring-1 focus:ring-[#b1e90e]/50">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-zinc-700 mb-1">Adresse email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:outline-none focus:border-[#b1e90e] focus:ring-1 focus:ring-[#b1e90e]/50">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-[#b1e90e] text-zinc-900 font-medium text-sm hover:bg-[#a0d40d] transition">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

</x-client.layouts.app>
