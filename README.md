# Lucid Gallery Lightbox [![devDependency Status](https://david-dm.org/elusiveunit/lucid-gallery-lightbox/dev-status.svg)](https://david-dm.org/elusiveunit/lucid-gallery-lightbox#info=devDependencies)

A tiny plugin that changes [gallery] shortcode output to HTML5 `figure` and `figcaption` elements. Also adds lightbox functionality through Magnific Popup by Dmitry Semenov.

Lucid Gallery Lightbox is currently available in the following languages:

* English
* Swedish

Magnific Popup links:

* [Project site](http://dimsemenov.com/plugins/magnific-popup/)
* [Docs](http://dimsemenov.com/plugins/magnific-popup/documentation.html)
* [Github](https://github.com/dimsemenov/Magnific-Popup)

## Basic usage

Just install it to rock and roll!

### Shortcode modifications

As mentioned above, the gallery item elements are changed from `dl`, `dt` and `dd` to `figure`, `div` and `figcaption`. Additionally, the item links to the large version of an image, instead of the original, full size. Rarely does one need to view a 4 megapixel image squshed to a third of its real size. This can be set to any image size with the `lgljl_large_image_size` filter.

## Options via hooks

Since this is a very simple plugin, and the options are pretty 'code based', I didn't want to include an options page. Everything is instead adjustable via hooks.

-----

**lgljl\_load\_included\_css**

Whether to load the included lightbox CSS. Set to false and load in the theme's CSS file to save a request.

	add_filter( 'lgljl_load_included_css', '__return_false' );

-----

**lgljl\_load\_included\_js**

Whether to load the included lightbox JavaScript. Set to false and load in the theme's script file (if used) to save a request. Do note though, by default the script only loads when the gallery shortcode is used. Packing it with a theme's script file may introduce unnecessary weight for those who won't need it.

	add_filter( 'lgljl_load_included_js', '__return_false' );

-----

**lgljl\_use\_custom\_js\_build**

If loading the included JavaScript, this decides if a custom build should be loaded. This build only contains the image, gallery and fastclick components, skipping stuff like ajax and iframe, which isn't used in the gallery shortcode by default. Set to false to load the full version.

	add_filter( 'lgljl_use_custom_js_build', '__return_false' );

-----

**lgljl\_init\_lightbox**

Whether to initiallize the lightbox automatically (call the lightbox method on gallery jQuery objects). Disable if you want to use custom options, callbacks etc.

	add_filter( 'lgljl_init_lightbox', '__return_false' );

-----

**lgljl\_do\_lightbox**

_Exists as both a function and a filter._

By default, the lightbox JavaScript is only added if a page has a gallery. This filter and/or function can be used to add it to any page, in case you want to use it on something else as well.

**Note:** The container for any custom lightbox use must have the class name `lgljl-gallery` (filterable).

	// Before wp_head
	add_filter( 'lgljl_do_lightbox', '__return_true' );

or

	// Before wp_footer
	lgljl_do_lightbox();

-----

**lgljl\_separate\_galleries**

Whether to load a separate lightbox instance for every gallery. This basically decides if the previous and next arrows should work between multiple galleries or not. Defaults to false for optimal performace.

	add_filter( 'lgljl_separate_galleries', '__return_true' );

-----

**lgljl\_sanitize\_caption\_html**

By default, HTML tags inside the lightbox captions (the `title` and `data-desc` attributes) will be output as text. Setting this to false will insert the content as is.

	add_filter( 'lgljl_sanitize_caption_html', '__return_false' );

-----

**lgljl\_gallery\_class** and **lgljl\_gallery\_item\_class**

Filter the HTML classes used as jQuery selectors. The gallery class is used for initializing the the lightbox, while the gallery item is used as a delegate selector. Defaults to `lgljl-gallery` and `lgljl-gallery-item` respectively.

	/**
	 * Set the jQuery delegation selector used in Lucid Gallery Lightbox.
	 *
	 * @param string $default_selector The default selector set in the plugin.
	 * @return string The new selector.
	 */
	function my_prefix_set_gallery_item_class( $default_selector ) {
		return 'my-link-class';
	}
	add_filter( 'lgljl_gallery_item_class', 'my_prefix_set_gallery_item_class' );

-----

**lgljl\_gallery\_type**

Use to set other gallery types than `image`. At this time of writing, available types are image, iframe, inline, and ajax. See [the documentation](http://dimsemenov.com/plugins/magnific-popup/documentation.html#content_types) for usage.

	/**
	 * Set the gallery type used in Lucid Gallery Lightbox.
	 *
	 * @param string $default_type The default gallery type set in the plugin.
	 * @return string The new gallery type.
	 */
	function my_prefix_set_lightbox_gallery_type( $default_type ) {
		return 'iframe';
	}
	add_filter( 'lgljl_gallery_type', 'my_prefix_set_lightbox_gallery_type' );

-----

**lgljl\_large\_image\_size**

Name of image size to use for the magnified/lightboxed image. Defaults to `'large'`. Takes the shortcode attributes as an optional second argument.

	/**
	 * Set the magnified image size used in Lucid Gallery Lightbox.
	 *
	 * @param string $default_size The default image size set in the plugin.
	 * @return string The new image size.
	 */
	function my_prefix_set_lightbox_image_size( $default_size ) {
		return 'full';
	}
	add_filter( 'lgljl_large_image_size', 'my_prefix_set_lightbox_image_size' );

-----

**lgljl\_default\_thumbnail\_size**

Name of image size to use for the gallery thumbnail images. Defaults to `'thumbnail'`.

	/**
	 * Set the default gallery thumbnail size used in Lucid Gallery Lightbox.
	 *
	 * @param string $default_size The default image size set in the plugin.
	 * @return string The new image size.
	 */
	function my_prefix_set_gallery_thumb_size( $default_size ) {
		return 'medium';
	}
	add_filter( 'lgljl_default_thumbnail_size', 'my_prefix_set_gallery_thumb_size' );

-----

**lgljl\_include\_image\_title**

Whether to include the image (attachment) title in the lightbox caption (and thus on the thumbnail link). Defaults to false, since the title on attachments are often just the file names if left unchanged. Takes the shortcode attributes as an optional second argument.

	add_filter( 'lgljl_include_image_title', '__return_true' );

-----

**lgljl\_include\_image\_caption**

Whether to include the image (attachment) description in the lightbox caption. Defaults to true. Takes the shortcode attributes as an optional second argument.

	add_filter( 'lgljl_include_image_caption', '__return_false' );

-----

**lgljl\_force\_image\_link**

Whether to force linking directly to the images, regardless of gallery setting. Defaults to true, since the lightbox doesn't have anything to display otherwise. If set to false and a non-image gallery is inserted, the gallery items won't have the HTML class name that the JavaScript uses, so no lightbox functionality will be used on those items. Takes the shortcode attributes as an optional second argument.

	add_filter( 'lgljl_force_image_link', '__return_false' );

-----

**lgljl\_caption\_title** and **lgljl\_caption\_text**

Allows filtering of the text on each gallery item.

	/**
	 * Filter gallery item title text.
	 *
	 * @param string $title Defaults to the attachment post title (name).
	 * @param WP_Post $attachment The attachment post object.
	 */
	function my_prefix_reverse_title( $title, $attachment ) {
		return strrev( $title );
	}
	add_filter( 'lgljl_caption_title', 'my_prefix_reverse_title', 10, 2 );

-----

## Changelog

### 2.3.0: Jun 08, 2014

* New: Add `lgljl_include_image_caption` filter to control if the image caption (description field) should be used.
* New: Add `lgljl_force_image_link` filter to control the forced linking to the image file.
* New: Add `lgljl_caption_title` and `lgljl_caption_text` filters to allow manipulation of said text strings.
* New: Follow in WordPress 3.9's footsteps and add inline documentation to all hooks.
* Fix/tweak: Add 'gallery' context to `shortcode_atts`.
* Tweak: Some formatting and structural improvements for readability/scannability.
* Removed: The `lgljl_html5_shortcode_output` filter serves no practical purpose now that the shortcode has been so customized.

### 2.2.0: Dec 10, 2013

* New: Add `lgljl_default_thumbnail_size` filter, to control the default thumbnail image size.
* Tweak: The Magnific Popup options are now printed as a global `LGLJL_OPTIONS` object. This is done in the footer before enqueued scripts are printed, so the options can be modified via JavaScript, instead of having to replace them completely when doing something custom.
* Tweak/fix: Include [this](https://gist.github.com/aubreypwd/7828624) temporary workaround for the issue with `__FILE__` in symlinked plugins, see [trac ticket #16953](http://core.trac.wordpress.org/ticket/16953).

### 2.1.1: Dec 01, 2013

* New: Add `lgljl_sanitize_caption_html` filter, to allow HTML in lightbox captions.

### 2.1.0: Nov 21, 2013

* New: Add `lgljl_do_lightbox` function, to load the script and initialize the lightbox on pages without galleries.
* New: Add `lgljl_do_lightbox` filter. Filter version of the above.
* New: Add `lgljl_gallery_class` and `lgljl_gallery_item_class` filters, to control the HTML classes used as jQuery selectors.
* New: Add `lgljl_gallery_type` filter, to set the gallery 'type' to use.
* New: Add `lgljl_include_image_title` filter, to control if attachment title is included in the lightbox.
* Fix: Actually set the `title` and `data-desc` attributes the script is looking for, derp.
* Tweak: Update Magnific Popup to 0.9.9.
* Tweak: Change the default jQuery selector for initialization from `.gallery` to `.lgljl-gallery`.
* Tweak: Change the lightbox text classes to `lgljl-title` and `lgljl-desc`, and make the title bold.

### 2.0.1: Sep 16, 2013

* Tweak: Only load the script when the gallery shortcode is used.
* Tweak: Update Magnific Popup to 0.9.5.

### 2.0.0: Aug 13, 2013

* New: Change lightbox to Magnific Popup by Dmitry Semenov. Colorbox has some issues with responsive sites and Magnific Popup simply follows a lot of good practices. The downside is a larger file, but that is redeemed by not requiring any images.
* New: The `lgljl_use_custom_js_build` filter can be used to control if a custom build of Magnific Popup should be loaded, instead of the full one. See options section, defaults to true.
* New: The `lgljl_large_image_size` filter can be used to set what image size should be used for the lightboxed/full image. Uses `'large'` by default.
* New: Use the `lgljl_separate_galleries` to control paging between multiple galleries.
* Tweak: The gallery shortcode is slightly tweaked to allow the 'large' image size and filter.
* Removed: The `lgljl_lightbox_theme_style` filter has been removed, since Magnific Popup doesn't have different themes.

### 1.0.1: May 05, 2013

* Tweak: Update Colorbox to 1.4.15 and with it, update 'ColorBox' to the new 'Colorbox'.

### 1.0.0: Mar 27, 2013

* Initial version.