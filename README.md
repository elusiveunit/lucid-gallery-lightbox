# Lucid Gallery Lightbox

A tiny plugin that changes [gallery] shortcode output to HTML5 `figure` and `figcaption` elements. Also adds lightbox functionality through [ColorBox](http://www.jacklmoore.com/colorbox/) by Jack Moore.

Lucid Gallery Lightbox is currently available in the following languages:

* English
* Swedish

## Basic usage

Just install it to rock and roll!

## Options via hooks

I didn't want to include an options page for such a simple plugin, so everything is adjustable via hooks.

**lgljl\_init\_lightbox**

Whether to initiallize ColorBox automatically. Disable if you want to use custom options, callbacks etc.

	add_filter( 'lgljl_init_lightbox', '__return_false' );

-----

**lgljl\_html5\_shortcode\_output**

Whether to output HTML5 figure and figcaption instead of the default dl/dt/dd elements. Some themes target the default elements directly with their gallery styling, instead of using the available classes, which may break the gallery appearance if using this output.

	add_filter( 'lgljl_html5_shortcode_output', '__return_false' );

-----

**lgljl\_load\_included\_js**

Whether to load included ColorBox JavaScript. Set to false and load in the theme's script file to save a request.

	add_filter( 'lgljl_load_included_js', '__return_false' );

-----

**lgljl\_load\_included\_css**

Whether to load included ColorBox CSS. Set to false and load in the theme's CSS file to save a request.

	add_filter( 'lgljl_load_included_css', '__return_false' );

-----

**lgljl\_lightbox\_theme\_style**

What ColorBox theme to use, if the included CSS is loaded. Return string between 1 and 5. Theme demos can be viewed on the [ColorBox project site](http://www.jacklmoore.com/colorbox/).

	function prefix_filter_function() { return '3' }
	add_filter( 'lgljl_lightbox_theme_style', 'prefix_filter_function' );

## Changelog

### 1.0.0: Mar 27, 2013

* Initial version.