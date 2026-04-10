@php
    $contactPrev = $contactPrev ?? 'index';
    $showSimulationLink = $showSimulationLink ?? false;
@endphp
<!-- Type de lien en mode Bureau -->
<div id="liens">
    <div>
        <a href="/" class="sous-liens">Accueil</a>
    </div>
    <div>
        <a href="/qui-sommes-nous" class="sous-liens">Qui sommes nous</a>
    </div>
    <div>
        <a href="/nos-realisations" class="sous-liens">Nos réalisations</a>
    </div>
    <div>
        <a href="/nous-achetons" class="sous-liens">Nous achetons</a>
    </div>
    <div>
        <a href="/nous-vendons" class="sous-liens">Nous vendons</a>
    </div>
    <div>
        <a href="/investisseurs" class="sous-liens">Investisseurs</a>
    </div>
    @if($showSimulationLink)
        <div>
            <a href="/simulation" class="sous-liens">Simuler mon projet</a>
        </div>
    @endif
    <div>
        <a href="/contact?prev={{ $contactPrev }}&amp;button=false" class="sous-liens">Contact</a>
    </div>
</div>

<!-- Structure du menu hamburger -->
<div class="menu-toggle">
    <input type="checkbox" id="toggle">
    <label for="toggle">&#9776;</label>
    <ul class="menu">
        <li class="liens"><a href="/">Accueil</a></li>
        <li class="liens"><a href="/qui-sommes-nous">Qui sommes nous</a></li>
        <li class="liens"><a href="/nos-realisations">Nos réalisations</a></li>
        <li class="liens"><a href="/nous-achetons">Nous achetons</a></li>
        <li class="liens"><a href="/nous-vendons">Nous vendons</a></li>
        <li class="liens"><a href="/investisseurs">Investisseurs</a></li>
        @if($showSimulationLink)
            <li class="liens"><a href="/simulation">Simuler mon projet</a></li>
        @endif
        <li class="liens"><a href="/contact?prev={{ $contactPrev }}&amp;button=false">Contact</a></li>
        <li class="liens">@include('vitrine.partials.login-nav-link', ['guestLabel' => 'Mon compte'])</li>
    </ul>
</div>
