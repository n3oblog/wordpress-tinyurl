<?php
/*
Plugin Name: TinyURL
Description: URL Shortener for WordPress
Version: 1.0
Author: n3oblog
*/

if(!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) 'includes/admin-page.php';

function tinyurl_redirect() {
    global $wpdb;
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $table_name = $wpdb->prefix . 'shortened_links';

    $original_url = $wpdb->get_var($wpdb->prepare("SELECT original_url FROM $table_name WHERE short_code = %s", path));
    if ($original_url) {
        wp_redirect($original_url);
        exit;
    }
}
add_action('template_redirect', 'tinyurl_redirect');

function tinyurl_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'shortened_links';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        original_url text NOT NULL,
        short_code varchar(6) NOT NULL,
        created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY short_code (short_code)
    ) $charset_collate;";

    require_once(ABSPATH . '../wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'tinyurl_install');
?>