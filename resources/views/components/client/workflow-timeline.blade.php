@props(['nodes', 'blocking' => []])

<div class="space-y-0">
    @forelse ($nodes as $node)
        @php
            $status = $node->status;
            $isCompleted = $status === WorkflowNodeStatus::Completed;
            $isSkipped = $status === WorkflowNodeStatus::Skipped;
            $isActive = $status === WorkflowNodeStatus::InProgress;
            $isBlocked = $status === WorkflowNodeStatus::Blocked;
            $isRejected = $status === WorkflowNodeStatus::Rejected;
            $dotColor = $isCompleted ? '#b1e90e' : ($isSkipped ? '#94a3b8' : ($isActive ? '#f59e0b' : ($isBlocked || $isRejected ? '#ef4444' : '#d4d4d8')));
        @endphp
        <div class="flex gap-4 {{ !$loop->last ? 'pb-6' : '' }}">
            <div class="flex flex-col items-center">
                <div class="w-4 h-4 rounded-full border-2 shrink-0"
                     style="border-color: {{ $dotColor }}; {{ $isCompleted ? 'background-color: ' . $dotColor : '' }}"></div>
                @unless ($loop->last)
                    <div class="w-px flex-1 bg-zinc-300 mt-1"></div>
                @endunless
            </div>
            <div class="pb-2 -mt-0.5 flex-1 min-w-0">
                <p class="text-sm font-medium {{ $isCompleted ? 'text-zinc-900' : ($isSkipped ? 'text-zinc-400 line-through' : ($isBlocked ? 'text-red-600' : 'text-zinc-700')) }}">
                    {{ $node->title }}
                    @if ($node->is_merge_node)
                        <span class="ml-1 text-xs text-violet-600 font-normal">(fusion)</span>
                    @endif
                </p>
                @if ($node->description)
                    <p class="text-xs text-zinc-500 mt-0.5">{{ \Illuminate\Support\Str::limit($node->description, 120) }}</p>
                @endif
                @if ($isCompleted && $node->completed_at)
                    <p class="text-xs text-zinc-400 mt-0.5">Terminé le {{ $node->completed_at->format('d/m/Y') }}</p>
                @elseif ($isActive)
                    <p class="text-xs text-amber-600 mt-0.5">En cours</p>
                @elseif ($isSkipped)
                    <p class="text-xs text-zinc-400 mt-0.5">Passée</p>
                @elseif ($isBlocked && $node->blockedBy)
                    <p class="text-xs text-red-600 mt-0.5 font-medium">
                        Bloqué : en attente de « {{ $node->blockedBy->title }} »
                    </p>
                @elseif ($isBlocked)
                    <p class="text-xs text-red-600 mt-0.5">Bloqué — une branche parallèle n’est pas terminée</p>
                @elseif ($isRejected)
                    <p class="text-xs text-red-600 mt-0.5">Refusé / à réviser</p>
                @endif
            </div>
        </div>
    @empty
        <p class="text-sm text-zinc-400">Aucune étape à afficher pour votre profil sur ce projet.</p>
    @endforelse
</div>
