@extends('vitrine.blog.layout', ['pageTitle' => 'Blog'])

@section('blog_content')
    <section class="blog-hero" aria-label="Articles récents">
        <div class="blog-hero-header">
            <h1 class="blog-page-title">Le blog</h1>
            <p class="blog-lead">L’essentiel de notre actualité immobilière et de nos analyses.</p>
        </div>
        @if($heroPosts->isEmpty())
            <p class="blog-empty-hero">Les prochains articles seront publiés ici.</p>
        @else
            <div class="blog-hero-scroll" role="list">
                @foreach($heroPosts as $post)
                    <article class="blog-hero-card" role="listitem">
                        <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="blog-hero-card-link">
                            <time class="blog-card-date" datetime="{{ $post->published_at->toIso8601String() }}">
                                {{ $post->published_at->translatedFormat('d M Y') }}
                            </time>
                            <h2 class="blog-hero-card-title">{{ $post->title }}</h2>
                            @if(filled($post->excerpt))
                                <p class="blog-hero-card-excerpt">{{ \Illuminate\Support\Str::limit($post->excerpt, 160) }}</p>
                            @endif
                            <span class="blog-hero-card-cta">Lire l’article</span>
                        </a>
                    </article>
                @endforeach
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
