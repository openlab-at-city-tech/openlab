<?php get_header( 'buddypress' ); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_activation_page' ); ?>

		<div class="page" id="activate-page">

			<h3><?php if ( bp_account_was_activated() ) :
				_e( 'Account Activated', 'bp-classic' );
			else :
				_e( 'Activate your Account', 'bp-classic' );
			endif; ?></h3>

			<?php do_action( 'template_notices' ); ?>

			<?php do_action( 'bp_before_activate_content' ); ?>

			<?php if ( bp_account_was_activated() ) : ?>

				<?php if ( isset( $_GET['e'] ) ) : ?>
					<p><?php _e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'bp-classic' ); ?></p>
				<?php else : ?>
					<p><?php printf( __( 'Your account was activated successfully! You can now <a href="%s">log in</a> with the username and password you provided when you signed up.', 'bp-classic' ), wp_login_url( bp_get_root_domain() ) ); ?></p>
				<?php endif; ?>

			<?php else : ?>

				<p><?php _e( 'Please provide a valid activation key.', 'bp-classic' ); ?></p>

				<form action="" method="post" class="standard-form" id="activation-form">

					<label for="key"><?php _e( 'Activation Key:', 'bp-classic' ); ?></label>
					<input type="text" name="key" id="key" value="<?php echo esc_attr( bp_get_current_activation_key() ); ?>" />

					<p class="submit">
						<input type="submit" name="submit" value="<?php esc_attr_e( 'Activate', 'bp-classic' ); ?>" />
					</p>

				</form>

			<?php endif; ?>

			<?php do_action( 'bp_after_activate_content' ); ?>

		</div><!-- .page -->

		<?php do_action( 'bp_after_activation_page' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar( 'buddypress' ); ?>

<?php get_footer( 'buddypress' ); ?>
