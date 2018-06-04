<?php
class Mappress_Poi extends Mappress_Obj {
	var $address,
		$body = '',
		$correctedAddress,
		$iconid,
		$point = array('lat' => 0, 'lng' => 0),
		$poly,
		$postid,
		$kml,
		$thumbnail,
		$title = '',
		$type,
		$url,
		$viewport;              // array('sw' => array('lat' => 0, 'lng' => 0), 'ne' => array('lat' => 0, 'lng' => 0))

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
		if (!Mappress::$pro)
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
		global $post;
		$html = Mappress::get_template('map-poi', array('poi' => $this));
		$html = apply_filters('mappress_poi_html', $html, $this);
		$this->html = $html;
	}

	function part($part) {
		switch($part) {
			case 'body' :
				$html = $this->body;
				break;

			case 'directions' :
				$html = (Mappress::$options->directions != 'none') ? sprintf("<a href='#' data-mapp-action='dir'>%s</a>", __('Directions', 'mappress-google-maps-for-wordpress')) : '';
				break;

			case 'icon' :
				$html = (Mappress::$pro) ? sprintf("<img class='mapp-icon' src='%s' />", Mappress_Icons::get($this->iconid)) : '';
				break;

			case 'thumbnail' :
				$html = ($this->thumbnail) ? sprintf("<a href='%s'>%s</a>", $this->url, $this->thumbnail) : '';
				break;

			case 'title' :
				$html = $this->title;
				break;

			case 'title-link' :
				$link = ($this->postid) ? sprintf("<a href='%s'>%s</a>", $this->url, esc_html($this->title)) : $this->title;
				$html = $link;
				break;

		}
		return (isset($html)) ? $html : "<!-- unknown poi part $part -->";
	}


	/**
	* Fast excerpt for a poi
	*/
	function get_post_excerpt($post) {
		// Fast excerpts: similar to wp_trim_excerpt() in formatting.php, but without (slow) call to get_the_content()
		$text = ($post->post_excerpt) ? $post->post_excerpt : $post->post_content;
		$text = strip_shortcodes($text);
		$excerpt_length = 55;
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
		return wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}

	function get_thumbnail($post) {
		$size = (Mappress::$options->thumbSize) ? Mappress::$options->thumbSize : null;
		$style = (Mappress::$options->thumbWidth && Mappress::$options->thumbHeight) ? sprintf("width: %spx; height : %spx;", Mappress::$options->thumbWidth, Mappress::$options->thumbHeight) : null;
		return get_the_post_thumbnail($post, $size, array('style' => $style));			// Slow due to get_post_thumbnail_id()
	}
}
?>