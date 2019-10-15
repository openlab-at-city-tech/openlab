<?php get_header( 'buddypress' ); ?>

	<div id="content">
		<div class="padder">
			<div id="buddypress" class="buddypress-wrap">
				<p>&nbsp;</p>
				<h1 style="text-align:center;"><?php _e( 'Blocked Profile', 'bp-toolkit' ); ?></h1>
				<h2 style="text-align:center;"><?php _e( 'You have selected to block this profile', 'bp-toolkit' ); ?></h2>
				<p style="text-align:center;"><a href="<?php echo bp_loggedin_user_domain() . 'settings/bptk_blocked/'; ?>" class="button button-large"><?php _e( 'Manage Blocked Users', 'bp-toolkit' ); ?></a><br /><br /><br /><br /><br /></p>
			</div>
		</div>
	</div>

<?php get_footer( 'buddypress' ); ?>
