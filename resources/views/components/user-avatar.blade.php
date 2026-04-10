@props([
    'user',
    'size' => 'h-16 w-16',
    'text' => 'text-2xl',
])

@php
    $url = $user->avatarUrl();
@endphp
<div {{ $attributes->class([
    'rounded-full shrink-0 overflow-hidden flex items-center justify-center font-bold',
    'bg-[#b1e90e] text-[#2b2b2b]' => ! $url,
    $size,
]) }}>
    @if ($url)
        <img src="{{ $url }}" alt="" class="h-full w-full object-cover">
    @else
        <span class="{{ $text }}">{{ strtoupper(mb_substr((string) ($user->name ?? ''), 0, 1)) ?: '?' }}</span>
    @endif
</div>
