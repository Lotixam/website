@php
    $href = route('login');
    $text = auth()->check()
        ? auth()->user()->vitrineDisplayName()
        : $guestLabel;
@endphp
<a href="{{ $href }}">{{ $text }}</a>
