<?php

/**
 * BuddyPress - Users Friends
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php echo openlab_submenu_markup('friends'); ?>

<?php
switch ( bp_current_action() ) :

	// Home/My Friends
	case 'my-friends' :
		do_action( 'bp_before_member_friends_content' ); ?>

		<div class="members friends">

			<?php bp_get_template_part( 'members/members-loop' ) ?>

		</div><!-- .members.friends -->

		<?php do_action( 'bp_after_member_friends_content' );
		break;

	case 'requests' :
		bp_get_template_part( 'members/single/friends/requests' );
		break;

	// Any other
	default :
		bp_get_template_part( 'members/single/plugins' );
		break;
endswitch;
