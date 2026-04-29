<?php
/**
 * Template for card marketing block.
 *
 * @var C_Marketing_Block_Card $block
 * @var string $link_text
 */

?>
<div class="wp-block-column upsell ngg-block-card">

	<div class="ngg-block-card-title">
		<img src="<?php print esc_attr( $block->icon ); ?>"
			alt="<?php print esc_attr( $block->title ); ?>"/>
		<h2>
			<?php echo esc_html( $block->title ); ?>
		</h2>
	</div>

	<p>
		<?php echo wp_kses_post( $block->description ); ?>
	</p>

	<div class="wp-block-buttons">
		<div class="wp-block-button">
			<?php // Allow 'empty' cards to be generated to maintain two-column layouts. ?>
			<?php if ( ! empty( $block->title ) || ! empty( $block->description ) ) { ?>
				<a class="wp-block-button__link has-text-color has-background no-border-radius"
					href="<?php echo esc_url( $block->get_upgrade_link() ); ?>"
					style="background-color:#9ebc1b;color:#ffffff" target="_blank"
					rel="noreferrer noopener">
					<?php echo esc_html( $link_text ); ?>
				</a>
			<?php } ?>
		</div>
	</div>
</div>