<?php if ( bp_is_my_profile() ) : ?>
	<div class="item-list-tabs no-ajax" id="bpsubnav" role="navigation">
		<ul>
			<?php bp_get_options_nav(); ?>
		</ul>
		<div class="clear"></div>
	</div><!-- .item-list-tabs -->
<?php endif; ?>

<?php do_action( 'bp_before_profile_content' ); ?>

<div class="profile" role="main">
	<?php
		if ( bp_is_current_action( 'edit' ) )
			gconnect_locate_template( array( 'members/single/profile/edit.php' ), true );
		elseif ( bp_is_current_action( 'change-avatar' ) )
			gconnect_locate_template( array( 'members/single/profile/change-avatar.php' ), true );
		elseif ( bp_is_active( 'xprofile' ) )
			gconnect_locate_template( array( 'members/single/profile/profile-loop.php' ), true );
		else
			gconnect_locate_template( array( 'members/single/profile/profile-wp.php' ), true );
	?>
</div><!-- .profile -->

<?php
do_action( 'bp_after_profile_content' );