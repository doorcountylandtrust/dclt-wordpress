<?php
// Meta box UI + save handlers for Stats (MVP)
if (!defined('ABSPATH')) exit;

function dclt_add_stats_meta_box(){
  foreach (['page','post'] as $pt){
    add_meta_box('dclt-stats-fields','Stats Block Settings','dclt_stats_meta_box_callback',$pt,'normal','high');
  }
}
add_action('add_meta_boxes', 'dclt_add_stats_meta_box');

function dclt_stats_meta_box_callback($post){
  wp_nonce_field('dclt_stats_meta_box','dclt_stats_meta_box_nonce');

  $bid   = ''; // not needed for simple meta (post-wide block)
  $title = dclt_get_field($post->ID,'stats_title',$bid,'Our Impact');
  $kick  = dclt_get_field($post->ID,'stats_kicker',$bid,'');
  $desc  = dclt_get_field($post->ID,'stats_desc',$bid,'');

  $defaults = [
    1=>['Acres Preserved','10000',''],
    2=>['Landowners Helped','250',''],
    3=>['Preserves Established','36',''],
  ];
  ?>
  <style>
    .dclt-stats-admin .row{display:grid;grid-template-columns:180px 1fr;gap:10px;margin:8px 0;}
    .dclt-stats-admin input[type=text]{width:100%;}
    .dclt-stats-admin .grid{display:grid;grid-template-columns:1fr 160px 120px;gap:10px;}
    .description{color:#555;}
  </style>
  <div class="dclt-stats-admin">
    <div class="row"><label>Section Title</label><input type="text" name="stats_title" value="<?php echo esc_attr($title); ?>"></div>
    <div class="row"><label>Kicker (optional)</label><input type="text" name="stats_kicker" value="<?php echo esc_attr($kick); ?>"></div>
    <div class="row"><label>Description (optional)</label><input type="text" name="stats_desc" value="<?php echo esc_attr($desc); ?>"></div>

    <hr/>
    <p><strong>Stats</strong> <span class="description">(numbers only; suffix optional, e.g., “acres”)</span></p>
    <?php for($i=1;$i<=3;$i++):
      $label = dclt_get_field($post->ID,"stats_{$i}_label",$bid,$defaults[$i][0]);
      $value = dclt_get_field($post->ID,"stats_{$i}_value",$bid,$defaults[$i][1]);
      $suffix= dclt_get_field($post->ID,"stats_{$i}_suffix",$bid,$defaults[$i][2]);
    ?>
      <div class="grid">
        <input type="text" name="stats_<?php echo $i; ?>_label"  value="<?php echo esc_attr($label); ?>"  placeholder="Label">
        <input type="text" name="stats_<?php echo $i; ?>_value"  value="<?php echo esc_attr($value); ?>"  placeholder="12345">
        <input type="text" name="stats_<?php echo $i; ?>_suffix" value="<?php echo esc_attr($suffix); ?>" placeholder="suffix">
      </div>
    <?php endfor; ?>
  </div>
  <?php
}

function dclt_save_stats_meta_box($post_id){
  if (!isset($_POST['dclt_stats_meta_box_nonce']) || !wp_verify_nonce($_POST['dclt_stats_meta_box_nonce'],'dclt_stats_meta_box')) return;
  if (!current_user_can('edit_post',$post_id)) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

  $simple = ['stats_title','stats_kicker','stats_desc'];
  foreach($simple as $k){
    if (isset($_POST[$k])) dclt_update_field($post_id,$k,sanitize_text_field($_POST[$k]));
  }
  for($i=1;$i<=3;$i++){
    foreach(['label','value','suffix'] as $part){
      $key = "stats_{$i}_{$part}";
      if (isset($_POST[$key])){
        $val = $part==='value' ? preg_replace('/[^0-9]/','',$_POST[$key]) : sanitize_text_field($_POST[$key]);
        dclt_update_field($post_id,$key,$val);
      }
    }
  }
}
add_action('save_post','dclt_save_stats_meta_box');