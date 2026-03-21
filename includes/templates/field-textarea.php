<?php
/**
 * Template for Textarea Field
 * Variables available: $id, $name, $value, $class
 */
?>
<textarea
  name="wwgcbar_settings[<?php echo esc_attr($id); ?>]"
  id="wwgcbar_settings_<?php echo esc_attr($id); ?>"
  class="<?php echo esc_attr($class); ?>"><?php echo esc_textarea($value); ?></textarea>