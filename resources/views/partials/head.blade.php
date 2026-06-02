<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'HRMS') : config('app.name', 'HRMS') }}
</title>


<link rel="icon" href="{{ asset('favicon/favicon-32x32.png') }}" sizes="32x32" type="image/png">
<link rel="icon" href="{{ asset('favicon/favicon.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('favicon/apple-touch-icon.png') }}">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
