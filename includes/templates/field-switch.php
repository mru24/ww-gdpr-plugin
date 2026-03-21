<?php
/**
 * Template for Toggle Switch
 * Variables: $id, $value, $label_off, $label_on
 */
?>
<p>
  <span class="before-input" style="display:inline-block;min-width:60px;">
    <?php echo esc_html($label_off); ?>
  </span>
  <label class="switch">
    <input
      name="wwgcbar_settings[<?php echo esc_attr($id); ?>]"
      type="checkbox"
      id="wwgcbar_settings_<?php echo esc_attr($id); ?>"
      value="1"
      <?php checked('1', $value); ?>
    >
    <span class="slider round"></span>
    <span class="wwgcbar-checkbox-text"></span>
  </label>
  <span class="before-input">
    <?php echo esc_html($label_on); ?>
  </span>
</p>