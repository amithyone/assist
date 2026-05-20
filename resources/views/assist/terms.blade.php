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
    <h1>Terms of Use</h1>
    <p class="assist-legal-updated">Effective date: May 20, 2026 · Last updated: May 2026</p>

    <p>
        These Terms of Use (“Terms”) govern your access to and use of the <strong>{{ $site }}</strong> website,
        desktop application, and related services (the “Services”) provided by <strong>{{ $company }}</strong>
        (“{{ $company }}”, “we”, “us”), operated by {{ $owner }}.
    </p>
    <p>By creating an account, downloading the app, or using the Services, you agree to these Terms.</p>

    <h2>1. Eligibility</h2>
    <p>
        You must be at least 16 years old and able to form a binding contract. If you use the Services on behalf of a company,
        you represent that you have authority to bind that organization.
    </p>

    <h2>2. License to use the software</h2>
    <p>
        Subject to these Terms and your plan, we grant you a limited, non-exclusive, non-transferable, revocable license to
        install and use the Assist desktop application for your own commercial or personal post-production work.
        You may not reverse engineer, redistribute, sublicense, or resell the software except as expressly permitted in writing.
    </p>

    <h2>3. Accounts and security</h2>
    <ul>
        <li>You are responsible for keeping your login credentials confidential.</li>
        <li>You must provide accurate registration information.</li>
        <li>Notify us promptly at <a href="mailto:{{ $support }}">{{ $support }}</a> if you suspect unauthorized access.</li>
    </ul>

    <h2>4. Subscriptions and payments</h2>
    <p>
        Paid plans are billed according to the pricing shown on our website at the time of purchase.
        Fees are charged through third-party payment gateways. Taxes may apply based on your location.
    </p>
    <ul>
        <li>Plan limits (e.g. automated runs per month) are described on the Pricing page and in-app.</li>
        <li>Upgrades take effect when payment is confirmed; downgrades may apply at the next billing cycle unless stated otherwise.</li>
        <li>Refunds are handled case-by-case for billing errors or duplicate charges — contact {{ $support }} within 14 days of purchase.</li>
        <li>We may change prices with reasonable notice; continued use after the effective date constitutes acceptance.</li>
    </ul>

    <h2>5. Acceptable use</h2>
    <p>You agree not to:</p>
    <ul>
        <li>Use the Services to infringe copyright, trademark, or other intellectual property rights</li>
        <li>Upload or process unlawful content, including material you do not have rights to edit</li>
        <li>Attempt to bypass plan limits, license checks, or security measures</li>
        <li>Interfere with servers, APIs, or other users’ accounts</li>
        <li>Use the Services to build a competing product by scraping or automated extraction of our models or UI</li>
    </ul>
    <p>You retain ownership of your footage, projects, and creative output. You grant us only the rights needed to operate the Services (e.g. processing usage metadata).</p>

    <h2>6. DaVinci Resolve and third-party software</h2>
    <p>
        Assist integrates with DaVinci Resolve via local scripting. Blackmagic Design products are subject to their own licenses.
        We are not affiliated with or endorsed by Blackmagic Design. You are responsible for compliant use of Resolve and all plugins.
    </p>

    <h2>7. AI-assisted features</h2>
    <p>
        Automated cuts, transcriptions, and treatments are suggestions. You are solely responsible for reviewing timelines,
        clearing music and likeness rights, and delivering final work to clients. Outputs may be imperfect; always verify before delivery.
    </p>

    <h2>8. Disclaimers</h2>
    <p>
        THE SERVICES ARE PROVIDED “AS IS” AND “AS AVAILABLE” WITHOUT WARRANTIES OF ANY KIND, EXPRESS OR IMPLIED,
        INCLUDING MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT.
        WE DO NOT WARRANT UNINTERRUPTED OR ERROR-FREE OPERATION, OR THAT GENERATED TIMELINES WILL MEET EVERY CREATIVE EXPECTATION.
    </p>

    <h2>9. Limitation of liability</h2>
    <p>
        TO THE MAXIMUM EXTENT PERMITTED BY LAW, {{ strtoupper($company) }} AND {{ $owner }} SHALL NOT BE LIABLE FOR
        INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, OR LOSS OF PROFITS, DATA, OR GOODWILL,
        ARISING FROM YOUR USE OF THE SERVICES. OUR TOTAL LIABILITY FOR ANY CLAIM SHALL NOT EXCEED THE AMOUNT YOU PAID US
        IN THE TWELVE (12) MONTHS BEFORE THE CLAIM, OR ONE HUNDRED US DOLLARS (USD $100), WHICHEVER IS GREATER.
    </p>

    <h2>10. Indemnification</h2>
    <p>
        You agree to indemnify and hold harmless {{ $company }}, {{ $owner }}, and affiliates from claims arising out of
        your content, your breach of these Terms, or your violation of any law or third-party rights.
    </p>

    <h2>11. Termination</h2>
    <p>
        You may stop using the Services at any time. We may suspend or terminate access for violation of these Terms,
        non-payment, or risk to the platform. Upon termination, your license to the software ends; provisions that should survive will remain in effect.
    </p>

    <h2>12. Governing law</h2>
    <p>
        These Terms are governed by the laws of the Federal Republic of Nigeria, without regard to conflict-of-law principles,
        except where mandatory consumer protections in your country apply. Disputes shall be subject to the exclusive jurisdiction
        of courts in Nigeria, unless otherwise required by applicable law.
    </p>

    <h2>13. Changes</h2>
    <p>
        We may modify these Terms by posting an updated version on this page. Material changes will be indicated by updating the date above.
        Your continued use after changes constitutes acceptance.
    </p>

    <h2>14. Contact</h2>
    <p>
        <strong>{{ $company }}</strong><br>
        Legal: <a href="mailto:{{ $legal }}">{{ $legal }}</a><br>
        Support: <a href="mailto:{{ $support }}">{{ $support }}</a>
    </p>
    <p style="margin-top: 32px;">
        <a href="{{ route('assist.privacy') }}">Privacy policy</a>
        · <a href="{{ route('assist.docs') }}">Documentation</a>
    </p>
</article>
@endsection
