<div class="item-list-tabs no-ajax" id="bpsubnav" role="navigation">
	<ul>
		<?php if ( bp_is_my_profile() ) { bp_get_options_nav(); } ?>

		<?php if ( !bp_is_current_action( 'invites' ) ) : ?>
		<li id="members-order-select" class="last filter">

			<?php _e( 'Order By:', 'buddypress' ) ?>
			<select id="groups-all">
				<option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
				<option value="popular"><?php _e( 'Most Members', 'buddypress' ); ?></option>
				<option value="newest"><?php _e( 'Newly Created', 'buddypress' ); ?></option>
				<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>

				<?php do_action( 'bp_member_group_order_options' ); ?>
			</select>
		</li>
		<?php endif; ?>
	</ul>
	<div class="clear"></div>
</div>
<?php 

if ( bp_is_current_action( 'invites' ) ) :
	gconnect_locate_template( array( 'members/single/groups/invites.php' ), true );
else :
	do_action( 'bp_before_member_groups_content' ); 
?>
	<div class="groups mygroups">
		<?php gconnect_locate_template( array( 'groups/groups-loop.php' ), true ); ?>
	</div>
<?php 
	do_action( 'bp_after_member_groups_content' );
endif;
