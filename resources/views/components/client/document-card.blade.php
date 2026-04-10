@props(['request'])

@php
    $colors = [
        'pending' => ['bg' => 'bg-zinc-100', 'text' => 'text-zinc-600', 'badge' => 'bg-zinc-200 text-zinc-600'],
        'uploaded' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'badge' => 'bg-amber-100 text-amber-700'],
        'approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'badge' => 'bg-emerald-100 text-emerald-700'],
        'rejected' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'badge' => 'bg-red-100 text-red-700'],
    ];
    $status = $request->status->value;
    $c = $colors[$status] ?? $colors['pending'];
    $labels = ['pending' => 'En attente', 'uploaded' => 'Déposé', 'approved' => 'Validé', 'rejected' => 'Refusé'];
@endphp

<div class="{{ $c['bg'] }} rounded-xl p-4 border border-zinc-200/60">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="font-medium text-sm text-zinc-900 truncate">{{ $request->name }}</p>
            @if ($request->workflowNode)
                <p class="text-xs text-zinc-400 mt-0.5">Étape workflow : {{ $request->workflowNode->title }}</p>
            @elseif ($request->stage)
                <p class="text-xs text-zinc-400 mt-0.5">Étape : {{ $request->stage->name }}</p>
            @endif
            @if ($request->due_date)
                <p class="text-xs text-zinc-400">Échéance : {{ $request->due_date->format('d/m/Y') }}</p>
            @endif
        </div>
        <span class="shrink-0 text-xs font-medium px-2 py-0.5 rounded-full {{ $c['badge'] }}">
            {{ $labels[$status] }}
        </span>
    </div>

    @if ($status === 'pending')
        <form method="POST" action="{{ route('client.document.upload', $request) }}" enctype="multipart/form-data" class="mt-3">
            @csrf
            <div class="flex items-center gap-2">
                <input type="file" name="file" required
                       class="text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#b1e90e] file:text-zinc-900 hover:file:bg-[#a0d40d] cursor-pointer">
                <button type="submit"
                        class="shrink-0 text-xs font-medium px-3 py-1.5 rounded-lg bg-[#b1e90e] text-zinc-900 hover:bg-[#a0d40d] transition">
                    Déposer
                </button>
            </div>
        </form>
    @elseif ($status === 'rejected')
        <p class="text-xs text-red-600 mt-2">Document refusé — veuillez le déposer à nouveau.</p>
        <form method="POST" action="{{ route('client.document.upload', $request) }}" enctype="multipart/form-data" class="mt-2">
            @csrf
            <div class="flex items-center gap-2">
                <input type="file" name="file" required
                       class="text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-red-100 file:text-red-700 cursor-pointer">
                <button type="submit"
                        class="shrink-0 text-xs font-medium px-3 py-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                    Renvoyer
                </button>
            </div>
        </form>
    @endif
</div>
