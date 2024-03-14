<div class="activity-list item-list inline-element-list sidebar-sublinks">
	<?php $alert_items = openlab_whats_happening_at_city_tech_alerts_feed_items(); ?>
	<?php if ( $alert_items ) : ?>
		<h3 class="whats-happening-subheader">City Tech Alerts!</h3>

		<?php foreach ( $alert_items as $alert_item ) : ?>
			<article>
				<div class="sidebar-block activity-block">
					<div class="activity-row clearfix">
						<div class="activity-content overflow-hidden">
							<div class="whats-happening-date alert-item-date">
								<?php echo esc_html( date( 'F j, Y', $alert_item['date'] ) ); ?>
							</div>

							<div class="whats-happening-content alert-item-content">
								<?php echo wp_kses_post( $alert_item['content'] ); ?>
							</div>

						</div>
					</div>
				</div>
			</article>
		<?php endforeach; ?>

	<?php endif; ?>

    <?php $news_items = openlab_whats_happening_at_city_tech_news_feed_items(); ?>
    <?php if ( $news_items ) : ?>
		<h3 class="whats-happening-subheader">City Tech News &amp; Events</h3>

        <?php foreach ( $news_items as $news_item ) : ?>
			<article>
				<div class="sidebar-block activity-block">
					<div class="activity-row clearfix">
						<div class="activity-content overflow-hidden">
							<div class="whats-happening-date news-item-date">
								<?php echo esc_html( date( 'F j, Y', $news_item['date'] ) ); ?>
							</div>

							<div class="whats-happening-content news-item-content">
								<p>
									<?php echo wp_kses_post( $news_item['content'] ); ?>
								</p>
							</div>

						</div>
					</div>
				</div>
			</article>
        <?php endforeach; ?>
    <?php endif; ?>

</div><!-- .activity-list -->
