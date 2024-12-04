<?php

/**
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme simple-nova for publication on WordPress.org
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

require_once get_template_directory() . '/inc/tgm/class-tgm-plugin-activation.php';

add_action('tgmpa_register', 'simple_nova_register_required_plugins', 0);
function simple_nova_register_required_plugins()
{
	$plugins = array(
		array(
			'name'      => 'Superb Addons',
			'slug'      => 'superb-blocks',
			'required'  => false,
		),
	);

	$config = array(
		'id'           => 'simple-nova',
		'default_path' => '',
		'menu'         => 'tgmpa-install-plugins',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => true,
		'message'      => '',
	);

	tgmpa($plugins, $config);
}


function simple_nova_pattern_styles()
{
	wp_enqueue_style('simple-nova-patterns', get_template_directory_uri() . '/assets/css/patterns.css', array(), filemtime(get_template_directory() . '/assets/css/patterns.css'));
	if (is_admin()) {
		global $pagenow;
		if ('site-editor.php' === $pagenow) {
			// Do not enqueue editor style in site editor
			return;
		}
		wp_enqueue_style('simple-nova-editor', get_template_directory_uri() . '/assets/css/editor.css', array(), filemtime(get_template_directory() . '/assets/css/editor.css'));
	}
}
add_action('enqueue_block_assets', 'simple_nova_pattern_styles');


add_theme_support('wp-block-styles');

// Removes the default wordpress patterns
add_action('init', function () {
	remove_theme_support('core-block-patterns');
});

// Register customer Simple Nova pattern categories
function simple_nova_register_block_pattern_categories()
{
	register_block_pattern_category(
		'heros',
		array(
			'label'       => __('Heros', 'simple-nova'),
			'description' => __('Simple Nova hero patterns', 'simple-nova'),
		)
	);
	register_block_pattern_category(
		'navigation_headers',
		array(
			'label'       => __('Headers', 'simple-nova'),
			'description' => __('Simple Nova navigation header patterns', 'simple-nova'),
		)
	);
	register_block_pattern_category(
		'teams',
		array(
			'label'       => __('Teams', 'simple-nova'),
			'description' => __('Simple Nova team patterns', 'simple-nova'),
		)
	);
	register_block_pattern_category(
		'testimonials',
		array(
			'label'       => __('Testimonials', 'simple-nova'),
			'description' => __('Simple Nova testimonial patterns', 'simple-nova'),
		)
	);
	register_block_pattern_category(
		'contact',
		array(
			'label'       => __('Contact', 'simple-nova'),
			'description' => __('Simple Nova contact patterns', 'simple-nova'),
		)
	);
}

add_action('init', 'simple_nova_register_block_pattern_categories');



// Initialize information content
require_once trailingslashit(get_template_directory()) . 'inc/vendor/autoload.php';

use SuperbThemesThemeInformationContent\ThemeEntryPoint;

define('SUPERBTHEMES_INFO_CONTENT_TEXT_DOMAIN', "simple-nova");

ThemeEntryPoint::init([
	"templates" => [
		array(
			'name' => __("Front Page", "simple-nova"),
			'frontpage' => true,
			'required' => true,
			'image' => 'front-page.png',
		),
		array(
			'name' => __("About", "simple-nova"),
			'required' => false,
			'slug' => 'about',
			'image' => 'about.png',
		),
		array(
			'name' => __("Contact", "simple-nova"),
			'required' => false,
			'slug' => 'contact',
			'image' => 'contact.png',
		),
		array(
			'name' => __("Blog", "simple-nova"),
			'template_only' => true,
			'required' => true,
			'image' => 'blog.png',
		),
		array(
			'name' => __("Page", "simple-nova"),
			'template_only' => true,
			'required' => true,
			'image' => 'pages.png',
		),
		array(
			'name' => __("Post", "simple-nova"),
			'template_only' => true,
			'required' => true,
			'image' => 'posts.png',
		),
		array(
			'name' => __("Archives", "simple-nova"),
			'template_only' => true,
			'required' => true,
			'image' => 'archives.png',
		),
		array(
			'name' => __("Search", "simple-nova"),
			'template_only' => true,
			'required' => true,
			'image' => 'search.png',
		),
		array(
			'name' => __("404", "simple-nova"),
			'template_only' => true,
			'required' => true,
			'image' => '404.png',
		),
	],
	'theme_url' => 'https://superbthemes.com/simple-nova/',
	'demo_url' => 'https://superbthemes.com/demo/simple-nova/'
]);
