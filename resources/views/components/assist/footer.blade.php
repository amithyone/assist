@php
    $company = config('assist.company_name', 'Amithyone Media');
    $owner = config('assist.company_owner', 'Amithy Innocent');
    $support = config('assist.support_email', 'support@assist.app');
    $assistHasDownloads = app(\App\Services\AssistAppReleaseService::class)->hasAnyRelease();
@endphp
<footer class="assist-footer">
    <div class="assist-footer-grid">
        <div>
            <a href="{{ route('assist.home') }}" class="assist-nav-brand" style="margin-bottom: 16px;">
                <img src="{{ asset('assist/assist-logo.png') }}" alt="Assist" style="height: 36px;">
            </a>
            <p class="assist-text-muted" style="max-width: 280px; line-height: 1.6;">
                The professional standard for video post-production. From first assembly to final export in DaVinci Resolve.
            </p>
            <p class="assist-text-muted mt-4" style="font-size: 12px;">
                A product of <strong style="color: var(--on-surface);">{{ $owner }}</strong><br>
                <strong style="color: var(--on-surface);">{{ $company }}</strong>
            </p>
        </div>
        <div>
            <h5>Product</h5>
            <ul>
                <li><a href="{{ route('assist.home') }}">Features</a></li>
                <li><a href="{{ route('assist.pricing') }}">Pricing</a></li>
                @if ($assistHasDownloads)
                    <li><a href="{{ route('assist.home') }}#download">Download</a></li>
                @endif
                <li><a href="{{ route('assist.docs') }}">Documentation</a></li>
            </ul>
        </div>
        <div>
            <h5>Account</h5>
            <ul>
                <li><a href="{{ route('assist.register') }}">Sign up</a></li>
                <li><a href="{{ route('login') }}">Log in</a></li>
                <li><a href="{{ route('assist.dashboard') }}">Dashboard</a></li>
            </ul>
        </div>
        <div>
            <h5>Legal &amp; support</h5>
            <ul>
                <li><a href="{{ route('assist.docs') }}">Help center</a></li>
                <li><a href="{{ route('assist.docs') }}#resolve">Connecting Resolve</a></li>
                <li><a href="{{ route('assist.privacy') }}">Privacy policy</a></li>
                <li><a href="{{ route('assist.terms') }}">Terms of use</a></li>
                <li><a href="mailto:{{ $support }}">Contact</a></li>
            </ul>
        </div>
    </div>
    <p class="assist-footer-bottom">
        &copy; {{ date('Y') }} Assist AI Editor. All rights reserved.
        <span style="opacity: 0.6;"> · </span>
        {{ $company }}
    </p>
</footer>
