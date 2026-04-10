@props(['percent' => 0])

<div class="w-full bg-zinc-200 rounded-full h-2.5 overflow-hidden">
    <div class="h-full rounded-full transition-all duration-500"
         style="width: {{ $percent }}%; background-color: #b1e90e;"></div>
</div>
