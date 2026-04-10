<x-client.layouts.app>
    @section('title', $operation->name)

    {{-- Header projet --}}
    <div class="mb-8">
        <a href="{{ route('client.dashboard') }}" class="text-sm text-zinc-400 hover:text-zinc-600 transition mb-2 inline-block">&larr; Retour aux projets</a>
        <h1 class="text-2xl font-bold text-zinc-900">{{ $operation->name }}</h1>
        <p class="text-zinc-500">{{ $operation->address }} {{ $operation->postal_code }} {{ $operation->city }}</p>
    </div>

    {{-- Progression globale --}}
    <div class="bg-white rounded-xl border border-zinc-200 p-5 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-zinc-700">Progression globale</span>
            <span class="text-sm font-bold" style="color: #b1e90e;">{{ $progress }}%</span>
        </div>
        <x-client.progress-bar :percent="$progress" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Colonne gauche : Timeline des étapes --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h2 class="font-semibold text-zinc-900 mb-4">
                    @if ($hasWorkflow)
                        Avancement du projet
                    @else
                        Étapes du projet
                    @endif
                </h2>
                @if ($hasWorkflow)
                    <x-client.workflow-timeline :nodes="$workflowNodes" />
                @else
                    <x-client.stage-timeline :stages="$operation->stages" />
                @endif
            </div>
        </div>

        {{-- Colonne droite : Documents + Messages --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Documents demandés --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h2 class="font-semibold text-zinc-900 mb-4">
                    Documents demandés
                    @php
                        $pendingDocs = $operation->documentRequests->filter(fn ($d) => $d->status->value === 'pending')->count();
                    @endphp
                    @if ($pendingDocs > 0)
                        <span class="ml-2 text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                            {{ $pendingDocs }} en attente
                        </span>
                    @endif
                </h2>

                @forelse ($operation->documentRequests as $docRequest)
                    <div class="mb-3 last:mb-0">
                        <x-client.document-card :request="$docRequest" />
                    </div>
                @empty
                    <p class="text-sm text-zinc-400">Aucun document demandé pour le moment.</p>
                @endforelse
            </div>

            {{-- Messagerie --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <h2 class="font-semibold text-zinc-900 mb-4">Messagerie</h2>

                <div class="space-y-3 max-h-96 overflow-y-auto mb-4" id="messages-container">
                    @forelse ($operation->messages->reverse() as $message)
                        <x-client.message-bubble
                            :message="$message"
                            :isOwn="$message->user_id === auth()->id()" />
                    @empty
                        <p class="text-sm text-zinc-400 text-center py-6">Aucun message. Démarrez la conversation !</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('client.message.store', $operation) }}" class="flex gap-2">
                    @csrf
                    <input type="text" name="body" placeholder="Votre message..." required
                           class="flex-1 rounded-xl border border-zinc-300 px-4 py-2.5 text-sm focus:outline-none focus:border-[#b1e90e] focus:ring-1 focus:ring-[#b1e90e]/50 transition">
                    <button type="submit"
                            class="shrink-0 px-5 py-2.5 rounded-xl bg-[#b1e90e] text-zinc-900 font-medium text-sm hover:bg-[#a0d40d] transition">
                        Envoyer
                    </button>
                </form>
            </div>

            {{-- Unités du bien (modèle Lot : parcelles, lots, appartements, caves, etc.) --}}
            @if ($operation->lots->count() > 0)
                <div class="bg-white rounded-xl border border-zinc-200 p-5">
                    <h2 class="font-semibold text-zinc-900 mb-1">Détail du bien</h2>
                    <p class="text-xs text-zinc-500 mb-4">Unités suivies pour cette opération (terrain divisé, immeuble, maison, autre selon le cas).</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-100 text-left text-xs text-zinc-500 uppercase tracking-wider">
                                    <th class="pb-2 pr-4">Réf.</th>
                                    <th class="pb-2 pr-4">Surface</th>
                                    <th class="pb-2 pr-4">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operation->lots as $lot)
                                    <tr class="border-b border-zinc-50">
                                        <td class="py-2 pr-4 font-medium">{{ $lot->lot_number }}</td>
                                        <td class="py-2 pr-4">{{ $lot->surface ? $lot->surface . ' m²' : '—' }}</td>
                                        <td class="py-2 pr-4">
                                            <span class="text-xs px-2 py-0.5 rounded-full
                                                {{ $lot->status->value === 'sold' ? 'bg-emerald-100 text-emerald-700' : ($lot->status->value === 'reserved' ? 'bg-amber-100 text-amber-700' : 'bg-zinc-100 text-zinc-600') }}">
                                                {{ $lot->status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('messages-container');
            if (container) container.scrollTop = container.scrollHeight;
        });
    </script>

</x-client.layouts.app>
