<?php

$h5p_hub_urls = [
	'http://openlabdev.org/h5ptesting',
];

add_action(
	'init',
	function() use ( $h5p_hub_urls ) {
		foreach ( $h5p_hub_urls as $hub_url ) {
			$handle = 'h5p-' . sanitize_title_with_dashes( $hub_url );
			wp_embed_register_handler( $handle, '#' . preg_quote( trailingslashit( $hub_url ) ) . 'wp-admin/admin-ajax.php\?action=h5p_embed\&id=([\d]+)#i', 'openlab_handle_h5p_embed' );
		}
	}
);

function openlab_handle_h5p_embed( $matches, $attr, $url, $rawattr ) {
	$markup = sprintf(
		'<iframe src="%s" width="%s" height="500" frameborder="0" allowfullscreen="allowfullscreen" title=""></iframe><script src="http://openlabdev.org/wp-content/plugins/h5p/h5p-php-library/js/h5p-resizer.js" charset="UTF-8"></script>',
		$matches[0],
		'100%'
	);

	return $markup;
}
