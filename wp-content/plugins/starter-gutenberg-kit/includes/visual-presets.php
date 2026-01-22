<?php

if (!defined('ABSPATH')) {
    exit;
}

function sgk_get_visual_presets() {
    return array(
        'table-modern' => array(
            'label' => __('Table: Modern comparison', 'starter-gutenberg-kit'),
            'group' => 'tables',
            'css' => '
.table-wrap {
  border-radius: 16px;
  border: 1px solid #e4e7ec;
  overflow: hidden;
  background: #ffffff;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
}

.comparison-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.97rem;
}

.comparison-table thead {
  background: #f8fafc;
}

.comparison-table th,
.comparison-table td {
  padding: 16px 18px;
  text-align: left;
  vertical-align: top;
  border-bottom: 1px solid #e4e7ec;
}

.comparison-table th {
  font-weight: 600;
  color: #0f172a;
  font-size: 0.88rem;
  letter-spacing: 0.02em;
  text-transform: uppercase;
}

.comparison-table th + th,
.comparison-table td + td {
  border-left: 1px solid #eef2f7;
}

.comparison-table tbody tr:last-child td {
  border-bottom: none;
}

.comparison-table tbody tr:nth-child(even) {
  background: #fbfdff;
}

.comparison-table td:first-child {
  font-weight: 600;
  color: #1f2937;
  width: 160px;
  background: #f8fafc;
}

.comparison-table tbody tr:hover td {
  background: #f1f5ff;
}

@media (max-width: 720px) {
  .table-wrap {
    border-radius: 16px;
    padding: 8px;
    background: transparent;
  }

  .comparison-table,
  .comparison-table thead,
  .comparison-table tbody,
  .comparison-table th,
  .comparison-table td,
  .comparison-table tr {
    display: block;
    width: 100%;
  }

  .comparison-table thead {
    display: none;
  }

  .comparison-table tbody tr {
    border: 1px solid #e4e7ec;
    border-radius: 14px;
    padding: 12px 14px;
    background: #ffffff;
    margin-bottom: 10px;
  }

  .comparison-table td {
    border: none;
    padding: 8px 0;
  }

  .comparison-table td:first-child {
    width: auto;
    background: #f8fafc;
    font-weight: 700;
    color: #0f172a;
    padding: 8px 10px;
    border-radius: 10px;
  }

  .comparison-table td::before {
    content: attr(data-label);
    display: block;
    font-size: 0.72rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #98a2b3;
    margin-bottom: 6px;
  }
}
',
        ),
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
        'tables' => __('Tables', 'starter-gutenberg-kit'),
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
    if (empty($block['blockName'])) {
        return $block_content;
    }
    if (sgk_is_visual_preset_enabled('table-modern')) {
        $block_content = sgk_apply_table_preset($block_content);
    }
    if ($block['blockName'] === 'core/group' && sgk_is_visual_preset_enabled('proscons-cards')) {
        if (strpos($block_content, 'sgk-section-pros-cons') !== false || strpos($block_content, 'pros-cons__tables') !== false) {
            $block_content = sgk_apply_proscons_preset($block_content);
        }
    }
    return $block_content;
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

function sgk_apply_table_preset($block_content) {
    $block_content = preg_replace_callback('/<figure([^>]*)class="([^"]*)"/i', function ($matches) {
        if (strpos($matches[2], 'wp-block-table') === false) {
            return $matches[0];
        }
        if (strpos($matches[2], 'table-wrap') !== false) {
            return $matches[0];
        }
        return '<figure' . $matches[1] . 'class="' . $matches[2] . ' table-wrap"';
    }, $block_content);

    $block_content = preg_replace_callback('/<table([^>]*)class="([^"]*)"/i', function ($matches) {
        if (strpos($matches[2], 'comparison-table') !== false) {
            return $matches[0];
        }
        return '<table' . $matches[1] . 'class="' . $matches[2] . ' comparison-table"';
    }, $block_content);

    $block_content = preg_replace_callback('/<table(?![^>]*class=)([^>]*)>/i', function ($matches) {
        return '<table class="comparison-table"' . $matches[1] . '>';
    }, $block_content);

    return $block_content;
}
