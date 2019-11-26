<?php
/**
* Options
*/
class Mappress_Options extends Mappress_Obj {
	var $alignment,
		$autoicons,
		$autoupdate,
		$apiKey,
		$apiKeyServer,
		$autodisplay = 'top',
		$betas,
		$country,
		$css = true,
		$defaultIcon,
		$deregister,
		$directions = 'google',
		$directionsServer = 'https://maps.google.com',
		$engine = 'leaflet',
		$filter,
		$footer = true,
		$geocoder,
		$iconScale,
		$initialOpenInfo,
		$iwType = 'iw',
		$language,
		$layout = 'left',
		$license,
		$mapbox,
		$mapboxStyles = array(),
		$mashupBody = 'poi',
		$mashupClick = 'poi',
		$mashupKml,
		$metaKeys = array(),
		$metaSyncSave = true,
		$poiList = false,
		$poiZoom = 15,
		$postTypes = array('post', 'page'),
		$radius = 15,
		$search = true,
		$size = 1,
		$sizes = array(array('width' => 300, 'height' => 300), array('width' => 425, 'height' => 350), array('width' => 640, 'height' => 480)),
		$sort,
		$style,
		$styles = array(),
		$thumbs = true,
		$thumbSize,
		$thumbWidth = 64,
		$thumbHeight = 64,
		$tiles = 'google'
		;

	function __construct($options = '') {
		$this->update($options);
	}

	// Options are saved as array because WP settings API is fussy about objects
	static function get() {
		$options = get_option('mappress_options');
		return new Mappress_Options($options);
	}

	function save() {
		return update_option('mappress_options', get_object_vars($this));
	}
}      // End class Mappress_Options


/**
* Options menu display
*/
class Mappress_Settings {
	static $basename = 'mappress_options';
	var $options;

	function __construct() {
		$this->options = Mappress_Options::get();
		add_action('admin_init', array($this, 'admin_init'));
	}

	function admin_init() {
		register_setting('mappress', self::$basename, array($this, 'validate'));

		$this->add_section('demo', __('Sample Map', 'mappress-google-maps-for-wordpress'));

		$this->add_section('basic', __('Basic Settings', 'mappress-google-maps-for-wordpress'));
		$this->add_field('engine', __('Mapping Engine', 'mappress-google-maps-for-wordpress'), 'basic');

		if ($this->options->engine == 'leaflet') {
			$this->add_field('mapbox', __('Mapbox access token', 'mappress-google-maps-for-wordpress'), 'basic');
		} else {
			$this->add_field('apiKey', __('Google API key', 'mappress-google-maps-for-wordpress'), 'basic');
		}

		// License: single blogs, or main blog on multisite
		if (Mappress::$pro && (!is_multisite() || (is_super_admin() && is_main_site())) )
			$this->add_section('license', __('License', 'mappress-google-maps-for-wordpress'));

		$this->add_section('maps', __('Map Settings', 'mappress-google-maps-for-wordpress'));
		$this->add_field('postTypes', __('Post types', 'mappress-google-maps-for-wordpress'), 'maps');
		$this->add_field('autodisplay', __('Automatic display', 'mappress-google-maps-for-wordpress'), 'maps');
		$this->add_field('alignment', __('Map alignment', 'mappress-google-maps-for-wordpress'), 'maps');
		$this->add_field('directions', __('Directions', 'mappress-google-maps-for-wordpress'), 'maps');

		$this->add_section('pois', __('POI Settings', 'mappress-google-maps-for-wordpress'));
		$this->add_field('poiZoom', __('Default zoom', 'mappress-google-maps-for-wordpress'), 'pois');
		$this->add_field('initialOpenInfo', __('Open first POI', 'mappress-google-maps-for-wordpress'), 'pois');

		if (Mappress::$pro) {
			$this->add_section('mashups', __('Mashups', 'mappress-google-maps-for-wordpress'));
			$this->add_section('icons', __('Icons', 'mappress-google-maps-for-wordpress'));
				$this->add_section('styles', __('Styled Maps', 'mappress-google-maps-for-wordpress'));
			$this->add_section('geocoding', __('Geocoding', 'mappress-google-maps-for-wordpress'));
			$this->add_section('templates', __('Templates', 'mappress-google-maps-for-wordpress'));
		}

		$this->add_section('l10n', __('Localization', 'mappress-google-maps-for-wordpress'));
		$this->add_field('language', __('Language', 'mappress-google-maps-for-wordpress'), 'l10n');
		$this->add_field('country', __('Country', 'mappress-google-maps-for-wordpress'), 'l10n');
		$this->add_field('directionsServer', __('Directions server', 'mappress-google-maps-for-wordpress'), 'l10n');

		$this->add_section('misc', __('Miscellaneous', 'mappress-google-maps-for-wordpress'));
		if ($this->options->engine != 'leaflet')
			$this->add_field('deregister', __('Compatiblity', 'mappress-google-maps-for-wordpress'), 'misc');
		$this->add_field('footer', __('Scripts', 'mappress-google-maps-for-wordpress'), 'misc');
		$this->add_field('sizes', __('Map sizes', 'mappress-google-maps-for-wordpress'), 'misc');
	}

	function add_section($section, $title) {
		$callback = ($section == 'demo') ? array($this, 'set_preview') : null;
		add_settings_section($section, $title, $callback, 'mappress');
	}

	function add_field($field, $label, $section) {
		$callback = 'set_' . strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $field));
		add_settings_field($field, $label, array($this, $callback), 'mappress', $section, self::$basename . "[$field]");
	}

	function validate($input) {
		// If reset defaults was clicked
		if (isset($_POST['reset_defaults'])) {
			$options = new Mappress_Options();
			return get_object_vars($this);
		}

		// Trim fields if present
		foreach(array('apiKey', 'apiKeyServer') as $key) {
			if (isset($input[$key]))
				$input[$key] = trim($input[$key]);
		}

		// Sizes
		foreach( $input['sizes'] as &$size ) {
			// Strip 'px' from value but allow '%'.  also, % min/max = 5%/100%, px min/max = 200/2048
			if (strpos($size['width'], '%'))
				$size['width'] = max(5, min(100, (int) $size['width'])) . '%';
			else
				$size['width'] = max(200, min(2048, (int) $size['width']));

			if (strpos($size['height'], '%'))
				$size['height'] = max(5, min(100, (int) $size['height'])) . '%';
			else
				$size['height'] = max(200, min(2048, (int) $size['height']));
		}

		// If NO post types selected, set value to empty array
		if (!isset($input['postTypes']))
			$input['postTypes'] = array();

		// Force checkboxes to boolean
		foreach($input as &$item)
			$item = Mappress::string_to_boolean($item);

		// For arrays passed as checkboxes set empty array for no selection
		$input['postTypes'] = (isset($input['postTypes'])) ? $input['postTypes'] : array();

		// Merge in old values so they're not lost
		$input = array_merge((array)$this->options, $input);
		return $input;
	}

	function set_alignment($name) {
		$alignments = array(
			'' => __('Default', 'mappress-google-maps-for-wordpress'),
			'center' => __('Center', 'mappress-google-maps-for-wordpress'),
			'left' => __('Left', 'mappress-google-maps-for-wordpress'),
			'right' => __('Right', 'mappress-google-maps-for-wordpress')
		);
		echo Mappress_Controls::radios($name, $alignments, $this->options->alignment);
	}

	function set_api_key($name) {
		// Google API key; show as hidden field if using leaflet
		$type = ($this->options->engine == 'leaflet') ? 'hidden' : 'text';
		echo Mappress_Controls::input($name, $this->options->apiKey, array('type' => $type, 'size' => '50'));
		echo Mappress_Controls::help('', '#toc-google-maps-api-keys');
	}

	function set_autodisplay($name) {
		$autos = array(
			'top' => __('Top of post', 'mappress-google-maps-for-wordpress'),
			'bottom' => __('Bottom of post', 'mappress-google-maps-for-wordpress'),
			'none' => __('None', 'mappress-google-maps-for-wordpress')
		);
		echo Mappress_Controls::radios($name, $autos, $this->options->autodisplay);
	}

	function set_country($name) {
		$url = 'https://en.wikipedia.org/wiki/ISO_3166-1#Officially_assigned_code_elements';
		$link = sprintf('<a style="vertical-align:text-bottom" target="_blank" href="%s">%s</a>', $url, __("(list)", 'mappress-google-maps-for-wordpress'));
		$codes = array('', 'AF' ,'AX' ,'AL' ,'DZ' ,'AS' ,'AD' ,'AO' ,'AI' ,'AQ' ,'AG' ,'AR' ,'AM' ,'AW' ,'AU' ,'AT' ,'AZ' ,'BS' ,'BH' ,'BD' ,'BB' ,'BY' ,'BE' ,'BZ' ,'BJ' ,'BM' ,'BT' ,'BO' ,'BQ' ,'BA' ,'BW' ,'BV' ,'BR' ,'IO' ,'VG' ,'BN' ,'BG' ,'BF' ,'BI' ,'KH' ,'CM' ,'CA' ,'CV' ,'KY' ,'CF' ,'TD' ,'CL' ,'CN' ,'CX' ,'CC' ,'CO' ,'KM' ,'CK' ,'CR' ,'HR' ,'CU' ,'CW' ,'CY' ,'CZ' ,'CD' ,'DK' ,'DJ' ,'DM' ,'DO' ,'EC' ,'EG' ,'SV' ,'GQ' ,'ER' ,'EE' ,'ET' ,'FK' ,'FO' ,'FJ' ,'FI' ,'FR' ,'GF' ,'PF' ,'TF' ,'GA' ,'GM' ,'GE' ,'DE' ,'GH' ,'GI' ,'GR' ,'GL' ,'GD' ,'GP' ,'GU' ,'GT' ,'GG' ,'GN' ,'GW' ,'GY' ,'HT' ,'HM' ,'HN' ,'HK' ,'HU' ,'IS' ,'IN' ,'ID' ,'IR' ,'IQ' ,'IE' ,'IM' ,'IL' ,'IT' ,'CI' ,'JM' ,'JP' ,'JE' ,'JO' ,'KZ' ,'KE' ,'KI' ,'XK' ,'KW' ,'KG' ,'LA' ,'LV' ,'LB' ,'LS' ,'LR' ,'LY' ,'LI' ,'LT' ,'LU' ,'MO' ,'MK' ,'MG' ,'MW' ,'MY' ,'MV' ,'ML' ,'MT' ,'MH' ,'MQ' ,'MR' ,'MU' ,'YT' ,'MX' ,'FM' ,'MD' ,'MC' ,'MN' ,'ME' ,'MS' ,'MA' ,'MZ' ,'MM' ,'NA' ,'NR' ,'NP' ,'NL' ,'AN' ,'NC' ,'NZ' ,'NI' ,'NE' ,'NG' ,'NU' ,'NF' ,'KP' ,'MP' ,'NO' ,'OM' ,'PK' ,'PW' ,'PS' ,'PA' ,'PG' ,'PY' ,'PE' ,'PH' ,'PN' ,'PL' ,'PT' ,'PR' ,'QA' ,'CG' ,'RE' ,'RO' ,'RU' ,'RW' ,'BL' ,'SH' ,'KN' ,'LC' ,'MF' ,'PM' ,'VC' ,'WS' ,'SM' ,'ST' ,'SA' ,'SN' ,'RS' ,'SC' ,'SL' ,'SG' ,'SX' ,'SK' ,'SI' ,'SB' ,'SO' ,'ZA' ,'GS' ,'KR' ,'SS' ,'ES' ,'LK' ,'SD' ,'SR' ,'SJ' ,'SZ' ,'SE' ,'CH' ,'SY' ,'TW' ,'TJ' ,'TZ' ,'TH' ,'TL' ,'TG' ,'TK' ,'TO' ,'TT' ,'TN' ,'TR' ,'TM' ,'TC' ,'TV' ,'VI' ,'UG' ,'UA' ,'AE' ,'GB' ,'US' ,'UM' ,'UY' ,'UZ' ,'VU' ,'VA' ,'VE' ,'VN' ,'WF' ,'EH' ,'YE' ,'ZM' ,'ZW');
		sort($codes);
		$codes = array_combine($codes, $codes);
		echo Mappress_Controls::select($name, $codes, $this->options->country);
		echo ' ' . __("Country code for searches", 'mappress-google-maps-for-wordpress') . ' ' . $link;
	}

	function set_deregister($name) {
		echo Mappress_Controls::checkmark($name, $this->options->deregister, __('Prevent other plugins/themes from loading the Google Maps API', 'mappress-google-maps-for-wordpress'));
	}

	function set_directions($name) {
		$directions_types = array('google' => __('Google', 'mappress-google-maps-for-wordpress'), 'inline' => __('Inline', 'mappress-google-maps-for-wordpress'));
		echo Mappress_Controls::radios($name, $directions_types, $this->options->directions);
	}

	function set_directions_server($name) {
		echo Mappress_Controls::input($name, $this->options->directionsServer, array('size' => 25));
	}

	function set_engine($name) {
		$engines = array('leaflet' => __('Leaflet', 'mappress-google-maps-for-wordpress'), 'google' => __('Google', 'mappress-google-maps-for-wordpress'));
		echo Mappress_Controls::radios($name, $engines, $this->options->engine);
		echo Mappress_Controls::help(__('Leaflet is free and requires no API key.  Google requires an API key and has strict usage limits.', 'mappress-google-maps-for-wordpress'), '#toc-picking-a-mapping-engine');
	}

	function set_footer($name) {
		// Disable if jetpack infinite scroll is used
		if (class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' )) {
			echo Mappress_Controls::checkmark($name, false, __('Output scripts in footer', 'mappress-google-maps-for-wordpress'), array('disabled' => true));
			printf("<br/><i>%s</i>", __('Disabled because Jetpack Infinite Scroll is active', 'mappress-google-maps-for-wordpress'));
		} else {
			echo Mappress_Controls::checkmark($name, $this->options->footer, __('Output scripts in footer', 'mappress-google-maps-for-wordpress'));
			printf("<br/><i>(%s)</i>", __('disable if maps are output using AJAX', 'mappress-google-maps-for-wordpress'));
		}
	}

	function set_initial_open_info($name) {
		echo Mappress_Controls::checkmark($name, $this->options->initialOpenInfo, __('Open first POI', 'mappress-google-maps-for-wordpress'));
	}

	function set_language($name) {
		$url = ($this->options->engine == 'leaflet') ? 'https://en.wikipedia.org/wiki/ISO_639-1' : 'http://code.google.com/apis/maps/faq.html#languagesupport';
		$lang_link = sprintf('<a style="vertical-align:text-bottom" target="_blank" href="%s">%s</a>', $url, __("(list)", 'mappress-google-maps-for-wordpress'));
		$langs = array('' => '', 'ab' => 'Abkhazian', 'aa' => 'Afar', 'af' => 'Afrikaans', 'ak' => 'Akan', 'sq' => 'Albanian', 'am' => 'Amharic', 'ar' => 'Arabic', 'an' => 'Aragonese', 'hy' => 'Armenian', 'as' => 'Assamese', 'av' => 'Avaric', 'ae' => 'Avestan', 'ay' => 'Aymara', 'az' => 'Azerbaijani', 'bm' => 'Bambara', 'ba' => 'Bashkir', 'eu' => 'Basque', 'be' => 'Belarusian', 'bn' => 'Bengali', 'bh' => 'Bihari languages', 'bi' => 'Bislama', 'bs' => 'Bosnian', 'br' => 'Breton', 'bg' => 'Bulgarian', 'my' => 'Burmese', 'ca' => 'Catalan, Valencian', 'km' => 'Central Khmer', 'ch' => 'Chamorro', 'ce' => 'Chechen', 'ny' => 'Chichewa, Chewa, Nyanja', 'zh' => 'Chinese', 'cv' => 'Chuvash', 'kw' => 'Cornish', 'co' => 'Corsican', 'cr' => 'Cree', 'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'dv' => 'Divehi, Dhivehi, Maldivian', 'nl' => 'Dutch, Flemish', 'dz' => 'Dzongkha', 'en' => 'English', 'eo' => 'Esperanto', 'et' => 'Estonian', 'ee' => 'Ewe', 'fo' => 'Faroese', 'fj' => 'Fijian', 'fi' => 'Finnish', 'fr' => 'French', 'ff' => 'Fulah', 'gd' => 'Gaelic, Scottish Gaelic', 'gl' => 'Galician', 'lg' => 'Ganda', 'ka' => 'Georgian', 'de' => 'German', 'ki' => 'Gikuyu, Kikuyu', 'el' => 'Greek (Modern)', 'kl' => 'Greenlandic, Kalaallisut', 'gn' => 'Guarani', 'gu' => 'Gujarati', 'ht' => 'Haitian, Haitian Creole', 'ha' => 'Hausa', 'he' => 'Hebrew', 'hz' => 'Herero', 'hi' => 'Hindi', 'ho' => 'Hiri Motu', 'hu' => 'Hungarian', 'is' => 'Icelandic', 'io' => 'Ido', 'ig' => 'Igbo', 'id' => 'Indonesian', 'iu' => 'Inuktitut', 'ik' => 'Inupiaq', 'ga' => 'Irish', 'it' => 'Italian', 'ja' => 'Japanese', 'jv' => 'Javanese', 'kn' => 'Kannada', 'kr' => 'Kanuri', 'ks' => 'Kashmiri', 'kk' => 'Kazakh', 'rw' => 'Kinyarwanda', 'kv' => 'Komi', 'kg' => 'Kongo', 'ko' => 'Korean', 'kj' => 'Kwanyama, Kuanyama', 'ku' => 'Kurdish', 'ky' => 'Kyrgyz', 'lo' => 'Lao', 'la' => 'Latin', 'lv' => 'Latvian', 'lb' => 'Letzeburgesch, Luxembourgish', 'li' => 'Limburgish, Limburgan, Limburger', 'ln' => 'Lingala', 'lt' => 'Lithuanian', 'lu' => 'Luba-Katanga', 'mk' => 'Macedonian', 'mg' => 'Malagasy', 'ms' => 'Malay', 'ml' => 'Malayalam', 'mt' => 'Maltese', 'gv' => 'Manx', 'mi' => 'Maori', 'mr' => 'Marathi', 'mh' => 'Marshallese', 'ro' => 'Moldovan, Moldavian, Romanian', 'mn' => 'Mongolian', 'na' => 'Nauru', 'nv' => 'Navajo, Navaho', 'nd' => 'Northern Ndebele', 'ng' => 'Ndonga', 'ne' => 'Nepali', 'se' => 'Northern Sami', 'no' => 'Norwegian', 'nb' => 'Norwegian Bokmål', 'nn' => 'Norwegian Nynorsk', 'ii' => 'Nuosu, Sichuan Yi', 'oc' => 'Occitan (post 1500)', 'oj' => 'Ojibwa', 'or' => 'Oriya', 'om' => 'Oromo', 'os' => 'Ossetian, Ossetic', 'pi' => 'Pali', 'pa' => 'Panjabi, Punjabi', 'ps' => 'Pashto, Pushto', 'fa' => 'Persian', 'pl' => 'Polish', 'pt' => 'Portuguese', 'qu' => 'Quechua', 'rm' => 'Romansh', 'rn' => 'Rundi', 'ru' => 'Russian', 'sm' => 'Samoan', 'sg' => 'Sango', 'sa' => 'Sanskrit', 'sc' => 'Sardinian', 'sr' => 'Serbian', 'sn' => 'Shona', 'sd' => 'Sindhi', 'si' => 'Sinhala, Sinhalese', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'so' => 'Somali', 'st' => 'Sotho, Southern', 'nr' => 'South Ndebele', 'es' => 'Spanish, Castilian', 'su' => 'Sundanese', 'sw' => 'Swahili', 'ss' => 'Swati', 'sv' => 'Swedish', 'tl' => 'Tagalog', 'ty' => 'Tahitian', 'tg' => 'Tajik', 'ta' => 'Tamil', 'tt' => 'Tatar', 'te' => 'Telugu', 'th' => 'Thai', 'bo' => 'Tibetan', 'ti' => 'Tigrinya', 'to' => 'Tonga (Tonga Islands)', 'ts' => 'Tsonga', 'tn' => 'Tswana', 'tr' => 'Turkish', 'tk' => 'Turkmen', 'tw' => 'Twi', 'ug' => 'Uighur, Uyghur', 'uk' => 'Ukrainian', 'ur' => 'Urdu', 'uz' => 'Uzbek', 've' => 'Venda', 'vi' => 'Vietnamese', 'vo' => 'Volap_k', 'wa' => 'Walloon', 'cy' => 'Welsh', 'fy' => 'Western Frisian', 'wo' => 'Wolof', 'xh' => 'Xhosa', 'yi' => 'Yiddish', 'yo' => 'Yoruba', 'za' => 'Zhuang, Chuang', 'zu' => 'Zulu');
		echo Mappress_Controls::select($name, $langs, $this->options->language);
		echo ' ' . __('Language for map controls', 'mappress-google-maps-for-wordpress') . ' ' . $lang_link;
	}

	function set_mapbox($name) {
		echo Mappress_Controls::input($name, $this->options->mapbox, array('size' => '50', 'placeholder' => __('Enter token to use Mapbox map tiles', 'mappress-google-maps-for-wordpress')));
		echo Mappress_Controls::help('', 'https://www.mapbox.com/help/define-access-token/');
	}

	function set_poi_zoom($name) {
		$zooms = array_combine(range(1, 17), range(1,17));
		echo Mappress_Controls::select($name, $zooms, (int) $this->options->poiZoom);
		echo __("Default zoom when displaying a single POI", 'mappress-google-maps-for-wordpress');
	}

	function set_post_types($name) {
		$post_types = Mappress_Controls::get_post_types();
		echo Mappress_Controls::checkboxes($name, $post_types, $this->options->postTypes);
		return;
	}

	function set_preview() {
		$poi = new Mappress_Poi(array(
			'address' => 'San Francisco, CA',
			"title" => "MapPress",
			"body" => __("Easy Google Maps", 'mappress-google-maps-for-wordpress'),
			"point" => array('lat' => 37.774095, 'lng' => -122.418731)
		));
		$pois = array($poi);
		$map = new Mappress_Map(array('alignment' => 'default', 'width' => '100%', 'height' => 200, 'pois' => $pois, 'zoom' => 8));
		echo "<table class='form-table'><tr><td>" . $map->display() . "</td></tr></table>";
	}
	function set_sizes($name) {
		$headers = array(__('Width (px or %)', 'mappress-google-maps-for-wordpress'), __('Height (px)', 'mappress-google-maps-for-wordpress'), __('Default size', 'mappress-google-maps-for-wordpress'));
		$rows = array();

		foreach($this->options->sizes as $i => $size) {
			$checked = ($i == $this->options->size) ? "checked='checked'" : "";
			$rows[] = array(
				Mappress_Controls::input("{$name}[$i][width]", $size['width'], array('size' => 4)),
				Mappress_Controls::input("{$name}[$i][height]", $size['height'], array('size' => 4)),
				Mappress_Controls::input(self::$basename . "[size]", $i, array('type' => 'radio', 'checked' => $checked)),
			);
		}
		echo Mappress_Controls::table($headers, $rows);
	}

	function options_page() {
		?>
		<div class="wrap mapp-settings-screen">
			<h1><?php _e('MapPress', 'mappress-google-maps-for-wordpress'); ?></h1>
			<?php echo Mappress::get_support_links(); ?>
				<form action="options.php" method="post">
					<?php // Force default submit button ?>
					<div class='mapp-settings-hidden-toolbar'>
						<input name='save' type='submit' class='button button-primary' value='<?php _e("Save Changes", 'mappress-google-maps-for-wordpress'); ?>' />
						<input name='reset_defaults' type='submit' class='button' value='<?php _e("Reset Defaults", 'mappress-google-maps-for-wordpress'); ?>' />
					</div>

					<?php settings_fields('mappress'); ?>
					<?php do_settings_sections('mappress'); ?>

					<div class='mapp-settings-toolbar'>
						<input name='save' type='submit' class='button button-primary' value='<?php _e("Save Changes", 'mappress-google-maps-for-wordpress'); ?>' />
						<input name='reset_defaults' type='submit' class='button' value='<?php _e("Reset Defaults", 'mappress-google-maps-for-wordpress'); ?>' />
					</div>
				</form>
		</div>
		<?php
	}
}
?>
