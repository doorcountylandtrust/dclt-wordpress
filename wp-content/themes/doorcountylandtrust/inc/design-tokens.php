<?php
// inc/design-tokens.php

if (!defined('ABSPATH')) { exit; }

/**
 * Canonical list of author-facing color tokens.
 * Key => [label, css var name]
 */
function dclt_color_tokens(): array {
    return [
        'brand-700'    => ['Brand 700',    '--brand-700'],
        'brand-500'    => ['Brand 500',    '--brand-500'],
        'brand-900'    => ['Brand 900',    '--brand-900'],
        'neutral-900'  => ['Neutral 900',  '--neutral-900'],
        'neutral-700'  => ['Neutral 700',  '--neutral-700'],
        'neutral-500'  => ['Neutral 500',  '--neutral-500'],
        'success'      => ['Success',      '--success'],
        'warning'      => ['Warning',      '--warning'],
        'danger'       => ['Danger',       '--danger'],
        'info'         => ['Info',         '--info'],
        'white'        => ['White',        '#ffffff'],
        'black'        => ['Black',        '#000000'],
    ];
}

/**
 * Output CSS variables on :root (map to your brand)
 * Keep this aligned with Tailwind/tokens you already use.
 */
function dclt_output_design_token_css() {
    echo '<style id="dclt-design-tokens">:root{'
        . '--brand-700:#065f46;--brand-500:#006847;--brand-900:#002d1f;'
        . '--neutral-900:#111827;--neutral-700:#374151;--neutral-500:#6b7280;'
        . '--success:#10b981;--warning:#f59e0b;--danger:#ef4444;--info:#3b82f6;'
        . '}</style>';
}
add_action('wp_head', 'dclt_output_design_token_css');   // front-end
add_action('admin_head', 'dclt_output_design_token_css'); // editor/admin too

/**
 * Resolve a selected token key into a usable CSS value string.
 * Returns something you can drop into inline style, like: var(--brand-700)
 */
function dclt_color_token_to_css(string $token_key, string $fallback = 'var(--brand-700)'): string {
    $tokens = dclt_color_tokens();
    if (!isset($tokens[$token_key])) return $fallback;

    $var = $tokens[$token_key][1];
    // If it looks like a CSS var name, wrap it; otherwise it's a raw color hex.
    if (str_starts_with($var, '--')) {
        return 'var(' . $var . ')';
    }
    return $var;
}

/**
 * Shared <select> renderer for meta boxes.
 */
function dclt_render_color_select(string $field_name, string $current = '', string $label = 'Color') {
    $tokens = dclt_color_tokens();
    echo '<div class="dclt-field" style="margin-bottom:12px">';
    echo '<label for="'.esc_attr($field_name).'" style="display:block;font-weight:600;margin-bottom:6px">'.esc_html($label).'</label>';
    echo '<select id="'.esc_attr($field_name).'" name="'.esc_attr($field_name).'" style="min-width:220px">';
    foreach ($tokens as $key => [$nice, $_var]) {
        echo '<option value="'.esc_attr($key).'" '.selected($current, $key, false).'>'.esc_html($nice).'</option>';
    }
    echo '</select>';
    echo '</div>';
}