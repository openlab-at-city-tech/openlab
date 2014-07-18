<?php if ( bp_is_group_admin_page() && bp_group_is_visible() ) : ?>
	<?php locate_template( array( 'buddypress/groups/single/admin.php' ), true ) ?>

<?php elseif ( bp_is_group_members() && bp_group_is_visible() ) : ?>
	<?php locate_template( array( 'buddypress/groups/single/members.php' ), true ) ?>

<?php elseif ( bp_is_group_invites() && bp_group_is_visible() ) : ?>
	<?php locate_template( array( 'buddypress/groups/single/send-invites.php' ), true ) ?>

<?php elseif ( bp_is_group_forum() && bp_group_is_visible() ) : ?>
	<?php locate_template( array( 'buddypress/groups/single/forum.php' ), true ) ?>

<?php elseif ( bp_is_group_membership_request() ) : ?>
	<?php locate_template( array( 'buddypress/groups/single/request-membership.php' ), true ) ?>
<?php endif; ?>
