<?php
class Mappress_Map extends Mappress_Obj {
	var $alignment,
		$center,
		$class,     
		$filter,
		$geolocate,
		$height,
		$hideEmpty,
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
		$style, 
		$title,
		$width,
		$zoom
		;

	var $pois = array();

	function to_html() {
		// Exclude a few fields that don't need to be in the web component (pois are determined later)
		$vars = array_diff_key(get_object_vars($this), array('otitle' => '', 'pois' => '', 'status' => '', 'title' => ''));

		// Convert center from object to string for display in attributes
		$vars['center'] = (isset($this->center) && is_object($this->center)) ? $this->center->lat . ',' . $this->center->lng : '';

		// Force left layout
		$vars['layout'] = 'left';

		$atts = Mappress::to_atts($vars);
		$pois = join('', array_map(function($poi) { return $poi->to_html(); }, $this->pois));
		return "\r\n<mappress-map {$atts}>$pois\r\n</mappress-map>\r\n";
	}

	function to_json() {
		$json_pois = array();
		foreach($this->pois as $poi)
			$json_pois[] = $poi->to_json();

		return array(
			'mapid' => $this->mapid,
			'otype' => $this->otype,
			'oid' => $this->oid,
			'center' => $this->center,
			'filter' => $this->filter,
			'height' => $this->height,
			'mapTypeId' => $this->mapTypeId,
			'metaKey' => $this->metaKey,
			'pois' => $json_pois,
			'search' => $this->search,
			'status' => $this->status,
			'title' => $this->title,
			'width' => $this->width,
			'zoom' => $this->zoom
		);
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

		add_action('wp_ajax_mapp_get_post', array(__CLASS__, 'ajax_get_post'));
		add_action('wp_ajax_nopriv_mapp_get_post', array(__CLASS__, 'ajax_get_post'));

		add_action('deleted_post', array(__CLASS__, 'deleted_post'));
		add_action('trashed_post', array(__CLASS__, 'trashed_post'));
		add_action('media_buttons', array(__CLASS__, 'media_buttons'));

		add_action('show_user_profile', array(__CLASS__, 'display_user_map'));
		add_action('edit_user_profile', array(__CLASS__, 'display_user_map'));
		add_action('deleted_user', array(__CLASS__, 'deleted_user'));
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
	function display($atts = null, $in_iframe = false) {
		static $div = 0;

		$this->update($atts);

		// Assign a map name, if none was provided.  Uniqid is used for for ajax to prevent repeating ids
		if (empty($this->name)) {
			$this->name = (defined('DOING_AJAX') && DOING_AJAX) ? "mapp" . uniqid() : "mapp$div";
			$div++;
		} else {
			// Sanitize name, could be provided by user in iframe URL
			$this->name = sanitize_text_field($this->name);
		}

		if (Mappress::$options->webComponent)
			return $this->display_web_component(null, $in_iframe);

		// iframe container
		if (Mappress::$options->iframes && !$in_iframe) {
			// Convert booleans to strings
			$args = array_map(function($arg) { if (is_bool($arg)) return ($arg) ? "true" : "false"; else return $arg; }, (array) $this);

			// Query or mapid - no POIs in iframe URL
			if ($this->query || $this->mapid) {
				unset($args['pois']);
			} else {
				// Programmatic - URL contains only transient id
				$transient = 'mapp-iframe-' . md5(json_encode($this));
				set_transient($transient, $this, 30);
				$args = array('transient' => $transient);
			}

			$url = get_home_url() . '?mappress=embed&' . http_build_query($args);

			// Width + height attributes are required for Google AMP
			$iframe = "<iframe height='100%' width='100%' class='mapp-iframe' src='$url' scrolling='no' loading='lazy'></iframe>";
			return $this->get_layout($iframe);
		}

		// Prepare POIs
		$this->prepare();

		// Last chance to alter map before display
		do_action('mappress_map_display', $this);

		// Map data
		$script = Mappress::script(
			"window.mapp = window.mapp || {}; mapp.data = mapp.data || [];\r\n"
			. "mapp.data.push( " . json_encode($this) . " ); \r\nif (typeof mapp.load != 'undefined') { mapp.load(); };"
		);

		if ($in_iframe) {
			return "<div id='{$this->name}' class='mapp-content'></div>". $script;
		} else {
				Mappress::scripts_enqueue();
			return $this->get_layout() . $script;
		}
	}

	/**
	* Display a map web component
	*
	* @param mixed $atts - override attributes.  Attributes applied from options -> map -> $atts
	*/
	function display_web_component($atts = null, $in_iframe = false) {
		$alignment = ($this->alignment) ? $this->alignment : Mappress::$options->alignment;
		$alignment_class = ($alignment) ? ' align' . $alignment. ' mapp-align-' . $alignment : '';

		$dims = $this->get_dims();
		$style = sprintf("width: %s;", ($this->alignment == 'full') ? "auto" : $dims->width);
		$style .= (stristr($dims->height, '%')) ? sprintf("height: auto; aspect-ratio: 100/%d;", (int) $dims->height)  : "height: {$dims->height};";
		
		// iframe container
		if (Mappress::$options->iframes && !$in_iframe) {
			// Convert booleans to strings for iframe atts
			$args = array_map(function($arg) { if (is_bool($arg)) return ($arg) ? "true" : "false"; else return $arg; }, (array) $this);

			// Query or mapid - no POIs in iframe URL
			if ($this->query || $this->mapid) {
				unset($args['pois']);
			} else {
				// Programmatic - URL contains only transient id
				$transient = 'mapp-iframe-' . md5(json_encode($this));
				set_transient($transient, $this, 30);
				$args = array('transient' => $transient);
			}

			$url = get_home_url() . '?mappress=embed&' . http_build_query($args);
			
			// Note that width + height attributes are required for Google AMP
			$layout_atts = Mappress::to_atts($atts);
			
			// Iframes don't size like divs, so require a wrapper div			
			$wrapper_class = 'mapp-layout mapp-has-iframe' . $alignment_class;
			return "<div id='{$this->name}' class='$wrapper_class' style='$style'>"
				. "<iframe class='mapp-iframe ' src='$url' scrolling='no' loading='lazy'></iframe>"
				. "</div>";
		} else {       
			// Prepare POIs
			$this->prepare();
			
			if ($in_iframe) {
				// For Component inside iframe, alignment class and style (height/width) are applied to wrapper, not component
				$this->class = 'mapp-layout';
				$this->style = 'height: 100%';
			} else {
				$this->class = 'mapp-layout ' . $alignment_class;
				$this->style = $style;
			}
			
			// Last chance to alter map before display
			do_action('mappress_map_display', $this);

			Mappress::scripts_enqueue();
			return $this->to_html();
		}
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
		$layoutClass = 'mapp-layout';
		$layoutClass .= (Mappress::$options->iframes) ? ' mapp-has-iframe' : '';

		$alignment = ($this->alignment) ? $this->alignment : Mappress::$options->alignment;
		if ($alignment) {
			$layoutClass .= ' align' . $alignment;
			$layoutClass .= ' mapp-align-' . $alignment;
		}

		$dims = $this->get_dims();
		$layoutStyle = ($this->alignment == 'full') ? "width: auto" : "width: {$dims->width}";

		$wrapperClass = 'mapp-wrapper';
		$wrapperStyle = "padding-bottom: {$dims->height}";

		return "<div id='{$this->name}' class='$layoutClass' style='$layoutStyle'><div class='$wrapperClass' style='$wrapperStyle'>$content</div></div>";
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

		// Object title is needed for editor
		if ($mapdata->oid) {
			$obj = ($mapdata->otype == 'user') ? wp_get_current_user($mapdata->oid) : get_post($mapdata->oid);
			if ($obj)
				$mapdata->otitle = ($mapdata->otype == 'user') ? $obj->user_nicename : $obj->post_title;
		}

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
				$poi->body = do_shortcode($poi->body);
				$poi->body = $wp_embed->autoembed($poi->body);
				$poi->body = $wp_embed->run_shortcode($poi->body);
			}

			// Update image URLs
			$poi->update_images();
		}

		// Autoicons & sort
		if ($this->otype == 'post')
			$this->autoicons();

		if (!Mappress::$options->sort && !isset($this->query['orderby']))
			do_action('mappress_sort_pois', $this);
	}

	function save() {
		global $wpdb;
		$maps_table = $wpdb->prefix . 'mapp_maps';

		// Apply wpautop to POI bodies
		foreach($this->pois as &$poi)
			$poi->body = wpautop($poi->body);

		// Filter out poi field data that is no longer present in settings
		foreach($this->pois as &$poi) {
			if (Mappress::$options->poiFields) {
				$keys = array_map(function($entry) { return $entry['key']; }, Mappress::$options->poiFields);
				$data = (array) $poi->data;
				$poi->data = (object) array_intersect_key($data, array_combine($keys, $keys));
			} else {
				$poi->data = null;
			}
		}

		$obj = json_encode($this->to_json());

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
	* When a post is trashed, trash attached maps
	*/
	static function trashed_post($postid) {
		$mapids = self::get_list('post', $postid, 'ids');
		foreach($mapids as $mapid)
			self::mutate($mapid, array('status' => 'trashed'));
	}
}
?>