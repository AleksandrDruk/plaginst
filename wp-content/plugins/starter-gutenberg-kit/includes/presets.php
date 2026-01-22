<?php

if (!defined('ABSPATH')) {
    exit;
}

function sgk_get_presets() {
    return array(
        'none' => array(
            'label' => 'No preset selected',
            'sections' => array(),
        ),
        'crypto-casino' => array(
            'label' => 'Crypto casino',
            'sections' => array(
                'H1',
                'Intro',
                'Top YYY Casinos',
                'How we review',
                'Best crypto casinos in Country 2026',
                'Pros and cons',
                'How does a crypto casino work?',
                'Is a Bitcoin casino secure?',
                'How do no-KYC casinos work?',
                'Bonus types',
                'Online casino games',
                'Crypto payment methods',
                'Responsible & safe gambling organizations',
            ),
        ),
        'bitcoin-casino' => array(
            'label' => 'Bitcoin casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best Bitcoin online casinos in Country 2026',
                'Bonuses available at Bitcoin casinos',
                'Casino games offered at a Bitcoin casino',
                'Sports betting at Bitcoin casinos',
                'Payment methods at Bitcoin crypto casinos',
                'Security at crypto casinos',
                'Bitcoin casinos on mobile',
            ),
        ),
        'payment-method-base' => array(
            'label' => 'Payment method pages — Base template',
            'sections' => array(
                'Top YYY Casinos',
                'How I test [PAYMENT METHOD] casinos',
                'How does [PAYMENT METHOD] work?',
                'Deposits and withdrawals in casinos that accept [PAYMENT METHOD]',
                '[PAYMENT METHOD] limits / terms & conditions',
                '[PAYMENT METHOD] pros and cons',
                'Alternative payment methods',
                'Responsible gambling / regulation in Country',
            ),
        ),
        'payment-method-a' => array(
            'label' => 'Payment method pages — Variant A',
            'sections' => array(
                'Top YYY Casinos',
                'How I test [PAYMENT METHOD] casinos',
                'Deposits and withdrawals in casinos that accept [PAYMENT METHOD]',
                '[PAYMENT METHOD] casino pros and cons',
                '[PAYMENT METHOD] terms and conditions',
                'How do [PAYMENT METHOD] online casinos work?',
                '[PAYMENT METHOD] payment method alternatives',
                'Responsible gambling / regulation in Country',
            ),
        ),
        'payment-method-b' => array(
            'label' => 'Payment method pages — Variant B',
            'sections' => array(
                'Top YYY Casinos',
                'How I review online casinos that accept [PAYMENT METHOD]',
                'Deposits and withdrawals in [PAYMENT METHOD] casinos',
                'Pros and cons',
                'Terms and conditions',
                'How to choose an online casino that accepts [PAYMENT METHOD]',
                '[PAYMENT METHOD] payment alternatives',
                'Responsible gambling / regulation in Country',
            ),
        ),
        'payment-method-c' => array(
            'label' => 'Payment method pages — Variant C',
            'sections' => array(
                'Top YYY Casinos',
                'How we rate online casinos',
                'How to use [PAYMENT METHOD] at an online casino',
                '[PAYMENT METHOD] online casino deposits and withdrawals',
                '[PAYMENT METHOD] pros and cons',
                'How to choose casinos that accept [PAYMENT METHOD]',
                'Alternative payment methods',
                'Responsible gambling / regulation in Country',
            ),
        ),
        'bonus-pages' => array(
            'label' => 'Bonus pages',
            'sections' => array(
                'Top YYY Casinos',
                'What is X bonus',
                'How we review',
                'Types of X bonus',
                'Pros and cons',
                'Terms and conditions',
                'How to claim X bonus',
            ),
        ),
        'deposit-pages' => array(
            'label' => 'Deposit pages',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'What is YYY',
                'Pros and cons',
                'Best bonuses with a $1 deposit',
                'Payment methods',
                'How to choose the best $1 deposit online casino',
            ),
        ),
        'best-online-casino' => array(
            'label' => 'Best online casino / real money casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best online casinos in Country 2026',
                'Pros and cons of playing for real money',
                'Casino types',
                'Gambling regulation in Country',
                'License types',
                'Bonus types',
                'Online casino games',
                'Slot providers',
                'Payment methods in Country',
                'Responsible & safe gambling organizations',
            ),
        ),
        'new-online-casino' => array(
            'label' => 'New online casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'New online casinos in Country 2026',
                'Pros and cons',
                'Casino types',
                'Bonus types',
                'Online casino games',
                'Gambling regulation in Country',
            ),
        ),
        'casino-without-registration' => array(
            'label' => 'Casino without registration / no registration',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'No-registration online casinos in Country 2026',
                'How do no-registration casinos work?',
                'How to deposit at a casino with no registration?',
                'How to withdraw from a casino with no registration?',
            ),
        ),
        'fast-withdrawal' => array(
            'label' => 'Fast withdrawal online casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Fastest withdrawal online casinos in Country 2026',
                'What makes fast payouts possible?',
                'How to maximize speed at instant payout casinos',
                'Payment options for fast-withdrawal casinos',
                'Pros and cons',
                'Security in fastest-withdrawal casinos',
            ),
        ),
        'top-payout' => array(
            'label' => 'Top payout online casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best payout online casinos in Country 2026',
                'Pros and cons',
                'Highest payout online slots',
                'What you should know about best payout casinos',
            ),
        ),
        'tax-free' => array(
            'label' => 'Tax-free casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best tax-free online casinos in Country 2026',
                'How does a tax-free casino work?',
                'Pros and cons',
            ),
        ),
        'safe-online-casino' => array(
            'label' => 'Safe online casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best safe online casinos in Country 2026',
                'What makes an online casino safe?',
                'How to find the safest casinos in Country',
                'Casino licenses',
                'Pros and cons',
            ),
        ),
        'live-casino' => array(
            'label' => 'Live casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best live online casinos in Country 2026',
                'How do live casinos work?',
                'Live casino games',
                'Pros and cons',
            ),
        ),
        'mobile-casino' => array(
            'label' => 'Mobile casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best mobile online casinos in Country 2026',
                'How do mobile casinos work?',
                'Mobile casino app ratings',
                'Mobile casino games',
                'Pros and cons',
            ),
        ),
        'vpn-casino' => array(
            'label' => 'VPN casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best VPN online casinos in Country 2026',
                'How do VPN casinos work?',
                'Pros and cons',
            ),
        ),
        'no-kyc' => array(
            'label' => 'No KYC / no verification casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best no-KYC online casinos in Country 2026',
                'No-KYC casinos explained',
                'Are no-verification casino sites secure?',
                'Pros and cons',
                'Why players choose no-KYC casinos',
                'Pros and cons of no-ID casinos',
                'No-KYC bonuses',
                'Online casino games at no-verification casinos',
                'Payment methods for no-verification casinos in Country',
            ),
        ),
        'no-limits' => array(
            'label' => 'Casino with no limits',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best no-limit casinos in Country 2026',
                'How does it work?',
                'Pros and cons',
            ),
        ),
        'licensed' => array(
            'label' => 'Licensed casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best licensed online casinos in Country 2026',
                'How does it work?',
                'Casino licenses',
                'Pros and cons',
            ),
        ),
        'mga' => array(
            'label' => 'MGA casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best MGA online casinos in Country 2026',
                'How does it work?',
                'Casino licenses',
                'Pros and cons',
            ),
        ),
        'curacao' => array(
            'label' => 'Curaçao casino',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best Curaçao online casinos in Country 2026',
                'How does it work?',
                'Casino licenses',
                'Pros and cons',
            ),
        ),
        'outside-regulator' => array(
            'label' => 'Local regulator pages: non-AAMS / without Swedish license / ohne OASIS / sans ARJEL',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Best “outside local regulator” online casinos in Country 2026',
                'What does “outside local regulator” mean in Country?',
                'Pros and cons',
                'Casino types',
                'New online casinos 2026',
                'Gambling regulation in Country',
                'Casino licenses',
                'Bonus types',
                'Online casino games',
                'Slot providers',
                'Payment methods in Country',
                'Responsible & safe gambling organizations',
            ),
        ),
        'best-bookmaker' => array(
            'label' => 'Best bookmaker online / bookmaker / betting',
            'sections' => array(
                'Top YYY Casinos',
                'How I choose betting sites',
                'Top betting sites in Country 2026',
                'Pros and cons',
                'Sports betting regulation in Country',
                'Bet types',
                'Most popular sports in Country',
                'Bonus types',
                'Responsible gambling',
            ),
        ),
        'betting-welcome-bonus' => array(
            'label' => 'Betting welcome bonus / bonus benvenuto',
            'sections' => array(
                'Top YYY Casinos',
                'How I choose a betting bonus',
                'Best welcome bonuses 2026 from bookmakers',
                'How does a welcome bonus work?',
                'Best betting bonuses: what to look for',
                'Different types of betting bonuses',
            ),
        ),
        'hors-arjel' => array(
            'label' => 'Bookmaker hors ARJEL',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Top bookmakers hors ARJEL in Country 2026',
                'Local gambling authority in Country: what it is',
                'Bookmaker hors ARJEL: what does it mean in Country?',
                'Is hors-ARJEL sports betting legal or illegal in Country?',
                'Sports betting regulation in Country',
                'Pros and cons',
                'Bonus types',
                'Responsible gambling',
            ),
        ),
        'poker-sites' => array(
            'label' => 'Best poker sites / poker rooms',
            'sections' => array(
                'Poker site reviews',
                'How I choose poker sites',
                'Types of poker',
                'How to start playing on a poker site',
                'Poker basics: key concepts',
                'Poker room licenses',
            ),
        ),
        'provider-page' => array(
            'label' => 'Provider page',
            'sections' => array(
                'How I test Provider casinos',
                'About Provider',
                'Provider games',
                'Popular Provider slots',
            ),
        ),
        'casino-games-category' => array(
            'label' => 'Casino games — category page',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Pros and cons of playing',
                'Types of online slots/games',
                'Top popular slots/games in Country',
                'Best payout slots/games',
            ),
        ),
        'individual-game' => array(
            'label' => 'Individual game page',
            'sections' => array(
                'Top YYY Casinos',
                'How we review',
                'Pros and cons',
                'About the game',
                'How to play',
                'Strategies',
                'Game variants',
            ),
        ),
        'brand-review-templates' => array(
            'label' => 'Brand review section templates',
            'sections' => array(
                'Brand unit: 7 paragraphs per brand + pros/cons + user quote + reputation mention',
                'Internal links insertion',
            ),
        ),
        'review-structure-examples' => array(
            'label' => 'Review structure examples you rotate across sites',
            'sections' => array(
                'Structure 1',
                'Structure 2',
                'Structure 3',
            ),
        ),
        'system-pages' => array(
            'label' => 'Simple site-wide system pages',
            'sections' => array(
                'Contacts',
                'About Us',
                'How we review (editorial guidelines)',
                'Author page (bio)',
                'Advertising Disclosure',
                'Responsible Gambling',
                'Objectivity Disclosure',
            ),
        ),
        'author-bio' => array(
            'label' => 'Author bio — section structure',
            'sections' => array(
                'Who the author is',
                'Experience',
                'Areas of expertise',
                'Year-by-year history',
                'Education',
                'How to contact / social',
                'Optional: courses / conferences / workplaces + external links',
            ),
        ),
        'about-us' => array(
            'label' => 'About Us — section structure',
            'sections' => array(
                'Vision & mission',
                'Your story',
                'Who you serve',
                'Services/benefits',
                'Social proof / trust factors / stats',
                'History map by dates',
                'Editorial policy',
                'Contact info + location',
                'Reviews/testimonials',
                'Team',
                'Optional: backlinks to official sources',
            ),
        ),
        'how-we-review-guidelines' => array(
            'label' => 'How we review — example editorial guidelines structure',
            'sections' => array(
                'Our review philosophy',
                'Step-by-step review process',
                'Registration & verification',
                'Deposits & withdrawals',
                'Bonuses & promotions',
                'Game selection & software',
                'User experience & design',
                'Security & licensing',
                'Customer support',
                'Scoring system',
                'Who reviews our casinos?',
                'Our promise to readers',
                'Updates & re-reviews',
                'Affiliate disclosure',
            ),
        ),
    );
}

function sgk_get_active_preset_key() {
    $presets = sgk_get_presets();
    $key = get_option('sgk_active_preset', 'none');
    $key = sanitize_key($key);
    if (!isset($presets[$key])) {
        return 'none';
    }
    return $key;
}

function sgk_get_preset_titles($preset_key, $presets = null) {
    if ($presets === null) {
        $presets = sgk_get_presets();
    }
    if (!isset($presets[$preset_key])) {
        return array();
    }
    return $presets[$preset_key]['sections'];
}

