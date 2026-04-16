<?php
/**
 * Drawer toggle button partial.
 *
 * Variables expected:
 * - $drawer_id: The ID of the drawer this button controls.
 * - $options: Optional array of button options (see openlab_render_drawer_toggle()).
 */

if ( ! isset( $drawer_id ) ) {
	return;
}

$options = isset( $options ) ? $options : [];

echo openlab_render_drawer_toggle( $drawer_id, $options );
