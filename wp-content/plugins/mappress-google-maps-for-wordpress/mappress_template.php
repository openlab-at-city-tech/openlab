<?php
class Mappress_Template extends Mappress_Obj {

	var $name,
		$label,
		$content,
		$exists,
		$path,
		$standard
		;

	static $tokens;
	static $user_tokens;
	static $queue = array();

	function __construct($atts = null) {
		parent::__construct($atts);
	}

	static function register() {
		add_action('wp_ajax_mapp_tpl_get', array(__CLASS__, 'ajax_get'));
		add_action('wp_ajax_mapp_tpl_save', array(__CLASS__, 'ajax_save'));
		add_action('wp_ajax_mapp_tpl_delete', array(__CLASS__, 'ajax_delete'));

		// Print queued templates
		// wp_footer used instead of wp_footer_scripts because NGG reverses calling order of the two hooks
		add_action('admin_print_scripts', array(__CLASS__, 'print_templates'), -1);
		add_action('admin_print_footer_scripts', array(__CLASS__, 'print_footer_templates'), -10);

		// For frontend iframes, suppress templates in main page
		if (!Mappress::$options->iframes) {
			add_action('wp_print_scripts', array(__CLASS__, 'print_templates'), -1);
			add_action('wp_footer', array(__CLASS__, 'print_footer_templates'), -10);
		}

		self::$tokens = array(
			'address' => __('Address', 'mappress-google-maps-for-wordpress'),
			'body' => __('Body', 'mappress-google-maps-for-wordpress'),
			'distance' => __('Distance', 'mappress-google-maps-for-wordpress'),
			'icon' => __('Icon', 'mappress-google-maps-for-wordpress'),
			'title' => __('Title', 'mappress-google-maps-for-wordpress'),
			'url' => __('URL', 'mappress-google-maps-for-wordpress'),
			'props.myfield' => __('Custom Field', 'mappress-google-maps-for-wordpress')
		);

		self::$user_tokens = array(
			'address' => __('Address', 'mappress-google-maps-for-wordpress'),
			'user_email' => __('Email', 'mappress-google-maps-for-wordpress'),
			'user_display_name' => __('Display Name', 'mappress-google-maps-for-wordpress'),
			'icon' => __('Icon', 'mappress-google-maps-for-wordpress'),
			'props.myfield' => __('Custom Field', 'mappress-google-maps-for-wordpress'),
		);
	}

	static function ajax_delete() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('manage_options'))
			Mappress::ajax_response('Not authorized');

		$args = json_decode(wp_unslash($_POST['data']));
		$name = $args->name;
		$filepath = get_stylesheet_directory() . '/' . $name . '.php';

		$result = @unlink($filepath);
		if ($result === false)
			Mappress::ajax_response('Unable to delete');

		Mappress::ajax_response('OK');
	}

	static function ajax_get() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('manage_options'))
			Mappress::ajax_response('Not authorized');

		$name = (isset($_GET['name'])) ? $_GET['name'] : null;
		$filename = basename($name) . '.php';
		$filepath = get_stylesheet_directory() . '/' . $filename;
		$html = (file_exists($filepath)) ? @file_get_contents($filepath) : null;

		// Verify legitimate path
		$standard_path = realpath(Mappress::$basedir . "/templates/$filename");
		if (strpos($standard_path, realpath(Mappress::$basedir)) !== 0)
			Mappress::ajax_response('Invalid template path');

		$standard = (file_exists($standard_path)) ? file_get_contents($standard_path) : null;

		if (!$standard)
			Mappress::ajax_response('Invalid template');

		$template = new Mappress_Template(array(
			'name' => $name,
			'content' => ($html) ? $html : $standard,
			'path' => $filepath,
			'standard' => $standard,
			'exists' => ($html) ? true : false,
		));

		$tokens = (substr($name, 0, 4) == 'user') ? self::$user_tokens : self::$tokens;
		Mappress::ajax_response('OK', array('template' => $template, 'tokens' => $tokens));
	}

	static function ajax_save() {
		check_ajax_referer('mappress', 'nonce');

		if (!current_user_can('manage_options'))
			Mappress::ajax_response('Not authorized');

		if ((defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) || defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)
			Mappress::ajax_response('Unable to save, DISALLOW_FILE_EDIT or DISALLOW_FILE_MODS has been set in wp-config');

		if (!current_user_can('unfiltered_html'))
			Mappress::ajax_response('Not authorized: DISALLOW_UNFILTERED_HTML is set in wp-config.php');

		$args = json_decode(wp_unslash($_POST['data']));
		$name = $args->name;
		$content = $args->content;
		$filepath = get_stylesheet_directory() . '/' . $name . '.php';

		$result = @file_put_contents($filepath, $content);
		if ($result === false)
			Mappress::ajax_response('Unable to save');

		// Return filepath after save
		Mappress::ajax_response('OK', $filepath);
	}

	static function locate_template($template_name) {
		$template_name .= ".php";
		$template_file = locate_template($template_name, false);
		if (!Mappress::$pro || empty($template_file))
			$template_file = Mappress::$basedir . "/templates/$template_name";

		// Template exists, return it
		if (file_exists($template_file))
			return $template_file;

		// Check forms directory
		$template_file = Mappress::$basedir . "/templates_admin/$template_name";
		if (file_exists($template_file))
			return $template_file;

		return null;
	}

	/**
	* Get template by requiring its file.
	* It would be much faster to read templates from the database and evaluate them,
	* but most WP "security" plugins flag this as a risk.
	*/
	static function get_template($template_name, $args = array()) {
		foreach($args as $arg => $value)
			$$arg = $value;
		$template_file = self::locate_template($template_name);

		if ($template_file) {
			ob_start();
			require($template_file);
			$html = ob_get_clean();
			$html = str_replace(array("\r\n", "\t"), array(), $html);  // Strip chars that won't display in html anyway
			return $html;
		} else {
			return false;
		}
	}

	static function get_poi_props($poi, $otype, $oid, $tokens) {
		$props = array();
		foreach($tokens as $token)
			$props[$token] = get_metadata($otype, $oid, $token, true);
		return apply_filters('mappress_poi_props', $props, $oid, $poi, 'user');
	}

	static function get_custom_tokens($otype) {
		$tokens = array();
		$templates = ($otype == 'user') ? array('user-mashup-popup', 'user-mashup-item') : array('map-popup', 'map-item', 'mashup-item', 'mashup-popup');

		// Scan all templates for props
		foreach($templates as $name) {
			$template = self::get_template($name);
			preg_match_all("/{{(poi.props[\s\S]+?)}}/", $template, $matches);
			if ($matches[1])
				$tokens = array_merge($tokens, $matches[1]);
		}
		// Some sites use props with hyphenated names, e.g. poi.props['a-b']
		$tokens = str_replace(array('poi.props.', "poi.props['", "']"), '', $tokens);
		return array_unique($tokens);
	}

	static function enqueue_template($template_name, $footer) {
		if (!array_key_exists($template_name, self::$queue))
			self::$queue[$template_name] = $footer;
	}

	static function print_footer_templates() {
		foreach(self::$queue as $template_name => $footer) {
			if ($footer)
				self::print_template($template_name);
		}
	}

	static function print_templates() {
		foreach(self::$queue as $template_name => $footer) {
			if (!$footer)
				self::print_template($template_name);
		}
	}

	static function print_template($template_name) {
		$template = self::get_template($template_name);
		if ($template)
			printf("<script type='text/html' class='mapp-tmpl' id='mapp-tmpl-$template_name'>%s</script>", $template);
	}


	static function print_js_templates() {
		$results = array();
		foreach(self::$queue as $template_name => $footer) {
			$template = self::get_template($template_name);
			// JS doesn't like dashes in property names
			$results[str_replace('-', '_', $template_name)] = $template;
		}
		return "var mappress_templates = " . json_encode($results) . ';';
	}
}
?>