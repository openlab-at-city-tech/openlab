<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
* Template file to be displayed when trying to access a user who is blocking you
*
* @since 3.0.0
*
*/

$block = new BPTK_Block();
$profile_id = bp_displayed_user_id();
$user = bp_loggedin_user_id();

get_header( 'buddypress' ); ?>

<div id="bptk-blocked-template">

	<div id="buddypress">
		<div id="item-header-avatar">
			<a href="<?php bp_displayed_user_link(); ?>">

				<?php bp_displayed_user_avatar( 'type=full' ); ?>

			</a>
		</div><!-- #item-header-avatar -->

		<div id="item-header-content">

			<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
				<h2 class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></h2>
			<?php endif; ?>

			<h3><?php _e( 'This member has blocked you.', 'bp-toolkit' ) ?></h3>

			<p><?php _e( 'You cannot see their profile, or any of their activity. Click below to block them.', 'bp-toolkit' ) ?></p>

		</div>

		<div id="bptk-blocked-template-buttons">

			<?php

            echo '<div class="generic-button bptk-block-profile"><a href="' . $block->bptk_block_link( $user, $profile_id ) . '" class="activity-button">' . __( 'Block', 'bp-toolkit' ) . '</a></div>';

			echo '<p>' . esc_html__( 'See all the users you are currently blocking', 'bp-toolkit' ). ' <a href="' . bp_loggedin_user_domain() . 'settings/bptk_blocked' .  '">' . esc_html__('here', 'bp-toolkit' ) . '</a></p>';

            ?>

		</div>
	</div><!-- #buddypress -->

</div><!-- #bptk-blocked-template -->

<?php get_footer( 'buddypress' );
