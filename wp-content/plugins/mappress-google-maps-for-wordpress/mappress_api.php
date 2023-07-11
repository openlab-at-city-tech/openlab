<?php
class Mappress_Api extends WP_REST_Controller {
	public $namespace = 'mapp/v1';

	public function __construct() {
		add_action('rest_api_init', array($this, 'rest_api_init'));
	}

	public function counts($otype = 'post', $oid = null) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mapp_maps';
		$otype = ($otype == 'user') ? 'user' : 'post';

		$counts = (object) array(
			'all' => $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $maps_table WHERE status != 'trashed' AND otype = %s ", $otype)),
			'trashed' => $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $maps_table WHERE status = 'trashed' AND otype = %s ", $otype)),
		);

		if ($oid)
			$counts->object = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $maps_table WHERE status != 'trashed' AND otype = %s AND oid = %d ", $otype, $oid));
		return $counts;
	}

	public function create_map($request) {
		$mapdata = $request->get_json_params();

		if (!$mapdata)
			return new WP_Error('create_map', 'Map save data missing');

		$map = new Mappress_Map($mapdata);
		$result = $map->save();

		if (!$result)
			return new WP_Error('create_map', 'Internal error, your data has not been saved!');

		return $this->rest_response($map->mapid);
	}

	public function delete_map($request) {
		ob_start();
		$mapid = $request->get_param('mapid');
		$result = Mappress_Map::delete($mapid);

		if (!$result)
			return new WP_Error('delete_item', "Internal error when deleting map ID '$mapid'!");

		return $this->rest_response($mapid);
	}

	public function duplicate_map($request) {
		ob_start();

		$mapid = $request->get_param('mapid');
		$oid = $request->get_param('oid');

		if (!$mapid)
			return new WP_Error('map_clone', 'Missing map ID');

		$map = Mappress_Map::get($mapid);
		if (!$map)
			return new WP_Error('map_clone', 'Original map not found');

		$map->mapid = null;
		$map->metaKey = null;						// Map is no longer automatic
		$map->oid = ($oid) ? $oid : 0;				// Assign dupe to current post (editor only, library will be null)
		$map->title = sprintf(__('Copy of %s', 'mappress-google-maps-for-wordpress'), (($map->title) ? $map->title : __('Untitled', 'mappress-google-maps-for-wordpress')));

		$result = $map->save();
		if ($result === true)
			return $this->rest_response($map);
		else
			return new WP_Error('map_clone', 'Internal error when copying');
	}

	public function get_counts($request) {
		ob_start();
		$otype = $request->get_param('otype');
		$oid = $request->get_param('oid');
		return $this->rest_response($this->counts($otype, $oid));
	}

	public function get_map($request) {
		ob_start();
		$mapid = $request->get_param('mapid');
		$map = ($mapid) ? Mappress_Map::get($mapid) : null;

		if (!$map)
			return new WP_Error('get_map', 'Map not found');

		return $this->rest_response($map);
	}

	public function get_map_schema() {
		$schema = array(
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title' => 'map',
			'type' => 'object',
			'properties' => array(
				'mapid' => array(
					'description' => esc_html('Unique identifier for the map.'),
					'type' => 'integer',
					'context' => array('view', 'edit'),
					'readonly' => true,
				),
				'center' => array(
					'description' => esc_html("Map center.  May be null for automatic center, or a string of lat,lng to force the center."),
					'type' => 'integer',
				),
				'mapTypeId' => array(
					'description' => esc_html('Map type.  May be null, a default type (roadmap, satellite or hybrid) or the name of a custom style.'),
					'type' => 'string',
				),
				'oid' => array(
					'description' => esc_html('Object the map is linked to.  May be null, a post ID, or a user ID.'),
					'type' => 'string',
				),
				'otype' => array(
					'description' => esc_html('Object type the map is linked to, may be post or user.'),
					'type' => 'string',
					'enum' => array('post', 'user')
				),
				'pois' => array(
					'description' => esc_html('Array of map markers (POIs) to display on the map.'),
					'type' => 'array',
					'items' => array(
						'type' => 'object',
						'properties' => array(
							'address' => array(
								'description' => esc_html('POI street address.'),
								'type' => 'string',
							),
							'body' => array(
								'description' => esc_html('POI body.'),
								'type' => 'string',
							),
							'iconid' => array(
								'description' => esc_html('POI iconid, use short names like yellow for standard icons.  Custom icons must be defined in MapPress settings and include an extension like myicon.png.'),
								'type' => 'string',
							),
							'point' => array(
								'description' => esc_html('POI location.'),
								'type' => 'object',
								'properties' => array(
									'lat' => array(
										'description' => esc_html('Latitude.'),
										'type' => 'number',
									),
									'lng' => array(
										'description' => esc_html('Longitude.'),
										'type' => 'number',
									),
								),
							),
							'title' => array(
								'description' => esc_html('POI title.'),
								'type' => 'string',
							)
						)
					)
				),
				'title' => array(
					'description'  => esc_html('Title for the map in the map editor.'),
					'type'         => 'string',
				),
				'zoom' => array(
					'description'  => esc_html('Map zoom.  May be null for automatic zoom, or a number from 1-18.' ),
					'type'         => 'integer',
				),
			),
		);

		return $schema;
	}

	public function get_maps($request) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mapp_maps';

		ob_start();

		// Allowed args are already limited and sanitized in rest_api_init
		foreach(array('filter', 'oid', 'otype', 'page', 'page_size', 'search_text', 'sort_by', 'sort_asc') as $arg)
			$$arg = $request->get_param($arg);
			
		$where = " WHERE 1=1 ";

		if ($otype == 'post') {
			$fields = "SELECT $maps_table.mapid, $maps_table.otype, $maps_table.oid, $maps_table.status, $maps_table.title, $wpdb->posts.post_title as otitle ";
			$from = " FROM $maps_table ";
			$join = " LEFT OUTER JOIN $wpdb->posts ON ( $wpdb->posts.ID = $maps_table.oid "
				. " AND $wpdb->posts.post_status != 'auto-draft' AND $wpdb->posts.post_status != 'inherit' )";
			$where .= " AND $maps_table.otype = 'post' ";
		} else {
			// User maps, not currently displayed in picker
			$fields = "SELECT $maps_table.mapid, $maps_table.otype, $maps_table.oid, $maps_table.status, $maps_table.title, $wpdb->users.nicename as otitle ";
			$from = " FROM $maps_table ";
			$join = " LEFT OUTER JOIN $wpdb->users ON ($wpdb->users.ID = $maps_table.oid) ";
			$where .= " AND $maps_table.otype = 'post' ";
		}
  
		$where .= ($filter == 'trashed') ? " AND $maps_table.status = 'trashed' " : " AND $maps_table.status != 'trashed' ";
		if ($filter == 'object' && $oid)
			$where .= $wpdb->prepare(" AND $maps_table.oid = %d ", $oid);

		if ($search_text) {
			// Can't use column alias in where
			$otitle = ($otype == 'post') ? "$wpdb->posts.post_title" : "$wpdb->users.nicename";
			$where .= $wpdb->prepare(" AND ($maps_table.mapid = %s OR $maps_table.title like '%%%s%%' OR $otitle like '%%%s%%') ", $search_text, $search_text, $search_text);
		}

		$orderby = '';

		// Note that sort_by should be ;already sanitized by wpdb->prepare
		if ($sort_by == 'mapid') {
			$orderby = $wpdb->prepare(" ORDER BY %1s ", $sort_by) . ( ($sort_asc == 'true') ? "ASC" : "DESC" );
			if ($sort_by != 'mapid')
				$orderby .= ", mapid";
		}

		if ($page_size > 0)
			$limit = $wpdb->prepare(" LIMIT %d, %d", ($page-1) * $page_size, $page_size);

		// Run query, then check if more results exist
		$results = $wpdb->get_results($fields . $from . $join . $where . $orderby . $limit);
		$more = $wpdb->get_var("SELECT count(*)" . $from . $join . $where . " GROUP BY $maps_table.mapid " . sprintf(" LIMIT %d, %d", $page * $page_size, 1));
		$more = ($more > 0) ? true : false;

		$maps = array();

		// Return map stubs, full map is only read when it is edited individually
		foreach($results as $result) {
			$maps[] = array(
				'mapid' => $result->mapid,
				'title' => $result->title,
				'oid' => $result->oid,
				'otype' => $result->otype,
				'otitle' => $result->otitle,
				'status' => $result->status,
				'stub' => true,
			);
		}
		return $this->rest_response(array('counts' => $this->counts($otype, $oid), 'more' => $more, 'maps' => $maps));
	}

	public function mutate_map($request) {
		ob_start();
		$mapid = $request->get_param('mapid');
		$mapdata = $request->get_param('changes');

		if (!$mapid || !$mapdata)
			return new WP_Error('mutate_map', 'Missing parameter while mutating');

		$result = Mappress_Map::mutate($mapid, $mapdata);
		if (!$result)
			return new WP_Error('mutate_map', 'Internal error when mutating, your data was not saved!');

		return $this->rest_response('OK');
	}

	public function update_map($request) {
		$mapid = $request->get_param('mapid');
		$mapdata = (object) $request->get_json_params();

		if (!$mapdata)
			return new WP_Error('update_map', 'Map save data missing');
		if (!$mapid || $mapid != $mapdata->mapid)
			return new WP_Error('update_map', 'Map ID missing');

		$map = new Mappress_Map($mapdata);
		$result = $map->save();

		if (!$result)
			return new WP_Error('update_map', 'Internal error, your data has not been saved!');

		return $this->rest_response($map->mapid);
	}

	public function rest_api_init() {
		register_rest_route(
			$this->namespace,
			'/maps',
			array(
				array(
					'methods' => 'GET',
					'callback' => array($this, 'get_maps'),
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
					'args' => array(
						'filter' => array('sanitize_callback' => 'sanitize_title', 'default' => 'all'),
						'oid' => array('sanitize_callback' => 'sanitize_title', 'default' => null),
						'otype' => array('sanitize_callback' => 'sanitize_title', 'default' => 'post'),
						'page' => array('sanitize_callback' => 'absint', 'default' => 1),
						'page_size' => array('sanitize_callback' => 'absint', 'default' => 10),
						'search' => array('sanitize_callback' => 'sanitize_title', 'default' => ''),
						'sort_by' => array('sanitize_callback' => 'sanitize_title', 'default' => 'mapid'),
						'sort_asc' => array('sanitize_callback' => 'rest_sanitize_boolean', 'default' => true),
					),

				),
				array(
					'methods' => 'POST',
					'callback' => array($this, 'create_map'),
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
				),
				'schema' => array($this, 'get_map_schema')
			)
		);

		register_rest_route(
			$this->namespace,
			'/maps/(?P<mapid>\d+)',
			array(
				array(
					'methods' => 'GET',
					'callback' => array($this, 'get_map'),
					'permission_callback' => '__return_true',
				),

				array(
					'methods' => 'DELETE',
					'callback' => array($this, 'delete_map'),
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
				),

				array(
					'methods' => 'POST',
					'callback' => array($this, 'update_map'),
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
				),

				array(
					'methods' => 'PATCH',
					'callback' => array($this, 'mutate_map'),
					'permission_callback' => function() {
						return current_user_can('edit_posts');
					},
				),
				'schema' => array($this, 'get_map_schema'),
			)
		);

		register_rest_route(
			$this->namespace,
			'/maps/clone/(?P<mapid>\d+)',
			array (
				'methods' => 'POST',
				'callback' => array($this, 'duplicate_map'),
				'permission_callback' => function() {
					return current_user_can('edit_posts');
				},
				'schema' => array($this, 'get_map_schema'),
			)
		);

		register_rest_route(
			$this->namespace,
			'/maps/counts/',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'get_counts'),
				'permission_callback' => function() {
					return current_user_can('edit_posts');
				},
				'args' => array(
					'otype' => array('sanitize_callback' => 'sanitize_title'),
					'oid' => array('sanitize_callback' => 'absint'),
				)

			)
		);

		register_rest_route(
			$this->namespace,
			'/maps/import/',
			array(
				'methods' => 'POST',
				'callback' => array('Mappress_Import', 'import'),
				'permission_callback' => function() {
					return current_user_can('manage_options');
				},
			)
		);

	}

	public function rest_response($response = null) {
		$output = trim(ob_get_clean());		// Ignore whitespace, any other output is an error

		// WP bug: when zlib active, warning messages are generated, which corrupt JSON output
		// Ticket has been open for 9 years.  Workaround is to disable flush when providing json response - may cause other conflicts!
		// https://core.trac.wordpress.org/ticket/22430, https://core.trac.wordpress.org/ticket/18525
		if (ini_get('zlib.output_compression'))
			remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

		if ($output)
			return new WP_Error('mapp', "Invalid output from fetch:\r\n$output");
		else
			return rest_ensure_response($response);
	}
}
?>