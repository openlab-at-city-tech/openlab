<?php
/**
 * @var \Imagely\NGG\Widget\MediaRSS $self
 * @var string $after_title
 * @var string $after_widget
 * @var string $before_widget
 * @var string $before_title
 * @var array $instance
 * @var string $title
 */
?>
<?php echo $before_widget . $before_title . $title . $after_title; ?>
<ul class='ngg-media-rss-widget'>
	<?php if ( $instance['show_global_mrss'] ) { ?>
		<li>
			<?php
			echo $self->get_mrss_link(
				nggMediaRss::get_mrss_url(),
				$instance['show_icon'],
				strip_tags( stripslashes( $instance['mrss_title'] ) ),
				stripslashes( $instance['mrss_text'] )
			);
			?>
		</li>
	<?php } ?>
</ul>
<?php echo $after_widget; ?>