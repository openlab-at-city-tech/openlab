<?php if ( bp_is_group_admin_page() && bp_group_is_visible() ) : ?>
	<?php gconnect_locate_template( array( 'groups/single/admin.php' ), true ) ?>

<?php elseif ( bp_is_group_members() && bp_group_is_visible() ) : ?>
	<?php gconnect_locate_template( array( 'groups/single/members.php' ), true ) ?>

<?php elseif ( bp_is_group_invites() && bp_group_is_visible() ) : ?>
	<?php gconnect_locate_template( array( 'groups/single/send-invites.php' ), true ) ?>

<?php elseif ( bp_is_group_forum() && bp_group_is_visible() ) : ?>
	<?php gconnect_locate_template( array( 'groups/single/forum.php' ), true ) ?>

<?php elseif ( bp_is_group_membership_request() ) : ?>
	<?php gconnect_locate_template( array( 'groups/single/request-membership.php' ), true ) ?>
<?php endif; ?>
