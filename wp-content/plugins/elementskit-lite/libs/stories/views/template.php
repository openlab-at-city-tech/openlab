<style>
	.wpmet-str {
		display: flex;
		flex-wrap: wrap;
		width: 100%;
		margin-bottom: 10px;
	}
	.wpmet-str-thumb {
		width: 75px;
		padding-right: 7px;
		box-sizing: border-box;
		align-self: flex-start;
		padding-top: 5px;
	}
	.wpmet-str-thumb img {
		width: 100%;
		display: block;
		min-height: 34px;
	}
	.wpmet-str-desc.with-image {
		width: calc(100% - 75px);
		font-weight: 400;
		line-height: 1.5;
		font-size: 13px;
	}
	.wpmet-str-desc a {
		font-weight: 500;
		color: #0073aa;
		text-decoration: none;
		padding-bottom: 5px;
		display: inline-block;
	}
	.wpmet-str:last-child {
		margin-bottom: 0;
	}
	.wpmet-str-desc span {
	display: block;
	}
	.wpmet-bullet-wall {
		width: 6px;
		height: 6px;
		border-radius: 50%;
		background-color: black;
		display: inline-block;
		margin: 0 5px;
	}
	.wpmet-dashboard-widget-block {
		width: 100%;
	}
	.wpmet-dashboard-widget-block .wpmet-title-bar a{
		color: #23282d;
		font-weight: 400;
		font-size: 12px;
	}
	.wpmet-dashboard-widget-block .wpmet-title-bar {
		display: table;
		width: 100%;
		-webkit-box-shadow: 0 5px 8px rgba(0, 0, 0, 0.05);
		box-shadow: 0 5px 8px rgba(0, 0, 0, 0.05);
		margin: 0 -12px 8px;
		padding: 0 12px 12px;
	}
	.wpmet-dashboard-widget-block .wpmet-footer-bar {
		border-top: 1px solid #eee;
		padding-top: 1rem;
	}
	.wpmet-dashboard-widget-block .wpmet-footer-bar a {
		padding: 0 5px;
	}
	.wpmet-dashboard-widget-block a {
		text-decoration: none;
		font-size: 14px;
		color: #007cba;
		font-weight: 600;
	}
	.wpmet-str .wpmet-banner {
		width: 100%;
	}
	.wpmet-dashboard-widget-block .dashicons {
		vertical-align: middle;
		font-size: 17px;
	}
</style>

<div class="wpmet-dashboard-widget-block">
	<div class="wpmet-title-bar">
		<?php
		foreach ( $this->plugin_link as $k => $link ) {
			echo '<a target="_blank" href="' . esc_url($link[1]) . '">' . esc_html($link[0]) . '</a>';
			if ( isset( $this->plugin_link[ $k + 1 ] ) ) {
				echo '<div class="wpmet-bullet-wall"></div>';
			}
		}
		?>
	</div>
</div>

<?php 
foreach ( $this->stories as $story ) :
	if ( $story['type'] === 'news' || $story['type'] === '' ) :
		?>
		<div class="wpmet-str <?php echo ( ( isset( $story['story_image'] ) && $story['story_image'] != '' ) ? 'with-image' : '' ); ?>">
			<?php if ( isset( $story['story_image'] ) && $story['story_image'] != '' ) : ?>
				<div class="wpmet-str-thumb">
					<img src="<?php echo esc_url( $story['story_image'] ); ?>" />
				</div>
			<?php endif; ?>

			<div  class="wpmet-str-desc">

				<a target="_blank" href="<?php echo esc_url( $story['story_link'] ); ?>">
					<?php echo esc_html( $story['title'] ); ?>    
				</a>

				<?php if ( isset( $story['description'] ) && $story['description'] != '' ) : ?>
					<span><?php echo esc_html( $story['description'] ); ?>  </span>
				<?php endif; ?>
				
			</div>
		</div>
		<?php
	elseif ( $story['type'] === 'banner' ) :
		?>
		<div class="wpmet-str">
			<a target="_blank" href="<?php echo esc_url( $story['story_link'] ); ?>">
				<img class="wpmet-banner" src="<?php echo isset( $story['story_image'] ) && $story['story_image'] != '' ? esc_url($story['story_image']) : '#'; ?>" />
			</a>
		</div>
		<?php
	endif;
endforeach;
?>

<div class="wpmet-dashboard-widget-block">
	<div class="wpmet-footer-bar">
		<a href="https://wpmet.com/support-ticket" target="_blank">
			<?php echo esc_html__( 'Need Help?', 'elementskit-lite' ); ?> 
			<span aria-hidden="true" class="dashicons dashicons-external"></span>
		</a>
		<a href="https://wpmet.com/blog/" target="_blank">
		<?php echo esc_html__( 'Blog', 'elementskit-lite' ); ?> 
			<span aria-hidden="true" class="dashicons dashicons-external"></span>
		</a>
		<a href="https://wpmet.com/fb-group" target="_blank" style="color: #27ae60;">
			<?php echo esc_html__( 'Facebook Community', 'elementskit-lite' ); ?> 
			<span aria-hidden="true" class="dashicons dashicons-external"></span>
		</a>
	</div>
</div>
