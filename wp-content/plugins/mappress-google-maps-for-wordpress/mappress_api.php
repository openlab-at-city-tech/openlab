<?php
class Mappress_Api {
	static function register() {
		add_action('rest_api_init', array(__CLASS__, 'rest_api_init'));
	}

	static function map_counts($request) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mapp_maps';
		ob_start();
		$otype = ($request->get_param('otype') == 'user') ? 'user' : 'post';
		$oid = $request->get_param('oid');

		$where = ($otype) ? " AND otype = '$otype' " : '';

		$counts = (object) array(
			'all' => $wpdb->get_var("SELECT count(*) FROM $maps_table WHERE status != 'trashed' $where"),
			'trashed' => $wpdb->get_var("SELECT count(*) FROM $maps_table WHERE status = 'trashed' $where"),
		);

		if ($oid)
			$counts->object = $wpdb->get_var("SELECT count(*) FROM $maps_table WHERE status != 'trashed' AND oid = $oid");

		return self::rest_response($counts);
	}

	static function map_delete($request) {
		ob_start();
		$mapid = $request->get_param('mapid');
		$result = Mappress_Map::delete($mapid);

		if (!$result)
			return new WP_Error('map_delete', "Internal error when deleting map ID '$mapid'!");

		return self::rest_response('OK');
	}

	static function map_duplicate($request) {
		ob_start();

		$mapid = $request->get_param('mapid');

		if (!$mapid)
			return new WP_Error('map_clone', 'Internal error, your data has not been saved!');

		$map = Mappress_Map::duplicate($mapid);
		if ($map)
			return self::rest_response(array('title' => $map->title, 'mapid' => $map->mapid));
		else
			return new WP_Error('map_clone', 'Internal error when copying');
	}

	static function map_find($request) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mapp_maps';

		ob_start();
		$defaults = array(
			'filter' => 'all',
			'oid' => null,
			'otype' => 'post',
			'page' => 1,
			'page_size' => 5,
			'sort_by' => 'mapid',
			'sort_asc' => true
		);

		foreach($defaults as $arg => $default) {
			$value = $request->get_param($arg);
			$$arg = ($value) ? $value : $default;
		}

		if ($otype == 'post') {
			$fields = "SELECT $maps_table.mapid, $maps_table.otype, $maps_table.oid, $maps_table.status, $maps_table.title, $wpdb->posts.post_title ";
			$from = " FROM $maps_table ";
			$join = " LEFT OUTER JOIN $wpdb->posts ON ( $wpdb->posts.ID = $maps_table.oid AND $maps_table.otype = 'post' "
				. " AND $wpdb->posts.post_status != 'auto-draft' AND $wpdb->posts.post_status != 'inherit' )";
		} else {
			$fields = "SELECT $maps_table.mapid, $maps_table.otype, $maps_table.oid, $maps_table.status, $maps_table.title, $wpdb->users.nicename ";
			$from = " FROM $maps_table ";
			$join = " LEFT OUTER JOIN $wpdb->users ON ($wpdb->users.ID = $maps_table.oid AND $maps_table.otype = 'user') ";
		}

		$where = ($filter == 'trashed') ? " WHERE $maps_table.status = 'trashed' " : " WHERE $maps_table.status != 'trashed' ";
		if ($filter == 'object' && $oid)
			$where .= " AND $maps_table.oid = $oid ";

		$orderby = '';
		if ($sort_by) {
			$orderby = " ORDER BY $sort_by " . ( ($sort_asc == 'true') ? "ASC" : "DESC" );
			if ($sort_by != 'mapid')
				$orderby .= ", mapid";
		}

		$limit = sprintf(" LIMIT %d, %d", ($page-1) * $page_size, $page_size);

		// Run query for row count, then for results
		$found = $wpdb->get_var("SELECT count(*)" . $from . $join . $where);
		$results = $wpdb->get_results($fields . $from . $join . $where . $orderby . $limit);

		$items = array();
		foreach($results as $result) {
			$items[] = array(
				'mapid' => $result->mapid,
				'title' => $result->title,
				'oid' => $result->oid,
				'otype' => $result->otype,
				'otitle' => ($otype == 'user') ? $result->nicename : $result->post_title,
				'status' => $result->status
			);
		}
		return self::rest_response(array('found' => $found, 'items' => $items));
	}

	static function map_get($request) {
		ob_start();
		$mapid = $request->get_param('mapid');
		$map = ($mapid) ? Mappress_Map::get($mapid) : null;

		if (!$map)
			return new WP_Error('map_get', 'Map not found');

		// Update existing images for editor and assign POI IDs
		foreach($map->pois as $poi) {
			$poi->update_images();
			$poi->id = uniqid();
		}

		return self::rest_response($map);
	}

	static function map_get_post($request) {
		global $post;
		ob_start();
		$oid = $request->get_param('oid');
		$post = get_post( $oid );

		if (!$post)
			return new WP_Error('map_get_post', 'Post not found');

		setup_postdata($post);
		$html = Mappress_Template::get_template('mashup-modal');
		return self::rest_response($html);
	}

	static function map_mutate($request) {
		ob_start();
		$mapid = $request->get_param('mapid');
		$mapdata = $request->get_json_params();

		if (!$mapid || !$mapdata)
			return new WP_Error('map_mutate', 'Missing parameter while mutating');

		$result = Mappress_Map::mutate($mapid, $mapdata);
		if (!$result)
			return new WP_Error('map_mutate', 'Internal error when mutating, your data was not saved!');

		return self::rest_response('OK');
	}

	static function map_save($request) {
		$mapdata = $request->get_json_params();

		if (!$mapdata)
			return new WP_Error('map_save', 'Internal error, your data has not been saved!');

		$map = new Mappress_Map($mapdata);
		$result = $map->save();

		if (!$result)
			return new WP_Error('map_save', 'Internal error, your data has not been saved!');

		return self::rest_response($map->mapid);
	}

	static function rest_api_init() {
		$namespace = 'mapp/v1';

		register_rest_route($namespace, '/maps',
			array(
				'methods' => 'GET',
				'callback' => array(__CLASS__, 'map_find'),
				'permission_callback' => function() {
					return current_user_can('edit_posts');
				},
			)
		);

		register_rest_route($namespace, '/maps/(?P<mapid>\d+)',
			array(
				array(
					'methods' => 'GET',
					'callback' => array(__CLASS__, 'map_get'),
					'permission_callback' => '__return_true',
				),

				array(
					'methods' => 'DELETE',
					'callback' => array(__CLASS__, 'map_delete'),
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
				),

				array(
					'methods' => 'POST',
					'callback' => array(__CLASS__, 'map_save'),
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
				),

				array(
					'methods' => 'PATCH',
					'callback' => array(__CLASS__, 'map_mutate'),
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
				),
			)
		);

		register_rest_route($namespace, '/maps/clone/(?P<mapid>\d+)',
			array (
				'methods' => 'POST',
				'callback' => array(__CLASS__, 'map_duplicate'),
				'permission_callback' => function() {
					return current_user_can('edit_posts');
				},
			)
		);

		register_rest_route($namespace, '/maps/counts',
			array(
				'methods' => 'GET',
				'callback' => array(__CLASS__, 'map_counts'),
				'permission_callback' => function() {
					return current_user_can('edit_posts');
				}
			)
		);
	}

	static function rest_response($response = null) {
		$output = trim(ob_get_clean());		// Ignore whitespace, any other output is an error

		// WP bug: when zlib active, warning messages are generated, which corrupt JSON output
		// Ticket has been open for 9 years.  Workaround is to disable flush when providing json response - may cause other conflicts!
		// https://core.trac.wordpress.org/ticket/22430, https://core.trac.wordpress.org/ticket/18525
		if (ini_get('zlib.output_compression'))
			remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

		if ($output)
			return new WP_Error('mapp', "Invalid output from fetch.  Deactivate plugins one by one to find the problem:\r\n$output");
		else
			return rest_ensure_response($response);
	}
}
?>
