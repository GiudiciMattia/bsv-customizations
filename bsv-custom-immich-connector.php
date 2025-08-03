<?php
/**
 * Plugin Name: Bsv Custom Immich Connector
 * Description: Connettore Immich per gestione album fotografici.
 * Version: 1.1b
 * Author: Mattia Giudici
 * License: GPL2+
 */

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/hooks.php';
require_once plugin_dir_path(__FILE__) . 'includes/utilities.php';

function bsv_custom_immich_connector_enqueue_assets() {
    wp_enqueue_style('bsv-custom-immich-connector-css', plugin_dir_url(__FILE__) . 'assets/css/bsv-custom-immich-connector.css', [], '1.0');
    wp_enqueue_script('bsv-custom-immich-connector-js', plugin_dir_url(__FILE__) . 'assets/js/bsv-custom-immich-connector.js', ['jquery'], '1.0', true);
}
add_action('wp_enqueue_scripts', 'bsv_custom_immich_connector_enqueue_assets');
