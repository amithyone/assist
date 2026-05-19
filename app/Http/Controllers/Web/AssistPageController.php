<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\View\View;

class AssistPageController extends Controller
{
    public function home(): View
    {
        return view('assist.home');
    }

    public function pricing(): View
    {
        $dbPlans = Plan::where('is_active', true)->orderBy('id')->get()->keyBy('slug');

        return view('assist.pricing', [
            'cards' => $this->pricingCards($dbPlans),
        ]);
    }

    public function docs(): View
    {
        return view('assist.docs');
    }

    /**
     * @param  \Illuminate\Support\Collection<string, \App\Models\Plan>  $dbPlans
     * @return list<array<string, mixed>>
     */
    protected function pricingCards($dbPlans): array
    {
        $definitions = [
            'free' => [
                'slug' => 'free',
                'name' => 'Solo',
                'price' => 'Free',
                'period' => null,
                'description' => 'Perfect for independent creators starting out.',
                'features' => [
                    '3 Timelines / month',
                    'Standard Transcription',
                    'Manual Beat Edit',
                    'Basic Story Graph',
                    'Community Support',
                ],
                'highlight' => false,
                'badge' => null,
            ],
            'pro' => [
                'slug' => 'pro',
                'name' => 'Pro',
                'price' => '$29',
                'period' => '/mo',
                'description' => 'For professional editors who need more power.',
                'features' => [
                    'Unlimited Timelines',
                    'Whisper High-Quality Transcription',
                    'Full Reels Cloner',
                    'Advanced AI Story Analysis',
                    'Priority Email Support',
                ],
                'highlight' => true,
                'badge' => 'Most Popular',
            ],
        ];

        $cards = [];

        foreach (['free', 'pro'] as $slug) {
            $card = $definitions[$slug];
            if ($dbPlans->has($slug)) {
                $card['name'] = $dbPlans[$slug]->name === 'Free' ? 'Solo' : $dbPlans[$slug]->name;
            }
            $card['cta_url'] = route('assist.register');
            $card['cta_label'] = 'Get Started';
            $cards[] = $card;
        }

        $cards[] = [
            'slug' => 'studio',
            'name' => 'Studio',
            'price' => '$99',
            'period' => '/mo',
            'description' => 'Enterprise features for production houses.',
            'features' => [
                'Multi-user Workspace',
                'External API Integrations',
                'Custom Project Templates',
                'Dedicated Success Manager',
                'Studio Mode (Local Host)',
            ],
            'highlight' => false,
            'badge' => null,
            'cta_url' => 'mailto:'.config('assist.support_email', 'support@assist.app'),
            'cta_label' => 'Contact Sales',
        ];

        return $cards;
    }
}
