<?php

namespace CBOX\OL\API;

use \WP_Site_Query;

use \WP_REST_Controller;
use \WP_REST_Server;
use \WP_REST_Request;
use \WP_REST_Response;

class Sites extends WP_REST_Controller {
	public function register_routes() {
		$version   = '1';
		$namespace = 'cboxol/v' . $version;

		register_rest_route(
			$namespace,
			'/sites/',
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_items' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
					'args'                => $this->get_endpoint_args_for_item_schema( true ),
				],
			]
		);
	}

	public function get_items( $request ) {
		$params = $request->get_params();

		$q    = $params['search'];
		$page = $params['page'];

		$per_page = 10;

		// Filter to match blogname.
		add_filter( 'sites_clauses', [ $this, 'filter_sites_clauses' ], 10, 2 );

		$query = new WP_Site_Query(
			[
				'number'        => $per_page,
				'search'        => $q,
				'site__not_in'  => [ cbox_get_main_site_id() ],
				'no_found_rows' => false,
				'offset'        => $per_page * ( $page - 1 ),
			]
		);

		remove_filter( 'sites_clauses', [ $this, 'filter_sites_clauses' ], 10, 2 );

		$retval = [
			'results' => [],
		];

		foreach ( $query->sites as $site ) {
			$label = sprintf(
				// translators: 1. Numeric ID of site, 2. Name of site, 3. URL of site
				__( '#%1$s %2$s (%3$s)', 'commons-in-a-box' ),
				$site->blog_id,
				$site->blogname,
				$site->siteurl
			);

			$retval['results'][] = [
				'text' => $label,
				'id'   => $site->blog_id,
			];
		}

		if ( $query->max_num_pages > $page ) {
			$retval['pagination'] = [
				'more' => true,
			];
		}

		return rest_ensure_response( $retval );
	}

	/**
	 * Filters the query clauses in WP_Site_Query to include blogname matches.
	 *
	 * @since 1.4.0
	 *
	 * @param array         $clauses SQL clauses.
	 * @param WP_Site_Query $query   Site query.
	 */
	public function filter_sites_clauses( $clauses, $query ) {
		global $wpdb;

		$search_terms = $query->query_vars['search'];
		if ( ! $search_terms ) {
			return $clauses;
		}

		$bp = buddypress();

		$blogname_matches = get_sites(
			[
				'count'      => false,
				'meta_query' => [
					[
						'key'     => 'blogname',
						'value'   => $search_terms,
						'compare' => 'LIKE',
					],
				],
			]
		);

		if ( $blogname_matches ) {
			$blogname_id_matches = wp_list_pluck( $blogname_matches, 'blog_id' );
		} else {
			$blogname_id_matches = array( 0 );
		}

		// We use the following heuristic, which will work unless WP changes its SQL syntax.
		$clauses['where'] = str_replace( 'AND (domain LIKE', 'AND (blog_id IN (' . implode( array_map( 'intval', $blogname_id_matches ) ) . ') OR domain LIKE', $clauses['where'] );

		return $clauses;
	}

	public function get_items_permissions_check( $request ) {
		return current_user_can( 'manage_network_options' );
	}
}
