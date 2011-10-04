<?php
class CAC_Group_Announcements extends BP_Group_Extension {

	var $enable_create_step = false;
	var $enable_nav_item = true;
	var $enable_edit_item = false;

	function cac_group_announcements() {
		$this->name = 'Announcements';
		$this->slug = 'announcements';

		$this->nav_item_position = 31;
	}

	function display() {

		if ( bp_group_is_admin() || is_site_admin() )
			$group_role = 'an administrator';
		else if ( bp_group_is_mod() )
			$group_role = 'a moderator';
?>
	<h3>Announcements</h3>

	<p>On this page, you'll see announcements that administrators and moderators have left for the group.</p>

	<?php if ( bp_group_is_admin() || bp_group_is_mod() || is_site_admin() ) : ?>
		<p>As <?php echo $group_role ?> in <?php bp_group_name() ?>, you can post announcements to the group's activity stream. You can also opt to email these announcements to each member of the group.</p>

		<?php locate_template( array( 'activity/post-form.php'), true ) ?>
	<?php endif; ?>


	<div class="activity single-group">
		<?php locate_template( array( 'activity/activity-loop.php' ), true ) ?>
	</div>

<?php
	}

	function widget_display() {}
}
bp_register_group_extension( 'CAC_Group_Announcements' );

function bp_is_group_announcements() {
	global $bp;

	if ( $bp->current_component == 'groups' && $bp->current_action == 'announcements' )
		return true;
	else
		return false;
}


function cac_set_announcement_filter( $qs ) {
	if ( bp_is_group_announcements() )
		return $qs . 'type=activity_update&action=activity_update';
	else
		return $qs;
}
add_filter( 'bp_dtheme_ajax_querystring', 'cac_set_announcement_filter' );
?>