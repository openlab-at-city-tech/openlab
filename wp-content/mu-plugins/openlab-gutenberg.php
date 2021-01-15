<?php
/**
 * Gutenberg modifications for OpenLab network.
 *
 * @package OpenLab\Gutenberg
 */

namespace OpenLab\Gutenberg;

/**
 * Disable Block-Based Widgets screen.
 *
 * Introduced in Gutenberg 8.9.
 */
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
