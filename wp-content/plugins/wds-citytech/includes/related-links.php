<?php

/**
 * "Related Links" group functionality
 */

/**
 * Get a list of saved links for a group.
 *
 * @param int $group_id
 * @param string $mode 'display' to show saved fields, 'edit' to append an
 *	  empty field for building markup
 * @return array
 */
function openlab_get_group_related_links( $group_id, $mode = 'display' ) {
	$related_links_list = groups_get_groupmeta( bp_get_current_group_id(), 'openlab_related_links_list' );

	if ( ! is_array( $related_links_list ) ) {
		$related_links_list = array();

		if ( 'edit' === $mode ) {
			$related_links_list[1] = array(
				'name' => '',
				'url'  => '',
			);
		}
	}

	return $related_links_list;
}

/**
 * Catch Related Links List settings saves and process.
 */
function openlab_process_related_links_settings_save( $group_id ) {
	if ( ! empty( $_POST['related-links-list-enable'] ) ) {
		groups_update_groupmeta( $group_id, 'openlab_related_links_list_enable', '1' );

		$heading = isset( $_POST['related-links-list-heading'] ) ? wp_unslash( $_POST['related-links-list-heading'] ) : '';
		groups_update_groupmeta( $group_id, 'openlab_related_links_list_heading', $heading );

		$links = array();
		foreach ( $_POST['related-links'] as $rl ) {
			if ( ! empty( $rl['name'] ) && ! empty( $rl['url'] ) ) {
				$links[] = array(
					'name' => wp_unslash( $rl['name'] ),
					'url'  => wp_unslash( $rl['url'] ),
				);
			}
		}

		if ( empty( $links ) ) {
			groups_delete_groupmeta( $group_id, 'openlab_related_links_list' );
		} else {
			groups_update_groupmeta( $group_id, 'openlab_related_links_list', $links );
		}
	} else {
		groups_delete_groupmeta( $group_id, 'openlab_related_links_list_enable' );
	}
}
add_action( 'groups_group_settings_edited', 'openlab_process_related_links_settings_save' );

/**
 * Add the related links display to group sidebars.
 */
function openlab_related_links_list_group_display() {
	$group_id = bp_get_current_group_id();

	if ( ! groups_get_groupmeta( $group_id, 'openlab_related_links_list_enable' ) ) {
		return;
	}

	// Non-public groups shouldn't show this to non-members.
	$group = groups_get_current_group();
	if ( 'public' !== $group->status && empty( $group->user_has_access ) ) {
		return false;
	}

	$related_links = openlab_get_group_related_links( $group_id );

	// Nothing to show
	if ( empty( $related_links ) ) {
		return;
	}

	$heading = groups_get_groupmeta( $group_id, 'openlab_related_links_list_heading' );

	?>

	<div id="group-related-links-sidebar-widget" class="sidebar-widget">
		<?php if ( $heading ) : ?>
			<h2 class="sidebar-header">
				<?php echo esc_html( $heading ) ?>
			</h2>
		<?php endif ?>

            <div class="sidebar-block">
		<ul class="group-related-links-list group-data-list inline-element-list sidebar-sublinks">
		<?php foreach ( $related_links as $rldata ) : ?>
			<li><span class="fa fa-external-link"></span> <a href="<?php echo esc_url( $rldata['url'] ) ?>"><?php echo esc_html( $rldata['name'] ) ?></a></li>
		<?php endforeach ?>
		</ul>
            </div>
	</div>

	<?php
}
add_action( 'bp_group_options_nav', 'openlab_related_links_list_group_display', 15 );