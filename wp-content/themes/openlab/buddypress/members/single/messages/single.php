<div id="message-thread">

	<?php do_action( 'bp_before_message_thread_content' ) ?>

	<?php if ( bp_thread_has_messages() ) : ?>

		<h3 id="message-subject"><?php bp_the_thread_subject() ?></h3>

		<p id="message-recipients">
			<span class="highlight">
				<?php printf( __( 'Sent between %s and %s', 'buddypress' ), bp_get_the_thread_recipients(), '<a href="' . bp_get_loggedin_user_link() . '" title="' . bp_get_loggedin_user_fullname() . '">' . bp_get_loggedin_user_fullname() . '</a>' ) ?> <span class="sep">|</a><a class="delete-button confirm" href="<?php bp_the_thread_delete_link() ?>" title="<?php _e( 'Delete Message', 'buddypress' ); ?>"><i class="fa fa-minus-circle"></i>Delete</a>
			</span>
		</p>

		<p class="inappropriate-message-notice">
			Please <a href="https://openlab.citytech.cuny.edu/blog/help/contact-us">contact the OpenLab Team</a> if you receive a message you feel is inappropriate or violates the <a href="https://openlab.citytech.cuny.edu/blog/help/community-guidelines/">OpenLab Community Guidelines</a>.
		</p>

		<?php do_action( 'bp_before_message_thread_list' ) ?>

		<?php while ( bp_thread_messages() ) : bp_thread_the_message(); ?>

			<div class="message-box panel panel-default">

                            <div class="panel-body">

				<div class="message-metadata">

					<?php do_action( 'bp_before_message_meta' ) ?>

					<?php bp_the_thread_message_sender_avatar( 'type=thumb&width=30&height=30' ) ?>
					<strong><a href="<?php bp_the_thread_message_sender_link() ?>" title="<?php bp_the_thread_message_sender_name() ?>"><?php bp_the_thread_message_sender_name() ?></a> <span class="activity"><?php bp_the_thread_message_time_since() ?></span></strong>

					<?php do_action( 'bp_after_message_meta' ) ?>

				</div><!-- .message-metadata -->

				<?php do_action( 'bp_before_message_content' ) ?>

				<div class="message-content">

					<?php bp_the_thread_message_content() ?>

				</div><!-- .message-content -->

				<?php do_action( 'bp_after_message_content' ) ?>

				</div>

			</div><!-- .message-box -->

		<?php endwhile; ?>

		<?php do_action( 'bp_after_message_thread_list' ) ?>

		<?php do_action( 'bp_before_message_thread_reply' ) ?>

		<form id="send-reply" action="<?php bp_messages_form_action() ?>" method="post" class="standard-form form-panel">

			<div class="message-box panel panel-default">

                            <div class="panel-heading semibold">
				<div class="message-metadata">

					<?php do_action( 'bp_before_message_meta' ) ?>

					<div class="avatar-box">
						<?php bp_loggedin_user_avatar( 'type=thumb&height=30&width=30' ) ?>

						<strong><?php _e( 'Send a Reply', 'buddypress' ) ?></strong>
					</div>

					<?php do_action( 'bp_after_message_meta' ) ?>

				</div><!-- .message-metadata -->
                            </div>

                            <div class="panel-body">
				<div class="message-content">

					<?php do_action( 'bp_before_message_reply_box' ) ?>

					<textarea class="form-control bp-suggestions" name="content" id="message_content" rows="15" cols="40"></textarea>

					<?php do_action( 'bp_after_message_reply_box' ) ?>

					<div class="submit">
						<input class="btn btn-primary" type="submit" name="send" value="<?php _e( 'Send Reply', 'buddypress' ) ?> &rarr;" id="send_reply_button"/>
						<span class="ajax-loader"></span>
					</div>

					<input type="hidden" id="thread_id" name="thread_id" value="<?php bp_the_thread_id(); ?>" />
					<?php wp_nonce_field( 'messages_send_message', 'send_message_nonce' ) ?>

				</div><!-- .message-content -->
                            </div>

			</div><!-- .message-box -->

		</form><!-- #send-reply -->

		<?php do_action( 'bp_after_message_thread_reply' ) ?>

	<?php endif; ?>

	<?php do_action( 'bp_after_message_thread_content' ) ?>

</div>
