<?php

namespace WeBWorK\Server\Subscription;

/**
 * Subscription query.
 *
 * @since 1.0.0
 */
class Query {
	protected $r;

	public function __construct( $args ) {
		$this->r = array_merge(
			array(
				'user_id' => null,
			),
			$args
		);
	}

	public function get() {
		$tax_slug = 'user_' . intval( $this->r['user_id'] );

		$posts = get_posts(
			array(
				'fields'         => 'ids',
				'post_type'      => 'webwork_question',
				'posts_per_page' => -1,
				'tax_query'      => array(
					array(
						'taxonomy' => 'webwork_subscribed_by',
						'terms'    => array( $tax_slug ),
						'field'    => 'slug',
					),
				),
			)
		);

		return array_map( 'intval', $posts );
	}
}
