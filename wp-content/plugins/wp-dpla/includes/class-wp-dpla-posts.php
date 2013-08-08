<?php

class WP_DPLA_Posts {
	public function __construct() {
		$show_on_posts = get_option( 'dpla_show_on_posts', 'on' );

		if ( 'on' == $show_on_posts ) {
			add_filter( 'the_content', array( $this, 'append_to_the_content' ) );
			add_action( 'save_post', array( $this, 'flush_transient' ) );
		}
	}

	public function append_to_the_content( $content ) {
		if ( ! is_singular() ) {
			return $content;
		}

		$dpla_query = new WP_DPLA_Query();
		$content .= $this->styles();
		$content .= $dpla_query->get_items_markup();

		return $content;
	}

	public function flush_transient( $post_id ) {
		$tkey = 'dpla_random_posts_post_' . $post_id;
		delete_transient( $tkey );
	}

	public function styles() {
		ob_start();
		?>
<style type="text/css">
.dpla-results {
	list-style-type: none !important;
	margin: 1rem 0;
	overflow: hidden;
}
.dpla-results li {
	width: 20%;
	margin: 0 2.5%;
	float: left;
}
.dpla-title, .dpla-provider {
	display: block;
	font-size: .8rem;
}
.dpla-title {
	font-style: italic;
}
.dpla-title a {
	text-decoration: none;
}
.dpla-provider a {
	opacity: .5;
}
</style>
		<?php
		return ob_get_clean();
	}
}
