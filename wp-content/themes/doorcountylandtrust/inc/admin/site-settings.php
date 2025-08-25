<?php
// inc/admin/site-settings.php
if (!defined('ABSPATH')) exit;

function dclt_get_option($key, $default = '') {
  $val = get_option("dclt_$key");
  return ($val !== false && $val !== null && $val !== '') ? $val : $default;
}

add_action('admin_menu', function () {
  add_options_page(
    'DCLT Site Settings',
    'DCLT Site Settings',
    'manage_options',
    'dclt-site-settings',
    'dclt_site_settings_page'
  );
});

add_action('admin_init', function () {
  register_setting('dclt_settings_group', 'dclt_org_name',        ['sanitize_callback' => 'sanitize_text_field']);
  register_setting('dclt_settings_group', 'dclt_org_address',     ['sanitize_callback' => 'sanitize_text_field']);
  register_setting('dclt_settings_group', 'dclt_org_phone',       ['sanitize_callback' => 'sanitize_text_field']);
  register_setting('dclt_settings_group', 'dclt_org_email',       ['sanitize_callback' => 'sanitize_email']);
  register_setting('dclt_settings_group', 'dclt_org_hours',       ['sanitize_callback' => 'sanitize_textarea_field']);
  register_setting('dclt_settings_group', 'dclt_org_ein',         ['sanitize_callback' => 'sanitize_text_field']);
  register_setting('dclt_settings_group', 'dclt_newsletter_url',  ['sanitize_callback' => 'esc_url_raw']);
  register_setting('dclt_settings_group', 'dclt_social_facebook', ['sanitize_callback' => 'esc_url_raw']);
  register_setting('dclt_settings_group', 'dclt_social_instagram',['sanitize_callback' => 'esc_url_raw']);
  register_setting('dclt_settings_group', 'dclt_social_youtube',  ['sanitize_callback' => 'esc_url_raw']);
  register_setting('dclt_settings_group', 'dclt_donate_url',      ['sanitize_callback' => 'esc_url_raw']);
});

function dclt_site_settings_page() {
  if (!current_user_can('manage_options')) return;
  ?>
  <div class="wrap">
    <h1>DCLT Site Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields('dclt_settings_group'); ?>

      <h2 class="title">Organization</h2>
      <table class="form-table" role="presentation">
        <tr>
          <th scope="row"><label for="dclt_org_name">Organization Name</label></th>
          <td><input name="dclt_org_name" id="dclt_org_name" type="text" class="regular-text"
                     value="<?php echo esc_attr( dclt_get_option('org_name', 'Door County Land Trust') ); ?>"></td>
        </tr>
        <tr>
          <th scope="row"><label for="dclt_org_address">Address</label></th>
          <td><input name="dclt_org_address" id="dclt_org_address" type="text" class="regular-text"
                     value="<?php echo esc_attr( dclt_get_option('org_address', 'P.O. Box 65, Sturgeon Bay, WI 54235') ); ?>"></td>
        </tr>
        <tr>
          <th scope="row"><label for="dclt_org_phone">Phone</label></th>
          <td><input name="dclt_org_phone" id="dclt_org_phone" type="text" class="regular-text"
                     value="<?php echo esc_attr( dclt_get_option('org_phone', '920-000-0000') ); ?>"></td>
        </tr>
        <tr>
          <th scope="row"><label for="dclt_org_email">Email</label></th>
          <td><input name="dclt_org_email" id="dclt_org_email" type="email" class="regular-text"
                     value="<?php echo esc_attr( dclt_get_option('org_email', 'info@doorcountylandtrust.org') ); ?>"></td>
        </tr>
        <tr>
          <th scope="row"><label for="dclt_org_hours">Hours</label></th>
          <td><textarea name="dclt_org_hours" id="dclt_org_hours" class="large-text" rows="3"><?php echo esc_textarea( dclt_get_option('org_hours', '') ); ?></textarea></td>
        </tr>
        <tr>
          <th scope="row"><label for="dclt_org_ein">EIN</label></th>
          <td><input name="dclt_org_ein" id="dclt_org_ein" type="text" class="regular-text"
                     value="<?php echo esc_attr( dclt_get_option('org_ein', 'XX-XXXXXXX') ); ?>"></td>
        </tr>
      </table>

      <h2 class="title">Engagement</h2>
      <table class="form-table" role="presentation">
        <tr>
          <th scope="row"><label for="dclt_donate_url">Donate URL</label></th>
          <td><input name="dclt_donate_url" id="dclt_donate_url" type="url" class="regular-text"
                     value="<?php echo esc_attr( dclt_get_option('donate_url', '/donate/') ); ?>"></td>
        </tr>
        <tr>
          <th scope="row"><label for="dclt_newsletter_url">Newsletter Signup URL</label></th>
          <td><input name="dclt_newsletter_url" id="dclt_newsletter_url" type="url" class="regular-text"
                     value="<?php echo esc_attr( dclt_get_option('newsletter_url', '#') ); ?>"></td>
        </tr>
      </table>

      <h2 class="title">Social</h2>
      <table class="form-table" role="presentation">
        <tr>
          <th scope="row"><label for="dclt_social_facebook">Facebook</label></th>
          <td><input name="dclt_social_facebook" id="dclt_social_facebook" type="url" class="regular-text"
                     value="<?php echo esc_attr( dclt_get_option('social_facebook', '') ); ?>"></td>
        </tr>
        <tr>
          <th scope="row"><label for="dclt_social_instagram">Instagram</label></th>
          <td><input name="dclt_social_instagram" id="dclt_social_instagram" type="url" class="regular-text"
                     value="<?php echo esc_attr( dclt_get_option('social_instagram', '') ); ?>"></td>
        </tr>
        <tr>
          <th scope="row"><label for="dclt_social_youtube">YouTube</label></th>
          <td><input name="dclt_social_youtube" id="dclt_social_youtube" type="url" class="regular-text"
                     value="<?php echo esc_attr( dclt_get_option('social_youtube', '') ); ?>"></td>
        </tr>
      </table>

      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}