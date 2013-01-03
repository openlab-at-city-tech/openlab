<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php 
		if ( bp_is_my_profile() )
			bp_get_options_nav();
		?>
	</ul>
</div>

<?php

if ( bp_is_current_action( 'notifications' ) ) :
	 gconnect_locate_template( array( 'members/single/settings/notifications.php' ), true );

elseif ( bp_is_current_action( 'delete-account' ) ) :
	 gconnect_locate_template( array( 'members/single/settings/delete-account.php' ), true );

elseif ( bp_is_current_action( 'general' ) ) :
	gconnect_locate_template( array( 'members/single/settings/general.php' ), true );

else :
	gconnect_locate_template( array( 'members/single/plugins.php' ), true );

endif;
