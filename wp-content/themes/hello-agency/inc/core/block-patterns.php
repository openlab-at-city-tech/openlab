<?php

/**
 * hello-agency: Block Patterns
 *
 * @since hello-agency 1.0.0
 */

/**
 * Registers pattern categories for hello-agency
 *
 * @since hello-agency 1.0.0
 *
 * @return void
 */
function hello_agency_register_pattern_category()
{
	$block_pattern_categories = array(
		'hello-agency' => array('label' => __('Hello Agency Patterns', 'hello-agency'))
	);

	$block_pattern_categories = apply_filters('hello_agency_block_pattern_categories', $block_pattern_categories);

	foreach ($block_pattern_categories as $name => $properties) {
		if (!WP_Block_Pattern_Categories_Registry::get_instance()->is_registered($name)) {
			register_block_pattern_category($name, $properties); // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_pattern_category
		}
	}
}
add_action('init', 'hello_agency_register_pattern_category', 9);
