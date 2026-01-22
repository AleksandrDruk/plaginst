<?php

if (!defined('ABSPATH')) {
    exit;
}

function sgk_get_visual_presets() {
    return array(
        'proscons-cards' => array(
            'label' => __('Pros & Cons: Split cards', 'starter-gutenberg-kit'),
            'group' => 'proscons',
            'css' => '
.pros-cons__tables {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 18px;
}

.pros-cons__table {
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #e4e7ec;
  background: #ffffff;
}

.pros-cons__table-head {
  font-size: 0.85rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 12px 16px;
}

.pros-cons__table ul {
  margin: 0;
  padding: 0 16px 8px;
  list-style: none;
}

.pros-cons__table li {
  padding: 10px 0;
  border-bottom: 1px solid #eef2f7;
  color: #344054;
}

.pros-cons__table li:last-child {
  border-bottom: none;
}

.pros-cons__table--pro {
  border-color: #bbf7d0;
}

.pros-cons__table--pro .pros-cons__table-head {
  background: #dcfce7;
  color: #166534;
}

.pros-cons__table--con {
  border-color: #fecaca;
}

.pros-cons__table--con .pros-cons__table-head {
  background: #fee2e2;
  color: #991b1b;
}

@media (max-width: 900px) {
  .pros-cons__tables {
    grid-template-columns: 1fr;
  }
}
',
        ),
    );
}

function sgk_get_visual_preset_groups() {
    return array(
        'proscons' => __('Pros / Cons', 'starter-gutenberg-kit'),
    );
}

function sgk_is_visual_preset_enabled($key) {
    return (bool) get_option('sgk_visual_preset_' . $key, 0);
}

function sgk_get_visual_presets_css() {
    $css = '';
    foreach (sgk_get_visual_presets() as $key => $preset) {
        if (sgk_is_visual_preset_enabled($key)) {
            $css .= "\n/* SGK Visual preset: {$key} */\n" . $preset['css'] . "\n";
        }
    }
    return trim($css);
}

function sgk_add_visual_preset_class_to_block($block_content, $block) {
    if (empty($block['blockName']) || $block['blockName'] !== 'core/group') {
        return $block_content;
    }
    if (!sgk_is_visual_preset_enabled('proscons-cards')) {
        return $block_content;
    }
    if (strpos($block_content, 'sgk-section-pros-cons') === false && strpos($block_content, 'pros-cons__tables') === false) {
        return $block_content;
    }
    return sgk_apply_proscons_preset($block_content);
}

function sgk_apply_proscons_preset($block_content) {
    if (strpos($block_content, 'pros-cons__tables') !== false) {
        return $block_content;
    }
    if (strpos($block_content, 'pros-cons__table') !== false || strpos($block_content, 'pros-cons__table-head') !== false) {
        $block_content = preg_replace(
            '/(<div class="pros-cons__table pros-cons__table--pro">.*?<\/div>)(\s*)(<div class="pros-cons__table pros-cons__table--con">.*?<\/div>)/s',
            '<div class="pros-cons__tables">$1$2$3</div>',
            $block_content,
            1
        );
        return $block_content;
    }
    if (strpos($block_content, 'sgk-list--pros') === false || strpos($block_content, 'sgk-list--cons') === false) {
        return $block_content;
    }

    $block_content = preg_replace(
        '/(<ul[^>]*class="[^"]*sgk-list--pros[^"]*"[^>]*>.*?<\/ul>)/s',
        '<div class="pros-cons__table pros-cons__table--pro"><div class="pros-cons__table-head">Pros</div>$1</div>',
        $block_content,
        1
    );
    $block_content = preg_replace(
        '/(<ul[^>]*class="[^"]*sgk-list--cons[^"]*"[^>]*>.*?<\/ul>)/s',
        '<div class="pros-cons__table pros-cons__table--con"><div class="pros-cons__table-head">Cons</div>$1</div>',
        $block_content,
        1
    );

    $block_content = preg_replace(
        '/(<div class="pros-cons__table pros-cons__table--pro">.*?<\/div>)(\s*)(<div class="pros-cons__table pros-cons__table--con">.*?<\/div>)/s',
        '<div class="pros-cons__tables">$1$2$3</div>',
        $block_content,
        1
    );

    return $block_content;
}
