<?php

use OpenLab\Favorites\Favorite\Query;

$type = ( isset( $_GET['type'] ) ) ? $_GET['type'] : '';

switch( $type ) {
	case 'mine':
		$args = [
			'scope' => 'just-me'
		];
		break;
	case 'favorites':
		$favorites = Query::get_results(
			[
				'user_id' => bp_loggedin_user_id(),
			]
		);

		$group_ids = '';

		if( $favorites ) {
			$group_ids = [];
			foreach( $favorites as $favorite ) {
				array_push( $group_ids, $favorite->get_group_id() );
			}
		}

		$args = [
			'filter_query'	=> [
				'relation'	=> 'AND',
				'component'	=> [
					'column'	=> 'component',
					'value'		=> 'groups',
				],
				'group_id'	=> [
					'column'	=> 'item_id',
					'value'		=> $group_ids,
					'compare'	=> 'IN',
				],
			],
		];
		
		break;
	case 'mentions':
		$args = [
			'scope' => 'mentions'
		];
		break;
	case 'pins':
		$args = [
			'scope' => 'favorites'
		];
		break;
	default:
		$args = [
			'scope' => 'groups',
		];
}
?>

<?php echo openlab_submenu_markup( 'my-activity' ); ?>

<div id="item-body" role="main">
	<?php do_action( 'bp_before_activity_loop' ); ?>

	<?php if ( bp_has_activities( $args ) ) : ?>
		<?php if ( empty( $_POST['page'] ) ) : ?>
			<div id="activity-stream" class="activity-list item-list group-list">
		<?php endif; ?>
	
		<?php while ( bp_activities() ) : bp_the_activity(); ?>
			<?php bp_get_template_part( 'activity/entry' ); ?>
		<?php endwhile; ?>
	

		<?php if ( bp_activity_has_more_items() ) : ?>
			<div class="load-more">
				<p><strong>TODO: Standard pagination</strong></p>
			</div>
		<?php endif; ?>
	
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
