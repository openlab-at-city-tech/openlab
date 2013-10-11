<?php

class WP_DPLA_Query {
	protected $_dpla;
	protected $fetched_items = array();

	public function get_items_markup() {
		$post = get_post();

		if ( empty( $post->ID ) ) {
			return '';
		}

		$tkey = $this->get_transient_key( $post->ID );
		$items = get_transient( $tkey );

		// Six hours by default
		$expiration = $this->get_expiration();

		if ( false === $items ) {
			$items = $this->get_random_items_for_post();
			set_transient( $tkey, $items, $expiration );
		}

		if ( empty( $items ) ) {
			return '';
		}

		$items_markup = '<h3 class="dpla-results-header">' . __( 'Related items from the Digital Public Library of America', 'wp-dpla' ) . '</h3>';
		$items_markup .= '<ul class="dpla-results">';

		foreach ( $items as $item ) {
			$item_markup  = '<li>';

			$item_markup .=   '<div class="dpla-thumbnail">';
			$item_markup .=     '<a href="' . esc_attr( $item['item_url'] ) . '">';
			$item_markup .=       '<img alt="' . esc_attr( $item['title'] ) . '" src="' . esc_attr( $item['thumbnail'] ) . '" />';
			$item_markup .=     '</a>';
			$item_markup .=   '</div>';

			$item_markup .=   '<div class="dpla-data">';
			$item_markup .=     '<span class="dpla-title">';
			$item_markup .=       '<a href="' . esc_attr( $item['item_url'] ) . '">';
			$item_markup .=         esc_html( $item['title'] );
			$item_markup .=       '</a>';
			$item_markup .=     '</span>';
			$item_markup .=     '<span class="dpla-provider">';
			$item_markup .=       '<a href="' . esc_attr( $item['provider_url'] ) . '">';
			$item_markup .=         esc_html( $item['provider_name'] );
			$item_markup .=       '</a>';
			$item_markup .=     '</span>';
			$item_markup .=   '</div>';

			$item_markup .= '</li>';

			$items_markup .= $item_markup;
		}

		$items_markup .= '</ul>';

		return $items_markup;
	}

	protected function get_transient_key( $post_id ) {
		return 'dpla_random_posts_post_' . $post_id;
	}

	protected function get_expiration() {
		$expiration = (int) apply_filters( 'wp_dpla_cache_expiration_time', 6 * 60 * 60 );

		// Prevent API flooding - don't allow a value less than
		// 10 minutes
		if ( $expiration < 600 ) {
			$expiration = 600;
		}

		return $expiration;
	}

	public function get_random_items_for_post( $args = array() ) {
		$retval = array();
		$post = get_post();
		if ( isset( $post->ID ) ) {
			$terms = wp_get_post_tags( $post->ID );

			if ( ! empty( $terms ) ) {
				$search_terms = array();
				foreach ( wp_list_pluck( $terms, 'name' ) as $raw_term ) {
					$search_terms[] = urlencode( $raw_term );
				}
				$args['search_term'] = implode( ' OR ', $search_terms );

				// We'll get random items, but we first do a
				// preliminary query to get a range
				$pre_args = $args;
				$pre_args['page'] = 1;
				$pre_args['per_page'] = 1;
				$pre_args['fields'] = implode( ',', array(
					'isShownAt',
					'sourceResource.title',
					'object',
					'provider',
				) );
				$pre_query = $this->create_query( $pre_args );
				$this->total_count = $pre_query->getTotalCount();

				$item_count = isset( $args['per_page'] ) ? (int) $args['per_page'] : 4;
				if ( $item_count > $this->total_count ) {
					$item_count = $this->total_count;
				}

				for ( $i = 0; $i < $item_count; $i++ ) {
					$retval[] = $this->get_random_item_by_search_term( $args['search_term'] );
				}
			}
		}
		return $retval;
	}

	protected function get_random_item_by_search_term( $search_term ) {
		$qargs = array(
			'search_term' => $search_term,
			'per_page' => 1,
			'page' => rand( 1, $this->total_count ),
		);

		$query = $this->create_query( $qargs );
		$documents = $query->getDocuments();
		$item = array_pop( $documents );

		// No dupes
		if ( in_array( $item['isShownAt'], $this->fetched_items ) ) {
			return $this->get_random_item_by_search_term( $search_term );
		} else {
			$this->fetched_items[] = $item['isShownAt'];
		}

		// sometimes a field is empty
		if ( ! isset( $item['sourceResource']['title'], $item['object'], $item['isShownAt'], $item['provider']['name'], $item['provider']['@id'] ) ) {
			return $this->get_random_item_by_search_term( $search_term );
		}

		// We need a thumbnail
		if ( ! isset( $item['object'] ) || empty( $item['object'] ) ) {
			return $this->get_random_item_by_search_term( $search_term );
		}

		// sometimes a field is not a string
		if ( ! is_string( $item['sourceResource']['title'] ) || ! is_string( $item['object'] ) || ! is_string( $item['isShownAt'] ) || ! is_string( $item['provider']['name'] ) || ! is_string( $item['provider']['@id'] ) ) {
			return $this->get_random_item_by_search_term( $search_term );
		}

		// If the title is an array, just take the first item
		$title = is_array( $item['sourceResource']['title'] ) ? array_pop( array_reverse( $item['sourceResource']['title'] ) ) : $item['sourceResource']['title'];

		return array(
			'title' => $title,
			'thumbnail' => $item['object'],
			'item_url' => $item['isShownAt'],
			'provider_name' => $item['provider']['name'],
			'provider_url' => $item['provider']['@id'],
		);
	}

	protected function create_query( $args ) {
		$r = wp_parse_args( $args, array(
			'per_page' => 4,
			'page' => 1,
			'search_term' => '',
		) );

		return $this->get_dpla()->createSearchQuery()->withSourceResourceField( 'title', $r['search_term'] )->withPaging( $r['page'], $r['per_page'] )->execute();
	}

	protected function get_dpla() {
		if ( ! isset( $this->_dpla ) ) {
			$api_key = get_option( 'dpla_api_key' );
			require_once __DIR__ . '/dpla/class-wp-dpla-dpla.php';
			$this->_dpla = new \TFN\WP_DPLA_DPLA( $api_key );
		}

		return $this->_dpla;
	}

}
