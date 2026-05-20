@php
    $releases = app(\App\Services\AssistAppReleaseService::class);
    $downloads = $releases->availableDownloads();
    $macNotice = $releases->macAvailabilityNotice();
    $layout = $layout ?? 'buttons';
    $primaryLabel = $primaryLabel ?? null;
@endphp
@if (count($downloads) > 0)
    @if ($macNotice && ($showNotice ?? true))
        <p class="assist-download-notice assist-text-muted">{{ $macNotice }}</p>
    @endif
    @if ($layout === 'list')
        <ul class="assist-download-list">
            @foreach ($downloads as $dl)
                <li class="assist-download-list-item glass-panel">
                    <div>
                        <strong>{{ $dl['label'] }}</strong>
                        @if (!empty($dl['description']))
                            <p class="assist-text-muted" style="font-size: 0.875rem; margin-top: 4px;">{{ $dl['description'] }}</p>
                        @endif
                        @if (!empty($dl['version']) || $dl['size_mb'] > 0)
                            <p class="assist-text-muted" style="font-size: 0.8125rem; margin-top: 6px;">
                                @if (!empty($dl['version'])) v{{ $dl['version'] }} @endif
                                @if ($dl['size_mb'] > 0) · {{ $dl['size_mb'] }} MB @endif
                            </p>
                        @endif
                    </div>
                    <a href="{{ $dl['url'] }}" class="assist-btn assist-btn-primary">Download</a>
                </li>
            @endforeach
        </ul>
    @else
        <div class="assist-download-buttons">
            @foreach ($downloads as $dl)
                <a href="{{ $dl['url'] }}" class="assist-btn {{ $loop->first && $primaryLabel ? 'assist-btn-primary' : ($loop->first ? 'assist-btn-primary' : 'assist-btn-outline') }}">
                    @if ($loop->first && $primaryLabel)
                        {{ $primaryLabel }}
                    @else
                        {{ $dl['label'] }}
                    @endif
                </a>
            @endforeach
        </div>
    @endif
@endif
