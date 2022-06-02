<?php
/**
 * BuddyPress - Activity Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

?>
<?php echo openlab_submenu_markup( 'group-activity' ); ?>
<div id="item-body" role="main">
    <?php do_action( 'bp_before_activity_loop' ); ?>

    <?php if ( bp_has_activities() ) : ?>
        <div class="activity-filter">
			<form action="" id="activity-filter-form" class="activity-filter-form" method="GET">
				<?php if( ! empty( $type ) ) { ?>
				<input type="hidden" name="type" value="<?php echo $type; ?>" />
				<?php } ?>
				<select id="activity-filter-by" class="form-control">
					<option value="-1"><?php _e( 'All Activity', 'openlab' ); ?></option>
					<?php 
						bp_activity_show_filters( 'group' );
						do_action( 'bp_group_activity_filter_options' ); 
					?>
				</select>
			</form>
		</div>
        
        <?php if ( empty( $_POST['page'] ) ) : ?>
			<div id="activity-stream" class="activity-list item-list group-list">
		<?php endif; ?>
	
		<?php while ( bp_activities() ) : bp_the_activity(); ?>
			<?php bp_get_template_part( 'activity/entry' ); ?>
		<?php endwhile; ?>
	
		<?php // echo openlab_activities_pagination_links(); ?>
	
		<?php if ( empty( $_POST['page'] ) ) : ?>
			</div>	
		<?php endif; ?>

    <?php else : ?>
        <div id="message" class="info">
			<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ); ?></p>
		</div>
    <?php endif; ?>

    <?php do_action( 'bp_after_activity_loop' ); ?>

    <?php if ( empty( $_POST['page'] ) ) : ?>
        <form action="" name="activity-loop-form" id="activity-loop-form" method="post">
            <?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>
        </form>
    <?php endif; ?>
</div>
