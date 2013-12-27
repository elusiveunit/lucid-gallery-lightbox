<?php
/**
 * Load assets and use custom shortcode output.
 *
 * @package Lucid
 * @subpackage GalleryLightbox
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Lightbox setup and gallery shortcode output, all filterable.
 *
 * @package Lucid
 * @subpackage GalleryLightbox
 */
class Lucid_Gallery_Lightbox {

	/**
	 * Full path to plugin main file.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public static $plugin_file;

	/**
	 * If setup (script enqueue and footer init) has been run.
	 *
	 * @since 2.1.0
	 * @var bool
	 */
	protected static $_is_setup_done = false;

	/**
	 * HTML class used for the gallery container.
	 *
	 * Filterable with lgljl_gallery_class.
	 *
	 * @since 2.1.0
	 * @var string
	 */
	protected $_gallery_class = 'lgljl-gallery';

	/**
	 * HTML class used for the gallery items.
	 *
	 * Filterable with lgljl_gallery_item_class.
	 *
	 * @since 2.1.0
	 * @var string
	 */
	protected $_gallery_item_class = 'lgljl-gallery-item';

	/**
	 * Constructor, add hooks.
	 *
	 * @since 1.0.0
	 * @param string $file Full path to plugin main file.
	 */
	public function __construct( $file ) {
		self::$plugin_file = $file;

		add_action( 'init', array( $this, 'load_translation' ) );
		add_filter( 'post_gallery', array( $this, 'gallery_shortcode' ), 10, 2 );
		add_filter( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'wp_head', array( $this, 'lightbox_filtering' ), 999 );
	}

	/**
	 * Load translation.
	 *
	 * @since 1.0.0
	 */
	public function load_translation() {
		load_plugin_textdomain( 'lgljl', false, trailingslashit( dirname( plugin_basename( self::$plugin_file ) ) ) . 'lang/' );
	}

	/**
	 * Load lightbox CSS and JavaScript.
	 *
	 * @since 1.0.0
	 */
	public function load_assets() {

		// JavaScript
		if ( apply_filters( 'lgljl_load_included_js', true ) ) :
			$version = ( apply_filters( 'lgljl_use_custom_js_build', true ) ) ? '-build.min' : '.min';

			wp_register_script( 'lgljl-magnific-popup', LGLJL_PLUGIN_URL . "js/jquery.magnific-popup{$version}.js", array( 'jquery-core' ), LGLJL_VERSION, true );

			// Add script enqueue and init here if not using custom output.
			// Otherwise it's handled on demand in the shortcode function.
			if ( ! apply_filters( 'lgljl_html5_shortcode_output', true ) ) :
				$this->setup_lightbox();
			endif;
		endif;

		// CSS
		if ( apply_filters( 'lgljl_load_included_css', true ) )
			wp_enqueue_style( 'lgljl-magnific-popup', LGLJL_PLUGIN_URL . 'css/magnific-popup.min.css', false, LGLJL_VERSION );
	}

	/**
	 * Allow for some filtering.
	 *
	 * Allow for a manual init through the 'lgljl_do_lightbox' filter, as
	 * well as changing the classes used.
	 *
	 * Runs on late wp_head since it's the last guaranteed (well, pretty much)
	 * hook before body output.
	 *
	 * @since 2.1.0
	 */
	public function lightbox_filtering() {
		$this->_gallery_class = apply_filters( 'lgljl_gallery_class', $this->_gallery_class );
		$this->_gallery_item_class = apply_filters( 'lgljl_gallery_item_class', $this->_gallery_item_class );

		if ( apply_filters( 'lgljl_do_lightbox', false ) )
			$this->setup_lightbox();
	}

	/**
	 * Enqueue the lightbox script and add the init script to the wp_footer hook.
	 *
	 * @since 2.1.0
	 */
	public function setup_lightbox() {
		if ( self::$_is_setup_done )
			return;

		wp_enqueue_script( 'lgljl-magnific-popup' );
		add_action( 'wp_footer', array( $this, 'lightbox_init' ), 999 );

		self::$_is_setup_done = true;
	}

	/**
	 * Initialize the lightbox in the footer.
	 *
	 * Prints directly to the footer to save a request, since the script is less
	 * than 700 bytes in size.
	 *
	 * @since 2.0.1
	 */
	public function lightbox_init() {
		if ( ! apply_filters( 'lgljl_init_lightbox', true ) ) return;

		$separate_galleries = apply_filters( 'lgljl_separate_galleries', false );
		$sanitize_caption = ( apply_filters( 'lgljl_sanitize_caption_html', true ) ) ? 'true' : 'false';

		// Don't output, minified is used below.
		// Passing sanitizeCaption in the IIFE to stop minifier from converting it.
		if ( false ) : ?>

		<script>(function($, win, sanitizeCaption) {
			'use strict';

			var $emptyDiv = $('<div></div>'),
			options = {
				delegate: ".<?php echo $this->_gallery_item_class; ?>",
				type: "<?php echo apply_filters( 'lgljl_gallery_type', 'image' ); ?>",
				disableOn: 0,
				tClose: "<?php _e( 'Close (Esc)', 'lgljl' ); ?>",
				tLoading: "<?php _e( 'Loading...', 'lgljl' ); ?>",
				gallery: {
					enabled: true,
					tPrev: "<?php _e( 'Previous (Left arrow key)', 'lgljl' ); ?>",
					tNext: "<?php _e( 'Next (Right arrow key)', 'lgljl' ); ?>",
					tCounter: "<?php _e( '%curr% of %total%', 'lgljl' ); ?>"
				},
				image: {
					tError: "<?php _e( '<a href=\"%url%\">The image</a> could not be loaded.', 'lgljl' ); ?>",
					titleSrc: function(item) {
						var title = item.el.attr( 'title' ),
						    desc = item.el.data( 'desc' ),
						    ret = '';

						if ( title ) {
							if ( sanitizeCaption ) {
								title = sanitizeText( title );
							}
							ret += '<div class="lgljl-title">' + title + '</div>';
						}

						if ( desc ) {
							if ( sanitizeCaption ) {
								desc = sanitizeText( desc );
							}
							ret += '<div class="lgljl-desc">' + desc + '</div>';
						}

						return ret;
					}
				},
				ajax: {
					tError: "<?php _e( '<a href=\"%url%\">The content</a> could not be loaded.', 'lgljl' ); ?>"
				}
			};

			function sanitizeText( text ) {
				return $emptyDiv.text( text ).html();
			}

			<?php if ( $separate_galleries ) : ?>
				$('.<?php echo $this->_gallery_class; ?>').each(function() {
					$(this).magnificPopup( options );
				});
			<?php else : ?>
				$('.<?php echo $this->_gallery_class; ?>').magnificPopup( options );
			<?php endif; ?>

		}(jQuery, window, <?php echo $sanitize_caption; ?>));</script>

		<?php

		// Output minified. Quote the 'free' php tags (add semicolons if needed)
		// to stop minifier from choking and un-quote when done.
		else : ?>
		<script>(function(a,g,d){var e=a("<div></div>"),f={delegate:".<?php echo $this->_gallery_item_class; ?>",type:"<?php echo apply_filters( 'lgljl_gallery_type', 'image' ); ?>",disableOn:0,tClose:"<?php _e( 'Close (Esc)', 'lgljl' ); ?>",tLoading:"<?php _e( 'Loading...', 'lgljl' ); ?>",gallery:{enabled:!0,tPrev:"<?php _e( 'Previous (Left arrow key)', 'lgljl' ); ?>",tNext:"<?php _e( 'Next (Right arrow key)', 'lgljl' ); ?>",tCounter:"<?php _e( '%curr% of %total%', 'lgljl' ); ?>"},image:{tError:"<?php _e( '<a href=\"%url%\">The image</a> could not be loaded.', 'lgljl' ); ?>", titleSrc:function(b){var a=b.el.attr("title");b=b.el.data("desc");var c="";a&&(d&&(a=e.text(a).html()),c+='<div class="lgljl-title">'+a+"</div>");b&&(d&&(b=e.text(b).html()),c+='<div class="lgljl-desc">'+b+"</div>");return c}},ajax:{tError:"<?php _e( '<a href=\"%url%\">The content</a> could not be loaded.', 'lgljl' ); ?>"}};<?php if ( $separate_galleries ) : ?>a(".<?php echo $this->_gallery_class; ?>").each(function(){a(this).magnificPopup(f)});<?php else : ?>a(".<?php echo $this->_gallery_class; ?>").magnificPopup(f);<?php endif; ?>})(jQuery,window,<?php echo $sanitize_caption; ?>);</script>
		<?php endif;
	}

	/**
	 * Custom [gallery] shortcode output.
	 *
	 * It's mostly the same as the default, only difference is using figure, div
	 * and figcaption instead of dl/dt/dd, as well as some minor customizations
	 * for the lightbox.
	 *
	 * @since 1.0.0
	 * @param string $output Default output before processing, an empty string.
	 * @param array $attr Shortcode attributes.
	 * @return string Gallery HTML.
	 */
	public function gallery_shortcode( $output, $attr ) {
		if ( ! apply_filters( 'lgljl_html5_shortcode_output', true ) ) return;

		// Load script and init
		$this->setup_lightbox();

		global $post;

		static $instance = 0;
		$instance++;

		if ( ! empty( $attr['ids'] ) ) :

			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) )
				$attr['orderby'] = 'post__in';

			$attr['include'] = $attr['ids'];
		endif;

		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) :
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );

			if ( ! $attr['orderby'] )
				unset( $attr['orderby'] );
		endif;

		extract( shortcode_atts( array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'itemtag'    => 'figure',
			'icontag'    => 'div',
			'captiontag' => 'figcaption',
			'columns'    => 3,
			'size'       => apply_filters( 'lgljl_default_thumbnail_size', 'thumbnail' ),
			'include'    => '',
			'exclude'    => ''
		), $attr ) );

		$id = intval( $id );

		if ( 'RAND' == $order )
			$orderby = 'none';

		if ( ! empty( $include ) ) :
			$_attachments = get_posts( array(
				'include' => $include,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $order,
				'orderby' => $orderby
			) );

			$attachments = array();
			foreach ( $_attachments as $key => $val )
				$attachments[$val->ID] = $_attachments[$key];

		elseif ( ! empty( $exclude ) ) :
			$attachments = get_children( array(
				'post_parent' => $id,
				'exclude' => $exclude,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $order,
				'orderby' => $orderby
			) );

		else :
			$attachments = get_children( array(
				'post_parent' => $id,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $order,
				'orderby' => $orderby
			) );
		endif;

		if ( empty( $attachments ) )
			return '';

		if ( is_feed() ) :
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment )
				$output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";

			return $output;
		endif;

		$itemtag = tag_escape( $itemtag );
		$captiontag = tag_escape( $captiontag );
		$icontag = tag_escape( $icontag );
		$valid_tags = wp_kses_allowed_html( 'post' );

		if ( ! isset( $valid_tags[ $itemtag ] ) )
			$itemtag = 'figure';
		if ( ! isset( $valid_tags[ $icontag ] ) )
			$icontag = 'div';
		if ( ! isset( $valid_tags[ $captiontag ] ) )
			$captiontag = 'figcaption';

		$columns = intval( $columns );
		$itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
		$float = is_rtl() ? 'right' : 'left';

		$size_class = sanitize_html_class( $size );
		$gallery_div = "<div id=\"gallery-{$instance}\" class=\"gallery {$this->_gallery_class} galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}\">";
		$output = $gallery_div;

		$include_title = apply_filters( 'lgljl_include_image_title', false );
		$large_image_size = apply_filters( 'lgljl_large_image_size', 'large' );

		$i = 0;
		foreach ( $attachments as $id => $attachment ) :
			$image = wp_get_attachment_image( $id, $size );
			$url = wp_get_attachment_image_src( $id, $large_image_size );
			$url = $url[0];
			$title = ( $include_title ) ? esc_attr( $attachment->post_title ) : '';
			$description = esc_attr( $attachment->post_content );

			$link = "<a href=\"{$url}\" title=\"{$title}\" data-desc=\"{$description}\" class=\"gallery-item-{$instance} {$this->_gallery_item_class}\">{$image}</a>";

			$output .= "<{$itemtag} class='gallery-item'>";
			$output .= "\n<{$icontag} class='gallery-icon'>$link</{$icontag}>";

			if ( $captiontag && trim( $attachment->post_excerpt ) )
				$output .= "\n<{$captiontag} class='wp-caption-text gallery-caption'>" . wptexturize( $attachment->post_excerpt ) . "</{$captiontag}>";

			$output .= "</{$itemtag}>";

			if ( $columns > 0 && ++$i % $columns == 0 )
				$output .= '<br style="clear: both">';
		endforeach;

		$output .= "
				<br style='clear: both;'>
			</div>\n";

		return $output;
	}
}

/**
 * Use to initalize the lightbox on a page.
 *
 * This can be used if a lightbox i desired for something else than the default
 * gallery. Just call this somewhere before wp_footer and use the proper HTML;
 * add .lgljl-gallery to a container and filter the delegate selector with the
 * lgljl_delegate_selector filter if necessary.
 *
 * @since 2.1.0
 */
function lgljl_do_lightbox() {
	global $lucid_gallery_lightbox;
	$lucid_gallery_lightbox->setup_lightbox();
}
