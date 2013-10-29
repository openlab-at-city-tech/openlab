<?php

/**
 * This template, which powers the group Send Invites tab when IA is enabled, can be overridden
 * with a template file at groups/single/invite-anyone.php
 *
 * @package Invite Anyone
 * @since 0.8.5
 */
?>

<?php do_action( 'bp_before_group_send_invites_content' ) ?>

<?php $group_type = openlab_get_group_type( bp_get_current_group_id()); ?>

<?php if ( !bp_get_new_group_id() ) : ?>
	<form action="<?php bp_group_permalink( groups_get_current_group() ) ?>/invite-anyone/send/" method="post" id="send-invite-form">
<?php endif; ?>

<div id="topgroupinvite">
	<h3>Invite OpenLab Members to Your <?php echo ucfirst($group_type); ?></h3>
	<h5><?php _e("Search for members to invite:", 'bp-invite-anyone') ?></h5>

	<ul class="first acfb-holder invite-search">
		<li>
			<input type="text" name="send-to-input" class="send-to-input" id="send-to-input" />
		</li>
	</ul>

	<div id="searchinvitemembersdescription">
		<p>Start typing a few letters of member's display name. When a dropdown list appears, select from the list.</p>
	</div>
</div>

<div class="left-menu">

	<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ) ?>
    <?php if ( invite_anyone_access_test() && !bp_is_group_create() ) : ?>
	<h5 class="invite-title"><?php _e('Invite new members by email:'); ?></h5>

    <p class="invite-copy"><?php _e('This link will take you to My Invitations, where you may invite people to join the OpenLab and this'.ucfirst($group_type)); ?></p>

    <p><a class="underline" href="<?php echo bp_loggedin_user_domain() . BP_INVITE_ANYONE_SLUG . '/invite-new-members/group-invites/' . bp_get_group_id() ?>"><?php _e( 'Invite New Members to OpenLab.', 'bp-invite-anyone' ) ?></a></p>
<?php endif; ?>

</div>

<div class="main-column">
	<h5 class="invite-title"><?php _e('Invites:'); ?></h5>
    <p class="invite-copy"><?php _e('These members will be sent an invitation to your '.ucfirst($group_type).'. Click the "Send Invites" button to continue.'); ?></p>

	<?php do_action( 'bp_before_group_send_invites_list' ) ?>

	<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
	<ul id="invite-anyone-invite-list" class="item-list">
	<?php if ( bp_group_has_invites() ) : ?>

		<?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

			<li id="<?php bp_group_invite_item_id() ?>">
				<?php bp_group_invite_user_avatar() ?>

				<h4><?php bp_group_invite_user_link() ?></h4>
				<span class="activity"><?php bp_group_invite_user_last_active() ?></span>

				<?php do_action( 'bp_group_send_invites_item' ) ?>

				<div class="action">
					<a class="remove" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><?php _e( 'Remove Invite', 'buddypress' ) ?></a>

					<?php do_action( 'bp_group_send_invites_item_action' ) ?>
				</div>
			</li>

		<?php endwhile; ?>

	<?php endif; ?>
	</ul>
	<?php do_action( 'bp_after_group_send_invites_list' ) ?>

</div>

<!-- <div class="clear"></div> -->

<?php if ( !bp_get_new_group_id() ) : ?>
<div class="submit">
	<input type="submit" name="submit" id="submit" value="<?php _e( 'Send Invites', 'buddypress' ) ?>" />
</div>
<?php endif; ?>

<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites') ?>

	<!-- Don't leave out this sweet field -->
<?php
if ( !bp_get_new_group_id() ) {
	?><input type="hidden" name="group_id" id="group_id" value="<?php bp_group_id() ?>" /><?php
} else {
	?><input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id() ?>" /><?php
}
?>

<?php if ( !bp_get_new_group_id() ) : ?>
	</form>
<?php endif; ?>

<?php do_action( 'bp_after_group_send_invites_content' ) ?>
