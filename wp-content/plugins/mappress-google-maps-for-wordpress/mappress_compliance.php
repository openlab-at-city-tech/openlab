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
	}

	static function cmplz_script( $tags ) {
		if (Mappress::$options->iframes) {
			// Iframes
			$tags[] = array(
				'name' => 'mappress iframes',
				'urls' => array(
					'mappress=embed',
				),
				'category' => 'marketing',
				'iframe' => 1,
			);
		}

		else if (Mappress::$options->engine == 'google') {
			// Google
			$tags[] = array(
				'name' => 'mappress',
				'category' => 'marketing',
				'urls' => array(
					'build/index_mappress',
					'maps.googleapis.com',
				),
				'enable_placeholder' => 1,
				'placeholder' => 'google-maps',
				'placeholder_class' => 'mapp-wrapper',
				'enable_dependency' => true,
				'dependency' => ['maps.googleapis.com' => 'index_mappress.js']
			);
		} else {
			// Leaflet

			// Sequence the scripts because WP sequence is ignored.  Alternative is to bundle leaflet/markercluster together
			if (Mappress::$options->clustering)
				$dependency = ['leaflet.js' => 'leaflet.markercluster.js', 'leaflet.markercluster.js' => 'index_mappress.js'];
			else
				$dependency = ['leaflet.js' => 'index_mappress.js'];

			$tags[] = array(
				'name' => 'mappress',
				'category' => 'marketing',
				'urls' => array(
					'leaflet.js',
					'leaflet.markercluster.js',
					'leaflet-omnivore.min.js',
					'build/index_mappress',
				),
				'enable_placeholder' => 1,
				'placeholder' => 'google-maps',
				'placeholder_class' => 'mapp-wrapper',
				'enable_dependency' => true,
				'dependency' => $dependency
			);
		}
		return $tags;
	}

	// Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
	static function cmplz_services( $services ) {
		if ( ! in_array( 'google-maps', $services ) )
			$services[] = 'google-maps';
		return $services;
	}

	// Whitelist the l10n script
	static function cmplz_whitelist($tags){
		$tags[] = 'var mappl10n';
		return $tags;
	}
}
