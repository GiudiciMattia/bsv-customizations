<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'bsv_add_main_menu');

function bsv_add_main_menu() {
    // Menu principale "BSV"
    add_menu_page(
        'BSV - Centro di Controllo',       // Page title
        'BSV',                             // Menu label
        'edit_others_pages',              // Capability
        'bsv-dashboard',                  // Menu slug
        'bsv_render_dashboard_page',      // Callback for the dashboard
        'dashicons-groups',               // Icon
        5.5                                // Position between Posts and Media
    );

    // Sottomenu "Immich Connector"
    add_submenu_page(
        'bsv-dashboard',
        'Immich Connector',
        'Immich Connector',
        'edit_others_pages',
        'bsv-immich-settings',
        'bsv_immich_render_settings_page'
    );
}

function bsv_render_dashboard_page() {
    echo '<div class="wrap"><h1>BSV - Centro di Controllo</h1><p>Benvenuto nel pannello principale BSV.</p></div>';
}
