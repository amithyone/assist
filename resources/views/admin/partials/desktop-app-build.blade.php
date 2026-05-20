<div class="assist-admin-card glass-panel">
    <h2>Desktop app build</h2>
    <p class="assist-text-muted mb-4">
        When you run <code>./build_dmg.sh</code> in the Assist desktop repo, copy these values into
        <code>build.env</code> so sign-in and subscription limits use this site.
    </p>
    <pre class="glass-panel" style="padding: 16px; border-radius: 12px; font-size: 12px; overflow-x: auto;">ASSIST_API_BASE_URL={{ rtrim($app['url'] ?? config('app.url'), '/') }}
ASSIST_APP_KEY={{ $app['assist_app_key_plain'] ? '(set in .env — copy from server or regenerate in form above)' : '(save an app key above first)' }}
ASSIST_UPGRADE_URL={{ rtrim($app['url'] ?? config('app.url'), '/') }}/pricing
ASSIST_DEV_AUTH=0</pre>
    <p class="assist-text-muted" style="font-size: 13px; margin-top: 12px;">
        API ping: <code>GET {{ rtrim($app['url'] ?? config('app.url'), '/') }}/api/assist/ping</code> —
        desktop login: <code>POST /api/assist/login</code> with header <code>X-Assist-App-Key</code>.
    </p>
</div>
