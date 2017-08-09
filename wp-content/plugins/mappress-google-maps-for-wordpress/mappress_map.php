<?php
class Mappress_Map extends Mappress_Obj {
	var $mapid,
		$width,
		$height,
		$zoom,
		$center = array('lat' => 0, 'lng' => 0),
		$mapTypeId = 'roadmap',
		$title,
		$metaKey,
		$query,
		$queryResult,
		$pois = array();

	// Not saved
	var $options,
		$name;

	function __sleep() {
		return array('mapid', 'width', 'height', 'zoom', 'center', 'mapTypeId', 'title', 'metaKey', 'pois');
	}

	function __construct($atts=null) {
		parent::__construct($atts);

		// Set the options; they may be passed as individual parameters or an 'options' array
		$this->options = Mappress_Options::get();
		if (isset($atts['options']))
			$this->options->update($atts['options']);
		else
			$this->options->update($atts);

		// Set language
		if (!$this->options->language)
			$this->options->language = Mappress::get_language();

		// Convert POIs from arrays to objects if needed
		foreach((array)$this->pois as $index => $poi) {
			if (is_array($poi))
				$this->pois[$index] = new Mappress_Poi($poi);
		}

		// Set default size if no width/height specified
		if (!$this->width || !$this->height) {
			$i = (int) $this->options->size;
			if (isset(Mappress::$options->sizes[$i])) {
				$size = Mappress::$options->sizes[$i];
				$this->width = ($this->width) ? $this->width : $size['width'];
				$this->height = ($this->height) ? $this->height : $size['height'];
			}
		}
	}

	static function register() {
		global $wpdb;

		// Ajax
		add_action('wp_ajax_mapp_create', array(__CLASS__, 'ajax_create'));
		add_action('wp_ajax_mapp_get', array(__CLASS__, 'ajax_get'));
		add_action('wp_ajax_mapp_save', array(__CLASS__, 'ajax_save'));
		add_action('wp_ajax_mapp_delete', array(__CLASS__, 'ajax_delete'));

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

	static function add_meta_boxes() {
		// Add editing meta box to standard & custom post types
		foreach(Mappress::$options->postTypes as $post_type)
			add_meta_box('mappress', 'MapPress', array(__CLASS__, 'meta_box'), $post_type, 'normal', 'high');
	}

	static function meta_box($post) {
		global $mappress;
		require(Mappress::$basedir . '/forms/map_media.php');
		$mappress->load('editor');
	}

	static function ajax_create() {
		ob_start();
		$map = new Mappress_Map();
		Mappress::ajax_response('OK', array('map' => $map));
	}

	static function ajax_get($mapid) {
		ob_start();
		$mapid = (isset($_GET['mapid'])) ? $_GET['mapid']  : null;
		$map = ($mapid) ? self::get($mapid) : null;
		if (!$map)
			Mappress::ajax_response(__('Map not found', 'mappress-google-maps-for-wordpress'));
		else
			Mappress::ajax_response('OK', array('map' => $map));
	}

	/**
	* Get a map.
	*
	* @param mixed $mapid
	* @return mixed false if failure, or a map object on success
	*/
	static function get($mapid) {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mappress_maps';
		$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $maps_table WHERE mapid = %d", $mapid) );  // May return FALSE or NULL

		if (!$result)
			return false;

		// Read the map data and construct a new map from it
		$mapdata = (array) unserialize($result->obj);
		$map = new Mappress_Map($mapdata);
		$map->mapid = $result->mapid;
		return $map;
	}

	/**
	* Get list of mapids for a post or all maps
	*
	* @return array of mapids | empty array
	*
	*/
	static function get_list($postid = null) {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';
		$sql = ($postid) ? $wpdb->prepare("SELECT mapid FROM $posts_table WHERE postid = %d", $postid) : "SELECT mapid FROM $posts_table";
		$result = $wpdb->get_col($sql);
		return (is_array($result)) ? $result : array();
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
		Mappress::ajax_response('OK', array('mapid' => $mapid, 'list' => self::get_map_list($postid)) );
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
	* Find any map for the post that was created automatically from a custom field
	*
	* @param mixed $postid
	* @return Mappress_Map or null if no map available
	*/
	static function get_post_meta_map ($postid) {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';

		// Search by meta_key
		$results = $wpdb->get_results($wpdb->prepare("SELECT mapid FROM $posts_table WHERE postid = %d", $postid));

		if ($results === false)
			return false;

		// Find which map, if any was generated automatically
		foreach($results as $key => $result) {
			$map = Mappress_Map::get($result->mapid);
			if ($map && $map->metaKey)
				return $map;
		}
	}


	/**
	* Get a list of maps attached to the post
	*
	* @param int $postid Post for which to get the list
	* @return an array of all maps for the post or FALSE if no maps
	*/
	static function get_post_map_list($postid) {
		global $wpdb;
		$posts_table = $wpdb->prefix . 'mappress_posts';

		$results = $wpdb->get_results($wpdb->prepare("SELECT postid, mapid FROM $posts_table WHERE postid = %d", $postid));

		if ($results === false)
			return false;

		// Get all of the maps
		$maps = array();
		foreach($results as $key => $result) {
			$map = Mappress_Map::get($result->mapid);
			if ($map)
				$maps[] = $map;
		}
		return $maps;
	}

	function width() {
		return ( stripos($this->width, 'px') || strpos($this->width, '%')) ? $this->width : $this->width. 'px';
	}

	function height() {
		return ( stripos($this->height, 'px') || strpos($this->height, '%')) ? $this->height : $this->height. 'px';
	}


	/**
	* Display a map
	*
	* @param mixed $atts - override attributes.  Attributes applied from options -> map -> $atts
	*/
	function display($atts = null) {
		static $div = 0;
		global $mappress;

		$this->update($atts);
		$this->options->update($atts);

		// Assign a map name, if none was provided.  Uniqid is used for for ajax to prevent repeating ids
		if (empty($this->name)) {
			$this->name = (defined('DOING_AJAX') && DOING_AJAX) ? "mapp" . uniqid() : "mapp$div";
			$div++;
		}

		// Layout
		if (class_exists('Mappress_Pro'))
			$html = $mappress->get_template($this->options->template, array('map' => $this));
		else
			$html = $mappress->get_template('map_layout', array('map' => $this));

		// Output map
		$mappress->load();

		// For static maps: prepare the pois
		if (empty($this->query))
			$this->prepare();

		if ($this->options->directions == 'inline') {
			$html .= "<div id='{$this->name}_directions_' style='display:none'>";
			$html .= $mappress->get_template($this->options->templateDirections, array('map' => $this));
			$html .= "</div>";
		}

		// For static maps: print the poi list
		if (empty($this->query) && $this->options->poiList) {
			$html .= "<div id='{$this->name}_poi_list_' style='display:none'>";
			$html .= $mappress->get_template($this->options->templatePoiList, array('map' => $this));
			$html .= "</div>";
		}

		$script = "mapp.data.push( " . json_encode($this) . " ); \r\nif (typeof mapp.load != 'undefined') { mapp.load(); };";

		// WP 4.5: queue maps for XHTML compatibility
		if (function_exists('wp_add_inline_script') && Mappress::$options->footer && (!defined('DOING_AJAX') || !DOING_AJAX))
			wp_add_inline_script('mappress', "//<![CDATA[\r\n" . $script . "\r\n//]]>");
		else
			$html .= Mappress::script($script);

		return $html;
	}

	/**
	* Prepare map for output
	*
	*/
	function prepare() {

		// Assign pois to map for template functions
		foreach($this->pois as $poi)
			$poi->map($this);

		// Prepare the pois
		foreach($this->pois as $poi)
			$poi->prepare();

		// Sort the pois
		if ($this->options->sort && !isset($this->query['orderby']))
			$this->sort_pois();

		// Set the HTML for each POI (comes *after* sort because links embed POI list position)
		foreach($this->pois as $poi)
			$poi->set_html();

		// Autoicons
		$this->autoicons();

		// Last chance to alter map before display
		do_action('mappress_map_display', $this);
	}

	/**
	* Autoicons
	*/
	function autoicons() {
		global $post;

		foreach ((array) Mappress::$options->autoicons as $rule) {
			$rule = (object) wp_parse_args($rule, array('key' => null, 'value' => null, 'iconid' => null));

			if ($rule->key == 'post_type') {
				$wpq = new WP_Query(array('post_type' => $rule->value, 'fields' => 'ids', 'posts_per_page' => -1));
				$postids = $wpq->posts;
			} else {
				$term = get_term_by('slug', $rule->value, $rule->key);
				if (!is_object($term))
					continue;

				$objects = get_objects_in_term($term->term_id, $rule->key);
				if (!is_array($objects))
					continue;

				$postids = array_keys(array_flip($objects));
			}

			// Now check each post ID to see if it's in the list of postids
			$current_post = ($post) ? $post->ID : null;
			foreach($this->pois as &$poi) {
				$postid = ($poi->postid) ? $poi->postid : $current_post;
				if (in_array($postid, $postids))
					$poi->iconid = $rule->iconid;
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

	/**
	* Get a list of maps for editing
	*
	* @param mixed $postid
	*/
	static function get_map_list($postid = null) {
		global $post;

		$postid = ($postid) ? $postid : $post->ID;
		$maps = self::get_post_map_list($postid);

		$actions = "<div class='mapp-m-actions'>"
			. "<a href='#' class='mapp-maplist-edit'>" . __('Edit', 'mappress-google-maps-for-wordpress') . "</a> | "
			. "<a href='#' class='mapp-maplist-insert'>" . __('Insert into post', 'mappress-google-maps-for-wordpress') . "</a> | "
			. "<a href='#' class='mapp-maplist-delete'>" . __('Delete', 'mappress-google-maps-for-wordpress') . "</a>"
			. "</div>";

		$html = "<table class='mapp-m-map-list'>";
		foreach($maps as $map)
			$html .= "<tr data-mapid='$map->mapid'><td><b><a href='#' class='mapp-maplist-title mapp-maplist-edit'>[$map->mapid] " . esc_html($map->title) . "</a></b>$actions</td></tr>";

		$html .= "</table>";
		return $html;
	}

	function get_layout_class() {
		$class = "mapp-layout";
		$class .= ($this->options->alignment && $this->options->alignment != 'default') ? " mapp-align-{$this->options->alignment}" : '';
		$class .= (wp_is_mobile()) ? " mobile" : '';
		return $class;
	}

	function get_layout_style() {
		$style = ($this->options->hidden) ? ' display:none;' : '';
		return $style;
	}

	function get_show_link() {
		$text = __('Show map', 'mappress-google-maps-for-wordpress');

		if (!$this->options->hidden)
			return '';

		$click = "{$this->name}.show(); return false;";
		return "<a href='#' onclick='$click'>$text</a>";
	}

	function get_center_link($args = '') {
		$text = __('Center map', 'mappress-google-maps-for-wordpress');
		$click = "{$this->name}.autoCenter(true); return false;";
		return "<a href='#' onclick='$click'>$text</a>";
	}

	function get_reset_link($args = '') {
		$text = __('Reset map', 'mappress-google-maps-for-wordpress');
		$click = "{$this->name}.reset(); return false;";
		return "<a href='#' onclick='$click'>$text</a>";
	}

	function get_bigger_link($args = '') {
		$big_text = "&raquo;&nbsp;" . __('Bigger map', 'mappress-google-maps-for-wordpress');
		$small_text = "&laquo;&nbsp;" . __('Smaller map', 'mappress-google-maps-for-wordpress');
		$click = "{$this->name}.bigger(this, \"$big_text\", \"$small_text\"); return false;";
		return "<a href='#' onclick='$click'>$big_text</a>";
	}

	function get_links() {
		$links = (array) $this->options->mapLinks;
		$a = array();

		if (in_array('center', $links))
			$a[] = $this->get_center_link();
		if (in_array('reset', $links))
			$a[] = $this->get_reset_link();
		if (in_array('bigger', $links))
			$a[] = $this->get_bigger_link();

		if (empty($a))
			return "";

		return implode('', $a);
	}
}
?>