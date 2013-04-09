<?php
/**
 * Load assets and use custom shortcode output.
 *
 * Uses ColorBox by Jack Moore
 * - Project site: http://www.jacklmoore.com/colorbox/
 * - Github: https://github.com/jackmoore/colorbox
 *
 * @package Lucid
 * @subpackage GalleryLightbox
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * ColorBox setup and gallery shortcode output, all filterable.
 *
 * @package Lucid
 * @subpackage GalleryLightbox
 */
class Lucid_Gallery_Lightbox {

	/**
	 * Full path to plugin main file.
	 *
	 * @var string
	 */
	public static $plugin_file;

	/**
	 * Constructor, add hooks.
	 *
	 * @param string $file Full path to plugin main file.
	 */
	public function __construct( $file ) {
		self::$plugin_file = $file;
		
		add_action( 'init', array( $this, 'load_translation' ), 1 );
		add_filter( 'wp_footer', array( $this, 'colorbox_init' ), 999 );
		add_filter( 'post_gallery', array( $this, 'gallery_shortcode' ), 10, 2 );
		add_filter( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
	}

	/**
	 * Load translation.
	 */
	public function load_translation() {
		load_plugin_textdomain( 'lgljl', false, trailingslashit( dirname( plugin_basename( self::$plugin_file ) ) ) . 'lang/' );
	}

	/**
	 * Load ColorBox CSS and JavaScript.
	 */
	public function load_assets() {
		$load_js = apply_filters( 'lgljl_load_included_js', true );
		$load_css = apply_filters( 'lgljl_load_included_css', true );

		if ( $load_js ) wp_enqueue_script( 'colorbox', LGLJL_PLUGIN_URL . 'js/jquery.colorbox-min.js', array( 'jquery' ), null, true );

		if ( $load_css ) :
			$theme = apply_filters( 'lgljl_lightbox_theme_style', '3' );
			wp_enqueue_style( 'colorbox', LGLJL_PLUGIN_URL . "themes/{$theme}/colorbox.min.css", false, null );
		endif;
	}

	/**
	 * Initialize the ColorBox in the footer.
	 *
	 * Original in wp-includes/media.php
	 */
	public function colorbox_init() {
		$init = apply_filters( 'lgljl_init_lightbox', true );
		if ( ! $init ) return;

		if ( false ) : // Don't output, minified is used below ?>

		<script>(function($, win) {
			'use strict';

			var resized, colorboxLoaded = false;

			$('.gallery').each(function() {
				var self = this,
				    id = self.id.replace( /gallery-/, '' );

				$(self).find('a').colorbox({
					rel: 'gallery-item-' + id,
					maxWidth: '95%',
					maxHeight: '90%',
					current: '<?php _e( "Image {current} of {total}", "lgljl" ); ?>',
					previous: '<?php _e( "Previous", "lgljl" ); ?>',
					next: '<?php _e( "Next", "lgljl" ); ?>',
					close: '<?php _e( "Close", "lgljl" ); ?>',
					xhrError: '<?php _e( "This content failed to load.", "lgljl" ); ?>',
					imgError: '<?php _e( "This image failed to load.", "lgljl" ); ?>',
					onComplete: function() {
						colorboxLoaded = true;
					}
				});
			});

			// Resize ColorBox on window resize, so it fits in window.
			$(win).resize(function(){
				clearTimeout(resized);

				resized = setTimeout(function() {
					// The $.colorbox.resize() method seems to be doing something
					// other than the load resizing.
					if ( colorboxLoaded ) { $.colorbox.load(); }
				}, 300);
			});
		}(jQuery, window));</script>

		<?php else : // Output minified ?>
		<script>(function(a,d){var b,c=!1;a(".gallery").each(function(){var b=this.id.replace(/gallery-/,"");a(this).find("a").colorbox({rel:"gallery-item-"+b,maxWidth:"95%",maxHeight:"90%",current:'<?php _e( "Image {current} of {total}", "lgljl" ); ?>',previous:'<?php _e( "Previous", "lgljl" ); ?>',next:'<?php _e( "Next", "lgljl" ); ?>',close:'<?php _e( "Close", "lgljl" ); ?>',xhrError:'<?php _e( "This content failed to load.", "lgljl" ); ?>',imgError:'<?php _e( "This image failed to load.", "lgljl" ); ?>',onComplete:function(){c=!0}})});a(d).resize(function(){clearTimeout(b);b=setTimeout(function(){c&&a.colorbox.load()},300)})})(jQuery,window);</script>
		<?php endif;
	}

	/**
	 * Custom [gallery] shortcode output.
	 *
	 * It's mostly the same as the default, only difference is using figure, div
	 * and figcaption instead of dl/dt/dd, as well as a class on every item used
	 * for ColorBox grouping.
	 *
	 * @param string $output Default output before processing, an empty string.
	 * @param array $attr Shortcode attributes.
	 * @return string Gallery HTML.
	 */
	public function gallery_shortcode( $output, $attr ) {
		$custom_output = apply_filters( 'lgljl_html5_shortcode_output', true );
		if ( ! $custom_output ) return;

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

		$i = 0;
		foreach ( $attachments as $id => $attachment ) :
			//$link = ( isset( $attr['link'] ) && 'file' == $attr['link'] ) ? wp_get_attachment_link( $id, $size, false, false ) : wp_get_attachment_link( $id, $size, true, false );
			$link = wp_get_attachment_link( $id, $size, false, false );
			$link = preg_replace( '/<a(?!\/>)/', "<a class=\"gallery-item-{$instance}\" ", $link );

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