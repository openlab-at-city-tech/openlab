=== Genesis Responsive Slider ===
Contributors: marksabbath, nathanrice, studiopress, wpmuguru
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5553118
Tags: slider, slideshow, responsive, genesis, genesiswp, studiopress
Requires at least: 3.2
Tested up to: 5.1
Stable tag: 1.0.0

This plugin allows you to create a simple responsive slider that displays the featured image, along with the title and excerpt from each post.

== Description ==

This plugin allows you to create a simple responsive slider that displays the featured image, along with the title and excerpt from each post.

It includes options for the maximum dimensions of your slideshow, allows you to choose to display posts or pages, what category to pull from, and even the specific post IDs of the posts you want to display. It includes next/previous arrows and a pager along with the option to turn both on or off. Finally, you can place the slider into a widget area.

The slideshow is also responsive and will automatically adjust for the screen it is being displayed on.

Note: This plugin only supports Genesis child themes.

== Installation ==

1. Upload the entire `genesis-responsive-slider` folder to the `/wp-content/plugins/` directory
1. DO NOT change the name of the `genesis-responsive-slider` folder
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Navigate to the `Genesis > Slider Settings` menu
1. Configure the slider
1. In the "Widgets" screen, drag the "Genesis Responsive Slider" widget to the widget area of your choice

== Child Theme Integration ==

To adjust the slider defaults for a child theme use a filter similiar to the following:

`add_filter( 'genesis_responsive_slider_settings_defaults', 'my_child_theme_responsive_slider_defaults' );

function my_child_theme_responsive_slider_defaults( $defaults ) {
	$defaults = array(
		'post_type' => 'post',
		'posts_term' => '',
		'exclude_terms' => '',
		'include_exclude' => '',
		'post_id' => '',
		'posts_num' => 5,
		'posts_offset' => 0,
		'orderby' => 'date',
		'slideshow_timer' => 4000,
		'slideshow_delay' => 800,
		'slideshow_arrows' => 1,
		'slideshow_pager' => 1,
		'slideshow_loop' => 1,
		'slideshow_height' => 400,
		'slideshow_width' => 920,
		'slideshow_effect' => 'slide',
		'slideshow_excerpt_content' => 'excerpts',
		'slideshow_excerpt_content_limit' => 150,
		'slideshow_more_text' => '[Continue Reading]',
		'slideshow_excerpt_show' => 1,
		'slideshow_excerpt_width' => 50,
		'location_vertical' => 'bottom',
		'location_horizontal' => 'right',
		'slideshow_hide_mobile' => 1
	);
	return $defaults;
}
`

== Changelog ==

= 1.0.0 =
* Major restructuring
* Coding Standards compatibility
* Added clean up settings on uninstall

= 0.9.6 =
* WordPress compatibility

= 0.9.5 =
* Plugin header i18n

= 0.9.4 =
* Update POT file.

= 0.9.2 =
* add alt attribute to images for validation
* Fix image links

= 0.9.1 =
* Fix slider HTML markup for validation
* Fix SSL mixed content warning
* Add setting to turn off image links
* Fix Excerpt More filter to only apply to slides
* UI text changes

= 0.9.0 =
* Beta Release
