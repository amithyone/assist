@extends('layouts.assist')

@section('title', 'Documentation')

@section('content')
<div class="assist-docs-layout">
    <aside class="assist-docs-sidebar">
        <input type="search" class="assist-input" placeholder="Search docs..." style="margin-bottom: 24px;">
        <nav class="assist-docs-nav">
                <div>
                    <h4>Getting Started</h4>
                    <ul>
                        <li><a href="#">Installation</a></li>
                        <li><a href="#">Connecting Resolve</a></li>
                        <li><a href="#">First Project</a></li>
                        <li><a href="#">UI Overview</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Core Features</h4>
                    <ul>
                        <li><a href="#">AI Editor Engine</a></li>
                        <li><a href="#">Reels Cloner</a></li>
                        <li><a href="#">Beat Edit</a></li>
                        <li><a href="#">Transcriptions</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Workflows</h4>
                    <ul>
                        <li><a href="#">Wedding Highlights</a></li>
                        <li><a href="#">Documentary Assembly</a></li>
                        <li><a href="#">Social Ads</a></li>
                        <li><a href="#">Podcast Cuts</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Advanced</h4>
                    <ul>
                        <li><a href="#">Story Graph</a></li>
                        <li><a href="#">.assistproject Format</a></li>
                        <li><a href="#">Custom Templates</a></li>
                        <li><a href="#">API Access</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Troubleshooting</a></li>
                        <li><a href="#">System Requirements</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
        </nav>
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
</div>
@endsection
