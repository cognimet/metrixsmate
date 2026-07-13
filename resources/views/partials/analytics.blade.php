{{--
    Google Analytics 4 (gtag.js).
    Rendered site-wide from every layout <head>.
    ID is read from config (config/services.php -> google_analytics.id),
    which defaults to env('GOOGLE_ANALYTICS_ID', 'G-V6HEN6P73B').
    Renders nothing if no measurement ID is configured.
--}}
@php($gaId = config('services.google_analytics.id'))
@if($gaId)
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $gaId }}');
</script>
@endif
