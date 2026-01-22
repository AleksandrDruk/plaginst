<?php

if (!defined('ABSPATH')) {
    exit;
}

function sgk_asset_version($relative_path) {
    $path = SGK_PATH . ltrim($relative_path, '/');
    if (file_exists($path)) {
        return (string) filemtime($path);
    }
    return SGK_VERSION;
}

function sgk_enqueue_assets() {
    wp_enqueue_style(
        'sgk-styles',
        SGK_URL . 'assets/css/sgk.css',
        array(),
        sgk_asset_version('assets/css/sgk.css')
    );

    $custom_css = sgk_get_custom_css();
    if (!empty($custom_css)) {
        wp_add_inline_style('sgk-styles', $custom_css);
    }
    if (function_exists('sgk_get_visual_presets_css')) {
        $visual_css = sgk_get_visual_presets_css();
        if (!empty($visual_css)) {
            wp_add_inline_style('sgk-styles', $visual_css);
        }
    }
}

function sgk_enqueue_editor_assets() {
    $editor_path = SGK_PATH . 'assets/css/sgk-editor.css';
    if (!file_exists($editor_path)) {
        return;
    }

    wp_enqueue_style(
        'sgk-editor-styles',
        SGK_URL . 'assets/css/sgk-editor.css',
        array('sgk-styles'),
        sgk_asset_version('assets/css/sgk-editor.css')
    );

    $custom_css = sgk_get_custom_css();
    if (!empty($custom_css)) {
        wp_add_inline_style('sgk-editor-styles', $custom_css);
    }
    if (function_exists('sgk_get_visual_presets_css')) {
        $visual_css = sgk_get_visual_presets_css();
        if (!empty($visual_css)) {
            wp_add_inline_style('sgk-editor-styles', $visual_css);
        }
    }
}

function sgk_get_custom_css() {
    $global_css = get_option('sgk_custom_css_global', '');
    $active_preset = function_exists('sgk_get_active_preset_key') ? sgk_get_active_preset_key() : 'none';
    $preset_css = get_option('sgk_custom_css_preset_' . $active_preset, '');

    $css = '';
    if (!empty($global_css)) {
        $css .= "\n/* SGK Global CSS */\n" . $global_css . "\n";
    }
    if (!empty($preset_css)) {
        $css .= "\n/* SGK Preset CSS: " . $active_preset . " */\n" . $preset_css . "\n";
    }

    return trim($css);
}
