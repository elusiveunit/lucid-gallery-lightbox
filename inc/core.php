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
	 * Constructor, add hooks.
	 *
	 * @since 1.0.0
	 * @param string $file Full path to plugin main file.
	 */
	public function __construct( $file ) {
		self::$plugin_file = $file;

		add_action( 'init', array( $this, 'load_translation' ), 1 );
		add_filter( 'post_gallery', array( $this, 'gallery_shortcode' ), 10, 2 );
		add_filter( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
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

			wp_register_script( 'lgljl-magnific-popup', LGLJL_PLUGIN_URL . "js/jquery.magnific-popup{$version}.js", array( 'jquery-core' ), null, true );

			// Add script enqueue and init here if not using custom output.
			// Otherwise it's handled on demand in the shortcode function.
			if ( ! apply_filters( 'lgljl_html5_shortcode_output', true ) ) :
				wp_enqueue_script( 'lgljl-magnific-popup' );
				add_filter( 'wp_footer', array( $this, 'lightbox_init' ), 999 );
			endif;
		endif;

		// CSS
		if ( apply_filters( 'lgljl_load_included_css', true ) )
			wp_enqueue_style( 'lgljl-magnific-popup', LGLJL_PLUGIN_URL . 'css/magnific-popup.min.css', false, null );
	}

	/**
	 * Initialize the lightbox in the footer.
	 *
	 * @since 2.0.1
	 */
	public function lightbox_init() {
		if ( ! apply_filters( 'lgljl_init_lightbox', true ) ) return;

		$separate_galleries = apply_filters( 'lgljl_separate_galleries', false );

		// Don't output, minified is used below
		if ( false ) : ?>

		<script>(function($, win) {
			'use strict';

			var $emptyDiv = $('<div></div>'),
			options = {
				delegate: 'a',
				type: 'image',
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
							ret += '<div class="lgljl-title">' + sanitizeText( title ) + '</div>';
						}

						if ( desc ) {
							ret += '<div class="lgljl-desc">' + sanitizeText( desc ) + '</div>';
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
				$('.gallery').each(function() {
					$(this).magnificPopup( options );
				});
			<?php else : ?>
				$('.gallery').magnificPopup( options );
			<?php endif; ?>

		}(jQuery, window));</script>

		<?php
		// Output minified. Remove PHP conditional from above and re-insert it
		// manually to avoid minifier complaining.
		else : ?>
		<script>(function(a,d){var c=a("textarea"),b={delegate:"a",type:"image",disableOn:0,tClose:"<?php _e( 'Close (Esc)', 'lgljl' ); ?>",tLoading:"<?php _e( 'Loading...', 'lgljl' ); ?>",gallery:{enabled:!0,tPrev:"<?php _e( 'Previous (Left arrow key)', 'lgljl' ); ?>",tNext:"<?php _e( 'Next (Right arrow key)', 'lgljl' ); ?>",tCounter:"<?php _e( '%curr% of %total%', 'lgljl' ); ?>"},image:{tError:"<?php _e( '<a href=\"%url%\">The image</a> could not be loaded.', 'lgljl' ); ?>",titleSrc:function(a){var b=c.html(a.el.data("desc")).text();return'<div class="lightbox-title">'+a.el.attr("title")+'</div><div class="lightbox-content">'+b+"</div>"}},ajax:{tError:"<?php _e( '<a href=\"%url%\">The content</a> could not be loaded.', 'lgljl' ); ?>"}};<?php if ( $separate_galleries ) : ?>a(".gallery").each(function(){a(this).magnificPopup(b)})<?php else : ?>a(".gallery").magnificPopup(b)<?php endif; ?>})(jQuery,window);</script>
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
		wp_enqueue_script( 'lgljl-magnific-popup' );
		add_filter( 'wp_footer', array( $this, 'lightbox_init' ), 999 );

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
			'size'       => 'thumbnail',
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
		$gallery_div = "<div id=\"gallery-{$instance}\" class=\"gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}\">";
		$output = $gallery_div;

		$include_title = apply_filters( 'lgljl_include_image_title', false );
		$large_image_size = apply_filters( 'lgljl_large_image_size', 'large' );

		$i = 0;
		foreach ( $attachments as $id => $attachment ) :
			$image = wp_get_attachment_image( $id, $size );
			$url = wp_get_attachment_image_src( $id, apply_filters( 'lgljl_large_image_size', 'large' ) );
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