<?php

$vova_blocks_details_url = add_query_arg(
	array(
		'tab'       => 'plugin-information',
		'plugin'    => 'vova-blocks',
		'TB_iframe' => 'true',
		'width'     => '600',
		'height'    => '550',
	),
	network_admin_url( 'plugin-install.php' )
);

?>

<div class="su-admin-c-notice-pro su-admin-c-notice-vova-blocks">
	<div class="su-admin-c-notice-vova-blocks-content">
		<h2 class="su-admin-c-notice-vova-blocks-title"><?php esc_html_e( 'Building new pages with blocks?', 'shortcodes-ultimate' ); ?></h2>
		<p class="su-admin-c-notice-vova-blocks-description"><?php esc_html_e( 'If you use Shortcodes Ultimate and build new pages with blocks, try VovaBlocks — a lightweight collection of practical blocks for the WordPress editor.', 'shortcodes-ultimate' ); ?></p>
	</div>
	<p class="su-admin-c-notice-vova-blocks-action"><a href="<?php echo esc_url( $vova_blocks_details_url ); ?>" class="button button-primary thickbox open-plugin-details-modal" aria-label="<?php esc_attr_e( 'View details and install VovaBlocks', 'shortcodes-ultimate' ); ?>" data-title="VovaBlocks"><?php esc_html_e( 'View VovaBlocks', 'shortcodes-ultimate' ); ?> &rarr;</a></p>
</div>
