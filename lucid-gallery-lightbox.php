<?php
/**
 * Lucid Gallery Lightbox plugin definition.
 *
 * Plugin Name: Lucid Gallery Lightbox
 * Plugin URI: https://github.com/elusiveunit/lucid-gallery-lightbox
 * Description: Changes [gallery] shortcode output to HTML5 and adds lightbox functionality.
 * Author: Jens Lindberg
 * Version: 2.3.0
 * License: GPL-2.0+
 * Text Domain: lgljl
 * Domain Path: /lang
 *
 * @package Lucid\GalleryLightbox
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Plugin constants
if ( ! defined( 'LGLJL_VERSION' ) )
	define( 'LGLJL_VERSION', '2.3.0' );

if ( ! defined( 'LGLJL_PLUGIN_URL' ) )
	define( 'LGLJL_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

if ( ! defined( 'LGLJL_PLUGIN_PATH' ) )
	define( 'LGLJL_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

// Load and initialize the plugin parts
require LGLJL_PLUGIN_PATH . 'inc/core.php';
$GLOBALS['lucid_gallery_lightbox'] = new Lucid_Gallery_Lightbox( __FILE__ );