<div class="item-list-tabs no-ajax" id="bpsubnav" role="navigation">
	<ul>
		<?php bp_get_options_nav(); ?>
	</ul>
	<div class="clear"></div>
</div><!-- .item-list-tabs -->
<?php

if ( bp_is_current_action( 'compose' ) ) :
	gconnect_locate_template( array( 'members/single/messages/compose.php' ), true );
elseif ( bp_is_current_action( 'view' ) ) :
	gconnect_locate_template( array( 'members/single/messages/single.php' ), true );
else :
	do_action( 'bp_before_member_messages_content' );
?>

	<div class="messages" role="main">
		<?php
			if ( bp_is_current_action( 'notices' ) )
				gconnect_locate_template( array( 'members/single/messages/notices-loop.php' ), true );
			else
				gconnect_locate_template( array( 'members/single/messages/messages-loop.php' ), true );
		?>
	</div><!-- .messages -->
<?php 

	do_action( 'bp_after_member_messages_content' );
endif;
