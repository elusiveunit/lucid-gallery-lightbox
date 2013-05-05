<?php
/**
 * Lucid Gallery Lightbox definition.
 *
 * @package Lucid
 * @subpackage GalleryLightbox
 */

/*
Plugin Name: Lucid Gallery Lightbox
Plugin URI: https://github.com/elusiveunit/lucid-gallery-lightbox
Description: A tiny plugin that changes [gallery] shortcode output to HTML5 and adds lightbox functionality.
Author: Jens Lindberg
Version: 1.0.0
*/

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Plugin constants
if ( ! defined( 'LGLJL_VERSION' ) )
	define( 'LGLJL_VERSION', '1.0.0' );

if ( ! defined( 'LGLJL_PLUGIN_URL' ) )
	define( 'LGLJL_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

if ( ! defined( 'LGLJL_PLUGIN_PATH' ) )
	define( 'LGLJL_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

// Load and initialize the plugin parts
require LGLJL_PLUGIN_PATH . 'inc/core.php';
$lucid_gallery_lightbox = new Lucid_Gallery_Lightbox( __FILE__ );