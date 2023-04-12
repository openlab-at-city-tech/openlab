<?php

namespace OpenLab\Attributions\Content;

use DOMDocument;

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

	return openlab_get_formatted_content_with_attributions($content);
}
add_filter( 'the_content', __NAMESPACE__ . '\\render_attributions', 12 );

/**
 * Replace content <span> attributions with <a> tags when printing it on the public site.
 */
function openlab_get_formatted_content_with_attributions($content = '') {
	$doc = new DOMDocument();
	@$doc->loadHTML( '<?xml encoding="UTF-8">' . $content );

	$finder = new \DomXPath($doc);
	$className = 'attribution-anchor';

	$nodes = $finder->query("//*[contains(@class, '$className')]");

	foreach($nodes as $node) {
		$newNode = $doc->createElement('a');
		$href = openlab_get_node_href_attribute( $node );
		$newNode->setAttribute('href', $href );
		$newNode->setAttribute('id', $node->getAttribute('id'));
		$newNode->setAttribute('aria-label', $node->getAttribute('aria-label'));
		$newNode->setAttribute('class', $node->getAttribute('class'));

		$node->parentNode->replaceChild($newNode, $node);
	}

	return $doc->saveHTML();
}

/**
 * Get the "href" value from the DOM Node
 */
function openlab_get_node_href_attribute( $node ) {
	// If the dom node has "href" attribute, return it's value
	if( ! empty( $node->getAttribute('href' ) ) ) {
		return $node->getAttribute('href');
	}

	// If the dom node has "data-href" attribute,  return it's value (introduced in the later version of the plugin)
	if( ! empty( $node->getAttribute('data-href') ) ) {
		return $node->getAttribute('data-href');
	}

	// If none of the above exist, try to generate the href value from the "id" attribute
	return '#ref-' . openlab_get_attribute_id( $node );
}

/**
 * Get the attribution ID from the "id" dom element attribute
 */
function openlab_get_attribute_id( $node ) {
	// Remove "anchor-" from string in format "anchor-ABC-123"
	return str_replace( 'anchor-', '', $node->getAttribute('id'));
}
