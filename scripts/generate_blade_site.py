#!/usr/bin/env python3
import os

BASE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
T = "d" + "iv"


def write(rel, content):
    path = os.path.join(BASE, rel)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, "w") as f:
        f.write(content)


write(
    "resources/views/assist/home.blade.php",
    """@extends('layouts.assist')

@section('title', 'Home')

@section('content')
    @include('assist.sections.hero')
    @include('assist.sections.editing-engine')
    @include('assist.sections.features')
    @include('assist.sections.workspace')
    @include('assist.sections.interoperability')
@endsection
""",
)

write(
    "resources/views/assist/pricing.blade.php",
    f"""@extends('layouts.assist')

@section('title', 'Pricing')

@section('content')
<section class="assist-section" style="padding-top: 120px;">
    <{T} class="assist-container text-center mb-8">
        <span class="assist-eyebrow">Pricing</span>
        <h1 class="assist-h2">Simple, transparent plans.</h1>
    </{T}>
    <{T} class="assist-container assist-grid-3">
        @foreach ($cards as $card)
            <{T} class="glass-panel assist-pricing-card @{{ ($card['highlight'] ?? false) ? 'highlight' : '' }}" style="position: relative;">
                @if (!empty($card['badge']))
                    <span class="new-badge" style="position: absolute; top: 16px; right: 16px;">@{{ $card['badge'] }}</span>
                @endif
                <{T}>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">@{{ $card['name'] }}</h3>
                    <{T} class="assist-pricing-price">
                        @{{ $card['price'] }}
                        @if (!empty($card['period']))
                            <span class="assist-text-muted" style="font-size: 16px;">@{{ $card['period'] }}</span>
                        @endif
                    </{T}>
                    <p class="assist-text-muted" style="margin-top: 12px;">@{{ $card['description'] }}</p>
                </{T}>
                <ul class="assist-pricing-features">
                    @foreach ($card['features'] as $feature)
                        <li>@{{ $feature }}</li>
                    @endforeach
                </ul>
                <a href="@{{ $card['cta_url'] }}" class="assist-btn @{{ ($card['highlight'] ?? false) ? 'assist-btn-primary' : 'assist-btn-outline' }} assist-btn-block">
                    @{{ $card['cta_label'] ?? 'Get Started' }}
                </a>
            </{T}>
        @endforeach
    </{T}>
    <{T} class="assist-container" style="margin-top: 80px;">
        <{T} class="glass-panel" style="padding: 48px; border-radius: 3.5rem;">
            <{T} class="assist-grid-2 items-center">
                <{T}>
                    <h2 class="assist-h2 mb-4">Need a custom solution?</h2>
                    <p class="assist-text-muted">Volume discounts and custom deployments for post-production houses and educational institutions.</p>
                </{T}>
                <{T} style="text-align: right;">
                    <a href="mailto:@{{ config('assist.support_email') }}" class="assist-btn assist-btn-outline">Contact Sales</a>
                </{T}>
            </{T}>
        </{T}>
    </{T}>
</section>
@endsection
""",
)

docs_sections = [
    ("Getting Started", ["Installation", "Connecting Resolve", "First Project", "UI Overview"]),
    ("Core Features", ["AI Editor Engine", "Reels Cloner", "Beat Edit", "Transcriptions"]),
    ("Workflows", ["Wedding Highlights", "Documentary Assembly", "Social Ads", "Podcast Cuts"]),
    ("Advanced", ["Story Graph", ".assistproject Format", "Custom Templates", "API Access"]),
    ("Support", ["FAQ", "Troubleshooting", "System Requirements", "Contact Us"]),
]

sidebar = ""
for title, items in docs_sections:
    sidebar += f'                <{T}>\n                    <h4>{title}</h4>\n                    <ul>\n'
    for item in items:
        sidebar += f'                        <li><a href="#">{item}</a></li>\n'
    sidebar += f"                    </ul>\n                </{T}>\n"

write(
    "resources/views/assist/docs.blade.php",
    f"""@extends('layouts.assist')

@section('title', 'Documentation')

@section('content')
<{T} class="assist-docs-layout">
    <aside class="assist-docs-sidebar">
        <input type="search" class="assist-input" placeholder="Search docs..." style="margin-bottom: 24px;">
        <nav class="assist-docs-nav">
{sidebar}        </nav>
    </aside>
    <article class="assist-docs-content">
        <p class="assist-eyebrow">Docs &rsaquo; Getting Started</p>
        <h1>Installation Guide</h1>
        <p>Learn how to set up Assist and connect it with DaVinci Resolve for a seamless post-production workflow.</p>

        <h2 style="margin-top: 32px; font-size: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px;">1. Download and Install</h2>
        <p>Download Assist for macOS from your dashboard or the download link. Drag the application to <code>/Applications</code> and launch it.</p>
        <pre class="glass-panel" style="padding: 24px; border-radius: 16px; font-size: 12px; color: var(--primary); margin: 16px 0;">$ unzip Assist_macOS.zip
$ mv Assist_AI_Editor.app /Applications</pre>

        <h2 style="margin-top: 32px; font-size: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px;">2. Enable Resolve Scripting</h2>
        <p>Assist requires external scripting in DaVinci Resolve.</p>
        <ul style="color: var(--on-surface-variant); margin-left: 24px; line-height: 1.8;">
            <li>Open DaVinci Resolve Preferences (Cmd + ,)</li>
            <li>Go to <strong>System &gt; General</strong></li>
            <li>Set <strong>External scripting using</strong> to <code>Local</code></li>
            <li>Restart DaVinci Resolve</li>
        </ul>

        <h2 style="margin-top: 32px; font-size: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px;">3. First Connection</h2>
        <p>With both apps running, Assist detects the active Resolve project. A green indicator appears in the Assist sidebar when connected.</p>

        <p class="assist-text-muted" style="margin-top: 48px; font-size: 11px;">Last updated: May 2026</p>
    </article>
</{T}>
@endsection
""",
)

auth_wrap = f"""<{T} class="assist-auth-wrap">
    <{T} class="glass-panel assist-auth-card">
        <img src="@{{ asset('assist/assist-logo.png') }}" alt="Assist" class="assist-auth-logo">
"""

auth_end = f"""    </{T}>
</{T}>
"""

for name, title, subtitle, fields, form_action, method in [
    ("login", "Welcome back", "Sign in to your Assist account", "email,password", "assist.login", "POST"),
]:
    pass

write(
    "resources/views/assist/auth/login.blade.php",
    f"""@extends('layouts.assist')
@section('title', 'Log in')
@section('content')
{auth_wrap}
        <h1>Welcome back</h1>
        <p class="assist-auth-sub">Sign in to your Assist account</p>
        <x-assist.alert />
        <form method="POST" action="@{{ route('assist.login') }}">
            @csrf
            <x-assist.input label="Email" name="email" type="email" required />
            <x-assist.input label="Password" name="password" type="password" required />
            <label class="assist-checkbox-row">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <button type="submit" class="assist-btn assist-btn-primary assist-btn-block">Log in</button>
        </form>
        <p class="assist-auth-links">
            <a href="@{{ route('assist.password.request') }}">Forgot password?</a><br>
            No account? <a href="@{{ route('assist.register') }}">Register</a>
        </p>
{auth_end}
@endsection
""",
)

write(
    "resources/views/assist/auth/register.blade.php",
    f"""@extends('layouts.assist')
@section('title', 'Register')
@section('content')
{auth_wrap}
        <h1>Create account</h1>
        <p class="assist-auth-sub">Start with the free plan — upgrade anytime</p>
        <x-assist.alert />
        <form method="POST" action="@{{ route('assist.register') }}">
            @csrf
            <x-assist.input label="Name" name="name" required />
            <x-assist.input label="Email" name="email" type="email" required />
            <x-assist.input label="Password" name="password" type="password" required />
            <x-assist.input label="Confirm password" name="password_confirmation" type="password" required />
            <x-assist.input label="YouTube (optional)" name="youtube" />
            <x-assist.input label="Instagram (optional)" name="instagram" />
            <label class="assist-checkbox-row">
                <input type="checkbox" name="marketing_opt_in" value="1" @{{ old('marketing_opt_in') ? 'checked' : '' }}>
                Send me product updates
            </label>
            <button type="submit" class="assist-btn assist-btn-primary assist-btn-block">Create account</button>
        </form>
        <p class="assist-auth-links">Already have an account? <a href="@{{ route('assist.login') }}">Log in</a></p>
{auth_end}
@endsection
""",
)

write(
    "resources/views/assist/auth/forgot-password.blade.php",
    f"""@extends('layouts.assist')
@section('title', 'Forgot password')
@section('content')
{auth_wrap}
        <h1>Reset password</h1>
        <p class="assist-auth-sub">We will email you a reset link</p>
        <x-assist.alert />
        <form method="POST" action="@{{ route('assist.password.email') }}">
            @csrf
            <x-assist.input label="Email" name="email" type="email" required />
            <button type="submit" class="assist-btn assist-btn-primary assist-btn-block">Send reset link</button>
        </form>
        <p class="assist-auth-links"><a href="@{{ route('assist.login') }}">Back to log in</a></p>
{auth_end}
@endsection
""",
)

write(
    "resources/views/assist/auth/reset-password.blade.php",
    f"""@extends('layouts.assist')
@section('title', 'Reset password')
@section('content')
{auth_wrap}
        <h1>Choose a new password</h1>
        <p class="assist-auth-sub">Enter your new password below</p>
        <x-assist.alert />
        <form method="POST" action="@{{ route('assist.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="@{{ $token }}">
            <x-assist.input label="Email" name="email" type="email" :value="$email" required />
            <x-assist.input label="Password" name="password" type="password" required />
            <x-assist.input label="Confirm password" name="password_confirmation" type="password" required />
            <button type="submit" class="assist-btn assist-btn-primary assist-btn-block">Reset password</button>
        </form>
{auth_end}
@endsection
""",
)

write(
    "resources/views/assist/dashboard.blade.php",
    f"""@extends('layouts.assist')
@section('title', 'Dashboard')
@section('content')
<section class="assist-section" style="padding-top: 120px;">
    <{T} class="assist-container">
        <span class="assist-eyebrow">Account</span>
        <h1 class="assist-h2 mb-4">Hello, @{{ auth()->user()->name }}</h1>
        <{T} class="assist-dash-grid">
            <{T} class="glass-panel assist-stat">
                <p class="assist-stat-label">Current plan</p>
                <p class="assist-stat-value">@{{ $plan?->name ?? 'None' }}</p>
            </{T}>
            <{T} class="glass-panel assist-stat">
                <p class="assist-stat-label">Billing period</p>
                <p class="assist-stat-value">@{{ $limits['period'] ?? now()->format('Y-m') }}</p>
            </{T}>
        </{T}>
        @if (!empty($limits['features']))
            <h2 style="font-size: 1.25rem; margin: 32px 0 16px;">Usage this month</h2>
            <{T} class="assist-dash-grid">
                @foreach ($limits['features'] as $feature => $data)
                    <{T} class="glass-panel assist-stat">
                        <p class="assist-stat-label">@{{ str_replace('_', ' ', $feature) }}</p>
                        <p class="assist-stat-value">
                            @{{ $data['used'] }}
                            @if ($data['limit'] !== null)
                                / @{{ $data['limit'] }}
                            @else
                                <span class="assist-text-muted">(unlimited)</span>
                            @endif
                        </p>
                    </{T}>
                @endforeach
            </{T}>
        @endif
        <{T} style="margin-top: 48px; display: flex; flex-wrap: gap: 16px;">
            <a href="@{{ config('assist.download_url') }}" class="assist-btn assist-btn-primary">Download Assist for Mac</a>
            <a href="@{{ route('assist.pricing') }}" class="assist-btn assist-btn-outline">Upgrade plan</a>
        </{T}>
    </{T}>
</section>
@endsection
""",
)

print("Blade views generated.")
