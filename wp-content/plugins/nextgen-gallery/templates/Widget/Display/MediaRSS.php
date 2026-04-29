<?php
/**
 * Media RSS Widget Display Template.
 *
 * @package Nextgen Gallery
 * @var \Imagely\NGG\Widget\MediaRSS $self
 * @var string $after_title
 * @var string $after_widget
 * @var string $before_widget
 * @var string $before_title
 * @var array $instance
 * @var string $title
 */

?>
<?php echo wp_kses_post( $before_widget ) . wp_kses_post( $before_title ) . esc_html( $title ) . wp_kses_post( $after_title ); ?>
<ul class='ngg-media-rss-widget'>
	<?php if ( ! empty( $instance['show_icon'] ) ) { ?>
		<li>
			<?php
			echo wp_kses_post(
				$self->get_mrss_link(
					nggMediaRss::get_mrss_url(),
					$instance['show_icon'] ?? false,
					wp_strip_all_tags( wp_unslash( $instance['mrss_title'] ?? '' ) ),
					wp_unslash( $instance['mrss_text'] ?? '' )
				)
			);
			?>
		</li>
	<?php } ?>
</ul>
<?php echo wp_kses_post( $after_widget ); ?>
