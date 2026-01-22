<?php
/**
 * Plugin Name: Starter Gutenberg Kit
 * Description: Section patterns and styles for Gutenberg landing pages.
 * Version: 1.0.0
 * Author: SGK
 * License: GPL-2.0+
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('SGK_VERSION')) {
    define('SGK_VERSION', '1.0.0');
}
if (!defined('SGK_PATH')) {
    define('SGK_PATH', plugin_dir_path(__FILE__));
}
if (!defined('SGK_URL')) {
    define('SGK_URL', plugin_dir_url(__FILE__));
}

require_once SGK_PATH . 'includes/presets.php';
require_once SGK_PATH . 'includes/site-token.php';
require_once SGK_PATH . 'includes/visual-presets.php';
require_once SGK_PATH . 'includes/register-patterns.php';
require_once SGK_PATH . 'includes/register-styles.php';
require_once SGK_PATH . 'includes/enqueue-assets.php';
require_once SGK_PATH . 'includes/admin-page.php';

add_action('init', 'sgk_register_patterns');
add_action('init', 'sgk_register_styles');
add_action('enqueue_block_assets', 'sgk_enqueue_assets');
add_action('enqueue_block_editor_assets', 'sgk_enqueue_editor_assets');
add_action('admin_menu', 'sgk_register_admin_menu');
add_action('admin_enqueue_scripts', 'sgk_enqueue_admin_assets');
add_filter('render_block', 'sgk_add_site_token_to_section', 10, 2);

add_filter('block_pattern_categories_all', 'sgk_filter_pattern_categories', 20, 1);
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'sgk_add_plugin_action_links');

register_activation_hook(__FILE__, 'sgk_activate_plugin');

function sgk_activate_plugin() {
    sgk_get_site_token();
    if (get_option('sgk_enable_site_token', null) === null) {
        update_option('sgk_enable_site_token', 1);
    }
}

function sgk_add_plugin_action_links($links) {
    $settings_url = admin_url('admin.php?page=starter-gutenberg-kit');
    $settings_link = '<a href="' . esc_url($settings_url) . '">' . esc_html__('Settings', 'starter-gutenberg-kit') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

