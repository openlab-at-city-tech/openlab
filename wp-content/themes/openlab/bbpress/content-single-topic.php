<?php
/**
 * Single Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */
?>

	<?php bbp_breadcrumb(); ?>

	<?php do_action( 'bbp_template_before_single_topic' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

		<?php bbp_topic_tag_list(); ?>

		<?php if ( bbp_show_lead_topic() ) : ?>

			<?php bbp_get_template_part( 'content', 'single-topic-lead' ); ?>

		<?php endif; ?>

		<?php if ( bbp_current_user_can_access_create_reply_form() ) : ?>
		<?php elseif ( bbp_is_topic_closed() ) : ?>

			<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
				<div class="bbp-template-notice">
					<p><?php printf( __( 'The topic &#8216;%s&#8217; is closed to new replies.', 'bbpress' ), bbp_get_topic_title() ); ?></p>
				</div>
			</div>

		<?php elseif ( bbp_is_forum_closed( bbp_get_topic_forum_id() ) ) : ?>

			<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
				<div class="bbp-template-notice">
					<p><?php printf( __( 'The forum &#8216;%s&#8217; is closed to new topics and replies.', 'bbpress' ), bbp_get_forum_title( bbp_get_topic_forum_id() ) ); ?></p>
				</div>
			</div>

		<?php else : ?>

			<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
				<div class="bbp-template-notice">
					<p><?php is_user_logged_in() ? _e( 'You cannot reply to this topic.', 'bbpress' ) : _e( 'You must be logged in to reply to this topic.', 'bbpress' ); ?></p>
				</div>
			</div>

		<?php endif; ?>

		<?php if ( bbp_has_replies() ) : ?>

			<div class="panel panel-default">

				<?php bbp_get_template_part( 'loop', 'replies' ); ?>

			</div>

			<?php bbp_get_template_part( 'pagination', 'replies' ); ?>

		<?php endif; ?>

		<div class="bbp-back-to-course-discussion">
			<?php /* Trick: use the buddypress string so it gets translated */ ?>
			<p><a class="btn btn-primary link-btn" href="<?php bp_group_permalink(); ?>forum/"><span class="fa fa-chevron-circle-left"></span> <?php _e( 'All Topics', 'openlab' ); ?></a></p>
		</div>

		<?php bbp_get_template_part( 'form', 'reply' ); ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_topic' ); ?>
