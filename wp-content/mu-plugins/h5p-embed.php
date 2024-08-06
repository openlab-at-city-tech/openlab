<?php

function openlab_h5p_hub_urls() {
	return [
		'http://openlabdev.org/h5p-hub',
		'http://openlabdev.org/h5ptesting',
		'https://openlab.citytech.cuny.edu/id-',
		'https://openlab.citytech.cuny.edu/id-hub',
		'https://openlab.citytech.cuny.edu/oer-h5p-hub/',
	];
}

add_action(
	'init',
	function() {
		$h5p_hub_urls = openlab_h5p_hub_urls();

		foreach ( $h5p_hub_urls as $hub_url ) {
			$handle = 'h5p-' . sanitize_title_with_dashes( $hub_url );
			wp_embed_register_handler( $handle, '#' . preg_quote( trailingslashit( $hub_url ) ) . 'wp-admin/admin-ajax.php\?action=h5p_embed\&id=([\d]+)#i', 'openlab_handle_h5p_embed' );
		}
	}
);

function openlab_handle_h5p_embed( $matches, $attr, $url, $rawattr ) {
	if ( ! defined( 'ENV_TYPE' ) || 'production' === ENV_TYPE ) {
		$domain = 'https://openlab.citytech.cuny.edu';
	} else {
		$domain = set_url_scheme( get_blog_option( 1, 'siteurl' ) );
	}

	$markup = sprintf(
		'<iframe src="%s" width="%s" height="500" frameborder="0" allowfullscreen="allowfullscreen" title=""></iframe><script src="%s/wp-content/plugins/h5p/h5p-php-library/js/h5p-resizer.js" charset="UTF-8"></script>',
		$matches[0],
		'100%',
		$domain
	);

	return $markup;
}
