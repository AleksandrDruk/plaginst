<?php

if (!defined('ABSPATH')) {
    exit;
}

function sgk_register_admin_menu() {
    add_menu_page(
        __('Starter Gutenberg Kit', 'starter-gutenberg-kit'),
        __('Starter Gutenberg Kit', 'starter-gutenberg-kit'),
        'manage_options',
        'starter-gutenberg-kit',
        'sgk_render_admin_page',
        'dashicons-layout'
    );

    add_submenu_page(
        'starter-gutenberg-kit',
        __('SGK Editor', 'starter-gutenberg-kit'),
        __('SGK Editor', 'starter-gutenberg-kit'),
        'manage_options',
        'starter-gutenberg-kit-editor',
        'sgk_render_editor_page'
    );

    add_submenu_page(
        'starter-gutenberg-kit',
        __('SGK Visual', 'starter-gutenberg-kit'),
        __('SGK Visual', 'starter-gutenberg-kit'),
        'manage_options',
        'starter-gutenberg-kit-visual',
        'sgk_render_visual_page'
    );

    add_submenu_page(
        'options-general.php',
        __('Starter Gutenberg Kit', 'starter-gutenberg-kit'),
        __('Starter Gutenberg Kit', 'starter-gutenberg-kit'),
        'manage_options',
        'starter-gutenberg-kit',
        'sgk_render_admin_page'
    );
}

function sgk_render_visual_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $presets = function_exists('sgk_get_visual_presets') ? sgk_get_visual_presets() : array();
    $groups = function_exists('sgk_get_visual_preset_groups') ? sgk_get_visual_preset_groups() : array(
        'proscons' => __('Pros / Cons', 'starter-gutenberg-kit'),
    );

    if (!empty($_POST['sgk_visual_nonce']) && wp_verify_nonce($_POST['sgk_visual_nonce'], 'sgk_save_visual')) {
        foreach ($presets as $key => $preset) {
            $enabled = !empty($_POST['sgk_visual_' . $key]) ? 1 : 0;
            update_option('sgk_visual_preset_' . $key, $enabled);
        }
    }
    ?>
    <div class="wrap sgk-admin sgk-admin--editor">
        <div class="sgk-admin__panel">
            <div class="sgk-admin__panel-head">
                <h2><?php echo esc_html__('SGK Visual presets', 'starter-gutenberg-kit'); ?></h2>
                <p><?php echo esc_html__('Visual presets are currently empty. We will add a new list later.', 'starter-gutenberg-kit'); ?></p>
            </div>
            <form method="post" class="sgk-admin__editor">
                <?php wp_nonce_field('sgk_save_visual', 'sgk_visual_nonce'); ?>
                <div class="sgk-admin__visual-tabs">
                <button type="button" class="sgk-admin__visual-tab is-active" data-group="all">
                    <?php echo esc_html__('All', 'starter-gutenberg-kit'); ?>
                </button>
                <?php foreach ($groups as $group_key => $group_label) : ?>
                    <button type="button" class="sgk-admin__visual-tab" data-group="<?php echo esc_attr($group_key); ?>">
                        <?php echo esc_html($group_label); ?>
                    </button>
                <?php endforeach; ?>
                </div>
                <div class="sgk-admin__visual-grid" data-active-group="all">
                    <?php foreach ($groups as $group_key => $group_label) : ?>
                        <div class="sgk-admin__visual-group" data-group="<?php echo esc_attr($group_key); ?>">
                            <h3><?php echo esc_html($group_label); ?></h3>
                            <div class="sgk-admin__visual-tiles">
                                <?php $has_preset = false; ?>
                                <?php foreach ($presets as $key => $preset) : ?>
                                    <?php if (!isset($preset['group']) || $preset['group'] !== $group_key) { continue; } ?>
                                    <?php $has_preset = true; ?>
                                    <label class="sgk-admin__visual-tile sgk-admin__visual-tile--toggle">
                                        <span class="sgk-admin__visual-label"><?php echo esc_html($preset['label']); ?></span>
                                        <span class="sgk-admin__toggle">
                                            <input type="checkbox" name="sgk_visual_<?php echo esc_attr($key); ?>" value="1" <?php checked(sgk_is_visual_preset_enabled($key), true); ?>>
                                            <span class="sgk-admin__toggle-track" aria-hidden="true"></span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                                <?php if (!$has_preset) : ?>
                                    <div class="sgk-admin__visual-empty">
                                        <?php echo esc_html__('No visual presets yet.', 'starter-gutenberg-kit'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="sgk-admin__editor-actions">
                    <button class="button button-primary" type="submit">
                        <?php echo esc_html__('Save visual presets', 'starter-gutenberg-kit'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

function sgk_handle_post_actions() {
    if (empty($_POST['sgk_preset_nonce']) || !wp_verify_nonce($_POST['sgk_preset_nonce'], 'sgk_save_preset')) {
        return;
    }

    $presets = sgk_get_presets();
    $selected = isset($_POST['sgk_active_preset']) ? sanitize_key($_POST['sgk_active_preset']) : 'none';
    if (!isset($presets[$selected])) {
        $selected = 'none';
    }

    $action = isset($_POST['sgk_action']) ? sanitize_key($_POST['sgk_action']) : 'all';

    if ($action === 'save_preset' || $action === 'all') {
        update_option('sgk_active_preset', $selected);
        $token_enabled = !empty($_POST['sgk_enable_site_token']) ? 1 : 0;
        update_option('sgk_enable_site_token', $token_enabled);
        $preset_content = isset($_POST['sgk_preset_content']) ? wp_unslash($_POST['sgk_preset_content']) : '';
        if ($preset_content !== '') {
            update_option('sgk_preset_content_' . $selected, $preset_content);
        }
    }

    if ($action === 'save_css' || $action === 'all') {
        $global_css = isset($_POST['sgk_custom_css_global']) ? wp_unslash($_POST['sgk_custom_css_global']) : '';
        $preset_css = isset($_POST['sgk_custom_css_preset']) ? wp_unslash($_POST['sgk_custom_css_preset']) : '';
        update_option('sgk_custom_css_global', $global_css);
        update_option('sgk_custom_css_preset_' . $selected, $preset_css);
    }

}

function sgk_render_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    sgk_handle_post_actions();

    $presets = sgk_get_presets();
    $active_preset = sgk_get_active_preset_key();
    $token_enabled = sgk_site_token_enabled();
    $badge_class = 'sgk-admin__badge';
    if ($active_preset !== 'none') {
        $badge_class .= ' sgk-admin__badge--active';
    }
    ?>
    <div class="wrap sgk-admin">
        <div class="sgk-admin__hero">
            <div>
                <h1><?php echo esc_html__('Starter Gutenberg Kit', 'starter-gutenberg-kit'); ?></h1>
                <p class="sgk-admin__lead">
                    <?php echo esc_html__('Choose a preset to control which SGK sections appear in the editor.', 'starter-gutenberg-kit'); ?>
                </p>
            </div>
            <div class="<?php echo esc_attr($badge_class); ?>">
                <?php echo esc_html__('Active preset', 'starter-gutenberg-kit'); ?>
            </div>
        </div>

        <form method="post" class="sgk-admin__layout">
            <div class="sgk-admin__panel">
                <div class="sgk-admin__panel-head">
                    <h2><?php echo esc_html__('Select preset', 'starter-gutenberg-kit'); ?></h2>
                    <p><?php echo esc_html__('Only patterns from the selected preset will be visible in Gutenberg.', 'starter-gutenberg-kit'); ?></p>
                </div>

                <?php wp_nonce_field('sgk_save_preset', 'sgk_preset_nonce'); ?>
                <label class="sgk-admin__label" for="sgk-active-preset">
                    <?php echo esc_html__('Preset', 'starter-gutenberg-kit'); ?>
                </label>
                <select id="sgk-active-preset" name="sgk_active_preset" class="sgk-admin__select">
                    <?php foreach ($presets as $key => $preset) : ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($active_preset, $key); ?>>
                            <?php echo esc_html($preset['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="sgk-admin__preset-list">
                    <strong><?php echo esc_html__('Preset sections', 'starter-gutenberg-kit'); ?>:</strong>
                    <ul>
                        <?php foreach (sgk_get_preset_titles($active_preset, $presets) as $title) : ?>
                            <li><?php echo esc_html($title); ?></li>
                        <?php endforeach; ?>
                        <?php if ($active_preset === 'all') : ?>
                            <li><?php echo esc_html__('All available patterns are enabled.', 'starter-gutenberg-kit'); ?></li>
                        <?php endif; ?>
                        <?php if ($active_preset === 'none') : ?>
                            <li><?php echo esc_html__('No preset selected. All patterns remain available.', 'starter-gutenberg-kit'); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="sgk-admin__panel-actions">
                <input type="hidden" name="sgk_action" value="save_preset">
                <button class="button button-primary" type="submit">
                    <?php echo esc_html__('Save preset', 'starter-gutenberg-kit'); ?>
                </button>
            </div>

            <div class="sgk-admin__panel sgk-admin__panel--bottom sgk-admin__panel--bottom-narrow">
                <div class="sgk-admin__pill">
                    <span class="sgk-admin__pill-label">
                        <?php echo esc_html__('Unique site token', 'starter-gutenberg-kit'); ?>
                    </span>
                    <label class="sgk-admin__pill-toggle">
                        <input type="checkbox" name="sgk_enable_site_token" value="1" <?php checked($token_enabled, true); ?>>
                        <span class="sgk-admin__pill-track" aria-hidden="true"></span>
                        <span class="sgk-admin__pill-text">
                            <?php echo $token_enabled ? esc_html__('On', 'starter-gutenberg-kit') : esc_html__('Off', 'starter-gutenberg-kit'); ?>
                        </span>
                    </label>
                </div>
            </div>
        </form>
    </div>
    <?php
}

function sgk_render_editor_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    sgk_handle_post_actions();

    $presets = sgk_get_presets();
    $active_preset = sgk_get_active_preset_key();
    $global_css = get_option('sgk_custom_css_global', '');
    $preset_css = get_option('sgk_custom_css_preset_' . $active_preset, '');
    $preset_content = get_option('sgk_preset_content_' . $active_preset, '');
    ?>
    <div class="wrap sgk-admin sgk-admin--editor">
        <div class="sgk-admin__panel">
            <div class="sgk-admin__panel-head">
                <h2><?php echo esc_html__('SGK Editor', 'starter-gutenberg-kit'); ?></h2>
                <p><?php echo esc_html__('Manage preset content and custom CSS. Export everything as JSON.', 'starter-gutenberg-kit'); ?></p>
            </div>

            <form method="post" class="sgk-admin__editor" enctype="multipart/form-data">
                <?php wp_nonce_field('sgk_save_preset', 'sgk_preset_nonce'); ?>

                <div class="sgk-admin__editor-row">
                    <label class="sgk-admin__label" for="sgk-active-preset-editor">
                        <?php echo esc_html__('Preset', 'starter-gutenberg-kit'); ?>
                    </label>
                    <select id="sgk-active-preset-editor" name="sgk_active_preset" class="sgk-admin__select">
                        <?php foreach ($presets as $key => $preset) : ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($active_preset, $key); ?>>
                                <?php echo esc_html($preset['label']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sgk-admin__editor-grid">
                    <div>
                        <label class="sgk-admin__label" for="sgk-custom-css-global">
                            <?php echo esc_html__('Global CSS', 'starter-gutenberg-kit'); ?>
                        </label>
                        <textarea id="sgk-custom-css-global" name="sgk_custom_css_global" rows="14" class="sgk-admin__textarea"><?php echo esc_textarea($global_css); ?></textarea>
                    </div>
                    <div>
                        <label class="sgk-admin__label" for="sgk-custom-css-preset">
                            <?php echo esc_html__('Preset CSS (active preset only)', 'starter-gutenberg-kit'); ?>
                        </label>
                        <textarea id="sgk-custom-css-preset" name="sgk_custom_css_preset" rows="14" class="sgk-admin__textarea"><?php echo esc_textarea($preset_css); ?></textarea>
                    </div>
                </div>

                <div>
                    <label class="sgk-admin__label" for="sgk-preset-content">
                        <?php echo esc_html__('Preset content (blocks)', 'starter-gutenberg-kit'); ?>
                    </label>
                    <p class="description">
                        <?php echo esc_html__('Paste Gutenberg block HTML. Editor: More tools → Copy all content.', 'starter-gutenberg-kit'); ?>
                    </p>
                    <textarea id="sgk-preset-content" name="sgk_preset_content" rows="12" class="sgk-admin__textarea sgk-admin__textarea--blocks"><?php echo esc_textarea($preset_content); ?></textarea>
                </div>

                <div class="sgk-admin__editor-actions">
                    <button class="button button-primary" type="submit" name="sgk_action" value="all">
                        <?php echo esc_html__('Save all', 'starter-gutenberg-kit'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
}


function sgk_enqueue_admin_assets($hook_suffix) {
    if ($hook_suffix !== 'toplevel_page_starter-gutenberg-kit' && $hook_suffix !== 'settings_page_starter-gutenberg-kit' && $hook_suffix !== 'starter-gutenberg-kit_page_starter-gutenberg-kit-editor' && $hook_suffix !== 'starter-gutenberg-kit_page_starter-gutenberg-kit-visual') {
        return;
    }
    if ($hook_suffix === 'starter-gutenberg-kit_page_starter-gutenberg-kit-visual') {
        wp_add_inline_script(
            'jquery',
            'jQuery(function(){var $tabs=jQuery(".sgk-admin__visual-tab");if(!$tabs.length){return;}var $grid=jQuery(".sgk-admin__visual-grid");$tabs.on("click",function(){var group=jQuery(this).data("group");$tabs.removeClass("is-active");jQuery(this).addClass("is-active");$grid.attr("data-active-group",group);});});'
        );
    }

    wp_enqueue_style(
        'sgk-admin-styles',
        SGK_URL . 'assets/css/sgk-admin.css',
        array(),
        sgk_asset_version('assets/css/sgk-admin.css')
    );

    $editor_settings = wp_enqueue_code_editor(array('type' => 'text/css'));
    if (!empty($editor_settings)) {
        wp_enqueue_script('code-editor');
        wp_enqueue_style('code-editor');
        wp_enqueue_script('jquery');
        wp_add_inline_script(
            'code-editor',
            'jQuery(function(){var cssSettings=' . wp_json_encode($editor_settings) . ';' .
            'var htmlSettings=' . wp_json_encode(wp_enqueue_code_editor(array('type' => 'text/html'))) . ';' .
            'if (document.getElementById("sgk-custom-css-global")) { wp.codeEditor.initialize("sgk-custom-css-global", cssSettings); }' .
            'if (document.getElementById("sgk-custom-css-preset")) { wp.codeEditor.initialize("sgk-custom-css-preset", cssSettings); }' .
            'if (htmlSettings && document.getElementById("sgk-preset-content")) { wp.codeEditor.initialize("sgk-preset-content", htmlSettings); }' .
            '});'
        );
    }
}

