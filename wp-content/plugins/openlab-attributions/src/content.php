<?php

namespace OpenLab\Attributions\Content;

use const OpenLab\Attributions\ROOT_DIR;
use function OpenLab\Attributions\Helpers\get_supported_post_types;

function render_attributions( $content ) {
	if ( ! is_singular( get_supported_post_types() ) ) {
		return $content;
	}

	$post         = get_post();
	$attributions = get_post_meta( $post->ID, 'attributions', true );

	if ( empty( $attributions ) ) {
		return $content;
	}

	// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	extract(
		[
			'attributions' => $attributions,
		],
		EXTR_SKIP
	);

	ob_start();
	require_once ROOT_DIR . '/views/attributions.php';
	$content .= ob_get_clean();

	return $content;
}
add_filter( 'the_content', __NAMESPACE__ . '\\render_attributions', 12 );
