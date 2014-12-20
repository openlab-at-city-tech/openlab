<?php /* This template is only used on multisite installations */ ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_activation_page' ) ?>

		<div class="page row" id="activate-page">
                    
                    <div class="col-md-24">
                    
                    <div class="panel panel-default">

			<?php if ( bp_account_was_activated() ) : ?>

				<div class="panel-heading"><?php _e( 'Account Activated', 'buddypress' ) ?></div>
                                <div class="panel-body">
                                
                                <?php do_action( 'template_notices' ) ?>
                                
				<?php do_action( 'bp_before_activate_content' ) ?>

				<?php if ( isset( $_GET['e'] ) ) : ?>
					<p class="bp-template-notice updated no-margin no-margin-bottom"><?php _e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'buddypress' ) ?></p>
				<?php else : ?>
					<p class="bp-template-notice updated no-margin no-margin-bottom"><?php _e( 'Your account was activated successfully! You can now log in with the username and password you provided when you signed up.', 'buddypress' ) ?></p>
				<?php endif; ?>
                                </div>

			<?php else : ?>

				<div class="panel-heading"><?php _e( 'Activate your Account', 'buddypress' ) ?></div>
                                <div class="panel-body">
				<?php do_action( 'bp_before_activate_content' ) ?>

				<p><?php _e( 'Please provide a valid activation key.', 'buddypress' ) ?></p>

				<form action="" method="get" class="standard-form form" id="activation-form">

					<label for="key"><?php _e( 'Activation Key:', 'buddypress' ) ?></label>
					<input class="form-control" type="text" name="key" id="key" value="" />

					<p class="submit">
						<input class="btn btn-primary btn-margin btn-margin-top" type="submit" name="submit" value="<?php _e( 'Activate', 'buddypress' ) ?> &#xf138;" />
					</p>

				</form>
                                </div>

			<?php endif; ?>

			<?php do_action( 'bp_after_activate_content' ) ?>
                                
                    </div>
                        
                    </div>

		</div><!-- .page -->

		<?php do_action( 'bp_after_activation_page' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->
