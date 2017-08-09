<?php
class Mappress_Poi extends Mappress_Obj {
	var $address,
		$body = '',
		$correctedAddress,
		$iconid,
		$point = array('lat' => 0, 'lng' => 0),
		$poly,
		$kml,
		$title = '',
		$type,
		$viewport;              // array('sw' => array('lat' => 0, 'lng' => 0), 'ne' => array('lat' => 0, 'lng' => 0))

	// Not saved
	var $postid,
		$url;


	function __sleep() {
		return array('address', 'body', 'correctedAddress', 'iconid', 'point', 'poly', 'kml', 'title', 'type', 'viewport');
	}

	function __construct($atts = '') {
		parent::__construct($atts);
	}

	// Work-around for PHP issues with circular references (serialize, print_r, json_encode, etc.)
	function map($map = null) {
		static $_map;
		if ($map)
			$_map = $map;
		else
			return $_map;
	}

	/**
	* Geocode an address using http
	*
	* @param mixed $auto true = automatically update the poi, false = return raw geocoding results
	* @return true if auto=true and success | WP_Error on failure
	*/
	function geocode() {
		if (!class_exists('Mappress_Pro'))
			return new WP_Error('geocode', 'MapPress Pro required for geocoding');

		// If point has a lat/lng then no geocoding
		if (!empty($this->point['lat']) && !empty($this->point['lng'])) {
			$this->correctedAddress = ($this->address) ? $this->address : null;
			$this->viewport = null;
		} else {
			$location = Mappress_Geocoder::geocode($this->address);

			if (is_wp_error($location))
				return $location;

			$this->point = array('lat' => $location->lat, 'lng' => $location->lng);
			$this->correctedAddress = $location->formatted_address;
			$this->viewport = $location->viewport;
		}

		// Guess a default title / body - use address if available or lat, lng if not
		if (empty($this->title) && empty($this->body)) {
			if ($this->correctedAddress) {
				$parsed = Mappress_Geocoder::parse_address($this->correctedAddress);
				$this->title = $parsed[0];
				$this->body = (isset($parsed[1])) ? $parsed[1] : "";
			} else {
				$this->title = $this->point['lat'] . ',' . $this->point['lng'];
			}
		}
	}

	function set_html() {
		global $mappress, $post;

		if (class_exists('Mappress_Pro')) {
			$html = $mappress->get_template($this->map()->options->templatePoi, array('poi' => $this));
			$html = apply_filters('mappress_poi_html', $html, $this);
		} else {
			$html = "<div class='mapp-iw'>"
			. "<div class='mapp-title'>" . $this->title . "</div>"
			. "<div class='mapp-body'>" . $this->body . "</div>"
			. "<div class='mapp-links'>" . $this->get_links() . "</div>"
			. "</div>";
		}
		$this->html = $html;
	}

	/**
	* Prepare poi for output
	*/
	function prepare() {
		$map = $this->map();

		// Set title
		if ($map->options->mashupTitle == 'post' && $this->postid) {
			$post = get_post($this->postid);
			$this->title = $post->post_title;
		}

		$style = ($this->postid) ? $map->options->mashupBody : 'poi';

		// Set body
		if ($this->postid) {
			if ($map->options->mashupBody == 'post')
				$this->body = $this->get_post_excerpt();
			else if ($map->options->mashupBody == 'address')
				$this->body = $this->get_address();
		}

		// Set URL
		if ($this->postid && ($map->options->mashupClick == 'post' || $map->options->mashupLink))
			$this->url = get_permalink($this->postid);
	}

	/**
	* Get the poi title
	*
	*/
	function get_title() {
		return $this->title;
	}

	/**
	* Based on style settings, gets either the poi title or a link to the underlying post with poi title as text
	*
	*/
	function get_title_link() {
		$map = $this->map();
		$link = ($this->postid && $map->options->mashupLink) ? sprintf("<a href='%s'>%s</a>", $this->url, esc_html($this->title)) : $this->title;
		return $link;
	}

	/**
	* Get the poi body
	*
	*/
	function get_body() {
		return $this->body;
	}

	/**
	* Get a post excerpt for a poi
	* Uses the WP get_the_excerpt(), which requires postdata to be set up.
	*
	* @param mixed $postid
	*/
	function get_post_excerpt() {
		global $post;

		$post = get_post($this->postid);
		if (empty($this->postid) || empty($post))
			return "";

		$old_post = ($post) ? clone($post) : null;
		setup_postdata($post);
		$html = get_the_excerpt();

		// wp_reset_postdata() may not work with other plugins so use the cloned copy instead
		if ($old_post) {
			$post = $old_post;
			setup_postdata($post);
		}

		return $html;
	}

	/**
	* Get the formatted address as HTML
	* A <br> tag is inserted between the first line and subsequent lines
	*
	*/
	function get_address() {
		$parsed = Mappress_Geocoder::parse_address($this->correctedAddress);
		if (!$parsed)
			return "";

		return isset($parsed[1]) ? $parsed[0] . "<br/>" . $parsed[1] : $parsed[0];
	}

	/**
	* Get links for poi in infowindow or poi list
	*
	* @param mixed $context - blank or 'poi' | 'poi_list'
	*/
	function get_links($context = '') {
		$map = $this->map();

		$links = $map->options->poiLinks;

		$a = array();

		// Directions (not available for shapes, kml)
		if (empty($this->type)) {
			if (in_array('directions_to', $links) && $map->options->directions != 'none')
				$a[] = $this->get_directions_link(array('to' => $this, 'text' => __('Directions to', 'mappress-google-maps-for-wordpress')));
			if (in_array('directions_from', $links) && $map->options->directions != 'none')
				$a[] = $this->get_directions_link(array('from' => $this, 'to' => '', 'text' => __('Directions from', 'mappress-google-maps-for-wordpress')));
		}

		// Zoom isn't available in poi list by default
		if (in_array('zoom', $links) && $context != 'poi_list')
			$a[] = $this->get_zoom_link();

		if (empty($a))
			return "";

		$html = implode('&nbsp;&nbsp;', $a);
		return $html;
	}

	function get_icon() {
		$map = $this->map();
		return Mappress_Icons::get($this->iconid);
	}

	/**
	* Get a directions link
	*
	* @param bool $from - 'from' poi object or a string address
	* @param bool $to - 'to' poi object or a string address
	* @param mixed $text
	*/
	function get_directions_link($args = '') {
		$map = $this->map();

		$args = (object) wp_parse_args($args, array(
			'from' => $map->options->from,
			'to' => $map->options->to,
			'text' => __('Directions', 'mappress-google-maps-for-wordpress')
		));

		// Convert objects to indexes, quote strings
		if (is_object($args->from)) {
			$i = array_search($args->from, $map->pois);
			$from = "{$map->name}.getPoi($i)";
		} else {
			$from = "\"{$args->from}\"";
		}

		if (is_object($args->to)) {
			$i = array_search($args->to, $map->pois);
			$to = "{$map->name}.getPoi($i)";
		} else {
			$to = "\"{$args->to}\"";
		}

		$link = "<a href='#' onclick = '{$map->name}.openDirections(%s, %s, true); return false;'>{$args->text}</a>";
		return sprintf($link, $from, $to);
	}

	/**
	* Get a link to open a poi and optionally zoom in on it
	*
	* $args:
	*   text - text to print for the link, default is poi title
	*   zoom - false (default) = no zoom | true = zoom in to viewport (ignored for lat/lng pois with no viewport) | number = set zoom (0-15)
	*
	* @param mixed $map - map on which the poi should be opened
	* @param mixed $args
	* @return mixed
	*/
	function get_open_link ($args = '') {
		$map = $this->map();
		$title = $this->title;
		$i = array_search($this, $map->pois);
		return "<a href='#' onclick='{$map->name}.getPoi($i).open(null); return false;' >$title</a>";
	}

	function get_zoom_link ($args = '') {
		$map = $this->map();
		$text = __('Zoom', 'mappress-google-maps-for-wordpress');
		$i = array_search($this, $map->pois);
		$click = "{$map->name}.getPoi($i).zoomIn(); return false;";
		return "<a href='#' onclick='$click'>$text</a>";
	}

	/**
	* Get poi thumbnail
	*
	* @param mixed $map
	* @param mixed $args - arguments to pass to WP get_the_post_thumbnail() function
	*/
	function get_thumbnail( $args = '' ) {
		$map = $this->map();

		if (!$this->postid || !$map->options->thumbs)
			return '';

		$args = ($args) ? $args : array();
		$size = ($map->options->thumbSize) ? $map->options->thumbSize : null;

		if ($map->options->thumbWidth && $map->options->thumbHeight)
			$args['style'] = "width: {$map->options->thumbWidth}px; height : {$map->options->thumbHeight}px;";

		$html = get_the_post_thumbnail($this->postid, $size, $args);

		// If linking poi to underlying post, then link the featured image
		if ($map->options->mashupLink)
			$html = "<a href='" . $this->url . "'>$html</a>";

		return $html;
	}
}
?>