@php
    $pageTitle = $pageTitle ?? 'Blog';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $pageTitle }} — Lotixam</title>
    <link href="/img/logo.png" rel="icon" type="image/x-icon">
    <link href="/css/common.css" rel="stylesheet">
    <link href="/css/blog.css" rel="stylesheet">
    <link href="/css/foot.css" rel="stylesheet">
</head>
<body class="blog-body">
    <header class="blog-topbar">
        <div class="blog-topbar-inner">
            <a href="{{ route('home') }}" class="blog-logo-link" aria-label="Accueil Lotixam">
                <img src="/img/logo.png" width="56" height="56" alt="Lotixam">
            </a>
            <div class="blog-topbar-titles">
                <a href="{{ route('blog.index') }}" class="blog-brand">Blog Lotixam</a>
                <p class="blog-tagline">Actualités &amp; décryptages</p>
            </div>
            <nav class="blog-topbar-nav" aria-label="Navigation blog">
                <a href="{{ route('home') }}">Accueil site</a>
                <a href="{{ route('blog.index') }}">Tous les articles</a>
            </nav>
            <div class="blog-topbar-account">
                @include('vitrine.partials.login-nav-link', ['guestLabel' => 'Mon compte'])
            </div>
        </div>
    </header>

    <main class="blog-main">
        @yield('blog_content')
    </main>

    @include('vitrine.partials.footer', ['showContributors' => true])
    @stack('scripts')
</body>
</html>
