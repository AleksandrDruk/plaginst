<?php

if (!defined('ABSPATH')) {
    exit;
}

function sgk_get_site_token() {
    $token = get_option('sgk_site_token');
    if (empty($token)) {
        $raw = wp_generate_uuid4();
        $raw = strtolower(preg_replace('/[^a-z0-9]/', '', $raw));
        $token = 'sgk-' . substr($raw, 0, 8);
        update_option('sgk_site_token', $token);
    }
    return $token;
}

function sgk_site_token_enabled() {
    return (bool) get_option('sgk_enable_site_token', 1);
}

function sgk_add_site_token_to_section($block_content, $block) {
    if (!sgk_site_token_enabled()) {
        return $block_content;
    }
    if (empty($block['blockName']) || $block['blockName'] !== 'core/group') {
        return $block_content;
    }
    if (empty($block['attrs']['className'])) {
        return $block_content;
    }
    $class_name = $block['attrs']['className'];
    if (strpos($class_name, 'sec-') === false && strpos($class_name, 'is-style-sgk-') === false) {
        return $block_content;
    }

    $token = sgk_get_site_token();
    if (strpos($block_content, $token) !== false) {
        return $block_content;
    }

    return preg_replace(
        '/class="([^"]*)"/',
        'class="$1 ' . esc_attr($token) . '"',
        $block_content,
        1
    );
}

function sgk_add_site_token_to_pattern_content($content) {
    if (!sgk_site_token_enabled()) {
        return $content;
    }

    $token = sgk_get_site_token();
    if (strpos($content, $token) !== false) {
        return $content;
    }

    $content = preg_replace_callback(
        '/"className":"([^"]*)"/',
        function ($matches) use ($token) {
            $class_name = $matches[1];
            if (strpos($class_name, 'sec-') === false && strpos($class_name, 'is-style-sgk-') === false) {
                return $matches[0];
            }
            if (strpos($class_name, $token) !== false) {
                return $matches[0];
            }
            return '"className":"' . $class_name . ' ' . esc_attr($token) . '"';
        },
        $content
    );
    $content = preg_replace_callback(
        '/class="([^"]*)"/',
        function ($matches) use ($token) {
            $class_name = $matches[1];
            if (strpos($class_name, 'sec-') === false && strpos($class_name, 'is-style-sgk-') === false) {
                return $matches[0];
            }
            if (strpos($class_name, $token) !== false) {
                return $matches[0];
            }
            return 'class="' . $class_name . ' ' . esc_attr($token) . '"';
        },
        $content
    );

    return $content;
}
