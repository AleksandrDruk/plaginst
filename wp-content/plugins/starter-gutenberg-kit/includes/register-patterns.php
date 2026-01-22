<?php

if (!defined('ABSPATH')) {
    exit;
}

function sgk_register_patterns() {
    if (!function_exists('register_block_pattern')) {
        return;
    }

    $presets = sgk_get_presets();
    if (empty($presets) || (count($presets) === 1 && isset($presets['none']))) {
        return;
    }
    $active_preset = sgk_get_active_preset_key();
    $allowed_titles = in_array($active_preset, array('all', 'none'), true) ? array() : sgk_get_preset_titles($active_preset, $presets);

    if (function_exists('register_block_pattern_category')) {
        $base_label = __('SGK Sections', 'starter-gutenberg-kit');
        if ($active_preset !== 'none' && isset($presets[$active_preset])) {
            $base_label .= ' — ' . $presets[$active_preset]['label'];
        }
        register_block_pattern_category(
            'sgk-sections',
            array(
                'label' => $base_label,
            )
        );
    }

    $patterns = sgk_collect_patterns_from_files($active_preset);
    $patterns = sgk_add_generated_section_patterns($patterns, $presets, $active_preset);

    foreach ($patterns as $pattern) {
        if (empty($pattern['name']) || empty($pattern['title']) || empty($pattern['content'])) {
            continue;
        }
        if (!empty($allowed_titles) && !in_array($pattern['title'], $allowed_titles, true)) {
            continue;
        }
        if (empty($pattern['categories'])) {
            $pattern['categories'] = array('sgk-sections');
        }
        $pattern = sgk_add_shared_section_class_to_pattern($pattern);
        if (!empty($pattern['content'])) {
            $pattern['content'] = sgk_add_shared_block_classes_to_pattern_content($pattern['content']);
            $pattern['content'] = sgk_add_site_token_to_pattern_content($pattern['content']);
        }
        register_block_pattern($pattern['name'], $pattern);
    }
}

function sgk_collect_patterns_from_files($active_preset = 'none') {
    if (!in_array($active_preset, array('all', 'none'), true)) {
        return array();
    }
    $patterns = array();
    $pattern_files = glob(SGK_PATH . 'patterns/*.php');
    if (empty($pattern_files)) {
        return $patterns;
    }

    foreach ($pattern_files as $pattern_file) {
        $pattern = include $pattern_file;
        if (!is_array($pattern)) {
            continue;
        }
        if (!in_array($active_preset, array('all', 'none'), true) && !empty($pattern['title'])) {
            $dynamic_titles = array(
                'How we review',
                'Pros and cons',
                'Alternative payment methods',
                'Payment alternatives',
            );
            if (in_array($pattern['title'], $dynamic_titles, true)) {
                continue;
            }
        }
        $patterns[] = $pattern;
    }

    return $patterns;
}

function sgk_add_generated_section_patterns($patterns, $presets, $active_preset = 'none') {
    $existing_titles = array();
    foreach ($patterns as $pattern) {
        if (!empty($pattern['title'])) {
            $existing_titles[$pattern['title']] = true;
        }
    }

    $all_titles = array();
    if (!in_array($active_preset, array('all', 'none'), true) && isset($presets[$active_preset])) {
        foreach ($presets[$active_preset]['sections'] as $title) {
            $all_titles[$title] = true;
        }
    } else {
        foreach ($presets as $preset) {
            foreach ($preset['sections'] as $title) {
                $all_titles[$title] = true;
            }
        }
    }

    foreach (array_keys($all_titles) as $title) {
        if (isset($existing_titles[$title])) {
            continue;
        }
        $slug = sgk_pattern_slug_from_title($title);
        $section_class = 'sec-' . $slug . ' sec-variant-a';
        $content = sgk_build_basic_section_pattern($title, $section_class, $active_preset);
        $patterns[] = array(
            'name'       => 'starter-gutenberg-kit/' . $slug,
            'title'      => $title,
            'categories' => array('sgk-sections'),
            'content'    => $content,
        );
    }

    return $patterns;
}

function sgk_pattern_slug_from_title($title) {
    $slug = sanitize_title($title);
    if (empty($slug)) {
        $slug = 'section';
    }
    return $slug;
}

function sgk_build_basic_section_pattern($title, $section_class, $active_preset = 'none') {
    return sgk_build_section_pattern_template($title, $section_class, $active_preset);
}

function sgk_build_section_pattern_template($title, $section_class, $active_preset = 'none') {
    $shared_class = sgk_get_shared_section_class($title);
    if (!empty($shared_class) && strpos($section_class, $shared_class) === false) {
        $section_class .= ' ' . $shared_class;
    }

    $safe_title = esc_html($title);
    $lower_title = strtolower($title);

    if ($title === 'H1 + Intro') {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":1} -->
    <h1>Page headline</h1>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Intro paragraph that frames the topic and scope.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Second paragraph with key takeaways or context.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if ($title === 'H1') {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":1} -->
    <h1>Page headline</h1>
    <!-- /wp:heading -->
</div>
<!-- /wp:group -->
HTML;
    }

    if ($title === 'Intro') {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:paragraph -->
    <p>Intro paragraph that frames the topic and scope.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Second paragraph with key takeaways or context.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (strpos($lower_title, 'contacts') !== false
        || strpos($lower_title, 'about us') !== false
        || strpos($lower_title, 'author page') !== false
        || strpos($lower_title, 'advertising disclosure') !== false
        || strpos($lower_title, 'responsible gambling') !== false
        || strpos($lower_title, 'objectivity disclosure') !== false
        || strpos($lower_title, 'editorial guidelines') !== false
    ) {
        $para_count = (strpos($lower_title, 'about us') !== false || strpos($lower_title, 'responsible gambling') !== false || strpos($lower_title, 'editorial guidelines') !== false) ? 3 : 2;
        $paragraphs = '';
        for ($i = 0; $i < $para_count; $i++) {
            $paragraphs .= "\n    <!-- wp:paragraph -->\n    <p>Paragraph content.</p>\n    <!-- /wp:paragraph -->";
        }
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":1} -->
    <h1>{$safe_title}</h1>
    <!-- /wp:heading -->{$paragraphs}
</div>
<!-- /wp:group -->
HTML;
    }

    $h2_paragraph_titles = array(
        'Who the author is',
        'Experience',
        'Areas of expertise',
        'Year-by-year history',
        'Education',
        'How to contact / social',
        'Optional: courses / conferences / workplaces + external links',
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
    );
    if (in_array($title, $h2_paragraph_titles, true)) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Paragraph content.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if ($title === 'How we review' && in_array($active_preset, array('crypto-casino', 'bitcoin-casino'), true)) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Explain the review process for crypto casinos.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Describe how you verify licensing, fairness, and payouts.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($title, 'Top YYY Casinos') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Summarize the selection logic and what qualifies a casino for the top list.</p>
    <!-- /wp:paragraph -->

    <!-- wp:code -->
    <pre class="wp-block-code"><code>[casino_toplist country="Country" year="2026"]</code></pre>
    <!-- /wp:code -->

    <!-- wp:paragraph -->
    <p>After the list, add a short recap explaining who the top options fit best.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (preg_match('/best .* in country 2026/i', $title) || preg_match('/top .* in country 2026/i', $title) || stripos($title, 'Poker site reviews') !== false) {
        $intro = $title === 'Poker site reviews'
            ? 'Summarize the poker site selection and the main criteria.'
            : 'Introduce the list and explain the review scope.';
        $heading = $title === 'Poker site reviews' ? 'Poker site reviews' : $safe_title;
        $two_paragraph_titles = array(
            'Best Bitcoin online casinos in Country 2026',
            'Best MGA online casinos in Country 2026',
            'Best Curaçao online casinos in Country 2026',
        );
        $paragraphs = in_array($title, $two_paragraph_titles, true)
            ? "\n        <!-- wp:paragraph -->\n        <p>Brand summary paragraph with key highlights.</p>\n        <!-- /wp:paragraph -->\n\n        <!-- wp:paragraph -->\n        <p>Second paragraph with details or positioning.</p>\n        <!-- /wp:paragraph -->"
            : "\n        <!-- wp:paragraph -->\n        <p>Brand summary paragraph with key highlights.</p>\n        <!-- /wp:paragraph -->\n\n        <!-- wp:paragraph -->\n        <p>Second paragraph with details or positioning.</p>\n        <!-- /wp:paragraph -->\n\n        <!-- wp:paragraph -->\n        <p>Third paragraph with comparisons or context.</p>\n        <!-- /wp:paragraph -->";
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$heading}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>{$intro}</p>
    <!-- /wp:paragraph -->

    <!-- wp:group {"className":"is-style-sgk-card-a","layout":{"type":"constrained"}} -->
    <div class="wp-block-group is-style-sgk-card-a">
        <!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
        <figure class="wp-block-image size-large"><img src="https://via.placeholder.com/720x360" alt=""/></figure>
        <!-- /wp:image -->

        <!-- wp:heading {"level":3} -->
        <h3>Brand review 1</h3>
        <!-- /wp:heading -->

        {$paragraphs}

        <!-- wp:table {"className":"is-style-sgk-table-a"} -->
        <figure class="wp-block-table is-style-sgk-table-a"><table>
            <tbody>
                <tr>
                    <td>License</td>
                    <td>Example license</td>
                </tr>
                <tr>
                    <td>Bonus</td>
                    <td>Example offer</td>
                </tr>
                <tr>
                    <td>Withdrawal speed</td>
                    <td>0–24h</td>
                </tr>
            </tbody>
        </table></figure>
        <!-- /wp:table -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML;
    }

    if ($title === 'Brand review') {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":3} -->
    <h3>Brand name</h3>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Paragraph one.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph two.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph three.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph four.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph five.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph six.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph seven.</p>
    <!-- /wp:paragraph -->

    <!-- wp:heading {"level":3} -->
    <h3>Pros</h3>
    <!-- /wp:heading -->
    <!-- wp:list -->
    <ul>
        <li>Example pro.</li>
        <li>Example pro.</li>
        <li>Example pro.</li>
    </ul>
    <!-- /wp:list -->

    <!-- wp:heading {"level":3} -->
    <h3>Cons</h3>
    <!-- /wp:heading -->
    <!-- wp:list -->
    <ul>
        <li>Example con.</li>
        <li>Example con.</li>
        <li>Example con.</li>
    </ul>
    <!-- /wp:list -->

    <!-- wp:paragraph -->
    <p>User review quote.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Reputation mention (AskGamblers / Trustpilot / Casino.Guru).</p>
    <!-- /wp:paragraph -->

    <!-- wp:list -->
    <ul>
        <li>Internal link 1</li>
        <li>Internal link 2</li>
        <li>Internal link 3</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'brand unit') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":3} -->
    <h3>[BRAND NAME]</h3>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Paragraph one.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph two.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph three.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph four.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph five.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph six.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph seven.</p>
    <!-- /wp:paragraph -->

    <!-- wp:list {"className":"sgk-list sgk-list--pros"} -->
    <ul class="sgk-list sgk-list--pros">
        <li>Example pro.</li>
        <li>Example pro.</li>
        <li>Example pro.</li>
    </ul>
    <!-- /wp:list -->

    <!-- wp:list {"className":"sgk-list sgk-list--cons"} -->
    <ul class="sgk-list sgk-list--cons">
        <li>Example con.</li>
        <li>Example con.</li>
        <li>Example con.</li>
    </ul>
    <!-- /wp:list -->

    <!-- wp:paragraph -->
    <p>User review quote.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Reputation mention (AskGamblers / Trustpilot / Casino.Guru).</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'internal links insertion') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:list {"className":"sgk-list"} -->
    <ul class="sgk-list">
        <li>Internal link 1</li>
        <li>Internal link 2</li>
        <li>Internal link 3</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if ($title === 'Structure 1') {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:paragraph -->
    <p>Paragraph one.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph two.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph three.</p>
    <!-- /wp:paragraph -->
    <!-- wp:table {"className":"sgk-table"} -->
    <figure class="wp-block-table sgk-table"><table>
        <tbody>
            <tr>
                <td>Row label</td>
                <td>Value</td>
            </tr>
            <tr>
                <td>Row label</td>
                <td>Value</td>
            </tr>
        </tbody>
    </table></figure>
    <!-- /wp:table -->
    <!-- wp:paragraph -->
    <p>Final paragraph.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if ($title === 'Structure 2') {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:paragraph -->
    <p>Paragraph one.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph two.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph three.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph four.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph five.</p>
    <!-- /wp:paragraph -->

    <!-- wp:list {"className":"sgk-list sgk-list--pros"} -->
    <ul class="sgk-list sgk-list--pros">
        <li>Example pro.</li>
        <li>Example pro.</li>
    </ul>
    <!-- /wp:list -->
    <!-- wp:list {"className":"sgk-list sgk-list--cons"} -->
    <ul class="sgk-list sgk-list--cons">
        <li>Example con.</li>
        <li>Example con.</li>
    </ul>
    <!-- /wp:list -->

    <!-- wp:paragraph -->
    <p>Player quote.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if ($title === 'Structure 3') {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:paragraph -->
    <p>Paragraph one.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph two.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph three.</p>
    <!-- /wp:paragraph -->
    <!-- wp:table {"className":"sgk-table"} -->
    <figure class="wp-block-table sgk-table"><table>
        <tbody>
            <tr>
                <td>Parameter</td>
                <td>Value</td>
            </tr>
            <tr>
                <td>Parameter</td>
                <td>Value</td>
            </tr>
        </tbody>
    </table></figure>
    <!-- /wp:table -->
    <!-- wp:list {"className":"sgk-list"} -->
    <ul class="sgk-list">
        <li>Payment method one.</li>
        <li>Payment method two.</li>
        <li>Payment method three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (preg_match('/^Brand review\s+\d+/i', $title)) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
    <figure class="wp-block-image size-large"><img src="https://via.placeholder.com/720x360" alt=""/></figure>
    <!-- /wp:image -->

    <!-- wp:heading {"level":3} -->
    <h3>{$safe_title}</h3>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Brand summary paragraph with key highlights.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Second paragraph with details or positioning.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Third paragraph with comparisons or context.</p>
    <!-- /wp:paragraph -->

    <!-- wp:table {"className":"is-style-sgk-table-a"} -->
    <figure class="wp-block-table is-style-sgk-table-a"><table>
        <tbody>
            <tr>
                <td>License</td>
                <td>Example license</td>
            </tr>
            <tr>
                <td>Bonus</td>
                <td>Example offer</td>
            </tr>
            <tr>
                <td>Withdrawal speed</td>
                <td>0–24h</td>
            </tr>
        </tbody>
    </table></figure>
    <!-- /wp:table -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($title, 'Pros and cons of playing') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Short intro to the pros list.</p>
    <!-- /wp:paragraph -->
    <!-- wp:list {"className":"sgk-list sgk-list--pros"} -->
    <ul class="sgk-list sgk-list--pros">
        <li>Example pro.</li>
        <li>Example pro.</li>
        <li>Example pro.</li>
    </ul>
    <!-- /wp:list -->

    <!-- wp:paragraph -->
    <p>Short intro to the cons list.</p>
    <!-- /wp:paragraph -->
    <!-- wp:list {"className":"sgk-list sgk-list--cons"} -->
    <ul class="sgk-list sgk-list--cons">
        <li>Example con.</li>
        <li>Example con.</li>
        <li>Example con.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($title, 'Pros and cons') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:html -->
    <div class="pros-cons__tables">
      <div class="pros-cons__table pros-cons__table--pro">
        <div class="pros-cons__table-head">Pros</div>
        <ul>
          <li>Example pro item.</li>
          <li>Example pro item.</li>
          <li>Example pro item.</li>
        </ul>
      </div>

      <div class="pros-cons__table pros-cons__table--con">
        <div class="pros-cons__table-head">Cons</div>
        <ul>
          <li>Example con item.</li>
          <li>Example con item.</li>
          <li>Example con item.</li>
        </ul>
      </div>
    </div>
    <!-- /wp:html -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'how i test') !== false || stripos($lower_title, 'how i choose') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Introduce the testing criteria and evaluation flow.</p>
    <!-- /wp:paragraph -->

    <!-- wp:list -->
    <ul>
        <li>Criterion one with a short explanation.</li>
        <li>Criterion two with a short explanation.</li>
        <li>Criterion three with a short explanation.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'how i review online casinos that accept') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Paragraph one describing the review framework.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Paragraph two covering verification and safety checks.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'how we rate online casinos') !== false || stripos($lower_title, 'how we rate casinos') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Paragraph one describing the rating model.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Paragraph two explaining how scores are validated.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'how we review bonuses') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Explain the bonus evaluation criteria.</p>
    <!-- /wp:paragraph -->

    <!-- wp:list -->
    <ul>
        <li>Wagering requirements and time limits.</li>
        <li>Eligible games and max bet rules.</li>
        <li>Cashout and withdrawal restrictions.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if ($title === 'How we review') {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Introduce the review criteria and testing process.</p>
    <!-- /wp:paragraph -->

    <!-- wp:list -->
    <ul>
        <li>Criterion one with a short explanation.</li>
        <li>Criterion two with a short explanation.</li>
        <li>Criterion three with a short explanation.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'how to choose casinos that accept') !== false || stripos($lower_title, 'how to choose the best') !== false || stripos($lower_title, 'how to choose an online casino') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Short paragraph explaining what to prioritize.</p>
    <!-- /wp:paragraph -->
    <!-- wp:list -->
    <ul>
        <li>Factor one.</li>
        <li>Factor two.</li>
        <li>Factor three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'how to choose') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:list -->
    <ul>
        <li>Factor one.</li>
        <li>Factor two.</li>
        <li>Factor three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'how to claim') !== false || stripos($lower_title, 'how to play') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Short intro paragraph for the steps below.</p>
    <!-- /wp:paragraph -->

    <!-- wp:list {"className":"sgk-list"} -->
    <ul class="sgk-list">
        <li>Step one.</li>
        <li>Step two.</li>
        <li>Step three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'how to deposit') !== false || stripos($lower_title, 'how to withdraw') !== false || stripos($lower_title, 'how to start playing') !== false || stripos($lower_title, 'how to maximize') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>First explanatory paragraph.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Second explanatory paragraph.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'strategies') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>Short paragraph introducing the strategies list.</p>
    <!-- /wp:paragraph -->
    <!-- wp:list -->
    <ul>
        <li>Strategy one.</li>
        <li>Strategy two.</li>
        <li>Strategy three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'payment alternatives') !== false || stripos($lower_title, 'alternative payment methods') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:html -->
    <section class="alt-payments">
      <div class="alt-payments__head">
        <h2>Alternative Zahlungsmethoden zu Trustly</h2>
        <p>
          Für dich als Spieler ist es sinnvoll, Alternativen parat zu haben,
          falls Trustly im casino nicht verfügbar ist oder du beim Zahlungsweg
          lieber anders trennst. Entscheidend ist dabei weniger „was ist am
          beliebtesten“, sondern welche Methode zu deinem Ziel passt:
          schneller Start, klare Kontrolle, oder ein bewusst separiertes
          Budget.
        </p>
      </div>

      <div class="alt-payments__list">
        <article class="alt-payment">
          <div class="alt-payment__badge">
            <span class="alt-payment__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <rect x="3" y="6" width="18" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.6" />
                <path d="M7 10h10M7 14h6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              </svg>
            </span>
            Visa
          </div>
          <p>meist gut verfügbar, aber Auszahlungen laufen nicht immer identisch zur Einzahlung</p>
        </article>

        <article class="alt-payment">
          <div class="alt-payment__badge">
            <span class="alt-payment__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <rect x="3" y="6" width="18" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.6" />
                <rect x="6.5" y="8.5" width="3.5" height="3.5" fill="currentColor" />
                <path d="M12 12.5h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              </svg>
            </span>
            Mastercard
          </div>
          <p>ähnlich verbreitet, Bedingungen unterscheiden sich stark je Anbieter</p>
        </article>

        <article class="alt-payment">
          <div class="alt-payment__badge">
            <span class="alt-payment__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <path d="M12 4a8 8 0 100 16 8 8 0 000-16z" fill="none" stroke="currentColor" stroke-width="1.6" />
                <path d="M12 8v4l3 2" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              </svg>
            </span>
            Skrill
          </div>
          <p>separates Guthaben, oft praktisch für klare Budget-Kontrolle</p>
        </article>

        <article class="alt-payment">
          <div class="alt-payment__badge">
            <span class="alt-payment__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <path d="M6 9a6 6 0 0112 0v6a3 3 0 01-3 3H9a3 3 0 01-3-3V9z" fill="none" stroke="currentColor" stroke-width="1.6" />
                <path d="M9 12h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              </svg>
            </span>
            Neteller
          </div>
          <p>Wallet-Alternative mit Fokus auf schnelle interne Transfers</p>
        </article>

        <article class="alt-payment">
          <div class="alt-payment__badge">
            <span class="alt-payment__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <rect x="5" y="4" width="14" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="1.6" />
                <path d="M8 9h8M8 13h5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              </svg>
            </span>
            Paysafecard
          </div>
          <p>gut, wenn du ohne Bankzugriff einzahlen willst; Auszahlungen brauchen meist einen anderen Weg</p>
        </article>

        <article class="alt-payment">
          <div class="alt-payment__badge">
            <span class="alt-payment__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24">
                <rect x="3" y="6" width="18" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.6" />
                <path d="M7 10h10M7 14h6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
              </svg>
            </span>
            SEPA-Lastschrift
          </div>
          <p>planbar und vertraut, dafür oft prozesslastiger als Online Banking</p>
        </article>
      </div>

      <div class="alt-payments__tip">
        <p>
          Wenn du die Optionen gegeneinander abwägst, achte immer darauf, dass
          Einzahlung und Auszahlung für dich logisch zusammenpassen. So
          vermeidest du Umwege, selbst wenn du Trustly gerade nicht nutzen
          kannst.
        </p>
      </div>
    </section>
    <!-- /wp:html -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'bonus types') !== false || stripos($lower_title, 'bonuses available') !== false || stripos($lower_title, 'casino games') !== false || stripos($lower_title, 'sports betting') !== false || stripos($lower_title, 'security') !== false || stripos($lower_title, 'casino types') !== false || stripos($lower_title, 'gambling regulation') !== false || stripos($lower_title, 'license types') !== false || stripos($lower_title, 'online casino games') !== false || stripos($lower_title, 'slot providers') !== false || stripos($lower_title, 'payment options') !== false || stripos($lower_title, 'highest payout') !== false || stripos($lower_title, 'what you should know') !== false || stripos($lower_title, 'what makes') !== false || stripos($lower_title, 'how to find') !== false || stripos($lower_title, 'casino licenses') !== false || stripos($lower_title, 'live casino games') !== false || stripos($lower_title, 'mobile casino app ratings') !== false || stripos($lower_title, 'mobile casino games') !== false || stripos($lower_title, 'game variants') !== false || stripos($lower_title, 'poker room licenses') !== false || stripos($lower_title, 'poker basics') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>First paragraph covering the topic.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Second paragraph with details or examples.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'alternative payment methods') !== false || stripos($lower_title, 'payment alternatives') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->
    <!-- wp:list -->
    <ul>
        <li>Item one.</li>
        <li>Item two.</li>
        <li>Item three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'bitcoin casinos on mobile') !== false || stripos($lower_title, 'mobile bitcoin casinos') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>First paragraph covering mobile compatibility and UX.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Second paragraph with device and performance notes.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'alternative payment methods') !== false || stripos($lower_title, 'payment alternatives') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:list {"className":"sgk-list"} -->
    <ul class="sgk-list">
        <li>Item one.</li>
        <li>Item two.</li>
        <li>Item three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'types of x bonus') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Short intro paragraph.</p>
    <!-- /wp:paragraph -->

    <!-- wp:list {"className":"sgk-list"} -->
    <ul class="sgk-list">
        <li>Item one.</li>
        <li>Item two.</li>
        <li>Item three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'payment methods in country') !== false || stripos($lower_title, 'payment methods at') !== false || stripos($lower_title, 'payment methods for') !== false || stripos($lower_title, 'crypto payment methods') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>First paragraph covering the main payment options.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Second paragraph with notes on availability and suitability.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'types of') !== false || stripos($lower_title, 'payment alternatives') !== false || stripos($lower_title, 'alternative payment methods') !== false || stripos($lower_title, 'payment methods') !== false || stripos($lower_title, 'best bonuses') !== false || stripos($lower_title, 'why players choose') !== false) {
        $needs_paragraph = stripos($lower_title, 'payment methods') !== false || stripos($lower_title, 'best bonuses') !== false;
        $paragraph = $needs_paragraph ? "\n    <!-- wp:paragraph -->\n    <p>Short intro paragraph.</p>\n    <!-- /wp:paragraph -->" : '';
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->{$paragraph}
    <!-- wp:list -->
    <ul>
        <li>Item one.</li>
        <li>Item two.</li>
        <li>Item three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'deposits and withdrawals') !== false) {
        if (stripos($lower_title, 'online casino deposits and withdrawals') !== false) {
            return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:heading {"level":3} -->
    <h3>Deposits</h3>
    <!-- /wp:heading -->
    <!-- wp:list -->
    <ul>
        <li>Deposit item one.</li>
        <li>Deposit item two.</li>
        <li>Deposit item three.</li>
    </ul>
    <!-- /wp:list -->

    <!-- wp:heading {"level":3} -->
    <h3>Withdrawals</h3>
    <!-- /wp:heading -->
    <!-- wp:list -->
    <ul>
        <li>Withdrawal item one.</li>
        <li>Withdrawal item two.</li>
        <li>Withdrawal item three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
        }

        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Short paragraph introducing deposit and withdrawal details.</p>
    <!-- /wp:paragraph -->

    <!-- wp:table {"className":"is-style-sgk-table-a"} -->
    <figure class="wp-block-table is-style-sgk-table-a"><table>
        <tbody>
            <tr>
                <td>Row label</td>
                <td>Value</td>
            </tr>
            <tr>
                <td>Row label</td>
                <td>Value</td>
            </tr>
            <tr>
                <td>Row label</td>
                <td>Value</td>
            </tr>
        </tbody>
    </table></figure>
    <!-- /wp:table -->

    <!-- wp:paragraph -->
    <p>Add a short note or clarification below the table.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'terms and conditions') !== false || stripos($lower_title, 'limits / terms') !== false || stripos($lower_title, 'limits and terms') !== false || stripos($lower_title, 'terms / limits') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:table {"className":"is-style-sgk-table-a"} -->
    <figure class="wp-block-table is-style-sgk-table-a"><table>
        <tbody>
            <tr>
                <td>Condition</td>
                <td>Value</td>
            </tr>
            <tr>
                <td>Condition</td>
                <td>Value</td>
            </tr>
            <tr>
                <td>Condition</td>
                <td>Value</td>
            </tr>
        </tbody>
    </table></figure>
    <!-- /wp:table -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'how to use') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Short paragraph describing the usage flow.</p>
    <!-- /wp:paragraph -->

    <!-- wp:table {"className":"is-style-sgk-table-a"} -->
    <figure class="wp-block-table is-style-sgk-table-a"><table>
        <tbody>
            <tr>
                <td>Step</td>
                <td>Details</td>
            </tr>
            <tr>
                <td>Step</td>
                <td>Details</td>
            </tr>
            <tr>
                <td>Step</td>
                <td>Details</td>
            </tr>
        </tbody>
    </table></figure>
    <!-- /wp:table -->

    <!-- wp:paragraph -->
    <p>Add a short clarification below the table.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'how it works') !== false || stripos($lower_title, 'how does') !== false || stripos($lower_title, 'what is') !== false || stripos($lower_title, 'are ') !== false || stripos($lower_title, 'is ') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>First explanatory paragraph.</p>
    <!-- /wp:paragraph -->

    <!-- wp:paragraph -->
    <p>Second explanatory paragraph.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'user review quote') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:paragraph -->
    <p>“Insert a short user review quote here.”</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'reputation mention') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:paragraph -->
    <p>Reputation mention goes here.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'internal links') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:list -->
    <ul>
        <li>Internal link 1</li>
        <li>Internal link 2</li>
        <li>Internal link 3</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'paragraphs + table') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:paragraph -->
    <p>Paragraph one.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph two.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph three.</p>
    <!-- /wp:paragraph -->
    <!-- wp:table {"className":"is-style-sgk-table-a"} -->
    <figure class="wp-block-table is-style-sgk-table-a"><table>
        <tbody>
            <tr>
                <td>Row label</td>
                <td>Value</td>
            </tr>
            <tr>
                <td>Row label</td>
                <td>Value</td>
            </tr>
        </tbody>
    </table></figure>
    <!-- /wp:table -->
    <!-- wp:paragraph -->
    <p>Final paragraph.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'paragraphs + pros/cons + quote') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:paragraph -->
    <p>Paragraph one.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph two.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph three.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph four.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph five.</p>
    <!-- /wp:paragraph -->

    <!-- wp:list -->
    <ul>
        <li>Example pro.</li>
        <li>Example pro.</li>
    </ul>
    <!-- /wp:list -->
    <!-- wp:list -->
    <ul>
        <li>Example con.</li>
        <li>Example con.</li>
    </ul>
    <!-- /wp:list -->

    <!-- wp:paragraph -->
    <p>“Insert a short user review quote here.”</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'paragraphs + parameters table + payment methods list') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:paragraph -->
    <p>Paragraph one.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph two.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Paragraph three.</p>
    <!-- /wp:paragraph -->
    <!-- wp:table {"className":"is-style-sgk-table-a"} -->
    <figure class="wp-block-table is-style-sgk-table-a"><table>
        <tbody>
            <tr>
                <td>Parameter</td>
                <td>Value</td>
            </tr>
            <tr>
                <td>Parameter</td>
                <td>Value</td>
            </tr>
        </tbody>
    </table></figure>
    <!-- /wp:table -->
    <!-- wp:list -->
    <ul>
        <li>Payment method one.</li>
        <li>Payment method two.</li>
        <li>Payment method three.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'popular provider slots') !== false || stripos($lower_title, 'top popular slots') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>Short intro paragraph.</p>
    <!-- /wp:paragraph -->
    <!-- wp:list -->
    <ul>
        <li>Example item.</li>
        <li>Example item.</li>
        <li>Example item.</li>
    </ul>
    <!-- /wp:list -->
</div>
<!-- /wp:group -->
HTML;
    }

    if (stripos($lower_title, 'best payout slots/games') !== false) {
        return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>Short intro paragraph.</p>
    <!-- /wp:paragraph -->
    <!-- wp:table {"className":"is-style-sgk-table-a"} -->
    <figure class="wp-block-table is-style-sgk-table-a"><table>
        <tbody>
            <tr>
                <td>Game</td>
                <td>Payout</td>
            </tr>
            <tr>
                <td>Game</td>
                <td>Payout</td>
            </tr>
            <tr>
                <td>Game</td>
                <td>Payout</td>
            </tr>
        </tbody>
    </table></figure>
    <!-- /wp:table -->
</div>
<!-- /wp:group -->
HTML;
    }

    return <<<HTML
<!-- wp:group {"className":"{$section_class}","layout":{"type":"constrained"}} -->
<div class="wp-block-group {$section_class}">
    <!-- wp:heading {"level":2} -->
    <h2>{$safe_title}</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>Replace this text with the section content.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML;
}

function sgk_get_shared_section_class_map() {
    return array(
        'How we review' => 'sgk-section-how-we-review',
        'Pros and cons' => 'sgk-section-pros-cons',
        'Bonus types' => 'sgk-section-bonus-types',
        'Online casino games' => 'sgk-section-online-casino-games',
        'Casino types' => 'sgk-section-casino-types',
        'Casino licenses' => 'sgk-section-casino-licenses',
        'Payment methods in Country' => 'sgk-section-payment-methods',
        'Payment methods' => 'sgk-section-payment-methods',
        'Gambling regulation in Country' => 'sgk-section-gambling-regulation',
        'Responsible & safe gambling organizations' => 'sgk-section-responsible-gambling',
        'Alternative payment methods' => 'sgk-section-alternative-payments',
        'Payment alternatives' => 'sgk-section-alternative-payments',
        'Terms and conditions' => 'sgk-section-terms-conditions',
        'Limits and terms' => 'sgk-section-terms-conditions',
        'Deposits and withdrawals' => 'sgk-section-deposits-withdrawals',
        'How does it work?' => 'sgk-section-how-it-works',
        'How it works' => 'sgk-section-how-it-works',
    );
}

function sgk_get_shared_section_class($title) {
    $map = sgk_get_shared_section_class_map();
    if (isset($map[$title])) {
        return $map[$title];
    }

    $lower_title = strtolower($title);

    if (strpos($lower_title, 'how we review') !== false || strpos($lower_title, 'how i review') !== false || strpos($lower_title, 'how i test') !== false) {
        return 'sgk-section-how-we-review';
    }
    if (strpos($lower_title, 'pros and cons') !== false || strpos($lower_title, 'pros & cons') !== false) {
        return 'sgk-section-pros-cons';
    }
    if (strpos($lower_title, 'bonus types') !== false) {
        return 'sgk-section-bonus-types';
    }
    if (strpos($lower_title, 'online casino games') !== false || strpos($lower_title, 'casino games') !== false) {
        return 'sgk-section-online-casino-games';
    }
    if (strpos($lower_title, 'casino types') !== false) {
        return 'sgk-section-casino-types';
    }
    if (strpos($lower_title, 'casino licenses') !== false || strpos($lower_title, 'license types') !== false) {
        return 'sgk-section-casino-licenses';
    }
    if (strpos($lower_title, 'payment methods') !== false) {
        return 'sgk-section-payment-methods';
    }
    if (strpos($lower_title, 'gambling regulation') !== false) {
        return 'sgk-section-gambling-regulation';
    }
    if (strpos($lower_title, 'responsible') !== false) {
        return 'sgk-section-responsible-gambling';
    }
    if (strpos($lower_title, 'alternative payment methods') !== false || strpos($lower_title, 'payment alternatives') !== false) {
        return 'sgk-section-alternative-payments';
    }
    if (strpos($lower_title, 'terms and conditions') !== false || strpos($lower_title, 'limits / terms') !== false || strpos($lower_title, 'terms / limits') !== false || strpos($lower_title, 'limits and terms') !== false) {
        return 'sgk-section-terms-conditions';
    }
    if (strpos($lower_title, 'deposits and withdrawals') !== false) {
        return 'sgk-section-deposits-withdrawals';
    }
    if (strpos($lower_title, 'how does') !== false || strpos($lower_title, 'how it works') !== false) {
        return 'sgk-section-how-it-works';
    }

    return '';
}

function sgk_add_shared_section_class_to_pattern($pattern) {
    if (empty($pattern['title']) || empty($pattern['content'])) {
        return $pattern;
    }
    $shared_class = sgk_get_shared_section_class($pattern['title']);
    if (empty($shared_class) || strpos($pattern['content'], $shared_class) !== false) {
        return $pattern;
    }
    $pattern['content'] = preg_replace(
        '/<!-- wp:group {"className":"([^"]*)"/',
        '<!-- wp:group {"className":"$1 ' . $shared_class . '"',
        $pattern['content'],
        1
    );
    $pattern['content'] = preg_replace(
        '/class="([^"]*)"/',
        'class="$1 ' . $shared_class . '"',
        $pattern['content'],
        1
    );
    return $pattern;
}

function sgk_add_shared_block_classes_to_pattern_content($content) {
    if (strpos($content, 'pros-cons__tables') !== false) {
        return $content;
    }
    $content = preg_replace_callback('/<ul([^>]*)>/i', function ($matches) {
        $tag = $matches[0];
        if (strpos($tag, 'sgk-list') !== false) {
            return $tag;
        }
        if (strpos($tag, 'class="') !== false) {
            return preg_replace('/class="([^"]*)"/', 'class="$1 sgk-list"', $tag, 1);
        }
        return '<ul class="sgk-list"' . $matches[1] . '>';
    }, $content);

    $content = preg_replace_callback('/<ol([^>]*)>/i', function ($matches) {
        $tag = $matches[0];
        if (strpos($tag, 'sgk-list') !== false) {
            return $tag;
        }
        if (strpos($tag, 'class="') !== false) {
            return preg_replace('/class="([^"]*)"/', 'class="$1 sgk-list sgk-list--ordered"', $tag, 1);
        }
        return '<ol class="sgk-list sgk-list--ordered"' . $matches[1] . '>';
    }, $content);

    $content = preg_replace_callback('/<figure([^>]*)class="([^"]*wp-block-table[^"]*)"/i', function ($matches) {
        if (strpos($matches[2], 'sgk-table') !== false) {
            return $matches[0];
        }
        return '<figure' . $matches[1] . 'class="' . $matches[2] . ' sgk-table"';
    }, $content);

    $content = preg_replace_callback('/<table([^>]*)>/i', function ($matches) {
        $tag = $matches[0];
        if (strpos($tag, 'sgk-table') !== false) {
            return $tag;
        }
        if (strpos($tag, 'class="') !== false) {
            return preg_replace('/class="([^"]*)"/', 'class="$1 sgk-table"', $tag, 1);
        }
        return '<table class="sgk-table"' . $matches[1] . '>';
    }, $content);

    return $content;
}


function sgk_filter_pattern_categories($categories) {
    if (isset($categories['twentytwentyfive-child'])) {
        unset($categories['twentytwentyfive-child']);
    }
    return $categories;
}

