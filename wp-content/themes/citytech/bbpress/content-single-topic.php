<?php

/**
 * Single Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums">

	<?php bbp_breadcrumb(); ?>

	<?php do_action( 'bbp_template_before_single_topic' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

		<?php bbp_topic_tag_list(); ?>

		<?php bbp_single_topic_description(); ?>

		<?php if ( bbp_show_lead_topic() ) : ?>

			<?php bbp_get_template_part( 'content', 'single-topic-lead' ); ?>

		<?php endif; ?>

		<?php if ( bbp_has_replies() ) : ?>
			<div class="bbp-back-to-course-discussion">
				<?php /* Trick: use the buddypress string so it gets translated */ ?>
				<p><a href="<?php bp_group_permalink() ?>forum/">&larr; <?php _e( 'Group Forum', 'buddypress' ) ?></a></p>
			</div>

			<?php /* Prev/next - this is not beautiful */ ?>
			<?php
			$group_topics = new WP_Query( array(
				'post_type' => bbp_get_topic_post_type(),
				'post_parent' => bbp_get_forum_id(),
				'meta_key' => '_bbp_last_active_time',
				'orderby' => 'meta_value',
				'order' => 'DESC',
				'posts_per_page' => -1,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields' => 'ids',
			) );

			$this_topic_index = array_search( bbp_get_topic_id(), $group_topics->posts );
			$last_topic_index = end( $group_topics->posts );

			$prev_url = $next_url = '';

			// Previous is +1.
			if ( $this_topic_index < $last_topic_index ) {
				$prev_topic_id = $group_topics->posts[ $this_topic_index + 1 ];
				$prev_url = get_permalink( $prev_topic_id );
				$prev_link = '<a href="' . $prev_url . '">&lt;&lt;&lt; Previous Topic</a>';
			}

			// Next is -1.
			if ( $this_topic_index > 0 ) {
				$next_topic_id = $group_topics->posts[ $this_topic_index - 1 ];
				$next_url = get_permalink( $next_topic_id );
				$next_link = '<a href="' . $next_url . '">Next Topic &gt;&gt;&gt;</a>';
			}

			?>

			<div class="bbp-prev-next">
				<p>
					<?php echo implode( '&nbsp;&nbsp;&nbsp;', array( $prev_link, $next_link ) ) ?>
				</p>
			</div>

			<?php bbp_get_template_part( 'loop',       'replies' ); ?>

			<?php bbp_get_template_part( 'pagination', 'replies' ); ?>

		<?php endif; ?>

		<?php bbp_get_template_part( 'form', 'reply' ); ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_topic' ); ?>

</div>
