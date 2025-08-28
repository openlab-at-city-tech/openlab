<?php
/**
 * Setting > Privacy
 */

$account_type = openlab_get_user_member_type( bp_displayed_user_id() );

$profile_args = [
	'exclude_fields' => [ openlab_get_xprofile_field_id( 'Email address (Student)' ) ],
	'exclude_groups' => openlab_get_exclude_groups_for_account_type( $account_type ),
];

$social_fields = openlab_social_media_fields();

$field_ids = [];

do_action( 'bp_before_member_settings_template' ); ?>

<?php echo openlab_submenu_markup(); ?>

<div id="item-body" role="main">
	<?php do_action( 'template_notices' ); ?>

	<form class="standard-form form-panel" id="bp-privacy" method="post">
		<div class="panel panel-default">
			<div class="panel-heading">Profile Privacy Settings</div>

			<div class="panel-body">
				<p>Adjust the settings below to choose who can see what on your OpenLab profile.</p>

				<?php if ( bp_has_profile( $profile_args ) ) : ?>
					<div class="panel-subheading">My Profile</div>

					<div class="privacy-panel-options">
						<?php while ( bp_profile_groups() ) :  ?>
							<?php bp_the_profile_group(); ?>

							<?php while ( bp_profile_fields() ) : ?>
								<?php bp_the_profile_field(); ?>

								<?php
								if ( bp_xprofile_get_meta( bp_get_the_profile_field_id(), 'field', 'is_legacy_social_media_field' ) ) {
									// Skip legacy social media fields.
									continue;
								}
								?>

								<?php $field_ids[] = bp_get_the_profile_field_id(); ?>

								<div class="privacy-panel-option">
									<label for="field-visibility-settings-select-<?php bp_the_profile_field_id(); ?>"><?php echo bp_get_the_profile_field_name(); ?></label>

									<div class="field-visibility-options">
										<span>Who can see this?</span>
										<?php openlab_xprofile_field_visibility_selector( bp_get_the_profile_field_id(), false ); ?>
									</div>
								</div>
							<?php endwhile; ?>
						<?php endwhile; ?>

						<?php foreach ( $social_fields as $field_slug => $field_data ) : ?>
							<?php
							$field_value = openlab_get_social_media_field_for_user( bp_displayed_user_id(), $field_slug );
							if ( ! $field_value ) {
								continue;
							}

							$field_ids[] = $field_data['field_id'];
							?>

							<div class="privacy-panel-option">
								<label for="field-visibility-settings-select-<?php echo esc_attr( $field_data['field_id'] ); ?>"><?php echo esc_html( $field_data['title'] ); ?></label>

								<div class="field-visibility-options">
									<span>Who can see this?</span>
									<?php openlab_xprofile_field_visibility_selector( $field_data['field_id'], false ); ?>
								</div>
							</div>

						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<input type="hidden" name="profile-privacy-field-ids" value="<?php echo esc_attr( implode( ',', $field_ids ) ); ?>" />

				<?php if ( openlab_user_has_portfolio( bp_displayed_user_id() ) && ( ! openlab_group_is_hidden( openlab_get_user_portfolio_id() ) ) ) : ?>
					<div class="panel-subheading">My <?php openlab_portfolio_label( [ 'case' => 'upper' ] ); ?></div>

					<div class="privacy-panel-options">
						<div class="privacy-panel-option">
							<label for="portfolio-visibility">Include a link to My Portfolio on My Profile</label>

							<div class="field-visibility-options">
								<select id="portfolio-visibility" name="portfolio-visibility">
									<option value="1" <?php selected( openlab_show_portfolio_link_on_user_profile() ); ?>>Enabled</option>
									<option value="0" <?php selected( openlab_show_portfolio_link_on_user_profile(), false ); ?>>Disabled</option>
								</select>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="panel panel-default panel-privacy-info">
			<div class="panel-heading">Display Name and Username Privacy</div>

			<div class="panel-body">
				<div class="panel-subheading">Display Name</div>
				<div class="privacy-panel-options">
					<p>Your Display Name will appear on your public OpenLab profile and wherever you post on the OpenLab. Because your Display Name is public, you don't need to use your real name or your full name. Your Display Name can be changed at any time by <a href="<?php echo esc_url( bp_loggedin_user_url( bp_members_get_path_chunks( [ 'profile', 'edit' ] ) ) ); ?>">editing your profile</a>.</p>
				</div>

				<div class="panel-subheading">Username</div>
				<div class="privacy-panel-options">
					<p>You use your username to sign in, and it will also be displayed in the URL of your public OpenLab member profile. You cannot change your username yourself. Please contact us for assistance if you have concerns.</p>
				</div>
			</div>
		</div>

		<div class="panel panel-default panel-privacy-info">
			<div class="panel-heading">Privacy in Courses, Projects, Clubs and Portfolios</div>

			<div class="panel-body">
				<div class="panel-subheading">Membership in a Course, Project or Club</div>
				<div class="privacy-panel-options">
					<p>There are a few ways to control your privacy when you are a <strong>member</strong> of a course, project, or club.</p>
					<ul>
						<li>If you don’t want to appear in the membership list of a public course, project, or club, you can <a href="https://openlab.citytech.cuny.edu/blog/help/hiding-your-membership-in-a-course-project-or-club/">hide your membership</a>.</li>
						<li>When you write a post on a site that is visible to anyone or to any OpenLab Member, you can choose to <a href="https://openlab.citytech.cuny.edu/blog/help/writing-a-post-block-editor#additionalprivacysettings">make that post visible</a> only to members of the site.</li>
					</ul>
				</div>

				<div class="panel-subheading">Admin of a Course, Project, Club, or Portfolio</div>
				<div class="privacy-panel-options">
					<p>There are a few ways to control privacy when you are an <strong>admin</strong> of a course, project, or club.</p>

					<ul>
						<li>There are various privacy options available for the profile and site of any course, project, club, and portfolio. You can <a href="https://openlab.citytech.cuny.edu/blog/help/changing-privacy-and-other-settings-for-a-course-project-or-club/">choose the combination of settings</a> that work best for you.</li>
						<li>When you write a post or create a page on a site that is visible to anyone or to any OpenLab Member, you can choose to <a href="https://openlab.citytech.cuny.edu/blog/help/writing-a-post-block-editor#additionalprivacysettings">make that post or page visible</a> only to members of the site.</li>
					</ul>
				</div>
			</div>
		</div>

		<input type="submit" name="save" id="save" class="btn btn-primary" value="Save Changes" />

		<?php wp_nonce_field( 'bp_settings_privacy' ); ?>
	</form>
</div>

<?php

do_action( 'bp_after_member_settings_template' );
