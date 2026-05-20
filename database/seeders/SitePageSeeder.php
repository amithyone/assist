<?php

namespace Database\Seeders;

use App\Models\SitePage;
use App\Models\SitePageSection;
use Illuminate\Database\Seeder;

class SitePageSeeder extends Seeder
{
    public function run(): void
    {
        $siteUrl = rtrim(config('app.url', 'https://assist.amithyone.com'), '/');

        $this->seedHome($siteUrl);
        $this->seedSimplePage('pricing', 'Pricing', $siteUrl.'/pricing', [
            'eyebrow' => 'Pricing',
            'heading' => 'Simple, transparent plans.',
            'subheading' => 'Pay in Naira (Nigeria) or USD (international). Plans cap automated runs — not your creativity in Resolve.',
        ], [
            'meta_title' => 'Pricing — Assist | DaVinci Resolve AI Editor',
            'meta_description' => 'Free, Pro, and Unlimited plans for Music Video Cuts, Beat Edit, Reels Cloner, and AI Editor workflows in DaVinci Resolve.',
        ]);
        $this->seedSimplePage('docs', 'Documentation', $siteUrl.'/docs', [
            'eyebrow' => 'Docs',
            'heading' => 'Assist documentation',
            'subheading' => 'Install Assist, connect DaVinci Resolve, run workflows, and stay in creative control.',
            'body_html' => '<p>Assist builds first timelines inside Resolve — you refine every cut. Start with <a href="#resolve">Connecting Resolve</a>.</p>',
        ], [
            'meta_title' => 'Documentation — Assist | DaVinci Resolve',
            'meta_description' => 'Full Assist docs: Resolve local scripting, Music Video Cuts, Reels Cloner, Beat Edit, AI Editor, troubleshooting, and system requirements.',
        ]);
        $this->seedSimplePage('login', 'Log in', $siteUrl.'/login', [
            'heading' => 'Welcome back',
            'subheading' => 'Sign in to your Assist account',
        ], [
            'meta_title' => 'Log in — Assist',
            'meta_description' => 'Sign in to sync usage limits and manage your Assist plan for DaVinci Resolve.',
            'robots' => 'noindex,follow',
        ]);
        $this->seedSimplePage('register', 'Register', $siteUrl.'/register', [
            'heading' => 'Create your account',
            'subheading' => 'Start with Assist and DaVinci Resolve',
        ], [
            'meta_title' => 'Register — Assist | Free plan',
            'meta_description' => 'Create an Assist account for desktop sign-in, Music Video Cuts, and monthly editing workflows.',
            'robots' => 'noindex,follow',
        ]);
        $this->seedSimplePage('forgot_password', 'Forgot password', $siteUrl.'/forgot-password', [
            'heading' => 'Forgot password?',
            'subheading' => 'We will email you a reset link',
        ], [
            'meta_title' => 'Forgot password — Assist',
            'meta_description' => 'Reset your Assist account password.',
            'robots' => 'noindex,nofollow',
        ]);
        $this->seedSimplePage('reset_password', 'Reset password', $siteUrl.'/reset-password', [
            'heading' => 'Choose a new password',
            'subheading' => 'Enter your new password below',
        ], [
            'meta_title' => 'Reset password — Assist',
            'meta_description' => 'Set a new password for your Assist account.',
            'robots' => 'noindex,nofollow',
        ]);
    }

    protected function seedHome(string $siteUrl): void
    {
        $page = SitePage::updateOrCreate(
            ['slug' => 'home'],
            [
                'name' => 'Homepage',
                'is_published' => true,
                'meta_title' => 'Assist — AI Editor for DaVinci Resolve | Music Video Cuts',
                'meta_description' => 'Build first timelines in DaVinci Resolve: Music Video Cuts, beat-synced montages, reel clones, and dialogue assemblies. You keep creative control.',
                'meta_keywords' => 'DaVinci Resolve, video editor, music video cuts, beat edit, AI editing, post production',
                'og_title' => 'Assist — Enjoy the edit again',
                'og_description' => 'Music Video Cuts and story-first workflows inside DaVinci Resolve. We speed up the process; you own the creative call.',
                'og_image' => config('assist.default_og_image', 'assist/assist-logo.png'),
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'canonical_url' => $siteUrl.'/',
                'robots' => 'index,follow',
            ]
        );

        $sections = [
            'hero' => [
                'sort' => 1,
                'content' => [
                    'badge' => '✦ Music Video Cuts & story-first workflows',
                    'heading' => 'Love shooting. Enjoy the edit again.',
                    'lead' => 'Assist works inside DaVinci Resolve to build your first timeline — music video cuts, beat-synced montages, reel clones, and dialogue-led assemblies — so you stay in the director\'s chair. We speed up the process; you own the creative call.',
                    'cta_primary_label' => 'Download for Mac (Apple Silicon)',
                    'cta_secondary_label' => 'See how it works',
                    'cta_secondary_url' => '/docs',
                ],
                'image_alt' => 'Assist editor interface in DaVinci Resolve',
            ],
            'philosophy' => [
                'sort' => 2,
                'content' => [
                    'eyebrow' => 'How we think about editing',
                    'heading' => 'We are not here to edit for you.',
                    'lead' => 'Assist handles the repetitive lift — syncing bins, laying down a first pass, matching a reference reel, cutting on the beat — so you can spend your energy on pacing, story, and the choices only you can make.',
                    'pills' => ['You approve every timeline', 'Full control in Resolve', 'Refine, replace, or rebuild anytime', 'AI suggests — you decide'],
                ],
            ],
            'editing_engine' => [
                'sort' => 3,
                'content' => [
                    'eyebrow' => 'Intelligent Workflow',
                    'heading' => 'Powerful Editing Engine',
                    'lead' => 'Assist reads your rushes, understands project type, and delivers a real timeline in Resolve — not a locked export. Tweak every cut, swap clips, and rebuild layers whenever you want.',
                    'items' => [
                        ['icon' => '🎙', 'title' => 'Auto-Transcription', 'text' => 'Dialogue-aware Interview-Led edits in Resolve.'],
                        ['icon' => '🎬', 'title' => 'Music Video Cuts', 'text' => 'Multi-layer performance + b-roll timelines synced to your track.'],
                        ['icon' => '📁', 'title' => 'Smart Scene Detection', 'text' => 'Bins by project type — music video, wedding, doc, podcast, and more.'],
                    ],
                    'cta_label' => 'Execute First Assembly',
                    'project_types' => ['Music Video', 'Wedding', 'Documentary', 'Interview'],
                ],
            ],
            'features_intro' => [
                'sort' => 4,
                'content' => [
                    'eyebrow' => 'Features',
                    'heading' => 'A faster path to your first cut.',
                    'lead' => 'From music videos to documentaries, each tool gives you a strong starting timeline inside DaVinci Resolve — then you shape the final edit your way.',
                ],
            ],
            'feature_spotlight' => [
                'sort' => 5,
                'content' => [
                    'badge' => 'New',
                    'icon' => '🎬',
                    'heading' => 'Music Video Cuts',
                    'body' => 'Need a quick cut on a music video? Drop your performance takes and b-roll, add the track, and Assist builds a multi-layer timeline in Resolve — full takes on dedicated tracks, b-roll woven in, synced to the song. You still pick the hero moments, trim the fat, and grade the look. We just get you out of the blank timeline.',
                    'bullets' => [
                        'Reads bins like Full Take 1, Full Take 2, and b-roll pools',
                        'Aligns layers to your music track',
                        'One click to generate — unlimited refinement in Resolve',
                    ],
                ],
            ],
            'feature_reels' => [
                'sort' => 6,
                'content' => ['icon' => '📋', 'heading' => 'Reels Cloner', 'body' => 'Bring a reference reel and your footage. Assist studies rhythm and pacing, then maps your clips into a similar flow — a draft you can push, pull, and make yours.'],
            ],
            'feature_beat' => [
                'sort' => 7,
                'content' => ['icon' => '🎵', 'heading' => 'Beat Edit', 'body' => 'Montage energy without manual marker hell. Transients and musical phrasing drive cut points so you can focus on performance and story beats instead of frame-by-frame clicking.'],
            ],
            'feature_ai' => [
                'sort' => 8,
                'content' => ['icon' => '🎙', 'heading' => 'AI Editor', 'body' => 'Interview-led and dialogue-driven projects: transcribe, analyze, and assemble a narrative-first timeline. Assist proposes structure; you keep the director\'s cut.'],
            ],
            'feature_prepro' => [
                'sort' => 9,
                'content' => ['icon' => '📝', 'heading' => 'Preproduction Workspace', 'body' => 'Briefs, shot lists, story graphs, and treatment ideas in one place — so when you hit post, the creative intent is already clear.'],
            ],
            'feature_transcription' => [
                'sort' => 10,
                'content' => ['icon' => '💬', 'heading' => 'Transcription', 'body' => 'Clip-level transcripts power smarter dialogue edits and searchable rushes. Less scrubbing, more crafting.'],
            ],
            'workspace' => [
                'sort' => 11,
                'content' => [
                    'eyebrow' => 'Preparation',
                    'heading' => 'Preproduction Workspace',
                    'lead' => 'Plan the story before the shoot. Your brief and shot list travel with the project so post-production starts with intent, not guesswork.',
                ],
            ],
            'interoperability' => [
                'sort' => 12,
                'content' => [
                    'eyebrow' => 'Interoperability',
                    'heading' => 'The .assistproject Package.',
                    'lead' => 'Carry briefs, shot lists, and story graphs from prep into post — so automated timelines respect the story you planned.',
                    'bridge_title' => 'Bridge to Director',
                    'bridge_text' => 'Compile your preproduction graph into the edit engine.',
                ],
            ],
        ];

        foreach ($sections as $key => $data) {
            SitePageSection::updateOrCreate(
                ['site_page_id' => $page->id, 'section_key' => $key],
                [
                    'sort_order' => $data['sort'],
                    'content' => $data['content'],
                    'image_alt' => $data['image_alt'] ?? null,
                ]
            );
        }
    }

    /**
     * @param  array<string, string>  $intro
     * @param  array<string, string>  $seo
     */
    protected function seedSimplePage(string $slug, string $name, string $canonical, array $intro, array $seo): void
    {
        SitePage::updateOrCreate(
            ['slug' => $slug],
            array_merge([
                'name' => $name,
                'is_published' => true,
                'intro' => $intro,
                'canonical_url' => $canonical,
                'og_image' => config('assist.default_og_image', 'assist/assist-logo.png'),
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'robots' => 'index,follow',
            ], $seo)
        );
    }
}
