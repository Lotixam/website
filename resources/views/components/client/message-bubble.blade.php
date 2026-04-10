@props(['message', 'isOwn' => false])

@if ($isOwn)
    <div class="flex gap-2 justify-end items-end">
        <div class="max-w-[75%] bg-[#b1e90e]/20 border-[#b1e90e]/30 border rounded-2xl px-4 py-2.5">
            <p class="text-sm text-zinc-800 whitespace-pre-line">{{ $message->body }}</p>
            <p class="text-[10px] text-zinc-400 mt-1 text-right">
                {{ $message->created_at->format('d/m H:i') }}
            </p>
        </div>
        <x-user-avatar :user="$message->user" size="h-8 w-8" text="text-xs" class="ring-1 ring-[#b1e90e]/40 shrink-0 mb-0.5" />
    </div>
@else
    <div class="flex gap-2 justify-start items-end">
        <x-user-avatar :user="$message->user" size="h-8 w-8" text="text-xs" class="ring-1 ring-zinc-200 shrink-0 mb-0.5" />
        <div class="max-w-[75%] bg-white border-zinc-200 border rounded-2xl px-4 py-2.5">
            <p class="text-xs font-semibold text-zinc-500 mb-0.5">{{ $message->user->name }}</p>
            <p class="text-sm text-zinc-800 whitespace-pre-line">{{ $message->body }}</p>
            <p class="text-[10px] text-zinc-400 mt-1">
                {{ $message->created_at->format('d/m H:i') }}
            </p>
        </div>
    </div>
@endif
