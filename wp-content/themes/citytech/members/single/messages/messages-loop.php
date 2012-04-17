<?php do_action( 'bp_before_member_messages_loop' ) ?>

<?php if ( bp_has_message_threads() ) : ?>

	<?php do_action( 'bp_before_member_messages_threads' ) ?>

		<?php
		$count = 1;
		global $messages_template, $bp;
		while ( bp_message_threads() ) : bp_message_thread(); 
		$mstatus = true;
		if ( $_GET["status"] == 'unread' ) 
			$mstatus = bp_message_thread_has_unread();
		if ( $_GET["status"] == 'read' )
			$mstatus = !bp_message_thread_has_unread();
		?>
		<?php if ( $mstatus ) { ?>
			<div id="m-<?php bp_message_thread_id() ?>"<?php if ( bp_message_thread_has_unread() ) : ?> class="unread message<?php echo cuny_o_e_class($count) ?>"<?php else: ?> class="read message<?php echo cuny_o_e_class($count) ?>"<?php endif; ?>>
				<div class="item-avatar alignleft">	
					<?php echo bp_core_fetch_avatar( array( 'item_id' => $messages_template->thread->last_sender_id, 'type' => 'full' ) ) ?>
				</div>
				<div class="item">
					<h2 class="item-title"><a href="<?php bp_message_thread_view_link() ?>" title="<?php _e( "View Message", "buddypress" ); ?>"><?php bp_message_thread_subject() ?></a></h2>
					<div class="info-line">
						<?php if ( 'sentbox' != bp_current_action() ) : ?>
							<?php _e( 'From:', 'buddypress' ); ?> <?php bp_message_thread_from() ?><br />
							<span class="activity"><?php bp_message_thread_last_post_date() ?></span>
						<?php else: ?>
							<?php _e( 'To:', 'buddypress' ); ?> <?php bp_message_thread_to() ?><br />
							<span class="activity"><?php bp_message_thread_last_post_date() ?></span>
						<?php endif; ?>
					</div>
					<p class="thread-excerpt"><?php bp_message_thread_excerpt() ?>... <a href="<?php bp_message_thread_view_link() ?>" title="<?php _e( "View Message", "buddypress" ); ?>"><i><b>(See More)</b></i></a></p>
	
	
					<?php do_action( 'bp_messages_inbox_list_item' ) ?>
	
					<?php /* <input type="checkbox" name="message_ids[]" value="<?php bp_message_thread_id() ?>" /> 
					<a class="delete-button confirm" href="<?php bp_message_thread_delete_link() ?>" title="<?php _e( "Delete Message", "buddypress" ); ?>">Delete</a> &nbsp; */ ?>
				</div>
				</div>
				<?php if ( $count % 2 == 0 ) { echo '<hr style="clear:both;" />'; } ?>
				<?php $count++ ?>
		<?php } ?>
		<?php endwhile; ?>


	<div class="pagination no-ajax" id="user-pag">
	
		<div class="pagination-links" id="messages-dir-pag">
			<?php bp_messages_pagination() ?>
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
<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no messages were found.', 'buddypress' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_member_messages_loop' ) ?>