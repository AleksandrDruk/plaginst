<?php

if (!defined('ABSPATH')) {
    exit;
}

function sgk_register_styles() {
    if (!function_exists('register_block_style')) {
        return;
    }

    register_block_style(
        'core/table',
        array(
            'name'  => 'sgk-table-a',
            'label' => __('SGK Table A', 'starter-gutenberg-kit'),
        )
    );
    register_block_style(
        'core/table',
        array(
            'name'  => 'sgk-table-b',
            'label' => __('SGK Table B', 'starter-gutenberg-kit'),
        )
    );
    register_block_style(
        'core/table',
        array(
            'name'  => 'sgk-table-c',
            'label' => __('SGK Table C', 'starter-gutenberg-kit'),
        )
    );

    register_block_style(
        'core/group',
        array(
            'name'  => 'sgk-card-a',
            'label' => __('SGK Card A', 'starter-gutenberg-kit'),
        )
    );
    register_block_style(
        'core/group',
        array(
            'name'  => 'sgk-card-b',
            'label' => __('SGK Card B', 'starter-gutenberg-kit'),
        )
    );

    register_block_style(
        'core/button',
        array(
            'name'  => 'sgk-btn-a',
            'label' => __('SGK Button A', 'starter-gutenberg-kit'),
        )
    );
    register_block_style(
        'core/button',
        array(
            'name'  => 'sgk-btn-b',
            'label' => __('SGK Button B', 'starter-gutenberg-kit'),
        )
    );
}
