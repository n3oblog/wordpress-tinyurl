<?php

function tinyurl_add_admin_menu() {
    add_menu_page(
        'TinyURL 1.0 by N3oBlog',        
        'TinyURL',             
        'manage_options',            
        'tinyurl',           
        'tinyurl_page_html',
        'dashicons-admin-links',      
        100                           
    );
}
add_action('admin_menu', 'tinyurl_add_admin_menu');

function tinyurl_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['original_url'])) {
        $original_url = esc_url_raw($_POST['original_url']);
        $short_code = tinyurl_generate_code($original_url);
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>Shortened link: <a href="' . site_url($short_code) . '" target="_blank">' . site_url($short_code) . '</a></p>';
        echo '</div>';
    }

    ?>
    <div class="wrap">
        <h1>TinyURL 1.0 by N3oBlog</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enter link</th>
                    <td><input type="url" name="original_url" required class="regular-text" /></td>
                </tr>
            </table>
            <?php submit_button('Shorten link'); ?>
        </form>
    </div>
    <?php
}

function tinyurl_generate_code($original_url) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'shortened_links';

    $existing_code = $wpdb->get_var($wpdb->prepare("SELECT short_code FROM $table_name WHERE original_url = %s", $original_url));
    if ($existing_code) {
        return $existing_code;
    }

    do {
        $short_code = substr(md5(uniqid(rand(), true)), 0, 6);
        $code_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE short_code = %s", $short_code));
    } while ($code_exists > 0);

    $wpdb->insert($table_name, array(
        'original_url' => $original_url,
        'short_code'   => $short_code,
        'created_at'   => current_time('mysql'),
    ));

    return $short_code;
}
