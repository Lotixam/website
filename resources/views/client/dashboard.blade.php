<x-client.layouts.app>
    @section('title', 'Mes projets')

    <div class="mb-8 flex items-center gap-4">
        <x-user-avatar :user="auth()->user()" size="h-14 w-14" text="text-xl" class="ring-2 ring-zinc-200 shrink-0" />
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-zinc-900">Bonjour, {{ auth()->user()->name }}</h1>
            <p class="text-zinc-500 mt-1">Bienvenue dans votre espace client Lotixam.</p>
        </div>
    </div>

    {{-- Stats rapides --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-zinc-200 p-5">
            <p class="text-sm text-zinc-500">Projets en cours</p>
            <p class="text-2xl font-bold text-zinc-900 mt-1">{{ $operations->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-zinc-200 p-5">
            <p class="text-sm text-zinc-500">Documents à fournir</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingDocRequests }}</p>
        </div>
        <div class="bg-white rounded-xl border border-zinc-200 p-5">
            <p class="text-sm text-zinc-500">Messages non lus</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $unreadMessages }}</p>
        </div>
    </div>

    {{-- Liste des projets --}}
    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Mes projets</h2>

    @forelse ($operations as $operation)
        @php
            $progress = $operation->client_progress_percent;
        @endphp
        <a href="{{ route('client.project.show', $operation) }}"
           class="block bg-white rounded-xl border border-zinc-200 p-5 mb-4 hover:border-[#b1e90e]/50 hover:shadow-sm transition group">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-zinc-900 group-hover:text-[#8bc20a] transition">{{ $operation->name }}</h3>
                    <p class="text-sm text-zinc-500">{{ $operation->city ?? 'Localisation non définie' }}</p>
                </div>
                <span class="text-sm font-medium text-[#8bc20a]">{{ $progress }}%</span>
            </div>
            <x-client.progress-bar :percent="$progress" />
            <div class="flex gap-4 mt-3 text-xs text-zinc-400">
                <span>{{ $operation->lots_count }} unité{{ $operation->lots_count > 1 ? 's' : '' }} du bien</span>
                @if ($operation->workflow_nodes_count > 0)
                    <span>Parcours workflow</span>
                @else
                    <span>{{ $operation->completed_stages_count }}/{{ $operation->total_stages_count }} étapes</span>
                @endif
                <span>{{ $operation->document_requests_count }} document{{ $operation->document_requests_count > 1 ? 's' : '' }}</span>
            </div>
        </a>
    @empty
        <div class="bg-white rounded-xl border border-zinc-200 p-8 text-center">
            <p class="text-zinc-500">Aucun projet ne vous est assigné pour le moment.</p>
        </div>
    @endforelse

</x-client.layouts.app>
