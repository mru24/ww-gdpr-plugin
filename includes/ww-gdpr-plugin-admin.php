<?php

// CREATE MENU ITEM
function wwgcbar_menu_link() {
    if (!current_user_can('manage_options')) {
        return;
    }

    add_options_page(
        'WW GDPR Bar Link Options',
        'WW GDPR Bar Link',
        'manage_options',
        'wwgcbar-options',
        'wwgcbar_options_content'
    );
}
add_action('admin_menu', 'wwgcbar_menu_link');

// CREATE SETTINGS LINK
function wwgcbar_settings_link($links) {
    if (!current_user_can('manage_options')) {
        return $links;
    }

    $settings_link = '<a href="admin.php?page=wwgcbar-options">' . __('Settings') . '</a>';
    $links[] = $settings_link;
    return $links;
}

global $pluginFile;
add_filter('plugin_action_links_' . $pluginFile, 'wwgcbar_settings_link');

// SANITIZATION FUNCTION
function wwgcbar_sanitize_settings($input) {
    $sanitized = array();

    // Sanitize checkbox fields
    $sanitized['enable'] = !empty($input['enable']) ? 1 : 0;
    $sanitized['position'] = !empty($input['position']) ? 1 : 0;
    $sanitized['pp_target'] = !empty($input['pp_target']) ? 1 : 0;
    $sanitized['buttons_swap'] = !empty($input['buttons_swap']) ? 1 : 0;
    $sanitized['cookies_non_essential'] = !empty($input['cookies_non_essential']) ? 1 : 0;

    // Sanitize text fields
    $sanitized['content'] = !empty($input['content']) ? wp_kses_post($input['content']) : '';
    $sanitized['content1'] = !empty($input['content1']) ? wp_kses_post($input['content1']) : '';
    $sanitized['content2'] = !empty($input['content2']) ? wp_kses_post($input['content2']) : '';
    $sanitized['content3'] = !empty($input['content3']) ? wp_kses_post($input['content3']) : '';
    $sanitized['content4'] = !empty($input['content4']) ? wp_kses_post($input['content4']) : '';

    // Sanitize color fields
    $sanitized['content_col'] = !empty($input['content_col']) ? sanitize_hex_color($input['content_col']) : '';
    $sanitized['content_bg'] = !empty($input['content_bg']) ? sanitize_hex_color($input['content_bg']) : '';
    $sanitized['content_col_link'] = !empty($input['content_col_link']) ? sanitize_hex_color($input['content_col_link']) : '';
    $sanitized['button_1_col'] = !empty($input['button_1_col']) ? sanitize_hex_color($input['button_1_col']) : '';
    $sanitized['button_1_bg'] = !empty($input['button_1_bg']) ? sanitize_hex_color($input['button_1_bg']) : '';
    $sanitized['button_2_col'] = !empty($input['button_2_col']) ? sanitize_hex_color($input['button_2_col']) : '';
    $sanitized['button_2_bg'] = !empty($input['button_2_bg']) ? sanitize_hex_color($input['button_2_bg']) : '';

    // Sanitize URL field
    $sanitized['pp_link'] = !empty($input['pp_link']) ? esc_url_raw($input['pp_link']) : '';

    // Sanitize text fields
    $sanitized['button_1_text'] = !empty($input['button_1_text']) ? sanitize_text_field($input['button_1_text']) : '';
    $sanitized['button_2_text'] = !empty($input['button_2_text']) ? sanitize_text_field($input['button_2_text']) : '';

    // Sanitize tracking code with strict rules
    $allowed_tracking_html = array(
        'script' => array(
            'src' => array(),
            'async' => array(),
            'defer' => array(),
            'charset' => array(),
            'type' => array(),
            'id' => array()
        ),
        'meta' => array(
            'name' => array(),
            'content' => array(),
            'charset' => array(),
            'http-equiv' => array()
        ),
        'noscript' => array(),
        'iframe' => array(
            'src' => array(),
            'width' => array(),
            'height' => array(),
            'frameborder' => array(),
            'allow' => array(),
            'allowfullscreen' => array()
        ),
        'link' => array(
            'rel' => array(),
            'href' => array(),
            'type' => array()
        )
    );
    $sanitized['content_tracking_code'] = !empty($input['content_tracking_code']) ? wp_kses($input['content_tracking_code'], $allowed_tracking_html) : '';

    // Sanitize custom shortcode HTML
    $allowed_shortcode_html = array(
        'a' => array(
            'href' => array(),
            'id' => array(),
            'class' => array(),
            'style' => array(),
            'title' => array(),
            'target' => array()
        ),
        'span' => array(
            'class' => array(),
            'style' => array(),
            'id' => array()
        ),
        'div' => array(
            'class' => array(),
            'style' => array(),
            'id' => array()
        ),
        'button' => array(
            'type' => array(),
            'class' => array(),
            'style' => array(),
            'id' => array(),
            'onclick' => array()
        ),
        'img' => array(
            'src' => array(),
            'alt' => array(),
            'class' => array(),
            'style' => array(),
            'width' => array(),
            'height' => array()
        )
    );
    $sanitized['cookie_shortcode'] = !empty($input['cookie_shortcode']) ? wp_kses($input['cookie_shortcode'], $allowed_shortcode_html) : '';

    return $sanitized;
}

function wwgcbar_render_template($template_name, $args = array()) {
  extract($args);
  $path = plugin_dir_path(__FILE__) . 'templates/' . $template_name . '.php';
  if (file_exists($path)) {
    include($path);
  }
}

function wwgcbar_options_content() {
    // Security check
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // Check if form was submitted and verify nonce
    if (isset($_POST['submit']) && check_admin_referer('wwgcbar_save_settings', 'wwgcbar_nonce')) {
        // Settings will be sanitized via wwgcbar_sanitize_settings
        add_settings_error(
            'wwgcbar_settings',
            'wwgcbar_settings_updated',
            __('Settings saved successfully.'),
            'success'
        );
    }

    // init options global
    global $wwgcbar_options;

    ob_start();
?>
<div class="wrap">
  <div class="wwgcbar-header">
    <h2><?php echo esc_html__('WW GDPR Bar Settings', 'wwgcbar_domain'); ?></h2>
    <?php settings_errors('wwgcbar_settings'); ?>
  </div>
  <div class="wwgcbar-content admin">
    <form method="post" action="options.php">
      <?php
        settings_fields('wwgcbar_settings_group');
        wp_nonce_field('wwgcbar_save_settings', 'wwgcbar_nonce');
      ?>
      <p class="submit" style="text-align:right;">
        <input type="submit" name="submit" id="submit" class="button button-primary disabled" value="<?php echo esc_attr__('Save changes', 'wwgcbar_domain'); ?>" />
      </p>

      <table class="form-table">
        <tbody>
<!-- BAR ENABLE -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_enable">
                <?php echo esc_html__('Status', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-switch', [
                  'id'        => 'enable',
                  'value'     => isset($wwgcbar_options['enable']) ? $wwgcbar_options['enable'] : 0,
                  'label_off' => __('Disabled', 'wwgcbar_domain'),
                  'label_on'  => __('Enabled', 'wwgcbar_domain')
                ]);
              ?>
            </td>
          </tr>
<!-- BAR POSITION -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_position">
                <?php echo esc_html__('Bar position', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-switch', [
                  'id'        => 'position',
                  'value'     => isset($wwgcbar_options['position']) ? $wwgcbar_options['position'] : 0,
                  'label_off' => __('Bottom', 'wwgcbar_domain'),
                  'label_on'  => __('Top', 'wwgcbar_domain')
                ]);
              ?>
            </td>
          </tr>
<!-- BAR TEXT CONTENT -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_content">
                <?php echo esc_html__('Bar text content', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-textarea', [
                  'id'    => 'content',
                  'value' => isset($wwgcbar_options['content']) ? $wwgcbar_options['content'] : '',
                  'class' => 'regular-text'
                ]);
              ?>
            </td>
          </tr>
<!-- BAR TEXT COLOR -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_content_col">
                <?php echo esc_html__('Bar text colour', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'content_col',
                  'value' => isset($wwgcbar_options['content_col']) ? $wwgcbar_options['content_col'] : '',
                  'class' => 'regular-text',
                  'placeholder' => '#ffffff'
                ]);
              ?>
            </td>
          </tr>
<!-- BAR BACKGROUND COLOR -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_content_bg">
                <?php echo esc_html__('Bar background colour', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'content_bg',
                  'value' => isset($wwgcbar_options['content_bg']) ? $wwgcbar_options['content_bg'] : '',
                  'class' => 'regular-text',
                  'placeholder' => '#000000'
                ]);
              ?>
            </td>
          </tr>
<!-- PRIVACY POLICY LINK -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_pp_link">
                <?php echo esc_html__('Privacy policy link', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'pp_link',
                  'value' => isset($wwgcbar_options['pp_link']) ? $wwgcbar_options['pp_link'] : '',
                  'class' => 'regular-text'
                ]);
              ?>
              <p class="description">
                <?php echo esc_html__('Privacy Policy page link', 'wwgcbar_domain'); ?>
              </p>
            </td>
          </tr>
<!-- PRIVACY POLICY OPEN TARGET -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_pp_target">
                <?php echo esc_html__('Open link in a new tab', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-switch', [
                  'id'        => 'pp_target',
                  'value'     => isset($wwgcbar_options['pp_target']) ? $wwgcbar_options['pp_target'] : 0,
                  'label_off' => __('Disabled', 'wwgcbar_domain'),
                  'label_on'  => __('Enabled', 'wwgcbar_domain')
                ]);
              ?>
            </td>
          </tr>
<!-- PRIVACY POLICY LINK COLOR -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_content_col_link">
                <?php echo esc_html__('Privacy policy link colour', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'content_col_link',
                  'value' => isset($wwgcbar_options['content_col_link']) ? $wwgcbar_options['content_col_link'] : '',
                  'class' => 'regular-text',
                  'placeholder' => '#ffffff'
                ]);
              ?>
            </td>
          </tr>
<!-- ACCEPT BUTTON -->
          <tr>
            <th>
              <strong><?php echo esc_html__('Accept button', 'wwgcbar_domain'); ?></strong>
            </th>
          </tr>
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_button_1_text">
                <?php echo esc_html__('Accept button text', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'button_1_text',
                  'value' => isset($wwgcbar_options['button_1_text']) ? $wwgcbar_options['button_1_text'] : '',
                  'class' => 'regular-text',
                  'placeholder' => '#000000'
                ]);
              ?>
            </td>
          </tr>
<!-- ACCEPT BUTTON TEXT COLOR -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_button_1_col">
                <?php echo esc_html__('Accept button text colour', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'button_1_col',
                  'value' => isset($wwgcbar_options['button_1_col']) ? $wwgcbar_options['button_1_col'] : '',
                  'class' => 'regular-text',
                  'placeholder' => '#ffffff'
                ]);
              ?>
            </td>
          </tr>
<!-- ACCEPT BUTTON BACKGROUND COLOR -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_button_1_bg">
                <?php echo esc_html__('Accept button background colour', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'button_1_bg',
                  'value' => isset($wwgcbar_options['button_1_bg']) ? $wwgcbar_options['button_1_bg'] : '',
                  'class' => 'regular-text',
                  'placeholder' => '#000000'
                ]);
              ?>
            </td>
          </tr>
<!-- SETTINGS BUTTON TEXT -->
          <tr>
            <th>
              <strong><?php echo esc_html__('Settings button', 'wwgcbar_domain'); ?></strong>
            </th>
          </tr>
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_button_2_text">
                <?php echo esc_html__('Settings button text', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'button_2_text',
                  'value' => isset($wwgcbar_options['button_2_text']) ? $wwgcbar_options['button_2_text'] : '',
                  'class' => 'regular-text',
                  'placeholder' => '#ffffff'
                ]);
              ?>
            </td>
          </tr>
<!-- SETTINGS BUTTON COLOR -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_button_2_col">
                <?php echo esc_html__('Setting button text colour', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'button_2_col',
                  'value' => isset($wwgcbar_options['button_2_col']) ? $wwgcbar_options['button_2_col'] : '',
                  'class' => 'regular-text',
                  'placeholder' => '#ffffff'
                ]);
              ?>
            </td>
          </tr>
<!-- SETTINGS BUTTON BAKGROUND -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_button_2_bg">
                <?php echo esc_html__('Settings button background colour', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'button_2_bg',
                  'value' => isset($wwgcbar_options['button_2_bg']) ? $wwgcbar_options['button_2_bg'] : '',
                  'class' => 'regular-text',
                  'placeholder' => '#000000'
                ]);
              ?>
            </td>
          </tr>
<!-- SWAP BUTTONS PLACES -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_buttons_swap">
                <?php echo esc_html__('Swap buttons', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-switch', [
                  'id'        => 'buttons_swap',
                  'value'     => isset($wwgcbar_options['buttons_swap']) ? $wwgcbar_options['buttons_swap'] : 0,
                  'label_off' => __('Accept / Settings', 'wwgcbar_domain'),
                  'label_on'  => __('Settings / Accept', 'wwgcbar_domain')
                ]);
              ?>
            </td>
          </tr>
<!-- SETTINGS MODAL	-->

<!-- TEXT CONTENT 1 -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_content1">
                <?php echo esc_html__('Policy overview main text part 1', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-textarea', [
                  'id'    => 'content1',
                  'value' => isset($wwgcbar_options['content1']) ? $wwgcbar_options['content1'] : '',
                  'class' => 'regular-text'
                ]);
              ?>
            </td>
          </tr>
<!-- TEXT CONTENT 2 -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_content2">
                <?php echo esc_html__('Policy overview main text part 2', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-textarea', [
                  'id'    => 'content2',
                  'value' => isset($wwgcbar_options['content1']) ? $wwgcbar_options['content1'] : '',
                  'class' => 'regular-text'
                ]);
              ?>
            </td>
          </tr>
<!-- TEXT CONTENT 3 NECESSARY -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_content3">
                <?php echo esc_html__('Necessary cookies text', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-textarea', [
                  'id'    => 'content3',
                  'value' => isset($wwgcbar_options['content1']) ? $wwgcbar_options['content1'] : '',
                  'class' => 'regular-text'
                ]);
              ?>
            </td>
          </tr>
<!-- TEXT CONTENT 3 NONNECESARRY-->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_content4">
                <?php echo esc_html__('Non-Necessary cookies text', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-textarea', [
                  'id'    => 'content4',
                  'value' => isset($wwgcbar_options['content1']) ? $wwgcbar_options['content1'] : '',
                  'class' => 'regular-text'
                ]);
              ?>
            </td>
          </tr>
<!-- GOOGLE TRACKING CODE -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_content_tracking_code">
                <?php echo esc_html__('Tracking code', 'wwgcbar_domain'); ?>
              </label><br>
              <small><?php echo esc_html__('Google analytics (GA4) code to insert on page (non-necessary).', 'wwgcbar_domain'); ?></small><br>
              <small><?php echo esc_html__('Code will be added to HEAD when non-essential cookies enabled.', 'wwgcbar_domain'); ?></small>
            </th>
            <td>
              <?php
                wwgcbar_render_template('field-input-text', [
                  'id'    => 'content_tracking_code',
                  'value' => isset($wwgcbar_options['content_tracking_code']) ? $wwgcbar_options['content_tracking_code'] : '',
                  'class' => 'regular-text'
                ]);
              ?>
            </td>
          </tr>
<!-- COOKIE BAR SETTINGS BUTTON SHORTCODE -->
          <tr>
            <th scope="row">
              <label for="wwgcbar_settings_cookie_shortcode">
                <?php echo esc_html__('Show cookie bar button shortcode', 'wwgcbar_domain'); ?>
              </label>
            </th>
            <td>
              <div>
                <input class="regular-text" type="text" readonly value="[wwgcbar]" />
              </div>
              <br>
              <div>
                <label><?php echo esc_html__('Default button code', 'wwgcbar_domain'); ?></label>
                <br>
                <input class="regular-text" type="text" readonly value='&lt;a id="wwgcbar-collapsed" href="#"&gt;Cookies&lt;/a&gt;' />
                <br><br>
                <label><?php echo esc_html__('Custom button code', 'wwgcbar_domain'); ?></label>
                <br>
                <textarea name="wwgcbar_settings[cookie_shortcode]" id="wwgcbar_settings_cookie_shortcode" class="regular-text"><?php echo isset($wwgcbar_options['cookie_shortcode']) ? esc_textarea($wwgcbar_options['cookie_shortcode']) : ''; ?></textarea>
              </div>
            </td>
          </tr>
  <!-- FORM END -->
        </tbody>
      </table>
      <p class="submit" style="text-align:right;">
        <input type="submit" name="submit" id="submit" class="button button-primary disabled" value="<?php echo esc_attr__('Save changes', 'wwgcbar_domain'); ?>" />
      </p>
    </form>
  </div>
  <div class="wwgcbar-footer"></div>
</div>

<style>
.wwgcbar-content table tr {
    padding: 20px 0;
    border-bottom: 2px solid #ddd;
}
.wwgcbar-content p.submit {
    margin-top: 30px !important;
}
</style>

<?php
    echo ob_get_clean();
}

// REGISTER SETTINGS
function wwgcbar_register_settings() {
  register_setting(
    'wwgcbar_settings_group',
    'wwgcbar_settings',
    'wwgcbar_sanitize_settings'
  );
}

add_action('admin_init', 'wwgcbar_register_settings');