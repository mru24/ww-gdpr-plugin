<?php
/**
 * Uninstall WW GDPR Cookie Bar Plugin
 *
 * @package WW_GDPR_Bar
 */

// Prevent direct access
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Security: Verify user capabilities
if (!current_user_can('delete_plugins')) {
    return;
}

// Security: Verify we're deleting the correct plugin
$plugin_file = isset($_REQUEST['plugin']) ? sanitize_text_field(wp_unslash($_REQUEST['plugin'])) : '';
if (false === strpos($plugin_file, 'ww-gdpr-plugin.php')) {
    return;
}

// Delete plugin options
delete_option('wwgcbar_settings');

// For multisite installations
if (is_multisite()) {
    $sites = get_sites();
    foreach ($sites as $site) {
        switch_to_blog($site->blog_id);
        delete_option('wwgcbar_settings');
        restore_current_blog();
    }
}

// Clear any cached data
wp_cache_delete('wwgcbar_settings', 'options');

// Log uninstall (only in debug mode)
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('WW GDPR Plugin: Plugin uninstalled and all data removed');
}