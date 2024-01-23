<?php
/**
 * @var stdClass $i18n
 * @var string $header_image_url
 * @var array $marketing_blocks
 */

?>
<div id="ngg_page_content">

	<div class="ngg_page_content_header">
		<img src="<?php print esc_attr( $header_image_url ); ?>"
			alt=""/>
		<h3>
			<?php print $i18n->page_title; ?>
		</h3>
	</div>

	<h3><?php esc_html_e( 'Unlock More Features', 'nggallery' ); ?></h3>
	<p>
		<strong><?php esc_html_e( 'Want even more features', 'nggallery' ); ?></strong>
		<?php $url = M_Marketing::get_utm_link( 'https://www.imagely.com/lite', 'extensions', 'wantevenmorefeatures' ); ?>
		<a href="<?php print esc_attr( $url ); ?>"
			target="_blank"
			rel="noreferrer noopener"><?php esc_html_e( 'Upgrade your NextGEN Gallery account', 'nggallery' ); ?></a>
		<?php esc_html_e( 'and unlock the following awesome features.', 'nggallery' ); ?>
	</p>

	<div id="ngg_upgrade_to_pro_page_wrapper">
		<?php
		$blocks_per_column = 2;
		$current           = 1;
		foreach ( $marketing_blocks as $block ) {
			/** @var C_Marketing_Block_Card $block */
			?>

			<!-- start new block -->
			<?php if ( $current === 1 ) { ?>
				<div class="wp-block-columns">
			<?php } ?>

			<?php print $block->render(); ?>

			<?php if ( $current === $blocks_per_column ) { ?>
				</div>
			<?php } ?>

			<?php
			if ( $current === $blocks_per_column ) {
				$current = 1;
			} else {
				++$current;
			}
			?>

		<?php } ?>
	</div>

</div>
