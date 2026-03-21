<?php
/**
 * Plugin Name: WW GDPR Cookie Bar Plugin
 * Description: Simple GDPR Cookie Compliance plugin. Author email: mru24@yahoo.com
 * Version: 3.2.3
 * Author: Val Wroblewski
 * License: GPLv2 or later
 * Text Domain: ww-gdpr-bar
 * Requires at least: 5.0
 * Tested up to: 6.9.4
 * Requires PHP: 7.4
 */


if (!defined('ABSPATH')) {
    exit;
}

define('WW_GDPR_BAR_VERSION', '3.2.3');
define('WW_GDPR_BAR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WW_GDPR_BAR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WW_GDPR_BAR_PLUGIN_BASENAME', plugin_basename(__FILE__));

$wwgcbar_options = get_option('wwgcbar_settings');
$pluginFile = WW_GDPR_BAR_PLUGIN_BASENAME;
$pluginName = "ww-gdpr-plugin";

if (is_admin()) {
    $admin_file = WW_GDPR_BAR_PLUGIN_PATH . 'includes/ww-gdpr-plugin-admin.php';
    if (file_exists($admin_file)) {
        require_once $admin_file;
    } else {
        error_log('WW GDPR Plugin: Admin file not found at ' . $admin_file);
    }
}

$front_file = WW_GDPR_BAR_PLUGIN_PATH . 'includes/ww-gdpr-plugin-front.php';
if (file_exists($front_file)) {
    require_once $front_file;
} else {
    error_log('WW GDPR Plugin: Front file not found at ' . $front_file);
}
function wwgcbar_activate() {
    if (!current_user_can('activate_plugins')) {
        return;
    }
    $default_options = array(
        'enable' => 1,
        'position' => 0,
        'content' => 'We use cookies to ensure that we give you the best possible experience on our website. By using this site you agree to our <a href="/privacy-policy">Privacy Policy</a>',
        'content_col' => '#FFFFFF',
        'content_bg' => '#000000',
        'pp_link' => '/privacy-policy',
        'pp_target' => 0,
        'content_col_link' => '#FFFFFF',
        'button_1_text' => 'Accept',
        'button_1_col' => '#FFFFFF',
        'button_1_bg' => '#45af0c',
        'button_2_text' => 'Cookie settings',
        'button_2_col' => '#FFFFFF',
        'button_2_bg' => '#e27a18',
        'buttons_swap' => 0,
        'cookies_non_essential' => 1
    );
    if (false === get_option('wwgcbar_settings')) {
        add_option('wwgcbar_settings', $default_options, '', 'no');
    }
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('WW GDPR Plugin: Plugin activated successfully');
    }
    wp_cache_delete('wwgcbar_settings', 'options');
}
register_activation_hook(__FILE__, 'wwgcbar_activate');

function wwgcbar_deactivate() {
    if (!current_user_can('activate_plugins')) {
        return;
    }

    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('WW GDPR Plugin: Plugin deactivated');
    }

    // Clear cache
    // wp_cache_delete('wwgcbar_settings', 'options');

    // Note: We're NOT deleting options on deactivation to preserve user settings
    // delete_option('wwgcbar_settings');
}
register_deactivation_hook(__FILE__, 'wwgcbar_deactivate');

function wwgcbar_uninstall() {
    if (!defined('WP_UNINSTALL_PLUGIN')) {
        exit;
    }
    if (!current_user_can('delete_plugins')) {
        return;
    }
    if (WP_UNINSTALL_PLUGIN !== 'ww-gdpr-plugin/ww-gdpr-plugin.php') {
        return;
    }
    delete_option('wwgcbar_settings');
    wp_cache_delete('wwgcbar_settings', 'options');

    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('WW GDPR Plugin: Plugin uninstalled and all data removed');
    }
}

function wwgcbar_load_textdomain() {
    load_plugin_textdomain('ww-gdpr-bar', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'wwgcbar_load_textdomain');

function wwgcbar_security_headers() {
    if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'wwgcbar-options') {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
    }
}
add_action('admin_init', 'wwgcbar_security_headers');

function wwgcbar_check_version() {
    if (WW_GDPR_BAR_VERSION !== get_option('wwgcbar_version')) {
        wwgcbar_activate();
        update_option('wwgcbar_version', WW_GDPR_BAR_VERSION);
    }
}
add_action('admin_init', 'wwgcbar_check_version');