{{--
    Site-wide structured data (JSON-LD): Organization + WebSite.
    Include on public, indexable pages only.
    URLs derive from config('app.url') via url()/asset().
--}}
@php
    $organizationSchema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => __('app.brand'),
        'url'         => url('/'),
        'logo'        => asset('favicon.ico'),
        'description' => __('app.seo_org_description'),
    ];

    $webSiteSchema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'WebSite',
        'name'       => __('app.brand'),
        'url'        => url('/'),
        'inLanguage' => app()->getLocale(),
    ];
@endphp
<script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($webSiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
