<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1" />

        <title>Lotixam - Nos réalisations</title>
        <link href="/img/logo.png" rel="icon" type="image/x-icon">

        <link href="/css/common.css" rel="stylesheet">
        <link href="/css/about.css" rel="stylesheet">
        <link href="/css/welcome.css" rel="stylesheet">
        <link href="/css/realizations.css" rel="stylesheet">
        <link href="/css/banniere/banniere.css" rel="stylesheet">
        <link href="/css/foot.css" rel="stylesheet">

    </head>

    <body id="page">
        <script src="/script/about.js"></script>
        <div id="banniere">

            <div class="sign ">
                @include('vitrine.partials.login-nav-link', ['guestLabel' => "Se connecter / S'inscrire"])
            </div>

            <div id="logo">
                <img src="/img/logo.png" width="170" alt="Logo Lotixam">
                <div id="devanture">
                    <img src="/img/lotixam.png" width="150" alt="Enseigne Lotixam">
                    <label id="sentence">L'INVESTISSEUR IMMOBILIER PROFESSIONNEL SI PARTICULIER</label>
                </div>
            </div>
            @include('vitrine.partials.main-nav', ['contactPrev' => 'realizations'])
        </div>

        <div class="longueur">
            <div class="entete">
                <h1 class="h1-up" id="h1up">NOS RÉALISATIONS</h1>
            </div>
        </div>
        <div class="body-main">
            <div class="main margin-top-div realizations-main">
                @if($publicRealizations->isEmpty())
                    <p class="realizations-empty">Nos réalisations seront bientôt présentées ici.</p>
                @else
                    @foreach($publicRealizations as $realization)
                        @php
                            $highlights = is_array($realization->highlights) ? $realization->highlights : [];
                        @endphp
                        <section class="realization-block">
                            <h2>{{ $realization->title }}</h2>
                            @if(filled($realization->excerpt))
                                <p class="realization-excerpt">{{ $realization->excerpt }}</p>
                            @endif

                            @if(count($highlights) > 0)
                                <section class="public-metrics" aria-label="Chiffres clés">
                                    <div class="public-metrics-grid">
                                        @foreach($highlights as $row)
                                            @if(! empty($row['label']) || ! empty($row['value']))
                                                <article class="public-metric-card">
                                                    <div class="public-metric-value">{{ $row['value'] ?? '' }}</div>
                                                    <p class="public-metric-label">{{ $row['label'] ?? '' }}</p>
                                                </article>
                                            @endif
                                        @endforeach
                                    </div>
                                </section>
                            @endif

                            @if(filled($realization->body))
                                <div class="realization-body">
                                    {!! $realization->body !!}
                                </div>
                            @endif

                            @if($realization->slides->isNotEmpty())
                                <div class="carousel realization-carousel" role="region" aria-roledescription="carousel" aria-label="Photos : {{ $realization->title }}">
                                    @foreach($realization->slides as $slide)
                                        <div class="carousel-item">
                                            <img
                                                src="{{ $slide->imageUrl() }}"
                                                alt="{{ $slide->caption ?: 'Photo — '.$realization->title }}"
                                                loading="lazy"
                                            >
                                            @if(filled($slide->caption))
                                                <p class="carousel-caption">{{ $slide->caption }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    @endforeach
                @endif
            </div>
        </div>

    </body>
    @include('vitrine.partials.footer')
</html>
