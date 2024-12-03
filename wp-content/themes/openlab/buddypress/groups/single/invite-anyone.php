<?php
/**
 * This template, which powers the group Send Invites tab when IA is enabled, can be overridden
 * with a template file at groups/single/invite-anyone.php
 *
 * @package Invite Anyone
 * @since 0.8.5
 */
?>

<?php do_action('bp_before_group_send_invites_content') ?>

<?php
$group_type  = openlab_get_group_type( bp_get_current_group_id() );
$member_type = openlab_get_user_member_type( bp_loggedin_user_id() );

$group_type_label = openlab_get_group_type_label( 'case=upper' );
?>

<?php if ( ! bp_get_new_group_id() ) : ?>
    <form action="<?php bp_group_permalink(groups_get_current_group()) ?>/invite-anyone/send/" method="post" class="form-panel" id="send-invite-form">
<?php endif; ?>

    <div id="topgroupinvite" class="panel panel-default">
        <div class="panel-heading semibold">Invite OpenLab Members to Your <?php echo ucfirst($group_type); ?></div>
        <div class="panel-body">

            <?php do_action('template_notices') ?>

            <label for="send-to-input"><?php _e("Search for members to invite:", 'bp-invite-anyone') ?></label>

            <ul class="first acfb-holder invite-search inline-element-list">
                <li>
                    <input type="text" name="send-to-input" class="send-to-input form-control" id="send-to-input" />
                </li>
            </ul>

            <div id="searchinvitemembersdescription">
                <p class="italics">Start typing a few letters of member's display name. When a dropdown list appears, select from the list.</p>

                <?php /* The ID 'friend-list' is important for AJAX support. */ ?>
                <ul id="invite-anyone-invite-list" class="item-list inline-element-list row">
                    <?php if (bp_group_has_invites()) : ?>

                        <?php while (bp_group_invites()) : bp_group_the_invite(); ?>

                            <li id="<?php bp_group_invite_item_id() ?>">
                                <?php bp_group_invite_user_avatar() ?>

                                <h4><?php bp_group_invite_user_link() ?></h4>
                                <span class="activity"><?php bp_group_invite_user_last_active() ?></span>

                                <?php do_action('bp_group_send_invites_item') ?>

                                <div class="action">
                                    <a class="remove" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><?php _e('Remove Invite', 'buddypress') ?></a>

                                    <?php do_action('bp_group_send_invites_item_action') ?>
                                </div>
                            </li>

                        <?php endwhile; ?>

                    <?php endif; ?>
                </ul>

            </div>

            <p class="invite-copy italics">
                <?php echo 'These members will be sent an invitation to your ' . ucfirst($group_type) . '.'; ?>

                <?php if (bp_is_group_create()) : ?>
                    Click 'Finish' to continue.
                <?php else : ?>
                    Click the "Send Invites" button to continue.
                <?php endif ?>
            </p>

            <?php do_action('bp_before_group_send_invites_list') ?>

            <?php if (!bp_get_new_group_id()) : ?>
                <div class="submit">
                    <input class="btn btn-primary" type="submit" name="submit" id="submit" value="<?php _e('Send Invites', 'buddypress') ?>" />
                </div>
            <?php endif; ?>

            <?php do_action('bp_after_group_send_invites_list') ?>
        </div>
    </div>

    <?php wp_nonce_field('groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user') ?>

    <?php if (invite_anyone_access_test() && !bp_is_group_create()) : ?>
        <div class="panel panel-default">
            <div class="panel-heading semibold"><?php _e('Invite new members by email:'); ?></div>
            <div class="panel-body">

                <p class="invite-copy"><?php _e('This link will take you to My Invitations, where you may invite people to join the OpenLab and this ' . ucfirst($group_type)); ?></p>

                <p><a class="btn btn-primary no-deco" href="<?php echo bp_loggedin_user_domain() . BP_INVITE_ANYONE_SLUG . '/invite-new-members/group-invites/' . bp_get_group_id() ?>"><?php _e('Invite New Members to OpenLab.', 'bp-invite-anyone') ?></a></p>

            </div>
        </div>
    <?php endif; ?>

    <!-- <div class="clear"></div> -->

    <?php wp_nonce_field('groups_send_invites', '_wpnonce_send_invites') ?>

    <!-- Don't leave out this sweet field -->
    <?php
    if (!bp_get_new_group_id()) {
        ?><input type="hidden" name="group_id" id="group_id" value="<?php bp_group_id() ?>" /><?php
    } else {
        ?><input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id() ?>" /><?php
    }
    ?>

<?php if (!bp_get_new_group_id()) : ?>
    </form>
<?php endif; ?>

<?php do_action('bp_after_group_send_invites_content') ?>

<?php if ( bp_is_item_admin() && in_array( $group_type, [ 'course', 'project', 'club' ] ) && in_array( $member_type, [ 'faculty', 'staff' ] ) ) : ?>

	<?php
	$import_results = null;
	if ( ! empty( $_GET['import_id'] ) ) {
		$import_id      = intval( wp_unslash( $_GET['import_id'] ) );
		$import_results = groups_get_groupmeta( bp_get_current_group_id(), 'import_' . $import_id );
	}
	?>
	<?php if ( ! bp_get_new_group_id() ) : ?>
		<form method="post" id="import-members-form" class="form-panel" action="<?php echo esc_attr( bp_get_group_permalink( groups_get_current_group() ) ); ?>invite-anyone/">
	<?php endif; ?>

		<div class="panel panel-default">
			<div class="panel-heading semibold">Import Members to Your <?php echo esc_html( openlab_get_group_type_label( 'case=upper' ) ); ?></div>
			<div class="panel-body">

				<?php $show_submit_border = false; ?>

				<?php if ( $import_results ) : ?>
					<?php if ( ! empty( $import_results['success'] ) ) : ?>
						<?php
						$user_links = [];
						foreach ( $import_results['success'] as $success_email ) {
							$success_user = get_user_by( 'email', $success_email );
							if ( ! $success_user ) {
								continue;
							}

							$user_links[] = sprintf(
								'<li><a href="%s">%s</a> (%s)</li>',
								esc_attr( bp_core_get_user_domain( $success_user->ID ) ),
								esc_html( bp_core_get_user_displayname( $success_user->ID ) ),
								esc_html( $success_email )
							);
						}
						?>

						<?php if ( $user_links ) : ?>
							<div class="import-results-section import-results-section-success">
								<p class="invite-copy">The following OpenLab members were successfully added to your <?php echo esc_html( ucfirst( $group_type ) ); ?>: <ul><?php echo implode( '', $user_links ); ?></ul>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( ! empty( $import_results['illegal_address'] ) ) : ?>
						<?php $show_submit_border = true; ?>
						<div class="import-results-section import-results-section-illegal">
							<p class="invite-copy">The following email addresses are not valid for the OpenLab. Please note that OpenLab members must have an <strong>@mail.citytech.cuny.edu</strong> or <strong>@citytech.cuny.edu</strong> email address in order to join.</p>

							<label for="illegal-addresses" class="sr-only">Illegal addresses</label>
							<textarea name="illegal-addresses" class="form-control" id="illegal-addresses"><?php echo esc_textarea( implode( ', ', $import_results['illegal_address'] ) ); ?></textarea>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $import_results['invalid_address'] ) ) : ?>
						<?php
						$invalid = [];
						foreach ( $import_results['invalid_address'] as $invalid_address ) {
							$invalid[] = sprintf(
								'<strong>%s</strong>',
								esc_html( $invalid_address )
							);
						}
						?>

						<?php if ( $invalid ) : ?>
							<?php $show_submit_border = true; ?>
							<div class="import-results-section import-results-section-invalid">
								<p class="invite-copy">The following don't appear to be valid email addresses. Please verify and resubmit. <?php echo implode( ', ', $invalid ); ?></p>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( ! empty( $import_results['not_found'] ) ) : ?>
						<?php $show_submit_border = true; ?>
						<div class="import-results-section import-results-section-not-found">
							<p class="invite-copy">The following email addresses are valid, but were not found in the OpenLab member directory. The link below wil take you to My Invitations > Invite New Members, where you may invite the following to join the OpenLab and your <?php echo esc_html( ucfirst( $group_type ) ); ?>.</p>

							<?php
							$invite_link = bp_loggedin_user_domain() . '/invite-anyone/';
							$invite_link = add_query_arg(
								[
									'emails'   => $import_results['not_found'],
									'group_id' => bp_get_current_group_id(),
								],
								$invite_link
							);
							?>

							<p class="invite-new-members-link"><span class="fa fa-chevron-circle-right" aria-hidden="true"></span> <a href="<?php echo esc_attr( $invite_link ); ?>">Invite the following to join the OpenLab and your <?php echo esc_html( ucfirst( $group_type ) ); ?></a></p>

							<label for="not-found-addresses" class="sr-only">Addresses not found in the system</label>
							<textarea name="not-found-addresses" class="form-control" id="not-found-addresses"><?php echo esc_textarea( implode( ', ', $import_results['not_found'] ) ); ?></textarea>
						</div>
					<?php endif; ?>

					<div class="import-results-section import-results-section-submit <?php if ( $show_submit_border ) { echo 'import-results-section-submit-show-border'; } ?> ">
						<p><a class="btn btn-primary no-deco" href="<?php echo esc_attr( bp_get_group_permalink( groups_get_current_group() ) . BP_INVITE_ANYONE_SLUG ); ?>/">Perform a new import</a></p>
					</div>

				<?php else : ?>
					<p class="invite-copy">Add OpenLab members to your <?php echo esc_html( $group_type_label ); ?> in bulk by entering a list of email addresses below. OpenLab members corresponding to this list will be added automatically to your <?php echo esc_html( $group_type_label ); ?> and will receive notification via email.</p>

					<p class="invite-copy import-acknowledge"><label><input type="checkbox" name="import-acknowledge-checkbox" id="import-acknowledge-checkbox" value="1" /> I acknowledge that the following individuals are officially enrolled in my course or have approved this action.</label></p>

					<label class="sr-only" for="email-addresses-to-import">Enter email addresses to import members to this <?php echo esc_html( ucfirst( $group_type ) ); ?></label>
					<textarea name="email-addresses-to-import" id="email-addresses-to-import" class="form-control" placeholder="Add an @mail.citytech.cuny.edu or @citytech.cuny.edu email addresses using a comma-separated list and/or one address per line."></textarea>

					<p><input type="submit" class="btn btn-primary no-deco" value="Import" /></p>
				<?php endif; ?>
			</div>
		</div>

		<?php wp_nonce_field( 'group_import_members', 'group-import-members-nonce' ) ?>

	<?php if ( ! bp_get_new_group_id() ) : ?>
		</form>
	<?php endif; ?>
<?php endif; ?>
