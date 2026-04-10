@props(['stages'])

<div class="space-y-0">
    @foreach ($stages as $stage)
        @php
            $status = $stage->pivot->status;
            $isCompleted = $status === 'completed';
            $isSkipped = $status === 'skipped';
            $isActive = $status === 'in_progress';
            $source = $stage->pivot->source ?? 'default';
            $dotColor = $isCompleted ? '#b1e90e' : ($isSkipped ? '#94a3b8' : ($isActive ? '#f59e0b' : '#d4d4d8'));
        @endphp
        <div class="flex gap-4 {{ !$loop->last ? 'pb-6' : '' }}">
            <div class="flex flex-col items-center">
                <div class="w-4 h-4 rounded-full border-2 shrink-0"
                     style="border-color: {{ $dotColor }}; {{ $isCompleted ? 'background-color: ' . $dotColor : '' }}"></div>
                @unless ($loop->last)
                    <div class="w-px flex-1 bg-zinc-300 mt-1"></div>
                @endunless
            </div>
            <div class="pb-2 -mt-0.5">
                <p class="text-sm font-medium {{ $isCompleted ? 'text-zinc-900' : ($isSkipped ? 'text-zinc-400 line-through' : 'text-zinc-700') }}">
                    {{ $stage->name }}
                    @if ($source === 'collaborator')
                        <span class="ml-1 text-xs text-blue-500 font-normal">(collaborateur)</span>
                    @endif
                </p>
                @if ($isCompleted && $stage->pivot->completed_at)
                    <p class="text-xs text-zinc-400 mt-0.5">Terminé le {{ \Carbon\Carbon::parse($stage->pivot->completed_at)->format('d/m/Y') }}</p>
                @elseif ($isActive)
                    <p class="text-xs text-amber-500 mt-0.5">En cours</p>
                @elseif ($isSkipped)
                    <p class="text-xs text-zinc-400 mt-0.5">Passée</p>
                @endif
            </div>
        </div>
    @endforeach
</div>
