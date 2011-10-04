<?php if ( 'invites' == bp_current_action() ) : ?>
	<h3>Your Invites</h3>
	<?php locate_template( array( 'members/single/groups/invites.php' ), true ) ?>

<?php else : ?>

	<?php do_action( 'bp_before_member_groups_content' ) ?>

	<div class="groups mygroups">
		<?php locate_template( array( 'groups/groups-loop.php' ), true ) ?>
	</div>

	<?php do_action( 'bp_after_member_groups_content' ) ?>

<?php endif; ?>
