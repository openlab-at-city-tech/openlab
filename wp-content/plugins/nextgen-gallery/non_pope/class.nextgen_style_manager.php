<?php

class C_NextGen_Style_Manager
{
	static $_instance 		= NULL;
	var $directories 		= array();
	var $unsafe_directories = array();
	var $default_dir        = '';
	var $new_dir			= '';

	function __construct()
	{
        $this->default_dir = implode(DIRECTORY_SEPARATOR, array(
            NGG_MODULE_DIR,
            'ngglegacy',
            'css'
        ));

		$this->new_dir = implode(DIRECTORY_SEPARATOR, array(
			rtrim(WP_CONTENT_DIR, "/\\"),
			'ngg_styles'
		));

		// The last place we look for a stylesheet is in ngglegacy
		$this->add_directory($this->default_dir);

		// This is where all stylesheets should be stored
		$this->add_directory($this->new_dir);

		// We also check wp-content/ngg/styles
		$this->add_directory(implode(DIRECTORY_SEPARATOR, array(
			WP_CONTENT_DIR, 'ngg', 'styles'
		)));

		// We check the parent theme directory. Needed for child themes
		$this->add_directory(rtrim(get_template_directory(), "/\\"), TRUE);

		// We also check parent_theme/nggallery
		$this->add_directory(implode(DIRECTORY_SEPARATOR, array(
            rtrim(get_template_directory(), "/\\"),
			'nggallery'
		)), TRUE);

		// We also check parent_theme/ngg_styles
		$this->add_directory(implode(DIRECTORY_SEPARATOR, array(
            rtrim(get_template_directory(), "/\\"),
			'ngg_styles'
		)), TRUE);

		// We check the root directory of the theme. Users shouldn't store here,
		// but they might
		$this->add_directory(rtrim(get_stylesheet_directory(), "/\\"), TRUE);

		// We also check the theme/nggallery directory
		$this->add_directory(implode(DIRECTORY_SEPARATOR, array(
            rtrim(get_stylesheet_directory(), "/\\"),
			'nggallery'
		)), TRUE);

		// We also check the theme/ngg_styles directory
		$this->add_directory(implode(DIRECTORY_SEPARATOR, array(
            rtrim(get_stylesheet_directory(), "/\\"),
			'ngg_styles'
		)), TRUE);
	}

	/**
	 * Add a directory to search for stylesheets
	 * @param $dir
	 * @param bool $unsafe
	 */
	function add_directory($dir, $unsafe=FALSE)
	{
		array_unshift($this->directories, $dir);
		if ($unsafe) {
			$this->unsafe_directories[] = $dir;
		}
	}

	/**
	 * Determines if a directory is upgrade-safe or not
	 * @param $dir
	 * @return bool
	 */
	function is_directory_unsafe($dir=FALSE)
	{
		if (!$dir) $dir = dirname($this->find_selected_stylesheet_abspath());

		return in_array($dir, $this->unsafe_directories);
	}

	/**
	 * Determines if the directory is the default ngglegacy path
	 * @param $dir
	 * @return bool
	 */
	function is_default_dir($dir)
	{
		return rtrim($dir, "/\\") == $this->default_dir;
	}

	function get_new_dir($filename)
	{
		return implode(DIRECTORY_SEPARATOR, array(
			rtrim($this->new_dir, "/\\"),
			$filename
		));
	}

	/**
	 * Gets the location where the selected stylesheet will be saved to
	 * @param bool|string $selected
	 * @return string
	 */
	function get_selected_stylesheet_saved_abspath($selected=FALSE)
	{
		if (!$selected) $selected = $this->get_selected_stylesheet();

		$abspath = $this->find_selected_stylesheet_abspath($selected);
		if ($this->is_default_dir(dirname($abspath))) {
			$abspath = $this->get_new_dir(basename($abspath));
		}

		return $abspath;
	}

	function save($contents, $selected=FALSE)
	{
		$retval = FALSE;

		if (!$selected) $selected = $this->get_selected_stylesheet();
		$abspath = $this->get_selected_stylesheet_saved_abspath($selected);

		wp_mkdir_p(dirname($abspath));
		if (is_writable($abspath) OR (!@file_exists($abspath) && is_writable(dirname($abspath)))) {
			$retval = file_put_contents($abspath, $contents);
		}
		return $retval;
	}

	/**
	 * Gets the selected stylesheet from the user
	 * @return mixed
	 */
	function get_selected_stylesheet()
	{
        $settings = C_NextGen_Settings::get_instance();

        // use the same css resource for all subsites when wpmuStyle=true
        if (!is_multisite() || (is_multisite() && $settings->get('wpmuStyle')))
            return $settings->get('CSSfile', 'nggallery.css');
        else
            return C_Nextgen_Global_Settings::get_instance()->get('wpmuCSSfile');

	}

	/**
	 * Finds the location of the selected stylesheet
	 */
	function find_selected_stylesheet_abspath($selected=FALSE)
	{
		if (!$selected) $selected = $this->get_selected_stylesheet();

		$retval = implode(DIRECTORY_SEPARATOR, array(
			rtrim($this->default_dir, "/\\"),
			$selected
		));

		foreach ($this->directories as $dir) {
			$path = implode(DIRECTORY_SEPARATOR, array(
				rtrim($dir, "/\\"),
				$selected
			));

			if (@file_exists($path)) {
				$retval = $path;
				break;
			}
		}

        $retval = str_replace('/', DIRECTORY_SEPARATOR, $retval);

		return $retval;
	}

	/**
	 * Returns the url to the selected stylesheet
	 * @return mixed
	 */
	function get_selected_stylesheet_url($selected=FALSE)
	{
        if (!$selected)
            $selected = $this->get_selected_stylesheet();

        $abspath = $this->find_selected_stylesheet_abspath($selected);

        // default_dir is the only resource loaded from inside the plugin directory
        $type = 'content';
        $url  = content_url();
        if (0 === strpos($abspath, $this->default_dir))
        {
            $type = 'plugins';
            $url = plugins_url();
        }

        // Credit to Sam Soysa for this line -- Windows servers have so many special needs.
        $abspath = str_replace('\\', '/', $abspath);

        $retval =  str_replace(
			C_Fs::get_instance()->get_document_root($type),
            $url,
			$abspath
		);

		return rtrim(str_replace('\\', '/', $retval), "/");
	}


	function find_all_stylesheets($dir = FALSE)
	{
		$retval = array();
        if (!$dir)
            $dir = $this->directories;

		foreach (array_reverse($dir) as $dir) {
			$path = implode(DIRECTORY_SEPARATOR, array(
				rtrim($dir, "/\\"),
				'*.css'
			));
			$files = glob($path);
			if (is_array($files)) foreach ($files as $abspath) {
				if (($meta = $this->get_stylesheet_metadata($abspath))) {
					$filename = $meta['filename'];
					$retval[$filename] = $meta;
				}
			}
		}

		return $retval;
	}

	/**
	 * Gets the metadata for a particular stylesheet
	 * @param $abspath
	 * @return array
	 */
	function get_stylesheet_metadata($abspath)
	{
		$retval 	= array();
		$contents	= file_get_contents($abspath);
		$name 		= '';
		$desc 		= '';
		$version	= '';
		$author		= '';

		// Find the name of the stylesheet
		if (preg_match("/CSS Name:(.*)/i", $contents, $match)) {
			$name = trim($match[1]);
		}

		// Find the description of the stylesheet
		if (preg_match("/Description:(.*)/", $contents, $match)) {
			$desc = trim($match[1]);
		}

		// Find the author of the stylesheet
		if (preg_match("/Author:(.*)/", $contents, $match)) {
			$author = trim($match[1]);
		}

		// Find the version of the stylesheet
		if (preg_match("/Version:(.*)/", $contents, $match)) {
			$version = trim($match[1]);
		}

		if ($name) {
			$retval = array(
				'filename'		=>	basename($abspath),
				'abspath'		=>	$abspath,
				'name'			=>	$name,
				'description'	=>	$desc,
				'author'		=>	$author,
				'version'		=>	$version
			);
		}

		return $retval;
	}

	/**
	 * Gets an instance of the class
	 * @return C_NextGen_Style_Manager
	 */
	static function get_instance()
	{
		if (is_null(self::$_instance)){
			$klass = get_class();
			self::$_instance = new $klass();
		}
		return self::$_instance;
	}
}
