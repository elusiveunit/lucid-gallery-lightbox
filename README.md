# Lucid Gallery Lightbox

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

I didn't want to include an options page for such a simple plugin, so everything is adjustable via hooks.

**lgljl\_init\_lightbox**

Whether to initiallize the lightbox automatically. Disable if you want to use custom options, callbacks etc.

	add_filter( 'lgljl_init_lightbox', '__return_false' );

-----

**lgljl\_html5\_shortcode\_output**

Whether to output HTML5 figure and figcaption instead of the default dl/dt/dd elements. Some themes target the default elements directly with their gallery styling, instead of using the available classes, which may break the gallery appearance if using this output.

	add_filter( 'lgljl_html5_shortcode_output', '__return_false' );

-----

**lgljl\_large\_image\_size**

Name of image size to use for the magnified/lightboxed image. Defaults to `'large'`.

	/**
	 * Set the magnified image size used in Lucid Gallery Lightbox.
	 *
	 * @param string $default_size The default image size set in the plugin.
	 * @return string The new image size.
	 */
	function themename_set_lightbox_image_size( $default_size ) {
		return 'full';
	}
	add_filter( 'lgljl_large_image_size', 'themename_set_lightbox_image_size' );

-----

**lgljl\_load\_included\_css**

Whether to load the included lightbox CSS. Set to false and load in the theme's CSS file to save a request.

	add_filter( 'lgljl_load_included_css', '__return_false' );

-----

**lgljl\_load\_included\_js**

Whether to load the included lightbox JavaScript. Set to false and load in the theme's script file to save a request.

	add_filter( 'lgljl_load_included_js', '__return_false' );

-----

**lgljl\_use\_custom\_js\_build**

If loading the included JavaScript, this decides if a custom build should be loaded. This build only contains the image, gallery and fastclick components, skipping stuff like ajax and iframe, which isn't used in the gallery shortcode by default. Set to false to load the full version.

	add_filter( 'lgljl_use_custom_js_build', '__return_false' );

-----

**lgljl\_separate\_galleries**

Whether to load a separate lightbox instance for every gallery. This basically decides if the previous and next arrows should work between multiple galleries or not. Defaults to false for optimal performace.

	add_filter( 'lgljl_separate_galleries', '__return_true' );

## Changelog

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