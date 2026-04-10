@php
    use Illuminate\Support\Facades\Route;

    $showContributors = $showContributors ?? true;
    $legalsUrl = Route::has('vitrine.legals') ? route('vitrine.legals') : url('/mentions-legales');
    $cookiesUrl = Route::has('vitrine.cookies') ? route('vitrine.cookies') : url('/politique-cookies');
    $contributorsUrl = Route::has('vitrine.contributors') ? route('vitrine.contributors') : url('/contributeurs');
@endphp
<footer>
    <div>
        <b>&copy; LOTIXAM SAS {{ date('Y') }}. Tous droits réservés</b>
    </div>
    <div class="separator">-</div>
    <div>
        <a href="{{ $legalsUrl }}">Mentions légales</a>
    </div>
    <div class="separator">-</div>
    <div>
        <a href="{{ $cookiesUrl }}">Politique de cookies</a>
    </div>
    <div class="separator">-</div>
    <div>
        <a href="https://blog.lotixam.fr/">Blog</a>
    </div>
    <div class="separator">-</div>
    <div>
        <a href="https://faq.lotixam.fr/">FAQ</a>
    </div>
    @if ($showContributors)
        <div class="separator">-</div>
        <div>
            <a href="{{ $contributorsUrl }}">Contributeurs &amp; Partenaires</a>
        </div>
    @endif
</footer>
