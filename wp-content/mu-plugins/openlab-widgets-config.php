<?php

/**
 * Disable some widgets on non-root blog.
 *
 * Hooked at 99 because this is as late as we can go before WP loads them up.
 */
function openlab_disable_widgets_on_non_root_blogs() {
	if ( ! function_exists( 'bp_is_root_blog' ) || bp_is_root_blog() ) {
		return;
	}

	unregister_widget( 'InviteAnyoneWidget' );
	unregister_widget( 'BP_Core_Friends_Widget' );
	unregister_widget( 'BP_Core_Login_Widget' );
	unregister_widget( 'BP_Core_Members_Widget' );
	unregister_widget( 'BP_Messages_Sitewide_Notices_Widget' );
	unregister_widget( 'BP_Group_Documents_Newest_Widget' );
	unregister_widget( 'BP_Group_Documents_Popular_Widget' );
}
add_action('widgets_init', 'openlab_disable_widgets_on_non_root_blogs', 99);
