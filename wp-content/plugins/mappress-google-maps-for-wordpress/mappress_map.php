<?php
class Mappress_Map extends Mappress_Obj {
	var $alignment,
		$center,
		$classname,
		$embed,
		$height,
		$hideEmpty,
		$initialOpenDirections,
		$initialOpenInfo,
		$layers,
		$layout,
		$lines,
		$linesOpts,
		$mapid,
		$mapOpts,
		$mapTypeId,
		$metaKey,
		$name,
		$oid,
		$otitle,
		$otype,
		$poiList,
		$query,
		$search,
		$status,
		$title,
		$width,
		$zoom
		;

	var $pois = array();

	function to_json() {
		$json_pois = array();
		foreach($this->pois as $poi)
			$json_pois[] = $poi->to_json();

		return json_encode(array(
			'mapid' => $this->mapid,
			'otype' => $this->otype,
			'oid' => $this->oid,
			'center' => $this->center,
			'height' => $this->height,
			'mapTypeId' => $this->mapTypeId,
			'metaKey' => $this->metaKey,
			'pois' => $json_pois,
			'search' => $this->search,
			'status' => $this->status,
			'title' => $this->title,
			'width' => $this->width,
			'zoom' => $this->zoom
		));
	}

	function __construct($atts = null) {
		$this->update($atts);

		// Convert POIs from arrays to objects if needed
		foreach((array)$this->pois as $index => $poi) {
			if (!$poi instanceof Mappress_Poi)
				$this->pois[$index] = new Mappress_Poi($poi);
		}
	}

	static function register() {
		global $wpdb;

		add_action('deleted_post', array(__CLASS__, 'deleted_post'));
		add_action('trashed_post', array(__CLASS__, 'trashed_post'));
		add_action('wp_ajax_mapp_delete', array(__CLASS__, 'ajax_delete'));
		add_action('wp_ajax_mapp_duplicate', array(__CLASS__, 'ajax_duplicate'));
		add_action('wp_ajax_mapp_find', array(__CLASS__, 'ajax_find'));
		add_action('wp_ajax_mapp_get', array(__CLASS__, 'ajax_get'));
		add_action('wp_ajax_mapp_get_post', array(__CLASS__, 'ajax_get_post'));
		add_action('wp_ajax_nopriv_mapp_get_post', array(__CLASS__, 'ajax_get_post'));
		add_action('wp_ajax_mapp_mutate', array(__CLASS__, 'ajax_mutate'));
		add_action('wp_ajax_mapp_save', array(__CLASS__, 'ajax_save'));
		add_action('media_buttons', array(__CLASS__, 'media_buttons'));

		add_action('show_user_profile', array(__CLASS__, 'display_user_map'));
		add_action('edit_user_profile', array(__CLASS__, 'display_user_map'));
		add_action('deleted_user', array(__CLASS__, 'deleted_user'));
	}

	static function ajax_delete() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('edit_posts'))
			Mappress::ajax_response('Not authorized');

		ob_start();
		$args = json_decode(wp_unslash($_POST['data']));
		$mapid = $args->mapid;
		$result = Mappress_Map::delete($mapid);

		if (!$result)
			Mappress::ajax_response("Internal error when deleting map ID '$mapid'!");

		Mappress::ajax_response('OK');
	}

	static function ajax_duplicate() {
		check_ajax_referer('mappress', 'nonce');
		ob_start();

		if (!current_user_can('edit_posts'))
			Mappress::ajax_response('Not authorized');

		$args = json_decode(wp_unslash($_POST['data']));
		$mapid = $args->mapid;

		if (!$mapid)
			Mappress::ajax_response('Internal error, your data has not been saved!');

		$map = self::duplicate($mapid);
		if ($map)
			Mappress::ajax_response('OK', $map);
		else
			Mappress::ajax_response('Internal error when copying');
	}

	static function ajax_find() {
		check_ajax_referer('mappress', 'nonce');
		ob_start();

		if (!current_user_can('edit_posts'))
			Mappress::ajax_response('Not authorized');

		Mappress::ajax_response('OK', self::find($_GET));
	}

	static function ajax_get() {
		check_ajax_referer('mappress', 'nonce');
		ob_start();
		$mapid = (isset($_GET['mapid'])) ? $_GET['mapid']  : null;
		$map = ($mapid) ? self::get($mapid) : null;

		// Update existing images for editor
		if ($map) {
			foreach($map->pois as $poi)
				$poi->update_images();
		}

		if (!$map)
			Mappress::ajax_response(sprintf(__('Map not found', 'mappress-google-maps-for-wordpress'), $mapid));
		else
			Mappress::ajax_response('OK', $map);
	}

	static function ajax_get_post() {
		global $post;

		check_ajax_referer('mappress', 'nonce');
		ob_start();
		$oid = (isset($_GET['oid'])) ? $_GET['oid']  : null;
		$post = get_post( $oid );

		if (!$post)
			die(sprintf(__('Post not found', 'mappress-google-maps-for-wordpress'), $oid));

		setup_postdata($post);
		$html = Mappress_Template::get_template('mashup-modal');
		die($html);
	}

	static function ajax_mutate() {
		global $wpdb;

		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('edit_posts'))
			Mappress::ajax_response('Not authorized');

		ob_start();
		$args = json_decode(wp_unslash($_POST['data']), true);
		$mapid = (isset($args['mapid'])) ? $args['mapid'] : null;
		$mapdata = (isset($args['mapdata'])) ? $args['mapdata'] : null;

		$result = self::mutate($mapid, $mapdata);
		if (!$result)
			Mappress::ajax_response('Internal error when mutating, your data was not saved!');

		// Return updated map
		Mappress::ajax_response('OK', $result);
	}

	static function ajax_save() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('edit_posts'))
			Mappress::ajax_response('Not authorized');

		ob_start();
		$args = json_decode(wp_unslash($_POST['data']), true);
		$mapdata = (isset($args['mapdata'])) ? $args['mapdata'] : null;

		if (!$mapdata)
			Mappress::ajax_response('Internal error, your data has not been saved!');

		$map = new Mappress_Map($mapdata);
		$result = $map->save();

		if (!$result)
			Mappress::ajax_response('Internal error, your data has not been saved!');

		// Return saved mapid
		Mappress::ajax_response('OK', $map->mapid);
	}

	/**
	* Autoicons
	*/
	function autoicons() {
		global $post;

		// Posts only
		if ($this->otype != 'post')
			return;

		// Only 1 rule allowed
		$rule = (object) wp_parse_args(Mappress::$options->autoicons, array('key' => null, 'values' => array()));

		foreach($rule->values as $value => $iconid) {
			// Get all post IDs that match the current key & value
			if ($rule->key == 'post_type') {
				$wpq = new WP_Query(array('post_type' => $value, 'fields' => 'ids', 'posts_per_page' => -1));
				$postids = $wpq->posts;
			} else {
				$term = get_term_by('slug', $value, $rule->key);
				if (!is_object($term))
					continue;

				$objects = get_objects_in_term($term->term_id, $rule->key);
				if (!is_array($objects))
					continue;

				$postids = array_keys(array_flip($objects));
			}

			// Check each post ID to see if it's in the map's POIs, if so set iconid
			$current_post = ($post) ? $post->ID : null;
			foreach($this->pois as &$poi) {
				$postid = ($poi->oid) ? $poi->oid : $current_post;
				if (in_array($postid, $postids))
					$poi->iconid = $iconid;
			}
		}

		// Filter
		foreach($this->pois as &$poi)
			$poi->iconid = apply_filters('mappress_poi_iconid', $poi->iconid, $poi);
	}

	/**
	* Compare two POIs by title
	* HTML tags are stripped - until URL is separated from title this is the only way to
	* sort titles with HTML
	*
	* @param mixed $a
	* @param mixed $b
	* @return mixed
	*/
	static function compare_title($a, $b) {
		return strcasecmp(strip_tags($a->title), strip_tags($b->title));
	}

	/**
	* Delete a map and all of its post assignments
	*
	* @param mixed $mapid
	*/
	static function delete($mapid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mapp_maps';

		$result = $wpdb->query($wpdb->prepare("DELETE FROM $maps_table WHERE mapid = %d", $mapid));
		if ($result === false)
			return false;

		$wpdb->query("COMMIT");
		do_action('mappress_map_delete', $mapid); 	// Use for your own developments
		return true;
	}

	/**
	* When a post is deleted, trash attached maps
	*/
	static function deleted_post($postid) {
		$mapids = self::get_list('post', $postid, 'ids');
		foreach($mapids as $mapid)
			self::mutate($mapid, array('status' => 'trashed', 'oid' => 0));
	}

	/**
	* When a user is deleted, delete attached maps
	*/
	static function deleted_user($userid) {
		$mapids = self::get_list('user', $userid, 'ids');
		$result = true;
		foreach($mapids as $mapid)
			$result = $result && self::delete($mapid);
		return $result;
	}

	/**
	* Display a map
	*
	* @param mixed $atts - override attributes.  Attributes applied from options -> map -> $atts
	*/
	function display($atts = null) {
		static $div = 0;

		$this->update($atts);

		// Assign a map name, if none was provided.  Uniqid is used for for ajax to prevent repeating ids
		if (empty($this->name)) {
			$this->name = (defined('DOING_AJAX') && DOING_AJAX) ? "mapp" . uniqid() : "mapp$div";
			$div++;
		}

		// Prepare POIs for maps
		if (empty($this->query))
			$this->prepare();

		// Last chance to alter map before display
		do_action('mappress_map_display', $this);

		// Map data
		$script = Mappress::script(
			"window.mapp = window.mapp || {}; mapp.data = mapp.data || [];\r\n"
			. "mapp.data.push( " . json_encode($this) . " ); \r\nif (typeof mapp.load != 'undefined') { mapp.load(); };"
		);

		if (Mappress::$options->iframes) {
			Mappress::generate_iframe();
			$url = Mappress::$baseurl . '/templates/iframe.html';
			$iframe = "<iframe class='mapp-iframe' id='{$this->name}' src='$url' scrolling='no' loading='lazy'></iframe>";
			return $script . $this->get_layout($iframe);
		} else {
			Mappress::scripts_enqueue();
			return $this->get_layout() . $script;
		}
	}

	function get_dims() {
		$suffix = function($dim) {
			return (is_string($dim) && (stristr($dim, 'px') || stristr($dim, '%') || stristr($dim, 'vh') || stristr($dim, 'vw'))) ? $dim : ($dim . 'px');
		};
		$defaultSize = (isset(Mappress::$options->sizes[Mappress::$options->size])) ? (object) Mappress::$options->sizes[Mappress::$options->size] : (object) Mappress::$options->sizes[0];
		return (object) array(
			'width' => ($this->width) ? $suffix($this->width) : $suffix($defaultSize->width),
			'height' => ($this->height) ? $suffix($this->height) : $suffix($defaultSize->height)
		);
	}

	function get_layout($content = '') {
		$layout = ($this->layout) ? $this->layout : Mappress::$options->layout;
		$layoutClass = (Mappress::$options->iframes) ? 'mapp-iframe-container' : 'mapp-layout';

		$alignment = ($this->alignment) ? $this->alignment : Mappress::$options->alignment;
		if ($alignment) {
			$layoutClass .= ' align' . $alignment;
			$layoutClass .= ' mapp-align-' . $alignment;
		}

		$dims = $this->get_dims();
		$layoutStyle = ($this->alignment == 'full') ? "width: auto" : "width: {$dims->width}";

		$wrapperClass = (Mappress::$options->iframes) ? 'mapp-iframe-wrapper' : 'mapp-wrapper';
		$wrapperStyle = (stristr($dims->height, 'vh')) ? "height: {$dims->height};" : "padding-bottom: {$dims->height}";

		return "<div id='{$this->name}' class='$layoutClass' style='$layoutStyle'><div class='$wrapperClass' style='$wrapperStyle'>$content</div></div>";
	}

	static function display_user_map($user) {
		$error = get_user_meta($user->ID, 'mappress_error', true);
		if ($error)
			echo "<div class='mapp-help-error'>" . sprintf(__('Geocoding error: %s', 'mappress-google-maps-for-wordpress'), $error) . "</div>";

		$maps = Mappress_Map::get_list('user', $user->ID);
		if (empty($maps))
			return;

		echo "<h2>" . __('Location', 'mappress-google-maps-for-wordpress') . "</h2><table class='form-table'><tbody>";

		foreach($maps as $map) {
			if ($map->status == 'trashed')
				continue;
			$map->poiList = false;
			$map->width = '80%';
			$map->height = '350px';
			echo "<tr><th></th><td>" . $map->display() . "</td></tr>";
		}
		echo "</tbody></table>";
	}


	static function duplicate($mapid) {
		$map = self::get($mapid);
		if (!$map)
			return null;

		$title = ($map->title) ? $map->title : __('Untitled', 'mappress-google-maps-for-wordpress');
		$map->title = sprintf(__('Copy of %s', 'mappress-google-maps-for-wordpress'), $title);

		$map->mapid = null;
		$map->metaKey = null;		// Map is no longer automatic

		$result = $map->save();
		return ($result) ? $map : null;
	}

	static function find($args) {
		global $wpdb;

		$maps_table = $wpdb->prefix . 'mapp_maps';

		$sql = "SELECT SQL_CALC_FOUND_ROWS $maps_table.mapid, $maps_table.obj, $maps_table.otype, $maps_table.oid, "
			. " $maps_table.status, $maps_table.title, $wpdb->posts.post_status, $wpdb->posts.post_title "
			. " FROM $maps_table "
			. " LEFT OUTER JOIN $wpdb->posts ON ($wpdb->posts.ID = $maps_table.oid AND $maps_table.otype = 'post')"
		;
		$results = $wpdb->get_results($sql);

		$items = array();
		foreach($results as $result) {
			// Check post status & permissions (posts only)
			if ($result->oid) {
				// Causes error if custom  type is deleted after map created
				//if (!current_user_can('edit_post', $result->oid))
				//	continue;
				if (in_array($result->post_status, array('auto-draft', 'inherit')))
					continue;
			}

			$items[] = array(
				'mapid' => $result->mapid,
				'title' => $result->title,
				'oid' => $result->oid,
				'otype' => $result->otype,
				'post_title' => $result->post_title,
				'status' => $result->status
			);
		}
		return $items;
	}

	/**
	* Get a map.  Output is 'raw' or 'object'
	*/
	static function get($mapid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mapp_maps';

		$sql = $wpdb->prepare("SELECT * FROM $maps_table WHERE mapid=%d", $mapid);
		$result = $wpdb->get_row($sql);

		if (!$result)
			return false;

		$mapdata = json_decode($result->obj);
		if (!$mapdata)
			return false;

		$mapdata->mapid = $result->mapid;
		$mapdata->otype = $result->otype;
		$mapdata->oid = $result->oid;
		$mapdata->status = $result->status;
		$mapdata->title = $result->title;

		$map = new Mappress_Map($mapdata);
		return $map;
	}

	/**
	* Get list of mapids for a post or all maps
	*
	* @return array of mapids | empty array
	*
	*/
	static function get_list($otype, $oid = null, $output = 'objects') {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mapp_maps';

		$otype = ($otype) ? $otype : 'post';
		$where = $wpdb->prepare("WHERE otype=%s", $otype);
		if ($oid)
			$where .= $wpdb->prepare(" AND oid=%d", $oid);

		$mapids = $wpdb->get_col("SELECT mapid FROM $maps_table $where");
		if (!$mapids)
			return array();

		if ($output == 'ids') {
			return $mapids;
		} else {
			$maps = array();
			foreach($mapids as $mapid) {
				$map = Mappress_Map::get($mapid);
				if ($map)
					$maps[] = $map;
			}
			return $maps;
		}
	}

	static function media_buttons($editor_id) {
		$button = sprintf("<button type='button' class='button wp-media-buttons-icon mapp-classic-button'><span class='dashicons dashicons-location'></span>%s</button>", __('MapPress', 'mappress-google-maps-for-wordpress'));
		echo "<div class='mapp-classic'>$button</div>";
	}

	static function mutate($mapid, $mapdata) {
		if (!$mapid || !$mapdata)
			return false;

		$map = self::get($mapid);
		if (!$map)
			return false;

		$map->update($mapdata);
		$result = $map->save();
		return ($result) ? true : false;
	}

	/**
	* Prepare map for output
	*
	*/
	function prepare() {
		global $post, $wp_embed;

		// Parse custom tokens from templates
		$custom_tokens = Mappress_Template::get_custom_tokens($this->otype);

		foreach($this->pois as $poi) {

			// Add props
			$oid = ($poi->oid) ? $poi->oid : $this->oid;
			$poi->props = Mappress_Template::get_poi_props($poi, $this->otype, $oid, $custom_tokens);

			// Populate user fields
			if ($this->otype == 'user') {
				$user = get_userdata($oid);
				$poi->email = $user->data->user_email;
				$poi->name = $user->data->display_name;
				$poi->images = array( (object) array('id' => $user->ID, 'type' => 'avatar'));
				$poi->url = get_author_posts_url($user->ID);
			}

			// Process oembeds and embed shortcodes ([embed], etc)
			if ($poi->body) {
				$poi->body = $wp_embed->autoembed($poi->body);
				$poi->body = $wp_embed->run_shortcode($poi->body);
			}

			// Update image URLs
			$poi->update_images();
		}

		// Autoicons & sort
		if ($this->otype == 'post')
			$this->autoicons();

		if (Mappress::$options->sort && !isset($this->query['orderby']))
			$this->sort_pois();
	}

	function save() {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mapp_maps';

		// Apply wpautop to POI bodies
		foreach($this->pois as &$poi)
			$poi->body = wpautop($poi->body);

		$obj = $this->to_json();

		// Insert if no ID, else update
		if (!$this->mapid) {
			$sql = "INSERT INTO $maps_table (otype, oid, status, title, obj) VALUES(%s, %d, %s, %s, %s)";
			$result = $wpdb->query($wpdb->prepare($sql, $this->otype, $this->oid, $this->status, $this->title, $obj));
			$this->mapid = $wpdb->get_var("SELECT LAST_INSERT_ID()");
		} else {
			$sql = "INSERT INTO $maps_table (mapid, otype, oid, status, title, obj) VALUES(%d, %s, %d, %s, %s, %s) "
				. " ON DUPLICATE KEY UPDATE mapid=%d, otype=%s, oid=%d, status=%s, title=%s, obj=%s ";
			$result = $wpdb->query($wpdb->prepare($sql, $this->mapid, $this->otype, $this->oid, $this->status, $this->title, $obj,
				$this->mapid, $this->otype, $this->oid, $this->status, $this->title, $obj));
		}

		if ($result === false || !$this->mapid)
			return false;

		$wpdb->query("COMMIT");
		do_action('mappress_map_save', $this); 	// Use for your own developments
		return true;
	}

	/**
	* Default action to sort the map
	* Titles are compared with HTML stripped
	*
	* @param mixed $map
	*/
	function sort_pois() {
		usort($this->pois, function($a, $b) {
			return strcasecmp(strip_tags($a->title), strip_tags($b->title));
		});
		do_action('mappress_sort_pois', $this);
	}

	/**
	* When a post is trashed, trash attached maps
	*/
	static function trashed_post($postid) {
		$mapids = self::get_list('post', $postid, 'ids');
		foreach($mapids as $mapid)
			self::mutate($mapid, array('status' => 'trashed'));
	}
}
?>