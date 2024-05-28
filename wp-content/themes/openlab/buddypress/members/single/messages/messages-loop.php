<?php do_action( 'bp_before_member_messages_loop' ) ?>

<p class="inappropriate-message-notice">
	We are phasing out OpenLab messages. Please <a href="https://openlab.citytech.cuny.edu/blog/help/contact-us">contact the OpenLab Team</a> if you have any questions or concerns.
</p>

<?php if ( bp_has_message_threads() ) : ?>

	<?php do_action( 'bp_before_member_messages_threads' ) ?>

		<?php
		global $messages_template, $bp;
		while ( bp_message_threads() ) : bp_message_thread();
			$mstatus = true;
			$read = 'unread';
			if ( isset( $_GET['status'] ) && 'unread' === $_GET['status'] ) {
				$mstatus = bp_message_thread_has_unread();
			}
			if ( isset( $_GET['status'] ) && 'read' === $_GET['status'] ) {
				$mstatus = ! bp_message_thread_has_unread();
			}
			$read = 'read';
			?>

			<?php if ( $mstatus ) { ?>
			<div id="m-<?php bp_message_thread_id() ?>" class="message col-xs-12 <?php echo $read ?>">
				<div class="group-item-wrapper">
					<div class="item-avatar col-sm-9 col-xs-7">
						<a href="<?php bp_message_thread_view_link() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar( array( 'item_id' => $messages_template->thread->last_sender_id, 'object' => 'member', 'type' => 'full', 'html' => false ) ) ?>" alt="Message #<?php echo bp_message_thread_id(); ?>"/></a>
					</div>

					<div class="item col-sm-15 col-xs-17">
						<h2 class="item-title"><a  class="no-deco"href="<?php bp_message_thread_view_link() ?>" title="<?php _e( 'View Message', 'buddypress' ); ?>"><?php bp_message_thread_subject() ?></a></h2>

						<div class="info-line">
							<?php if ( 'sentbox' != bp_current_action() ) : ?>
								<?php _e( 'From:', 'buddypress' ); ?> <?php bp_message_thread_from() ?><br />
							<?php else : ?>
								<?php _e( 'To:', 'buddypress' ); ?> <?php bp_message_thread_to() ?><br />
							<?php endif; ?>
						</div>

						<div class="timestamp">
							<span class="fa fa-undo"></span> <span class="timestamp"><?php bp_message_thread_last_post_date() ?></span>
						</div>

						<p class="thread-excerpt"><?php bp_message_thread_excerpt() ?>... <a href="<?php bp_message_thread_view_link() ?>" class="read-more" title="<?php _e( 'View Message', 'buddypress' ); ?>">See More</a></p>

						<?php do_action( 'bp_messages_inbox_list_item' ) ?>
					</div>

					<div class="message-actions">
						<?php if ( bp_message_thread_has_unread() ) : ?> <span class="message-unread">Unread</span> <span class="sep">|</span><?php endif; ?><a class="delete-button confirm" href="<?php bp_message_thread_delete_link() ?>" title="<?php _e( 'Delete Message', 'buddypress' ); ?>"><i class="fa fa-minus-circle"></i>Delete</a>
					</div>
			</div>
			</div>
		<?php } ?>
		<?php endwhile; ?>

	<div id="pag-bottom" class="pagination">

		<div class="pagination-links" id="messages-dir-pag">
			<?php echo openlab_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'bp_after_member_messages_pagination' ) ?>
<?php /*
	<div class="messages-options-nav">
		<?php bp_messages_options() ?>
	</div><!-- .messages-options-nav -->

	<?php do_action( 'bp_after_member_messages_threads' ) ?>

	<?php do_action( 'bp_after_member_messages_options' ) ?>
 */ ?>
<?php else : ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no messages were found.', 'buddypress' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_member_messages_loop' ) ?>
