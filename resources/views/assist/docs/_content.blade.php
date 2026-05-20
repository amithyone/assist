@php
    $support = config('assist.support_email', 'support@assist.app');
    $company = config('assist.company_name', 'Amithyone Media');
@endphp

<h2 id="overview">Overview</h2>
<p>
    Assist is a macOS companion for <strong>DaVinci Resolve</strong>. It builds first timelines — music video cuts,
    beat-synced montages, reel-style clones, and dialogue-led assemblies — so you can jump straight into creative refinement.
    Every workflow outputs a normal Resolve timeline you own completely.
</p>
<div class="assist-docs-callout">
    <strong>Creative control:</strong> Assist never locks you out of your edit. Think of it as a fast assistant for tedious assembly — not a replacement for your taste.
</div>

<h2 id="install">Installation</h2>
@php $installDownloads = app(\App\Services\AssistAppReleaseService::class)->availableDownloads(); @endphp
<ol>
    <li>
        @if (count($installDownloads) > 0)
            Download Assist from your <a href="{{ route('assist.dashboard') }}">dashboard</a> or the
            <a href="{{ route('assist.home') }}#download">homepage download section</a>:
            <ul style="margin-top: 8px;">
                @foreach ($installDownloads as $dl)
                    <li><a href="{{ $dl['url'] }}">{{ $dl['label'] }}</a>@if (!empty($dl['version'])) (v{{ $dl['version'] }})@endif</li>
                @endforeach
            </ul>
        @else
            When a build is published for your platform, download links appear on your <a href="{{ route('assist.dashboard') }}">dashboard</a> and the <a href="{{ route('assist.home') }}#download">homepage</a>. Assist is currently offered for <strong>Apple Silicon Mac (arm64)</strong>.
        @endif
    </li>
    <li>Unzip the archive and move <code>Assist_AI_Editor.app</code> to your <code>/Applications</code> folder.</li>
    <li>On first launch, macOS may ask you to allow the app — open <strong>System Settings → Privacy &amp; Security</strong> and approve if needed.</li>
    <li>Sign in with the same account you use on this website to sync plan limits.</li>
    <li>Before running any workflow, complete <a href="#resolve">Connecting Resolve</a> below.</li>
</ol>
<pre class="glass-panel">$ unzip Assist_macOS.zip
$ mv Assist_AI_Editor.app /Applications
$ open /Applications/Assist_AI_Editor.app</pre>

<h2 id="resolve">Connecting Resolve</h2>
<p>
    Assist talks to Resolve through <strong>local external scripting</strong>. No cloud bridge is required for timeline building —
    everything runs on your Mac between Assist and the Resolve instance you have open.
</p>

<h3>Before you start</h3>
<ul>
    <li><strong>DaVinci Resolve</strong> 18 or later (Studio or free) installed and licensed on the same Mac as Assist.</li>
    <li>A Resolve <strong>project open</strong> with at least one timeline or media in the Media Pool (empty projects can connect but have nothing to sync).</li>
    <li>Assist and Resolve both running — Assist detects the active project automatically.</li>
</ul>

<h3>Step 1 — Enable external scripting</h3>
<ol>
    <li>Open <strong>DaVinci Resolve</strong>.</li>
    <li>Open <strong>DaVinci Resolve → Preferences</strong> (macOS menu) or press <code>Cmd + ,</code>.</li>
    <li>Select <strong>System → General</strong> in the sidebar.</li>
    <li>Find <strong>External scripting using</strong> and set it to <code>Local</code> (not Disabled, not Network).</li>
    <li>Click <strong>Save</strong> and <strong>fully quit</strong> Resolve (<code>Cmd + Q</code>), then reopen it.</li>
</ol>
<div class="assist-docs-callout assist-docs-callout--warn">
    <strong>Important:</strong> Changing this setting requires a full restart of Resolve. Switching projects alone is not enough.
</div>

<h3>Step 2 — Open your edit project</h3>
<ol>
    <li>Create or open the project you want Assist to work in.</li>
    <li>Import performance takes, b-roll, and music into the Media Pool (organized bins help Music Video Cuts).</li>
    <li>Leave Resolve in the foreground at least once so scripting can attach to the active database.</li>
</ol>

<h3>Step 3 — Launch Assist and verify connection</h3>
<ol>
    <li>Open <strong>Assist</strong> from Applications.</li>
    <li>Sign in if prompted.</li>
    <li>Look for the <strong>connection indicator</strong> in the Assist sidebar — green means Resolve is linked to the active project.</li>
    <li>If yellow or red, see <a href="#troubleshooting">Troubleshooting</a> below.</li>
</ol>

<h3>What Assist can do once connected</h3>
<table class="assist-docs-table">
    <thead>
        <tr><th>Capability</th><th>Description</th></tr>
    </thead>
    <tbody>
        <tr><td>Read project</td><td>Media Pool clips, bins, timelines, frame rate, and project name</td></tr>
        <tr><td>Write timelines</td><td>Create or populate timelines for Music Video Cuts, Beat Edit, Reels, AI Editor</td></tr>
        <tr><td>Organize bins</td><td>Optional bin structure for takes and b-roll (workflow-dependent)</td></tr>
        <tr><td>Usage sync</td><td>Report automated run counts to your Assist account (plan limits)</td></tr>
    </tbody>
</table>

<h3>What Assist does not do</h3>
<ul>
    <li>It does not render final deliverables for you — export stays in Resolve.</li>
    <li>It does not replace color grading, audio mix, or VFX — you refine in Resolve as usual.</li>
    <li>It does not control Resolve over the network unless you explicitly enable network scripting (not recommended for Assist).</li>
    <li>It does not access projects when Resolve is quit — reopen Resolve first.</li>
</ul>

<h3>Resolve scripting checklist</h3>
<ul>
    <li>External scripting = <code>Local</code></li>
    <li>Resolve restarted after changing the setting</li>
    <li>Same macOS user session for both apps</li>
    <li>No blocking firewall rule on localhost (rare on Mac)</li>
    <li>Only one Resolve instance open</li>
</ul>

<h2 id="first-project">First project</h2>
<ol>
    <li>Connect Resolve (<a href="#resolve">above</a>).</li>
    <li>In Assist, pick a workflow — e.g. <strong>Music Video Cuts</strong> for performance + music timelines.</li>
    <li>Use <strong>Sync from Resolve</strong> to pull Media Pool metadata into Assist.</li>
    <li>Configure options (music track, take bins, cut style) and run <strong>Generate</strong>.</li>
    <li>Switch to Resolve — a new timeline appears. Trim, replace shots, and grade manually.</li>
</ol>

<h2 id="ui-overview">UI overview</h2>
<ul>
    <li><strong>Sidebar</strong> — connection status, account, and workflow picker.</li>
    <li><strong>Workflow panels</strong> — settings and generate actions per feature.</li>
    <li><strong>Activity / usage</strong> — plan limits and recent runs (when signed in).</li>
    <li><strong>Settings</strong> — account, updates, and API base URL (preconfigured in release builds).</li>
</ul>

<h2 id="account">Account &amp; plans</h2>
<p>
    Sign in from the desktop app with the same email and password as this site. Plans limit how many
    <strong>automated runs</strong> you can start per billing period — not how long you edit inside Resolve.
    Upgrade from <a href="{{ route('assist.pricing') }}">Pricing</a> or your dashboard.
</p>

<h2 id="music-video-cuts">Music Video Cuts</h2>
<p>Get a quick cut on a music video without staring at an empty timeline.</p>
<ol>
    <li>Open your music video project in Resolve and enable external scripting (<a href="#resolve">Local</a>).</li>
    <li>In Assist, open <strong>Music Video Cuts</strong> and sync media from the active project.</li>
    <li>Organize bins with performance takes (e.g. Full Take 1, Full Take 2) and b-roll where possible.</li>
    <li>Add your music track, then click <strong>Generate Music Video Cuts</strong>.</li>
    <li>Assist creates a multi-layer timeline in Resolve — refine cuts, swap shots, and grade as you normally would.</li>
</ol>

<h2 id="reels">Reels Cloner</h2>
<p>Drop a reference reel plus your footage. Assist analyzes pacing and builds a draft timeline you can push toward your own style.</p>
<ul>
    <li>Reference should be a short clip or reel you have rights to use as a pacing guide.</li>
    <li>Your footage should be in the active Resolve project before syncing.</li>
    <li>Review every cut — Assist suggests structure; you own creative choices.</li>
</ul>

<h2 id="beat-edit">Beat Edit</h2>
<p>Upload or select a music track; Assist detects transients and places cuts for montage-style edits. You keep performance and story choices.</p>

<h2 id="ai-editor">AI Editor</h2>
<p>Interview-led projects: transcribe clips, run analysis, then execute a narrative-first assembly. Review and reshape in Resolve.</p>

<h2 id="prepro">Preproduction</h2>
<p>
    Briefs, shot lists, story graphs, and AI treatments live in the Assist workspace (and .assistproject packages)
    so post-production starts with intent, not guesswork.
</p>

<h2 id="transcriptions">Transcriptions</h2>
<p>
    The AI Editor workflow can transcribe dialogue for search, selects, and assembly. Transcripts stay tied to your project context;
    review speaker labels and fix names before generating a story-led timeline.
</p>

<h2 id="workflow-wedding">Wedding highlights</h2>
<p>Use Beat Edit or Music Video Cuts with a licensed music bed; organize ceremony, reception, and couple moments in separate bins for cleaner sync.</p>

<h2 id="workflow-documentary">Documentary assembly</h2>
<p>Use AI Editor after transcription; mark strong sound bites in Assist or Resolve, then generate a first assembly and refine in the cut page.</p>

<h2 id="workflow-social">Social ads</h2>
<p>Reels Cloner plus short reference ads; export vertical timelines from Resolve after Assist builds the draft.</p>

<h2 id="workflow-podcast">Podcast cuts</h2>
<p>Transcribe episodes, remove filler in Assist-guided selects, and build a timeline for multicam or single-cam podcast video.</p>

<h2 id="story-graph">Story Graph</h2>
<p>Map acts, beats, and escalation in preproduction so automated edits respect story order — especially for AI Editor and hybrid music/performance pieces.</p>

<h2 id="assistproject">.assistproject format</h2>
<p>
    A portable package that carries briefs, shot lists, story graph nodes, treatments, and proxy references between prep and post.
    Export from Assist workspace; import when opening a new Resolve-linked session.
</p>

<h2 id="templates">Custom templates</h2>
<p>
    Save recurring bin naming, cut density, and music-video structure as templates (where enabled in your build).
    Templates speed up repeat clients and series work — they never override your manual trim in Resolve.
</p>

<h2 id="faq">FAQ</h2>
<h3>Does Assist work without internet?</h3>
<p>Resolve scripting and timeline building work offline. Sign-in and usage sync need internet periodically.</p>
<h3>Free Resolve vs Studio?</h3>
<p>Both support local scripting. Some advanced Resolve features (e.g. certain codecs) depend on your Resolve edition, not Assist.</p>
<h3>Will Assist delete my media?</h3>
<p>No. Assist adds timelines and may organize bins; it does not delete source files from disk.</p>

<h2 id="troubleshooting">Troubleshooting</h2>
<table class="assist-docs-table">
    <thead>
        <tr><th>Symptom</th><th>Fix</th></tr>
    </thead>
    <tbody>
        <tr><td>No green connection</td><td>Confirm scripting is <code>Local</code>, restart Resolve, reopen project</td></tr>
        <tr><td>Wrong project synced</td><td>Switch to the correct project in Resolve, then click Sync again in Assist</td></tr>
        <tr><td>Generate button disabled</td><td>Sign in, check plan usage, ensure media + music are selected</td></tr>
        <tr><td>Empty timeline after generate</td><td>Verify clips are in Media Pool, not only Finder — import to Resolve first</td></tr>
        <tr><td>Script timeout</td><td>Close other heavy apps; reduce open timelines; restart both apps</td></tr>
    </tbody>
</table>

<h2 id="requirements">System requirements</h2>
<ul>
    <li><strong>OS:</strong> macOS 12 Monterey or later (Apple Silicon and Intel)</li>
    <li><strong>Resolve:</strong> DaVinci Resolve 18+ with external scripting enabled</li>
    <li><strong>RAM:</strong> 16 GB+ recommended for 4K music video workflows</li>
    <li><strong>Disk:</strong> SSD recommended; proxy workflows supported via Resolve</li>
    <li><strong>Account:</strong> Assist account for plan sync (free tier available)</li>
</ul>

<h2 id="contact">Contact</h2>
<p>
    Documentation feedback or support:
    <a href="mailto:{{ $support }}">{{ $support }}</a>.
    Legal inquiries: <a href="mailto:{{ config('assist.legal_email', 'legal@amithyone.com') }}">{{ config('assist.legal_email', 'legal@amithyone.com') }}</a>.
    Operated by <strong>{{ $company }}</strong> — see <a href="{{ route('assist.privacy') }}">Privacy policy</a> and <a href="{{ route('assist.terms') }}">Terms of use</a>.
</p>

<h2 id="creative-control">Creative control</h2>
<p>
    Assist never locks you out of your edit. Every workflow creates a normal Resolve timeline you can trim, reorder, replace,
    and grade. Think of Assist as a fast assistant for the tedious assembly — not a replacement for your taste.
</p>
