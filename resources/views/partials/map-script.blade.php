@php
    /** @var \App\Services\Map\MapService $mapService */
    $mapService = app(\App\Services\Map\MapService::class);
    $config = $mapService->webConfig();
    $libraries = $libraries ?? null;
    $callback = $callback ?? null;
    $jsUrl = $config['js_url'] ?? null;

    if ($jsUrl && $libraries && !str_contains($jsUrl, 'libraries=')) {
        $separator = str_contains($jsUrl, '?') ? '&' : '?';
        $jsUrl .= $separator . 'libraries=' . $libraries;
    }

    if ($jsUrl && $callback && !str_contains($jsUrl, 'callback=')) {
        $separator = str_contains($jsUrl, '?') ? '&' : '?';
        $jsUrl .= $separator . 'callback=' . $callback;
    }
@endphp

@if(($config['driver'] ?? 'google') === 'google')
    {{-- Google Maps JS --}}
    <script src="{{ $jsUrl }}"></script>
@elseif(($config['driver'] ?? null) === 'iran')
    {{-- TODO: Neshan or other Iranian provider JS script --}}
    {{-- Placeholder for future Iranian map provider integration --}}
@endif

