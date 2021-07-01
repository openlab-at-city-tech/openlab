<?php

class C_Photocrati_Resource_Manager
{
	static $instance = NULL;

	public $marker = '<!-- ngg_resource_manager_marker -->';

	var $buffer = '';
	var $styles = '';
	var $scripts = '';
	var $other_output = '';
	var $wrote_footer =  FALSE;
	var $run_shutdown =  FALSE;
	var $valid_request = TRUE;

	/**
	 * Start buffering all generated output. We'll then do two things with the buffer
	 * 1) Find stylesheets lately enqueued and move them to the header
	 * 2) Ensure that wp_print_footer_scripts() is called
	 */
	function __construct()
	{
		// Validate the request
		$this->validate_request();

		add_action('init', [$this, 'start_buffer'], -1);
		add_action('wp_footer', [$this, 'print_marker'], -1);
	}

	/**
	 * Created early as possible in the wp_footer action this is the string to which we
	 * will move JS resources after
	 */
	function print_marker()
	{
        if (self::is_disabled())
            return;

		// is_feed() is important to not break Wordpress feeds and the WooCommerce api
		if ($this->valid_request && !is_feed())
		    print $this->marker;
	}

	/**
	 * Determines if the resource manager should perform it's routines for this request
	 */
	function validate_request()
	{
		$this->valid_request = $this->is_valid_request();
	}

    /**
     * Pro, Plus, and Starter versions below these were not ready to function without the resource manager
     *
     * @return bool
     */
	public static function addons_version_check()
    {
        if (defined('NGG_PRO_PLUGIN_VERSION') && version_compare(NGG_PRO_PLUGIN_VERSION, '3.3', '<'))
            return FALSE;
        if (defined('NGG_STARTER_PLUGIN_VERSION') && version_compare(NGG_STARTER_PLUGIN_VERSION, '1.1', '<'))
            return FALSE;
        if (defined('NGG_PLUS_PLUGIN_VERSION') && version_compare(NGG_PLUS_PLUGIN_VERSION, '1.8', '<'))
            return FALSE;

        return TRUE;
    }

    /**
     * @return bool
     */
	public static function is_disabled()
    {
        // This is admittedly an ugly hack, but much easier than reworking the entire nextgen_admin modules
        if (!empty($_GET['page']) && $_GET['page'] === 'ngg_addgallery' && isset($_GET['attach_to_post']))
            return FALSE;

        // Provide users a method of forcing this on should it be necessary
        if (defined('NGG_ENABLE_RESOURCE_MANAGER') && NGG_ENABLE_RESOURCE_MANAGER)
            return FALSE;

        return self::addons_version_check();
    }

	function is_valid_request()
	{
		$retval = TRUE;

		if (is_admin()) {
			if (isset($_REQUEST['page']) && !preg_match("#^(ngg|nextgen)#", $_REQUEST['page'])) $retval = FALSE;
		}

		if (preg_match("#wp-admin/(network/)?update|wp-login|wp-signup#", $_SERVER['REQUEST_URI'])) $retval = FALSE;
		else if (isset($_GET['display_gallery_iframe'])) 				  $retval = FALSE;
		else if (defined('WP_ADMIN') && WP_ADMIN && defined('DOING_AJAX') && DOING_AJAX) $retval = FALSE;
		else if (strpos($_SERVER['REQUEST_URI'], '/nextgen-image/') !== FALSE) $retval = FALSE;
		else if (preg_match("/(js|css|xsl|xml|kml)$/", $_SERVER['REQUEST_URI'])) $retval = FALSE;
		else if (preg_match("#/feed(/?)$#i", $_SERVER['REQUEST_URI']) || !empty($_GET['feed'])) $retval = FALSE;
		elseif (preg_match("/\\.(\\w{3,4})$/", $_SERVER['REQUEST_URI'], $match)) {
			if (!in_array($match[1], array('htm', 'html', 'php'))) {
				$retval = FALSE;
			}
		}
		elseif ((isset($_SERVER['PATH_INFO']) && strpos($_SERVER['PATH_INFO'], 'nextgen-pro-lightbox-gallery') !== FALSE) OR strpos($_SERVER['REQUEST_URI'], 'nextgen-pro-lightbox-gallery') !== FALSE) {
			$retval = FALSE;
		}
		else if ($this->is_rest_request()) $retval = FALSE;

		return $retval;
	}

	function is_rest_request()
	{
		return defined('REST_REQUEST') || strpos($_SERVER['REQUEST_URI'], 'wp-json') !== FALSE;
	}

	/**
	 * Start the output buffers
	 */
	function start_buffer()
	{
        if (self::is_disabled())
            return;

		if (apply_filters('run_ngg_resource_manager', $this->valid_request))
		{
			ob_start([$this, 'output_buffer_handler']);
			ob_start([$this, 'get_buffer']);

			add_action('wp_print_footer_scripts', [$this, 'get_resources'], 1);
			add_action('admin_print_footer_scripts', [$this, 'get_resources'], 1);
			add_action('shutdown', [$this, 'shutdown']);
		}
	}

	function get_resources()
	{
		ob_start();
		wp_print_styles();
		print_admin_styles();
		$this->styles = ob_get_clean();

		if (!is_admin()) {
			ob_start();
			wp_print_scripts();
			$this->scripts = ob_get_clean();
		}

		$this->wrote_footer = TRUE;
	}

	/**
	 * Output the buffer after PHP execution has ended (but before shutdown)
	 * @param $content
	 * @return string
	 */
	function output_buffer_handler($content)
	{
		return $this->output_buffer();
	}

	/**
	 * Removes the closing </html> tag from the output buffer. We'll then write our own closing tag
	 * in the shutdown function after running wp_print_footer_scripts()
	 * @param $content
	 * @return mixed
	 */
	function get_buffer($content)
	{
		$this->buffer = $content;
		return '';
	}

	/**
	 * Moves resources to their appropriate place
	 */
	function move_resources()
	{
		if ($this->valid_request) {

			// Move stylesheets to head
			if ($this->styles) {
				$this->buffer = str_ireplace('</head>', $this->styles.'</head>', $this->buffer);
			}

			// Move the scripts to the bottom of the page
			if ($this->scripts) {
				$this->buffer = str_ireplace($this->marker, $this->marker . $this->scripts, $this->buffer);
			}

			if ($this->other_output) {
				$this->buffer = str_replace($this->marker, $this->marker . $this->other_output, $this->buffer);
			}
		}
	}

	/**
	 * When PHP has finished, we output the footer scripts and closing tags
     * @param bool $in_shutdown
     * @return string
	 */
	function output_buffer($in_shutdown=FALSE)
	{
		// If the footer scripts haven't been outputted, then
		// we need to take action - as they're required
		if (!$this->wrote_footer) {

			// If W3TC is installed and activated, we can't output the
			// scripts and manipulate the buffer, so we can only provide a warning
			if (defined('W3TC') && defined('WP_DEBUG') && WP_DEBUG && !is_admin()) {
				if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', TRUE);
				if (!did_action('wp_footer')) {
					error_log("We're sorry, but your theme's page template didn't make a call to wp_footer(), which is required by NextGEN Gallery. Please add this call to your page templates.");
				}
				else {
					error_log("We're sorry, but your theme's page template didn't make a call to wp_print_footer_scripts(), which is required by NextGEN Gallery. Please add this call to your page templates.");
				}
			}

			// We don't want to manipulate the buffer if it doesn't contain HTML
			elseif (strpos($this->buffer, '</body>') === FALSE) {
				$this->valid_request = FALSE;
			}

			// The output_buffer() function has been called in the PHP shutdown callback
			// This will allow us to print the scripts ourselves and manipulate the buffer
			if ($in_shutdown === TRUE) {
				if ($this->valid_request) {
					ob_start();
					if (!did_action('wp_footer')) {
						wp_footer();
					}
					else {
						wp_print_footer_scripts();
					}
					$this->other_output = ob_get_clean();
					$this->buffer = str_ireplace('</body>', $this->marker.'</body>', $this->buffer);
				}
			}

			// W3TC isn't activated and we're not in the shutdown callback.
			// We'll therefore add a shutdown callback to print the scripts
			else {
				$this->run_shutdown = TRUE;
				return '';
			}
		}

		// Once we have the footer scripts, we can modify the buffer and
		// move the resources around
		if ($this->wrote_footer) $this->move_resources();

		return $this->buffer;
	}

	/**
	 * PHP shutdown callback. Manipulate and output the buffer
	 */
	function shutdown()
	{
		if ($this->run_shutdown) echo $this->output_buffer(TRUE);
	}

	static function init()
	{
		$klass = get_class();
		return self::$instance = new $klass;
	}
}
