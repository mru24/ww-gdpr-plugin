<?php
/**
 * Template for Standard Text Input
 * Variables available: $id, $value, $class, $placeholder
 */
?>
<input
    name="wwgcbar_settings[<?php echo esc_attr($id); ?>]"
    type="text"
    id="wwgcbar_settings_<?php echo esc_attr($id); ?>"
    value="<?php echo esc_attr($value); ?>"
    class="<?php echo esc_attr($class); ?>"
    placeholder="<?php echo isset($placeholder) ? esc_attr($placeholder) : ''; ?>"
>