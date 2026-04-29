<?php
/**
 * Template for single-line marketing block.
 *
 * @var C_Marketing_Block_Single_Line $block
 * @var string $link_text
 */

?>
<div class="ngg-marketing-single-line">
	<p>
		<?php echo esc_html( $block->title ); ?>
		&nbsp;
		<a class="ngg-marketing-single-line-link"
			href="<?php echo esc_url( $block->get_upgrade_link() ); ?>"
			target="_blank"
			rel="noreferrer noopener">
			<?php echo esc_html( $link_text ); ?>
		</a>
	</p>
</div>