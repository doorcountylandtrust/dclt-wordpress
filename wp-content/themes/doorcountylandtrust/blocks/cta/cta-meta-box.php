<?php
/**
 * CTA Block Meta Box
 * File: blocks/cta/cta-meta-box.php
 */
if (!defined('ABSPATH')) { exit; }

/**
 * Register the CTA meta box in the MAIN column
 */
function dclt_add_cta_meta_box() {
    foreach (['page','post'] as $pt) {
        add_meta_box(
            'dclt-cta-fields',
            'CTA Block Settings',
            'dclt_cta_meta_box_callback',
            $pt,
            'normal',
            'high'
        );
    }
}

/**
 * Meta box UI
 */
function dclt_cta_meta_box_callback($post) {
    // Nonce
    wp_nonce_field('dclt_cta_meta_box', 'dclt_cta_meta_box_nonce');

    // Read current values (using your native meta helpers)
    $layout          = dclt_get_field($post->ID, 'cta_layout', '', 'split');           // split | stacked
    $bg_style        = dclt_get_field($post->ID, 'cta_background_style', '', 'brand-primary'); // brand-primary | light | image
    $container_width = dclt_get_field($post->ID, 'cta_container_width', '', 'content'); // content | wide | full
    $spacing         = dclt_get_field($post->ID, 'cta_spacing', '', 'medium');         // compact | medium | spacious

    $icon_id         = dclt_get_field($post->ID, 'cta_icon_id', '', '');
    $headline        = dclt_get_field($post->ID, 'cta_headline', '', '');
    $description     = dclt_get_field($post->ID, 'cta_description', '', '');
    $urgency         = dclt_get_field($post->ID, 'cta_show_urgency', '', '0');

    $primary_text    = dclt_get_field($post->ID, 'cta_primary_text', '', '');
    $primary_type    = dclt_get_field($post->ID, 'cta_primary_type', '', 'link');      // link | phone | email
    $primary_target  = dclt_get_field($post->ID, 'cta_primary_target', '', '');
    $primary_style   = dclt_get_field($post->ID, 'cta_primary_style', '', 'primary');  // primary | secondary | outline

    $secondary_text  = dclt_get_field($post->ID, 'cta_secondary_text', '', '');
    $secondary_target= dclt_get_field($post->ID, 'cta_secondary_target', '', '');
    $secondary_style = dclt_get_field($post->ID, 'cta_secondary_style', '', 'secondary');

    $bg_image_id     = dclt_get_field($post->ID, 'cta_background_image', '', '');
    ?>
    <style>
      .dclt-field { margin-bottom:16px; }
      .dclt-field label { display:block; font-weight:600; margin-bottom:6px; }
      .dclt-field input[type="text"],
      .dclt-field input[type="url"],
      .dclt-field textarea,
      .dclt-field select { width:100%; max-width:540px; }
      .dclt-inline { display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap; }
      .dclt-image-preview img { max-width:200px; border:1px solid #ddd; border-radius:4px; margin-top:8px; display:block; }
    </style>

    <div class="dclt-field">
      <label>Layout & Style</label>
      <div class="dclt-inline">
        <div>
          <label for="cta_layout">Layout Style</label>
          <select id="cta_layout" name="cta_layout">
            <option value="split"   <?php selected($layout, 'split'); ?>>Split — content left, actions right</option>
            <option value="stacked" <?php selected($layout, 'stacked'); ?>>Stacked — content then actions</option>
          </select>
        </div>
        <div>
          <label for="cta_background_style">Background Style</label>
          <select id="cta_background_style" name="cta_background_style">
            <option value="brand-primary" <?php selected($bg_style, 'brand-primary'); ?>>Brand Primary (green)</option>
            <option value="light"         <?php selected($bg_style, 'light'); ?>>Light</option>
            <option value="image"         <?php selected($bg_style, 'image'); ?>>Image</option>
          </select>
        </div>
        <div>
          <label for="cta_container_width">Container Width</label>
          <select id="cta_container_width" name="cta_container_width">
            <option value="content" <?php selected($container_width, 'content'); ?>>Content</option>
            <option value="wide"    <?php selected($container_width, 'wide'); ?>>Wide</option>
            <option value="full"    <?php selected($container_width, 'full'); ?>>Full</option>
          </select>
        </div>
        <div>
          <label for="cta_spacing">Section Spacing</label>
          <select id="cta_spacing" name="cta_spacing">
            <option value="compact"  <?php selected($spacing, 'compact'); ?>>Compact</option>
            <option value="medium"   <?php selected($spacing, 'medium'); ?>>Medium</option>
            <option value="spacious" <?php selected($spacing, 'spacious'); ?>>Spacious</option>
          </select>
        </div>
      </div>
    </div>

    <div class="dclt-field">
      <label>Content</label>
      <div class="dclt-inline">
        <div>
          <label for="cta_headline">Headline</label>
          <input type="text" id="cta_headline" name="cta_headline"
                 value="<?php echo esc_attr($headline); ?>" placeholder="Ready to Protect Your Land?">
        </div>
        <div>
          <label for="cta_show_urgency">
            <input type="checkbox" id="cta_show_urgency" name="cta_show_urgency" value="1" <?php checked($urgency, '1'); ?> />
            Show urgency badge
          </label>
        </div>
      </div>

      <div style="margin-top:8px;">
        <label for="cta_description">Description</label>
        <textarea id="cta_description" name="cta_description" rows="3"
                  placeholder="A short supporting sentence."><?php echo esc_textarea($description); ?></textarea>
      </div>

      <div style="margin-top:8px;">
        <label for="cta_icon_id">Icon (optional)</label>
        <input type="hidden" id="cta_icon_id" name="cta_icon_id" value="<?php echo esc_attr($icon_id); ?>">
        <button type="button" class="button dclt-icon-choose">Choose Icon/Image</button>
        <button type="button" class="button dclt-icon-remove" style="<?php echo $icon_id ? '' : 'display:none;'; ?>">Remove</button>
        <div class="dclt-image-preview"><?php echo $icon_id ? wp_get_attachment_image($icon_id, 'thumbnail') : ''; ?></div>
      </div>
    </div>

    <div class="dclt-field">
      <label>Primary Action</label>
      <div class="dclt-inline">
        <div>
          <label for="cta_primary_text">Button Text</label>
          <input type="text" id="cta_primary_text" name="cta_primary_text" value="<?php echo esc_attr($primary_text); ?>" placeholder="Contact Us Today">
        </div>
        <div>
          <label for="cta_primary_type">Type</label>
          <select id="cta_primary_type" name="cta_primary_type">
            <option value="link"  <?php selected($primary_type, 'link'); ?>>Link</option>
            <option value="phone" <?php selected($primary_type, 'phone'); ?>>Phone</option>
            <option value="email" <?php selected($primary_type, 'email'); ?>>Email</option>
          </select>
        </div>
        <div>
          <label for="cta_primary_target">URL / Phone / Email</label>
          <input type="text" id="cta_primary_target" name="cta_primary_target"
                 value="<?php echo esc_attr($primary_target); ?>" placeholder="https://… or 920‑555‑1234 or info@example.org">
        </div>
        <div>
          <label for="cta_primary_style">Style</label>
          <select id="cta_primary_style" name="cta_primary_style">
            <option value="primary"   <?php selected($primary_style, 'primary'); ?>>Primary</option>
            <option value="secondary" <?php selected($primary_style, 'secondary'); ?>>Secondary</option>
            <option value="outline"   <?php selected($primary_style, 'outline'); ?>>Outline</option>
          </select>
        </div>
      </div>
    </div>

    <div class="dclt-field">
      <label>Secondary Action</label>
      <div class="dclt-inline">
        <div>
          <label for="cta_secondary_text">Button Text</label>
          <input type="text" id="cta_secondary_text" name="cta_secondary_text" value="<?php echo esc_attr($secondary_text); ?>" placeholder="Learn More">
        </div>
        <div>
          <label for="cta_secondary_target">URL</label>
          <input type="url" id="cta_secondary_target" name="cta_secondary_target" value="<?php echo esc_attr($secondary_target); ?>" placeholder="https://…">
        </div>
        <div>
          <label for="cta_secondary_style">Style</label>
          <select id="cta_secondary_style" name="cta_secondary_style">
            <option value="secondary" <?php selected($secondary_style, 'secondary'); ?>>Secondary</option>
            <option value="primary"   <?php selected($secondary_style, 'primary'); ?>>Primary</option>
            <option value="outline"   <?php selected($secondary_style, 'outline'); ?>>Outline</option>
          </select>
        </div>
      </div>
    </div>

    <div class="dclt-field" id="cta_bg_image_wrap" style="<?php echo $bg_style==='image' ? '' : 'display:none;'; ?>">
      <label for="cta_background_image">Background Image</label>
      <input type="hidden" id="cta_background_image" name="cta_background_image" value="<?php echo esc_attr($bg_image_id); ?>">
      <button type="button" class="button dclt-bg-choose">Choose Image</button>
      <button type="button" class="button dclt-bg-remove" style="<?php echo $bg_image_id ? '' : 'display:none;'; ?>">Remove</button>
      <div class="dclt-image-preview"><?php echo $bg_image_id ? wp_get_attachment_image($bg_image_id, 'large') : ''; ?></div>
    </div>

    <script>
    jQuery(function($){
      $('#cta_background_style').on('change', function(){
        $('#cta_bg_image_wrap').toggle($(this).val()==='image');
      });

      function pickImage(onSelect) {
        const frame = wp.media({ title:'Choose Image', button:{ text:'Use this image' }, multiple:false, library:{ type:'image' }});
        frame.on('select', function(){
          const a = frame.state().get('selection').first().toJSON();
          onSelect(a);
        });
        frame.open();
      }

      $('.dclt-icon-choose').on('click', function(e){
        e.preventDefault();
        pickImage(function(a){
          $('#cta_icon_id').val(a.id);
          $('.dclt-icon-remove').show();
          $('.dclt-image-preview').first().html('<img src="'+a.url+'" alt="">');
        });
      });
      $('.dclt-icon-remove').on('click', function(e){
        e.preventDefault();
        $('#cta_icon_id').val('');
        $(this).hide();
        $('.dclt-image-preview').first().empty();
      });

      $('.dclt-bg-choose').on('click', function(e){
        e.preventDefault();
        pickImage(function(a){
          $('#cta_background_image').val(a.id);
          $('.dclt-bg-remove').show();
          $('#cta_bg_image_wrap .dclt-image-preview').html('<img src="'+a.url+'" alt="">');
        });
      });
      $('.dclt-bg-remove').on('click', function(e){
        e.preventDefault();
        $('#cta_background_image').val('');
        $(this).hide();
        $('#cta_bg_image_wrap .dclt-image-preview').empty();
      });
    });
    </script>
    <?php
}

/**
 * Save handler (defensive)
 */
function dclt_save_cta_meta_box($post_id) {
    // Guards to prevent REST 500 / autosave issues
    if (!isset($_POST['dclt_cta_meta_box_nonce']) || !wp_verify_nonce($_POST['dclt_cta_meta_box_nonce'], 'dclt_cta_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields_text = [
        'cta_layout','cta_background_style','cta_container_width','cta_spacing',
        'cta_headline','cta_description',
        'cta_primary_text','cta_primary_type','cta_primary_target','cta_primary_style',
        'cta_secondary_text','cta_secondary_target','cta_secondary_style',
    ];
    foreach ($fields_text as $f) {
        if (!isset($_POST[$f])) continue;
        $val = in_array($f, ['cta_secondary_target','cta_primary_target'], true)
            ? esc_url_raw($_POST[$f])
            : sanitize_text_field($_POST[$f]);
        dclt_update_field($post_id, $f, $val);
    }

    // Media IDs (ints)
    if (isset($_POST['cta_icon_id'])) {
        dclt_update_field($post_id, 'cta_icon_id', (string)absint($_POST['cta_icon_id']));
    }
    if (isset($_POST['cta_background_image'])) {
        dclt_update_field($post_id, 'cta_background_image', (string)absint($_POST['cta_background_image']));
    }

    // Checkbox
    dclt_update_field($post_id, 'cta_show_urgency', isset($_POST['cta_show_urgency']) ? '1' : '0');
}