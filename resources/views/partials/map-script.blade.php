@php
    /**
     * Shared map script loader.
     *
     * @var string|null $libraries
     */
    /** @var \App\Services\Map\MapService $mapService */
    $mapService = app(\App\Services\Map\MapService::class);
    $config = $mapService->webConfig();
    $jsUrl = $config['js_url'] ?? null;

    if (!empty($libraries) && is_string($jsUrl)) {
        // Replace or append libraries query param to retain compatibility with existing scripts.
        if (str_contains($jsUrl, 'libraries=')) {
            $jsUrl = preg_replace('/libraries=[^&]+/', 'libraries=' . urlencode($libraries), $jsUrl);
        } else {
            $jsUrl .= (str_contains($jsUrl, '?') ? '&' : '?') . 'libraries=' . urlencode($libraries);
        }
    }
@endphp

@if(($config['driver'] ?? 'google') === 'google' && $jsUrl)
    {{-- Google Maps JS --}}
    <script src="{{ $jsUrl }}"></script>
@elseif(($config['driver'] ?? null) === 'iran')
    {{-- TODO: Neshan or other Iranian provider JS script --}}
    {{-- Placeholder for future Iranian web map integration --}}
@endif
