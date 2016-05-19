
		<li class="bpeo-upcoming-event-<?php echo esc_attr( $post->ID ) ?>">
			<div class="bpeo-upcoming-event-datetime">
				<span class="bpeo-upcoming-event-date"><?php echo date( 'M j, Y', strtotime( $post->StartDate ) ) ?></span> &middot; <span class="bpeo-upcoming-event-time"><?php echo date( 'g:ia', strtotime( $post->StartTime ) ) ?></span>
			</div>

			<a class="bpeo-upcoming-event-title" href="<?php echo esc_url( apply_filters( 'eventorganiser_calendar_event_link', get_permalink( $post->ID ), $post->ID ) ) ?>"><?php echo esc_html( $post->post_title ) ?></a>

		</li>