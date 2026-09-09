{{--
    Reusable SEO <head> partial.
    Renders: title, meta description, robots, canonical, hreflang alternates,
    Open Graph, Twitter Card, theme-color and favicon.

    All values derive from config('app.url') via url()/asset() so no domain is
    hardcoded — set APP_URL in .env for production.

    Optional variables (all have safe defaults):
      $seoTitle        string  Page title (defaults to brand name)
      $seoDescription  string  Meta description
      $seoCanonical    string  Canonical URL (defaults to current URL)
      $seoImage        string  Social share image URL (defaults to favicon)
      $seoType         string  og:type (default "website")
      $seoRobots       string  robots directive (default indexable)
      $seoAlternates   bool    Emit en/hi hreflang alternates (default false)
--}}
@php
    $brand           = __('app.brand');
    $metaTitle       = $seoTitle       ?? $brand;
    $metaDescription = $seoDescription ?? __('app.seo_default_description');
    $metaCanonical   = $seoCanonical   ?? url()->current();
    $metaImage       = $seoImage       ?? asset('favicon.ico');
    $metaType        = $seoType        ?? 'website';
    $metaRobots      = $seoRobots      ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    $showAlternates  = $seoAlternates  ?? false;
    $ogLocale        = app()->getLocale() === 'hi' ? 'hi_IN' : 'en_US';
    $altOgLocale     = app()->getLocale() === 'hi' ? 'en_US' : 'hi_IN';
@endphp
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="{{ $metaRobots }}">
<link rel="canonical" href="{{ $metaCanonical }}">
<meta name="theme-color" content="#111c2c">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
@if($showAlternates)
<link rel="alternate" hreflang="en" href="{{ $metaCanonical }}?lang=en">
<link rel="alternate" hreflang="hi" href="{{ $metaCanonical }}?lang=hi">
<link rel="alternate" hreflang="x-default" href="{{ $metaCanonical }}">
@endif

{{-- Open Graph --}}
<meta property="og:type" content="{{ $metaType }}">
<meta property="og:site_name" content="{{ $brand }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $metaCanonical }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:locale" content="{{ $ogLocale }}">
@if($showAlternates)
<meta property="og:locale:alternate" content="{{ $altOgLocale }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
