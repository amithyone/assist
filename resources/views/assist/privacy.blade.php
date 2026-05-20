@extends('layouts.assist')

@section('content')
@php
    $company = config('assist.company_name', 'Amithyone Media');
    $owner = config('assist.company_owner', 'Amithy Innocent');
    $support = config('assist.support_email', 'support@assist.app');
    $legal = config('assist.legal_email', 'legal@amithyone.com');
    $site = config('assist.site_name', 'Assist');
@endphp
<article class="assist-legal-layout">
    <p class="assist-eyebrow">Legal</p>
    <h1>Privacy Policy</h1>
    <p class="assist-legal-updated">Effective date: May 20, 2026 · Last updated: May 2026</p>

    <p>
        This Privacy Policy describes how <strong>{{ $company }}</strong> (“we”, “us”, or “our”), operated by {{ $owner }},
        collects, uses, and protects information when you use the <strong>{{ $site }}</strong> website
        ({{ config('app.url', 'https://assist.amithyone.com') }}), the Assist desktop application for macOS,
        and related services (collectively, the “Services”).
    </p>
    <p>By using the Services, you agree to this Policy. If you do not agree, please do not use the Services.</p>

    <h2>1. Who we are</h2>
    <p>
        {{ $site }} is a video post-production product published by {{ $company }}.
        For privacy questions, contact us at <a href="mailto:{{ $legal }}">{{ $legal }}</a>
        or <a href="mailto:{{ $support }}">{{ $support }}</a>.
    </p>

    <h2>2. Information we collect</h2>
    <h3 style="font-size: 1rem; margin-top: 1rem;">Account information</h3>
    <p>When you register, we collect your name, email address, and password (stored using industry-standard hashing). We may store billing country and plan tier.</p>
    <h3 style="font-size: 1rem; margin-top: 1rem;">Payment information</h3>
    <p>
        Paid plans are processed by third-party payment providers (e.g. Paystack, CheckoutPay).
        We do not store full card numbers on our servers; providers handle PCI-sensitive data under their own policies.
    </p>
    <h3 style="font-size: 1rem; margin-top: 1rem;">Desktop app usage</h3>
    <p>
        When you sign in on the Assist app, we may receive app version, anonymized workflow events (e.g. feature used, success/failure),
        optional Resolve project name metadata, and usage counts for plan enforcement. We do not upload your video files or Resolve timelines to our servers unless you explicitly use a cloud feature that states otherwise.
    </p>
    <h3 style="font-size: 1rem; margin-top: 1rem;">Website and cookies</h3>
    <p>
        We collect standard server logs (IP address, browser type, pages visited) and session cookies required for login and security.
        Analytics cookies, if enabled, help us improve the site; you can limit cookies in your browser settings.
    </p>
    <h3 style="font-size: 1rem; margin-top: 1rem;">Support communications</h3>
    <p>If you email us, we retain the content of your message and our replies to resolve your request.</p>

    <h2>3. How we use information</h2>
    <ul>
        <li>Provide, maintain, and improve the Services</li>
        <li>Authenticate you on the website and desktop app</li>
        <li>Enforce subscription limits and prevent abuse</li>
        <li>Process payments and send receipts</li>
        <li>Send service announcements, security alerts, and password resets</li>
        <li>Comply with legal obligations and respond to lawful requests</li>
    </ul>
    <p>We do not sell your personal information to advertisers.</p>

    <h2>4. Local processing in DaVinci Resolve</h2>
    <p>
        Most editing automation runs locally between Assist and DaVinci Resolve on your Mac.
        Your source media remains on your machine unless you choose to upload content to a separate service.
        Enable Resolve external scripting only on machines you trust.
    </p>

    <h2>5. Sharing with third parties</h2>
    <p>We may share limited data with:</p>
    <ul>
        <li><strong>Hosting and infrastructure</strong> — to operate the website and API</li>
        <li><strong>Payment processors</strong> — to complete transactions</li>
        <li><strong>Email delivery</strong> — for account and support messages</li>
        <li><strong>Legal authorities</strong> — when required by applicable law</li>
    </ul>
    <p>Each provider is bound by contractual or legal duties to protect your data.</p>

    <h2>6. Data retention</h2>
    <p>
        We keep account data while your account is active and for a reasonable period afterward for backups, fraud prevention, and legal compliance.
        You may request deletion of your account by contacting {{ $support }}; some records may be retained where law requires.
    </p>

    <h2>7. Security</h2>
    <p>
        We use HTTPS, access controls, and hashed passwords. No method of transmission over the Internet is 100% secure;
        we encourage strong passwords and keeping your Mac and Resolve projects secure.
    </p>

    <h2>8. Your rights</h2>
    <p>Depending on your location, you may have the right to:</p>
    <ul>
        <li>Access, correct, or delete personal data we hold about you</li>
        <li>Object to or restrict certain processing</li>
        <li>Withdraw consent where processing is consent-based</li>
        <li>Lodge a complaint with a supervisory authority</li>
    </ul>
    <p>Contact <a href="mailto:{{ $legal }}">{{ $legal }}</a> to exercise these rights.</p>

    <h2>9. Children</h2>
    <p>The Services are not directed at children under 16. We do not knowingly collect data from children.</p>

    <h2>10. International users</h2>
    <p>
        {{ $company }} may process data in jurisdictions where our servers or providers operate.
        By using the Services, you understand your information may be transferred internationally with appropriate safeguards.
    </p>

    <h2>11. Changes to this Policy</h2>
    <p>
        We may update this Policy from time to time. We will post the new version on this page and update the “Last updated” date.
        Continued use after changes constitutes acceptance.
    </p>

    <h2>12. Contact</h2>
    <p>
        <strong>{{ $company }}</strong><br>
        Email: <a href="mailto:{{ $legal }}">{{ $legal }}</a><br>
        Support: <a href="mailto:{{ $support }}">{{ $support }}</a>
    </p>
    <p style="margin-top: 32px;">
        <a href="{{ route('assist.terms') }}">Terms of use</a>
        · <a href="{{ route('assist.docs') }}">Documentation</a>
    </p>
</article>
@endsection
