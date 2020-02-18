<?php
class Mappress_Map extends Mappress_Obj {
	var $alignment,
		$center,
		$editable,
		$filter,
		$height,
		$hideEmpty,
		$initialOpenDirections,
		$initialOpenInfo,
		$layers,
		$layout,
		$mapid,
		$mapTypeId,
		$metaKey,
		$mapOpts,
		$name,
		$poiList,
		$postid,
		$query,
		$title,
		$width,
		$zoom
		;

	var $pois = array();

	function __sleep() {
		return array('mapid', 'center', 'height', 'mapTypeId', 'metaKey', 'pois', 'title', 'width', 'zoom');
	}

	function to_json() {
		// Use same keys as sleep
		$result = array_intersect_key(get_object_vars($this), array_flip($this->__sleep()));
		return $result;
	}

	function __construct($atts = null) {
		$this->update(Mappress::$options);
		$this->update($atts);

		// Convert POIs from arrays to objects if needed
		foreach((array)$this->pois as $index => $poi) {
			if (is_array($poi))
				$this->pois[$index] = new Mappress_Poi($poi);
		}
	}

	static function register() {
		global $wpdb;

		add_action('deleted_post', array(__CLASS__, 'delete_post_map'));

		add_action('wp_ajax_mapp_delete', array(__CLASS__, 'ajax_delete'));
		add_action('wp_ajax_mapp_find', array(__CLASS__, 'ajax_find'));
		add_action('wp_ajax_mapp_get', array(__CLASS__, 'ajax_get'));
		add_action('wp_ajax_mapp_save', array(__CLASS__, 'ajax_save'));

		// Editing meta boxes
		add_action('admin_init', array(__CLASS__, 'add_meta_boxes'));

		// Tables
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$wpdb->show_errors(true);

		$exists = $wpdb->get_var("show tables like '$maps_table'");
		if (!$exists) {
			$result = $wpdb->query ("CREATE TABLE $maps_table (
									mapid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
									obj LONGTEXT)
									CHARACTER SET utf8;");
		}

		$exists = $wpdb->get_var("show tables like '$posts_table'");
		if (!$exists) {
			$result = $wpdb->query ("CREATE TABLE $posts_table (
									postid INT,
									mapid INT,
									PRIMARY KEY (postid, mapid) )
									CHARACTER SET utf8;");
		}

		$wpdb->show_errors(false);
	}

	static function media_buttons($editor_id) {
		if ($editor_id == 'content') {
			echo '<button type="button" class="button mapp-media-button">';
			echo '<span class="wp-media-buttons-icon dashicons dashicons-location"></span> ' . __('MapPress', 'mappress-google-maps-for-wordpress') . '</button>';
		}
	}

	static function add_meta_boxes() {
		foreach(Mappress::$options->postTypes as $post_type)
			add_meta_box('mappress', 'MapPress', array(__CLASS__, 'meta_box'), $post_type, 'normal', 'high');
	}

	static function meta_box($post) {
		Mappress::load('editor');
		$map = new Mappress_Map(array('editable' => true, 'layout' => 'left', 'name' => 'mapp0', 'poiList' => true));
		require(Mappress::$basedir . '/forms/media.php');
	}

	static function find($args) {
		global $wpdb;

		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$sql = "SELECT SQL_CALC_FOUND_ROWS $maps_table.mapid, $maps_table.obj, $posts_table.postid, $wpdb->posts.post_title "
			. " FROM $maps_table "
			. " INNER JOIN $posts_table ON ($posts_table.mapid = $maps_table.mapid)"
			. " INNER JOIN $wpdb->posts ON ($wpdb->posts.ID = $posts_table.postid)"
		;
		$results = $wpdb->get_results($sql);

		$items = array();
		foreach($results as $result) {
			$mapdata = unserialize($result->obj);
			$items[] = array(
				'mapid' => $result->mapid,
				'map_title' => $mapdata->title,
				'postid' => $result->postid,
				'post_title' => $result->post_title
			);
		}

		return $items;
	}

	static function ajax_find() {
		Mappress::ajax_response('OK', self::find($_GET));
	}

	/**
	* Get a map.  Output is 'raw' or 'object'
	*/
	static function get($mapid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$sql = "SELECT $posts_table.postid, $maps_table.mapid, $maps_table.obj FROM $posts_table INNER JOIN $maps_table ON ($maps_table.mapid = $posts_table.mapid) WHERE $maps_table.mapid = %d";
		$result = $wpdb->get_row($wpdb->prepare($sql, $mapid));

		if (!$result)
			return false;

		// Read the map data and construct a new map from it
		$mapdata = unserialize($result->obj);
		$mapdata->postid = $result->postid;
		$mapdata->mapid = $result->mapid;
		return new Mappress_Map($mapdata);
	}

	static function ajax_get() {
		ob_start();
		$mapid = (isset($_GET['mapid'])) ? $_GET['mapid']  : null;
		$map = ($mapid) ? self::get($mapid) : null;
		if (!$map)
			Mappress::ajax_response(__('Map not found', 'mappress-google-maps-for-wordpress'));
		else
			Mappress::ajax_response('OK', array('map' => $map));
	}

	/**
	* Get list of mapids for a post or all maps
	*
	* @return array of mapids | empty array
	*
	*/
	static function get_list($postid = null, $output = 'objects') {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$where = ($postid) ? $wpdb->prepare("WHERE postid = %d", $postid) : '';

		$mapids = $wpdb->get_col("SELECT mapid FROM $posts_table $where");
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

	function save($postid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		// Apply wpautop to POI bodies
		foreach($this->pois as &$poi)
			$poi->body = wpautop($poi->body);

		$map = serialize($this);

		// Update map
		if (!$this->mapid) {
			// If no ID then autonumber
			$result = $wpdb->query($wpdb->prepare("INSERT INTO $maps_table (obj) VALUES(%s)", $map));
			$this->mapid = (int)$wpdb->get_var("SELECT LAST_INSERT_ID()");
		} else {
			// Id provided, so insert or update
			$result = $wpdb->query($wpdb->prepare("INSERT INTO $maps_table (mapid, obj) VALUES(%d, '%s') ON DUPLICATE KEY UPDATE obj = %s", $this->mapid, $map, $map));
		}

		if ($result === false || !$this->mapid)
			return false;

		// Update posts
		$result = $wpdb->query($wpdb->prepare("INSERT INTO $posts_table (postid, mapid) VALUES(%d, %d) ON DUPLICATE KEY UPDATE postid = %d, mapid = %d", $postid, $this->mapid,
			$postid, $this->mapid));

		if ($result === false)
			return false;

		$wpdb->query("COMMIT");
		return $this->mapid;
	}

	static function ajax_save() {
		ob_start();

		$mapdata = (isset($_POST['map'])) ? json_decode(stripslashes($_POST['map']), true) : null;
		$postid = (isset($_POST['postid'])) ? $_POST['postid'] : null;

		if (!$mapdata)
			Mappress::ajax_response('Internal error, your data has not been saved!');

		$map = new Mappress_Map($mapdata);
		$mapid = $map->save($postid);

		if ($mapid === false)
			Mappress::ajax_response('Internal error, your data has not been saved!');

		do_action('mappress_map_save', $mapid); 	// Use for your own developments

		// Return saved mapid
		Mappress::ajax_response('OK', array('mapid' => $mapid));
	}

	/**
	* Delete a map and all of its post assignments
	*
	* @param mixed $mapid
	*/
	static function delete($mapid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$posts_table = $wpdb->prefix . 'mappress_posts';

		// Delete from posts table
		$result = $wpdb->query($wpdb->prepare("DELETE FROM $posts_table WHERE mapid = %d", $mapid));
		if ($result === false)
			return false;

		$result = $wpdb->query($wpdb->prepare("DELETE FROM $maps_table WHERE mapid = %d", $mapid));
		if ($result === false)
			return false;

		$wpdb->query("COMMIT");
		return true;
	}

	static function ajax_delete() {
		ob_start();

		$mapid = (isset($_POST['mapid'])) ? $_POST['mapid'] : null;
		$result = Mappress_Map::delete($mapid);

		if (!$result)
			Mappress::ajax_response("Internal error when deleting map ID '$mapid'!");

		do_action('mappress_map_delete', $mapid); 	// Use for your own developments
		Mappress::ajax_response('OK', array('mapid' => $mapid));
	}

	/**
	* Delete a map assignment(s) for a post
	* If $mapid is null, then ALL maps will be removed from the post
	*
	* @param int $mapid Map to remove
	* @param int $postid Post to remove from
	* @return TRUE if map has been removed, FALSE if map wasn't assigned to the post
	*/
	static function delete_post_map($postid, $mapid=null) {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';

		if (!$postid)
			return true;

		if ($mapid)
			$results = $wpdb->query($wpdb->prepare("DELETE FROM $posts_table WHERE postid = %d AND mapid = %d", $postid, $mapid));
		else
			$results = $wpdb->query($wpdb->prepare("DELETE FROM $posts_table WHERE postid = %d", $postid));

		$wpdb->query("COMMIT");

		if ($results === false)
			return false;

		return true;
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

		$html = Mappress_Template::get_template('map', array('map' => $this));
		Mappress::load();
		$script = "mapp.data.push( " . json_encode($this) . " ); \r\nif (typeof mapp.load != 'undefined') { mapp.load(); };";

		// Use inline scripts for XHTML and some themes which match tags (incorrectly) in the content
		if (function_exists('wp_add_inline_script') && Mappress::$options->footer && (!defined('DOING_AJAX') || !DOING_AJAX))
			wp_add_inline_script('mappress', "//<![CDATA[\r\n" . $script . "\r\n//]]>");
		else
			$html .= Mappress::script($script);

		return $html;
	}

	function width() {
		$default = (object) Mappress::$options->sizes[Mappress::$options->size];
		$width = ($this->width) ? $this->width : $default->width;
		return ( stripos($width, 'px') || strpos($width, '%')) ? $width : $width. 'px';
	}

	function height() {
		$default = (object) Mappress::$options->sizes[Mappress::$options->size];
		$height = ($this->height) ? $this->height : $default->height;

		if (stristr($height, ':')) {
			$parts = explode(':', $height);
			if (count($parts) == 2 && $parts[0] > 0)
				return round((100 * $parts[1] / $parts[0]), 2) . '%';
		}
		return ( stripos($height, 'px') || strpos($height, '%')) ? $height : $height. 'px';
	}

	function check($part) {
		switch ($part) {
			case 'directions' :
				return !$this->editable && Mappress::$options->directions != 'google';

			case 'filters' :
			case 'filters-toggle' :
				return $this->query && $this->filter;

			case 'header' :
				return $this->check('filters') || $this->check('search');

			case 'list-inline' :
				return $this->poiList && $this->layout != 'left';

			case 'list-left' :
				return $this->poiList && $this->layout == 'left';

			case 'search' :
				return $this->editable || ($this->query && Mappress::$options->search);

			case 'show' :
				return $this->hidden;

			case 'view-toggles' :
				return $this->layout == 'left';

		}
		return true;
	}

	function part($part) {
		if (!$this->check($part))
			return;

		switch ($part) {
			case 'controls' :
				$html = "<div class='mapp-controls'></div>";
				break;

			case 'directions' :
			case 'filters' :
			case 'header' :
			case 'search' :
				$html = Mappress_Template::get_template("map-$part", array('map' => $this));
				break;

			case 'canvas' :
				$html = "<div class='mapp-canvas' id='{$this->name}'></div>";
				break;

			case 'filters-toggle' :
				$html = "<div class='mapp-caret mapp-header-button mapp-filters-toggle' data-mapp-action='filters-toggle'>" . __('Filter', 'mappress-google-maps-for-wordpress') . "</div>";
				break;

			case 'iw' :
				$html = "<div class='mapp-iw'></div>";
				break;

			case 'layout-atts' :
				$id = $this->name . '-layout';

				$class = 'mapp-layout';
				$class .= ($this->layout == 'left') ? ' mapp-left' : ' mapp-inline';
				$class .= (Mappress::$options->engine == 'leaflet') ? ' mapp-leaflet ' : ' mapp-google';
				if (wp_is_mobile())
					$class .= ' mobile';
				if (!$this->editable)
					$class .= ($this->alignment) ? " mapp-align-{$this->alignment}" : '';
				if ($this->check('filters'))
					$class .= ' mapp-has-filters';
				if ($this->check('search'))
					$class .= ' mapp-has-search';

				$style = sprintf("width: %s;", $this->width());
				$html = "id='$id' class='$class' style='$style'";
				break;

			case 'list-inline' :
			case 'list-left' :
				$html = "<div class='mapp-list'></div>";
				break;

			case 'show' :
				// Should be onclick...
				$html = sprintf("<a href='#' data-mapp-action='show'>%s</a>", __('Show map', 'mappress-google-maps-for-wordpress'));
				break;

			case 'view-toggles' :
				$html = sprintf("<div class='mapp-header-button' data-mapp-action='view-map'>%s</div>", __('Map', 'mappress-google-maps-for-wordpress'));
				$html .= sprintf("<div class='mapp-header-button' data-mapp-action='view-list'>%s</div>", __('List', 'mappress-google-maps-for-wordpress'));
				break;

			case 'wrapper-style' :
				$h = $this->height();
				// Responsive if aspect ratio present (':') otherwise use specified % or px
				$html = (stristr($this->height, ':')) ? "padding-bottom: $h;" : "height: $h";
				break;
		}

		return (isset($html)) ? $html : "<!-- unknown part $part -->";
	}

	/**
	* Prepare map for output
	*
	*/
	function prepare() {
		global $post;

		// Sort the pois
		if (Mappress::$options->sort && !isset($this->query['orderby']))
			$this->sort_pois();

		// Set properties (mashups and maps)
		foreach($this->pois as $poi) {
			if ($poi->postid)
				$postid = $poi->postid;
			else
				$postid = ($post) ? $post->ID : null;
			$poi->props = apply_filters('mappress_poi_props', $poi->props, $postid, $poi);
		}

		// Autoicons
		$this->autoicons();
	}

	/**
	* Autoicons
	*/
	function autoicons() {
		global $post;

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
				$postid = ($poi->postid) ? $poi->postid : $current_post;
				if (in_array($postid, $postids))
					$poi->iconid = $iconid;
			}
		}

		// Filter
		foreach($this->pois as &$poi)
			$poi->iconid = apply_filters('mappress_poi_iconid', $poi->iconid, $poi);
	}

	/**
	* Default action to sort the map
	*
	* @param mixed $map
	*/
	function sort_pois() {
		usort($this->pois, array(__CLASS__, 'compare_title'));
		do_action('mappress_sort_pois', $this);
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
}
?>