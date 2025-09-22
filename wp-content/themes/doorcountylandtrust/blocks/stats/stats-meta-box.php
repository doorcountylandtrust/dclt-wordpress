<?php
/**
 * Stats Block Meta Box with Custom SVG Support
 * Location: blocks/stats/stats-meta-box.php
 */

if (!defined('ABSPATH')) exit;

/**
 * Render meta box
 */
function dclt_stats_meta_box_callback($post) {
    wp_nonce_field('dclt_stats_meta_box', 'dclt_stats_meta_box_nonce');

    // Content fields
    $kicker = dclt_get_field($post->ID, 'stats_kicker', '');
    $title  = dclt_get_field($post->ID, 'stats_title', '');
    $desc   = dclt_get_field($post->ID, 'stats_desc', '');

    // Items (fixed 3 for MVP)
    $items = [];
    for ($i=1; $i<=3; $i++) {
        $items[$i] = [
            'label'    => dclt_get_field($post->ID, "stats_item{$i}_label", ''),
            'value'    => dclt_get_field($post->ID, "stats_item{$i}_value", ''),
            'suffix'   => dclt_get_field($post->ID, "stats_item{$i}_suffix", ''),
            'icon'     => dclt_get_field($post->ID, "stats_item{$i}_icon", ''),
            'icon_svg' => dclt_get_field($post->ID, "stats_item{$i}_icon_svg", ''),
        ];
    }

    // Layout/container
    $container = dclt_get_field($post->ID, 'stats_container', '', 'content'); // content|wide|full
    $spacing   = dclt_get_field($post->ID, 'stats_spacing', '', 'medium');    // compact|medium|spacious

    // Color tokens (Option A) + optional custom hex fallback
    $token_presets = [
        ''               => 'Default',
        'text-brand-700' => 'Brand / 700 (dark green)',
        'text-brand-500' => 'Brand / 500',
        'text-primary-700' => 'Primary / 700',
        'text-neutral-900'  => 'Neutral / 900',
        'text-neutral-700'  => 'Neutral / 700',
        'text-green-700'    => 'Green 700',
        'text-emerald-700'  => 'Emerald 700',
    ];

    // Icon presets for dropdown
    $icon_presets = [
        ''           => 'Default (simple circle)',
        'tree'       => 'Tree (great for acres protected)',
        'handshake'  => 'Partnership (landowner relationships)',
        'landscape'  => 'Landscape (preserves/habitats)',
        'bird'       => 'Bird (species/migration)',
        'shield'     => 'Shield (protection/conservation)',
        'leaf'       => 'Leaf (growth/sustainability)',
        'custom'     => 'Custom SVG (use field below)',
    ];

    // Modes: token | custom
    $headline_color_mode = dclt_get_field($post->ID, 'stats_headline_color_mode', '', 'token');
    $headline_token      = dclt_get_field($post->ID, 'stats_headline_token', '', 'text-neutral-900');
    $headline_custom     = dclt_get_field($post->ID, 'stats_headline_custom', '', '');

    $number_color_mode = dclt_get_field($post->ID, 'stats_number_color_mode', '', 'token');
    $number_token      = dclt_get_field($post->ID, 'stats_number_token', '', 'text-brand-700');
    $number_custom     = dclt_get_field($post->ID, 'stats_number_custom', '', '');

    $label_color_mode = dclt_get_field($post->ID, 'stats_label_color_mode', '', 'token');
    $label_token      = dclt_get_field($post->ID, 'stats_label_token', '', 'text-neutral-700');
    $label_custom     = dclt_get_field($post->ID, 'stats_label_custom', '', '');

    ?>
    <style>
      .dclt-field { margin-bottom: 14px; }
      .dclt-field label { display:block; font-weight:600; margin-bottom:6px; }
      .dclt-row { display:flex; gap:16px; flex-wrap:wrap; }
      .dclt-col-3 { flex: 1 1 260px; min-width:260px; }
      .dclt-token-row { display:flex; gap:8px; align-items:center; }
      .dclt-help { color:#646970; font-size:12px; margin-top:4px; line-height:1.4; }
      .dclt-box { border:1px solid #ddd; border-radius:6px; padding:12px; margin:12px 0; }
      .dclt-icon-preview { 
        width: 60px; height: 60px; border: 1px solid #ddd; border-radius: 4px; 
        display: inline-flex; align-items: center; justify-content: center;
        background: #f9f9f9; margin-top: 8px;
      }
      .dclt-icon-preview svg { width: 40px; height: 40px; color: #059669; }
      .dclt-svg-field { 
        width: 100%; height: 80px; font-family: 'Courier New', monospace; 
        font-size: 11px; border: 1px solid #ddd; border-radius: 4px; padding: 8px;
      }
      .dclt-icon-row { display: flex; gap: 12px; align-items: flex-start; }
      .dclt-icon-controls { flex: 1; }
    </style>

    <div class="dclt-stats-meta">

      <div class="dclt-box">
        <h3>Section Content</h3>
        <div class="dclt-field">
            <label for="stats_kicker">Kicker (small intro)</label>
            <input type="text" id="stats_kicker" name="stats_kicker" value="<?php echo esc_attr($kicker); ?>" style="width:100%;max-width:480px;">
            <div class="dclt-help">Optional text above the headline, like "Since 2009"</div>
        </div>
        <div class="dclt-field">
            <label for="stats_title">Headline</label>
            <input type="text" id="stats_title" name="stats_title" value="<?php echo esc_attr($title); ?>" style="width:100%;max-width:480px;">
            <div class="dclt-help">Main headline for the stats section</div>
        </div>
        <div class="dclt-field">
            <label for="stats_desc">Description</label>
            <textarea id="stats_desc" name="stats_desc" rows="2" style="width:100%;max-width:640px;"><?php echo esc_textarea($desc); ?></textarea>
            <div class="dclt-help">Optional description text below the headline</div>
        </div>
      </div>

      <div class="dclt-box">
        <h3>Stats (3 items)</h3>
        <div class="dclt-row">
        <?php for ($i=1; $i<=3; $i++): ?>
          <div class="dclt-col-3">
            <strong>Item <?php echo $i; ?></strong>
            
            <div class="dclt-field">
              <label for="stats_item<?php echo $i; ?>_value">Number</label>
              <input type="text" id="stats_item<?php echo $i; ?>_value" name="stats_item<?php echo $i; ?>_value" value="<?php echo esc_attr($items[$i]['value']); ?>" style="width:100%;">
              <div class="dclt-help">Examples: 2,847 or 2.8M</div>
            </div>
            
            <div class="dclt-field">
              <label for="stats_item<?php echo $i; ?>_suffix">Suffix</label>
              <input type="text" id="stats_item<?php echo $i; ?>_suffix" name="stats_item<?php echo $i; ?>_suffix" value="<?php echo esc_attr($items[$i]['suffix']); ?>" style="width:100%;">
              <div class="dclt-help">Examples: acres, landowners, preserves (optional)</div>
            </div>
            
            <div class="dclt-field">
              <label for="stats_item<?php echo $i; ?>_label">Label</label>
              <input type="text" id="stats_item<?php echo $i; ?>_label" name="stats_item<?php echo $i; ?>_label" value="<?php echo esc_attr($items[$i]['label']); ?>" style="width:100%;">
              <div class="dclt-help">Description text below the number</div>
            </div>

            <div class="dclt-field">
              <label for="stats_item<?php echo $i; ?>_icon">Icon</label>
              <div class="dclt-icon-row">
                <div class="dclt-icon-controls">
                  <select id="stats_item<?php echo $i; ?>_icon" name="stats_item<?php echo $i; ?>_icon" style="width:100%;">
                    <?php foreach($icon_presets as $val => $label): ?>
                      <option value="<?php echo esc_attr($val); ?>" <?php selected($items[$i]['icon'], $val); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="dclt-help">Choose a preset icon or use custom SVG below</div>
                </div>
                <div class="dclt-icon-preview" id="icon_preview_<?php echo $i; ?>">
                  <!-- Preview will be populated by JavaScript -->
                  <span style="color:#999;font-size:10px;">Preview</span>
                </div>
              </div>
            </div>

            <div class="dclt-field" id="custom_svg_field_<?php echo $i; ?>" style="<?php echo $items[$i]['icon'] !== 'custom' ? 'display:none;' : ''; ?>">
              <label for="stats_item<?php echo $i; ?>_icon_svg">Custom SVG Code</label>
              <textarea class="dclt-svg-field" id="stats_item<?php echo $i; ?>_icon_svg" name="stats_item<?php echo $i; ?>_icon_svg" placeholder="<svg viewBox='0 0 100 100'>&#10;  <circle cx='50' cy='50' r='20' fill='currentColor'/>&#10;</svg>"><?php echo esc_textarea($items[$i]['icon_svg']); ?></textarea>
              <div class="dclt-help">Paste your custom SVG code here. Use 'currentColor' for fills to match your color scheme. Keep it simple for best results.</div>
            </div>
            
          </div>
        <?php endfor; ?>
        </div>
      </div>

      <div class="dclt-box">
        <h3>Layout</h3>
        <div class="dclt-field">
          <label for="stats_container">Container Width</label>
          <select id="stats_container" name="stats_container">
            <option value="content" <?php selected($container,'content'); ?>>Content (max-w-6xl)</option>
            <option value="wide" <?php selected($container,'wide'); ?>>Wide (max-w-7xl)</option>
            <option value="full" <?php selected($container,'full'); ?>>Full width</option>
          </select>
        </div>
        <div class="dclt-field">
          <label for="stats_spacing">Vertical Spacing</label>
          <select id="stats_spacing" name="stats_spacing">
            <option value="compact" <?php selected($spacing,'compact'); ?>>Compact</option>
            <option value="medium" <?php selected($spacing,'medium'); ?>>Medium</option>
            <option value="spacious" <?php selected($spacing,'spacious'); ?>>Spacious</option>
          </select>
        </div>
      </div>

      <div class="dclt-box">
        <h3>Colors (Design Token first, custom optional)</h3>

        <div class="dclt-field">
          <label>Headline Color</label>
          <div class="dclt-token-row">
            <select name="stats_headline_color_mode">
              <option value="token" <?php selected($headline_color_mode,'token'); ?>>Use token</option>
              <option value="custom" <?php selected($headline_color_mode,'custom'); ?>>Custom hex</option>
            </select>
            <select name="stats_headline_token">
              <?php foreach($token_presets as $val=>$label): ?>
                <option value="<?php echo esc_attr($val); ?>" <?php selected($headline_token,$val); ?>><?php echo esc_html($label); ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="stats_headline_custom" value="<?php echo esc_attr($headline_custom); ?>" placeholder="#006847" style="width:120px;">
          </div>
          <div class="dclt-help">Recommend a token; custom only if necessary.</div>
        </div>

        <div class="dclt-field">
          <label>Number Color</label>
          <div class="dclt-token-row">
            <select name="stats_number_color_mode">
              <option value="token" <?php selected($number_color_mode,'token'); ?>>Use token</option>
              <option value="custom" <?php selected($number_color_mode,'custom'); ?>>Custom hex</option>
            </select>
            <select name="stats_number_token">
              <?php foreach($token_presets as $val=>$label): ?>
                <option value="<?php echo esc_attr($val); ?>" <?php selected($number_token,$val); ?>><?php echo esc_html($label); ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="stats_number_custom" value="<?php echo esc_attr($number_custom); ?>" placeholder="#065f46" style="width:120px;">
          </div>
        </div>

        <div class="dclt-field">
          <label>Label Color</label>
          <div class="dclt-token-row">
            <select name="stats_label_color_mode">
              <option value="token" <?php selected($label_color_mode,'token'); ?>>Use token</option>
              <option value="custom" <?php selected($label_color_mode,'custom'); ?>>Custom hex</option>
            </select>
            <select name="stats_label_token">
              <?php foreach($token_presets as $val=>$label): ?>
                <option value="<?php echo esc_attr($val); ?>" <?php selected($label_token,$val); ?>><?php echo esc_html($label); ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="stats_label_custom" value="<?php echo esc_attr($label_custom); ?>" placeholder="#374151" style="width:120px;">
          </div>
        </div>

      </div>
    </div>

    <script>
    jQuery(function($) {
        // Handle icon dropdown changes
        $('[id^="stats_item"][id$="_icon"]').on('change', function() {
            const itemNum = $(this).attr('id').match(/stats_item(\d+)_icon/)[1];
            const selectedIcon = $(this).val();
            const customField = $('#custom_svg_field_' + itemNum);
            
            if (selectedIcon === 'custom') {
                customField.show();
            } else {
                customField.hide();
            }
            
            updateIconPreview(itemNum, selectedIcon);
        });

        // Update icon previews on page load
        $('[id^="stats_item"][id$="_icon"]').each(function() {
            const itemNum = $(this).attr('id').match(/stats_item(\d+)_icon/)[1];
            const selectedIcon = $(this).val();
            updateIconPreview(itemNum, selectedIcon);
        });

        function updateIconPreview(itemNum, iconType) {
            const previewEl = $('#icon_preview_' + itemNum);
            const presetIcons = {
                'tree': '<svg viewBox="0 0 100 100"><rect x="45" y="65" width="10" height="25" fill="currentColor" opacity="0.7"/><circle cx="50" cy="45" r="20" fill="currentColor"/></svg>',
                'handshake': '<svg viewBox="0 0 100 100"><path d="M20 50 Q35 35 45 50 L48 55 Q43 65 30 60 Q20 65 15 50 Z" fill="currentColor"/><path d="M52 45 Q57 35 72 45 Q80 50 75 60 Q65 70 52 65 L48 55 Q52 45 52 45 Z" fill="currentColor" opacity="0.8"/><circle cx="50" cy="52" r="4" fill="#fbbf24"/></svg>',
                'landscape': '<svg viewBox="0 0 100 100"><polygon points="25,75 50,25 75,75" fill="currentColor"/><ellipse cx="50" cy="80" rx="25" ry="6" fill="currentColor" opacity="0.6"/></svg>',
                'bird': '<svg viewBox="0 0 100 100"><path d="M15 60 Q50 25 85 45" stroke="currentColor" stroke-width="3" fill="none" opacity="0.7"/><path d="M80 40 L88 43 L86 47 L80 45 L82 49 L78 47 Z" fill="currentColor"/></svg>',
                'shield': '<svg viewBox="0 0 100 100"><path d="M50 15 L70 25 L70 55 Q70 75 50 85 Q30 75 30 55 L30 25 Z" fill="currentColor"/><path d="M42 45 L48 52 L62 35" stroke="white" stroke-width="4" fill="none" stroke-linecap="round"/></svg>',
                'leaf': '<svg viewBox="0 0 100 100"><path d="M50 15 Q35 30 40 50 Q45 70 50 85 Q55 70 60 50 Q65 30 50 15 Z" fill="currentColor"/><path d="M50 25 Q52 45 50 65" stroke="white" stroke-width="2" opacity="0.7"/></svg>',
                'default': '<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="20" fill="currentColor"/></svg>'
            };

            if (iconType === 'custom') {
                const customSvg = $('#stats_item' + itemNum + '_icon_svg').val();
                if (customSvg.trim()) {
                    previewEl.html(customSvg);
                } else {
                    previewEl.html('<span style="color:#999;font-size:10px;">Custom</span>');
                }
            } else if (presetIcons[iconType]) {
                previewEl.html(presetIcons[iconType]);
            } else {
                previewEl.html(presetIcons['default']);
            }
        }

        // Update custom SVG preview when textarea changes
        $('[id^="stats_item"][id$="_icon_svg"]').on('input', function() {
            const itemNum = $(this).attr('id').match(/stats_item(\d+)_icon_svg/)[1];
            const selectedIcon = $('#stats_item' + itemNum + '_icon').val();
            if (selectedIcon === 'custom') {
                updateIconPreview(itemNum, 'custom');
            }
        });
    });
    </script>
    <?php
}

/**
 * Save handler
 */
function dclt_save_stats_meta_box($post_id) {
    if (!isset($_POST['dclt_stats_meta_box_nonce']) || !wp_verify_nonce($_POST['dclt_stats_meta_box_nonce'], 'dclt_stats_meta_box')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields = [
        'stats_kicker','stats_title','stats_desc',
        'stats_container','stats_spacing',
        'stats_headline_color_mode','stats_headline_token','stats_headline_custom',
        'stats_number_color_mode','stats_number_token','stats_number_custom',
        'stats_label_color_mode','stats_label_token','stats_label_custom',
    ];
    
    for ($i=1; $i<=3; $i++) {
        $fields[] = "stats_item{$i}_label";
        $fields[] = "stats_item{$i}_value";
        $fields[] = "stats_item{$i}_suffix";
        $fields[] = "stats_item{$i}_icon";
        $fields[] = "stats_item{$i}_icon_svg";
    }

    foreach ($fields as $f) {
        if (isset($_POST[$f])) {
            $value = $_POST[$f];
            
            // Special handling for SVG fields - more permissive sanitization
            if (strpos($f, '_icon_svg') !== false) {
                // Basic SVG sanitization - allow common SVG tags and attributes
                $allowed_tags = [
                    'svg' => ['viewBox' => [], 'width' => [], 'height' => [], 'xmlns' => [], 'class' => []],
                    'path' => ['d' => [], 'fill' => [], 'stroke' => [], 'stroke-width' => [], 'opacity' => [], 'class' => []],
                    'circle' => ['cx' => [], 'cy' => [], 'r' => [], 'fill' => [], 'stroke' => [], 'opacity' => [], 'class' => []],
                    'rect' => ['x' => [], 'y' => [], 'width' => [], 'height' => [], 'fill' => [], 'stroke' => [], 'opacity' => [], 'class' => []],
                    'polygon' => ['points' => [], 'fill' => [], 'stroke' => [], 'opacity' => [], 'class' => []],
                    'ellipse' => ['cx' => [], 'cy' => [], 'rx' => [], 'ry' => [], 'fill' => [], 'stroke' => [], 'opacity' => [], 'class' => []],
                    'line' => ['x1' => [], 'y1' => [], 'x2' => [], 'y2' => [], 'stroke' => [], 'stroke-width' => [], 'class' => []],
                    'g' => ['class' => [], 'transform' => []],
                ];
                $value = wp_kses($value, $allowed_tags);
            } else {
                $value = sanitize_text_field($value);
            }
            
            dclt_update_field($post_id, $f, $value);
        }
    }
}
add_action('save_post', 'dclt_save_stats_meta_box');