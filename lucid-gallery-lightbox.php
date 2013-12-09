<?php
/**
 * Lucid Gallery Lightbox plugin definition.
 *
 * Plugin Name: Lucid Gallery Lightbox
 * Plugin URI: https://github.com/elusiveunit/lucid-gallery-lightbox
 * Description: Changes [gallery] shortcode output to HTML5 and adds lightbox functionality.
 * Author: Jens Lindberg
 * Version: 2.1.1
 * License: GPL-2.0+
 * Text Domain: lgljl
 * Domain Path: /lang
 *
 * @package Lucid
 * @subpackage GalleryLightbox
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

// Symlink workaround, see http://core.trac.wordpress.org/ticket/16953
$lgljl_plugin_file = __FILE__;
if ( isset( $plugin ) )
	$lgljl_plugin_file = $plugin;
elseif ( isset( $network_plugin ) )
	$lgljl_plugin_file = $network_plugin;

// Plugin constants
if ( ! defined( 'LGLJL_VERSION' ) )
	define( 'LGLJL_VERSION', '2.1.1' );

if ( ! defined( 'LGLJL_PLUGIN_URL' ) )
	define( 'LGLJL_PLUGIN_URL', trailingslashit( plugin_dir_url( $lgljl_plugin_file ) ) );

if ( ! defined( 'LGLJL_PLUGIN_PATH' ) )
	define( 'LGLJL_PLUGIN_PATH', trailingslashit( plugin_dir_path( $lgljl_plugin_file ) ) );

// Load and initialize the plugin parts
require LGLJL_PLUGIN_PATH . 'inc/core.php';
$lucid_gallery_lightbox = new Lucid_Gallery_Lightbox( $lgljl_plugin_file );