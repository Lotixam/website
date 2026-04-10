@extends('vitrine.blog.layout', ['pageTitle' => 'Blog'])

@push('scripts')
    <script src="/script/blog-hero.js" defer></script>
@endpush

@section('blog_content')
    <section class="blog-hero-zone" aria-labelledby="blog-page-heading">
        <div class="blog-hero-intro">
            <h1 class="blog-page-title" id="blog-page-heading">Le blog</h1>
            <p class="blog-lead">L’essentiel de notre actualité immobilière et de nos analyses.</p>
        </div>
        @if($heroPosts->isEmpty())
            <p class="blog-empty-hero blog-hero-intro">Les prochains articles seront publiés ici.</p>
        @else
            <div
                class="blog-hero-fullbleed"
                data-blog-hero
                role="region"
                aria-roledescription="carrousel"
                aria-label="Derniers articles en vedette"
            >
                <div class="blog-hero-viewport">
                    @foreach($heroPosts as $i => $post)
                        @php
                            $coverUrl = $post->coverImageUrl();
                        @endphp
                        <article
                            class="blog-hero-slide {{ $i === 0 ? 'is-active' : '' }} {{ $coverUrl ? 'has-cover' : 'no-cover' }}"
                            data-hero-slide
                            id="blog-hero-slide-{{ $i }}"
                            aria-hidden="{{ $i === 0 ? 'false' : 'true' }}"
                            @if($coverUrl) style="--blog-hero-cover: url({{ \Illuminate\Support\Js::from($coverUrl) }})" @endif
                        >
                            <div class="blog-hero-slide-overlay" aria-hidden="true"></div>
                            <div class="blog-hero-slide-inner">
                                <time class="blog-hero-slide-date" datetime="{{ $post->published_at->toIso8601String() }}">
                                    {{ $post->published_at->translatedFormat('d M Y') }}
                                </time>
                                <h2 class="blog-hero-slide-title">
                                    <a href="{{ route('blog.show', ['slug' => $post->slug]) }}">{{ $post->title }}</a>
                                </h2>
                                @if(filled($post->excerpt))
                                    <p class="blog-hero-slide-excerpt">{{ \Illuminate\Support\Str::limit($post->excerpt, 320) }}</p>
                                @endif
                                <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="blog-hero-slide-cta">Lire l’article</a>
                            </div>
                        </article>
                    @endforeach
                </div>
                @if($heroPosts->count() > 1)
                    <div class="blog-hero-controls">
                        <button type="button" class="blog-hero-arrow" data-hero-prev>
                            <span aria-hidden="true">‹</span>
                            <span class="visually-hidden">Article précédent</span>
                        </button>
                        <div class="blog-hero-dots" role="tablist" aria-label="Choisir un article">
                            @foreach($heroPosts as $i => $post)
                                <button
                                    type="button"
                                    class="blog-hero-dot {{ $i === 0 ? 'is-active' : '' }}"
                                    data-hero-dot
                                    aria-label="Afficher : {{ $post->title }}"
                                    @if($i === 0) aria-current="true" @endif
                                ></button>
                            @endforeach
                        </div>
                        <button type="button" class="blog-hero-arrow" data-hero-next>
                            <span aria-hidden="true">›</span>
                            <span class="visually-hidden">Article suivant</span>
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </section>

    <section class="blog-toolbar" aria-label="Recherche et filtres">
        <form class="blog-search-form" method="get" action="{{ route('blog.index') }}" role="search">
            @if($filterYear !== null)
                <input type="hidden" name="year" value="{{ $filterYear }}">
            @endif
            @if($filterMonth !== null)
                <input type="hidden" name="month" value="{{ $filterMonth }}">
            @endif
            <label class="visually-hidden" for="blog-search-q">Rechercher un article</label>
            <input
                id="blog-search-q"
                class="blog-search-input"
                type="search"
                name="q"
                value="{{ $searchQuery }}"
                placeholder="Rechercher par titre ou extrait…"
                autocomplete="off"
            >
            <button type="submit" class="blog-search-btn">Rechercher</button>
        </form>

        @if($timeline->isNotEmpty())
            <div class="blog-timeline-wrap">
                <p class="blog-timeline-label">Par période</p>
                <div class="blog-timeline-scroll" role="tablist" aria-label="Filtrer par mois">
                    <a href="{{ $blogTimelineAllUrl }}" class="blog-chip {{ $filterYear === null && $filterMonth === null ? 'is-active' : '' }}">Toutes</a>
                    @foreach($timeline as $row)
                        <a href="{{ $row['url'] }}" class="blog-chip {{ $row['isActive'] ? 'is-active' : '' }}">{{ $row['label'] }}</a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <section class="blog-list-section" aria-label="Liste des articles">
        @if($posts->isEmpty())
            <p class="blog-empty">Aucun article ne correspond à votre recherche.</p>
        @else
            <ul class="blog-grid">
                @foreach($posts as $post)
                    <li>
                        <article class="blog-card">
                            <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="blog-card-link">
                                <time class="blog-card-date" datetime="{{ $post->published_at->toIso8601String() }}">
                                    {{ $post->published_at->translatedFormat('d M Y') }}
                                </time>
                                <h2 class="blog-card-title">{{ $post->title }}</h2>
                                @if(filled($post->excerpt))
                                    <p class="blog-card-excerpt">{{ $post->excerpt }}</p>
                                @endif
                            </a>
                        </article>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
