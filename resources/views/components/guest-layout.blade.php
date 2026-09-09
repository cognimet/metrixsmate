<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  @include('partials.analytics')
  <meta name="robots" content="noindex, follow" />
  <title>{{ $title ?? __('app.brand') }}</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any" />
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    .glass {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    body {
      background: linear-gradient(135deg, #667eea, #764ba2, #6ee7b7);
      background-size: 400% 400%;
      animation: gradientMove 15s ease infinite;
    }
    @keyframes gradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-md">
    {{ $slot }}
  </div>

</body>
</html>
