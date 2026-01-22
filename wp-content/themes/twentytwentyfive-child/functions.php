<?php

if (!defined('ABSPATH')) {
    exit;
}

function twentytwentyfive_child_enqueue_styles() {
    wp_enqueue_style(
        'twentytwentyfive-style',
        get_template_directory_uri() . '/style.css'
    );
}
add_action('wp_enqueue_scripts', 'twentytwentyfive_child_enqueue_styles');

function twentytwentyfive_child_register_block_styles() {
    $cards = array('card-a', 'card-b');
    foreach ($cards as $style) {
        register_block_style(
            'core/group',
            array(
                'name'  => $style,
                'label' => ucwords(str_replace('-', ' ', $style)),
            )
        );
    }
}
add_action('init', 'twentytwentyfive_child_register_block_styles');

function twentytwentyfive_child_register_pattern_category() {
    register_block_pattern_category(
        'twentytwentyfive-child',
        array('label' => __('TT5 Child Sections', 'twentytwentyfive-child'))
    );
}
add_action('init', 'twentytwentyfive_child_register_pattern_category');
