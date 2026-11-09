<?php
class Mappress_Compliance {

	static function register() {
		if (Mappress::is_plugin_active('complianz') || defined('cmplz_premium') )
			self::complianz();
	}

	static function complianz() {
		if (!defined('CMPLZ_GOOGLE_MAPS_INTEGRATION_ACTIVE') )
			define('CMPLZ_GOOGLE_MAPS_INTEGRATION_ACTIVE', true);

		add_filter('cmplz_known_script_tags', array(__CLASS__, 'cmplz_script'));
		add_filter('cmplz_detected_services', array(__CLASS__, 'cmplz_services'));
		add_filter('cmplz_whitelisted_script_tags', array(__CLASS__, 'cmplz_whitelist'));

		// Clear Complianz's blocked-scripts transient whenever MapPress options change,
		// so the new engine/tile-service is picked up immediately without needing ?cmplz_nocache.
		add_action('update_option_mappress_options', array(__CLASS__, 'clear_complianz_cache'));
	}

	static function clear_complianz_cache() {
		// Refresh MapPress options so we have the latest
		Mappress::$options = Mappress_Options::get();   
			 
		// Complianz uses its own cache, not standard WP transients
		if ( function_exists( 'cmplz_delete_transient' ) ) {
			cmplz_delete_transient( 'cmplz_blocked_scripts' );
		}
	}    
	
	static function cmplz_script( $tags ) {
		// OpenFreeMap + Leaflet: OFM does not track users and sets no cookies, so no consent required
		if (Mappress::$options->engine == 'leaflet' && Mappress::get_tile_service() == 'ofm') {
			return $tags;
		}

		// Iframe with google or mapbox
		if (Mappress::$options->iframes) {
			$tags[] = array(
				'name'        => 'mappress',
				//'placeholder' => 'google-maps',   // causes disconcerting flash before iframe loads
				'urls'        => array('mappress=embed'),
				'category'    => 'marketing',
				'iframe'      => 1,
			);
			return $tags;
		}
		
		if (Mappress::$options->engine == 'google') {
			// Google Maps — requires consent (tracks users)
			// maps.googleapis.com is loaded dynamically by index_mappress.js (not a WP-enqueued script),
			// so we only block index_mappress.js. No placeholder — Complianz fires it automatically on consent.
			$tags[] = array(
				'name' => 'mappress',
				'category' => 'marketing',
				'urls' => array(
					'build/index_mappress',
				),
			);
			return $tags;
		}

		else {
			// Leaflet with Mapbox tiles (or other non-OFM tile service) — Mapbox may track
			$dependency = array();
			if (Mappress::$options->clustering) {
				$dependency = [
					'leaflet.js' => 'leaflet.markercluster.js',
					'leaflet.markercluster.js' => 'index_mappress.js',
				];
			}

			// Without clustering, still ensure leaflet loads before index_mappress
			if (empty($dependency))
				$dependency = ['leaflet.js' => 'index_mappress.js'];

			$tags[] = array(
				'name' => 'mappress',
				'category' => 'marketing',
				'urls' => array(
					'leaflet.js',
					'leaflet.markercluster.js',
					'leaflet-omnivore.min.js',
					'togeojson.min.js',
					'build/index_mappress',
				),
				'enable_dependency' => true,
				'dependency' => $dependency,
			);
			return $tags;
		}        

		return $tags;
	}

	// Add services to the list of detected items so they appear in the Complianz wizard
	// and cookie policy. Only register tracking services — OpenFreeMap is omitted because
	// it sets no cookies and does not track.
	static function cmplz_services( $services ) {
		if (Mappress::$options->engine == 'google') {
		if ( ! in_array( 'google-maps', $services ) )
			$services[] = 'google-maps';
		}

		else if (Mappress::$options->engine == 'leaflet' && Mappress::get_tile_service() == 'ofm') {
			// OpenFreeMap — no tracking, no service to register
		}

		else {
			// Leaflet with Mapbox tiles (or other non-OFM tile service) — Mapbox tracks
			if ( ! in_array( 'mapbox', $services ) )
				$services[] = 'mapbox';
		}

		return $services;
	}

	// Whitelist the l10n script
	static function cmplz_whitelist($tags){
		$tags[] = 'var mappl10n';
		return $tags;
	}
}
