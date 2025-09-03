<?php
/**
 * Plugin Name:       Meteo Fr
 * Description:       Example block scaffolded with Create Block tool.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       meteo-fr
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function create_block_meteo_fr_block_init() {
	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata.
	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 */
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
	 * Added to WordPress 6.7 to improve the performance of block type registration.
	 *
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}
	/**
	 * Registers the block type(s) in the `blocks-manifest.php` file.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', 'create_block_meteo_fr_block_init' );

/*
Plugin Name: Météo FR V1
Description: Plugin météo avec dashboard admin et widget shortcode.
Version: 1.0
Author: Ayoub
*/

if (!defined('ABSPATH')) exit;

// Inclure les fichiers nécessaires
require_once plugin_dir_path(__FILE__) . 'meteo-api.php';
require_once plugin_dir_path(__FILE__) . 'admin-dashboard.php';
require_once plugin_dir_path(__FILE__) . 'widget-shortcode.php';

// Charger les assets admin
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook == 'toplevel_page_meteo_fr_dashboard') {
        $js_path = plugin_dir_path(__FILE__) . 'assets/js/meteo-admin.js';
        $js_url = plugin_dir_url(__FILE__) . 'assets/js/meteo-admin.js';
        wp_enqueue_script(
            'meteo-admin-js',
            $js_url,
            ['jquery'],
            file_exists($js_path) ? filemtime($js_path) : '1.0',
            true
        );
        wp_localize_script('meteo-admin-js', 'meteoFR', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('meteo_fr_nonce')
        ]);
    }
});

// Charger le CSS du widget côté public
add_action('wp_enqueue_scripts', function() {
    $css_path = plugin_dir_path(__FILE__) . 'assets/css/meteo-widget.css';
    $css_url = plugin_dir_url(__FILE__) . 'assets/css/meteo-widget.css';
    wp_enqueue_style(
        'meteo-widget-css',
        $css_url,
        [],
        file_exists($css_path) ? filemtime($css_path) : '1.0'
    );
});
