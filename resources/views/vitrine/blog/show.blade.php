@extends('vitrine.blog.layout', ['pageTitle' => $post->title])

@section('blog_content')
    <article class="blog-article">
        <header class="blog-article-header">
            <p class="blog-article-meta">
                <a href="{{ route('blog.index') }}" class="blog-back">← Blog</a>
                <time datetime="{{ $post->published_at->toIso8601String() }}">
                    {{ $post->published_at->translatedFormat('d F Y') }}
                </time>
            </p>
            <h1 class="blog-article-title">{{ $post->title }}</h1>
            @if(filled($post->excerpt))
                <p class="blog-article-excerpt">{{ $post->excerpt }}</p>
            @endif
        </header>
        <div class="blog-article-body">
            {!! $post->renderRichContent('content') !!}
        </div>
    </article>
@endsection
