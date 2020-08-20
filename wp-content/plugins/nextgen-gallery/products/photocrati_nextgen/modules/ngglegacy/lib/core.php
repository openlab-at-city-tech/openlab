<?php
/**
* Main PHP class for the WordPress plugin NextGEN Gallery
*
* @author Alex Rabe
*
*
*/
class nggGallery {

	/**
	* Show a error messages
	*/
	static function show_error($message) {
		echo '<div class="wrap"><h2></h2><div class="error" id="error"><p>' . $message . '</p></div></div>' . "\n";
	}

	/**
	* Show a system messages
	*/
	static function show_message($message, $message_id=NULL) {
		echo '<div class="wrap"><h2></h2><div class="updated fade '.$message_id.'" id="message"><p>' . $message . '</p></div></div>' . "\n";
	}

	/**
	* nggGallery::get_option() - get the options and overwrite them with custom meta settings
	*
	* @param string $key
	* @return array $options
	*/
	static function get_option($key) {
        global $post;

		// get first the options from the database
		$options = get_option($key);

        if ( $post == null )
            return $options;

		// Get all key/value data for the current post.
		$meta_array = get_post_custom();

		// Ensure that this is a array
		if ( !is_array($meta_array) )
			$meta_array = array($meta_array);

		// assign meta key to db setting key
		$meta_tags = array(
			'string' => array(
				'ngg_gal_ShowOrder' 		=> 'galShowOrder',
				'ngg_gal_Sort' 				=> 'galSort',
				'ngg_gal_SortDirection' 	=> 'galSortDir',
				'ngg_gal_ShowDescription'	=> 'galShowDesc',
				'ngg_ir_Audio' 				=> 'irAudio',
				'ngg_ir_Overstretch'		=> 'irOverstretch',
				'ngg_ir_Transition'			=> 'irTransition',
				'ngg_ir_Backcolor' 			=> 'irBackcolor',
				'ngg_ir_Frontcolor' 		=> 'irFrontcolor',
				'ngg_ir_Lightcolor' 		=> 'irLightcolor',
                'ngg_slideshowFX'			=> 'slideFx',
			),

			'int' => array(
				'ngg_gal_Images' 			=> 'galImages',
				'ngg_gal_Columns'			=> 'galColumns',
				'ngg_paged_Galleries'		=> 'galPagedGalleries',
				'ngg_ir_Width' 				=> 'irWidth',
				'ngg_ir_Height' 			=> 'irHeight',
				'ngg_ir_Rotatetime' 		=> 'irRotatetime'
			),

			'bool' => array(
				'ngg_gal_ShowSlide'			=> 'galShowSlide',
				'ngg_gal_ImageBrowser' 		=> 'galImgBrowser',
				'ngg_gal_HideImages' 		=> 'galHiddenImg',
				'ngg_ir_Shuffle' 			=> 'irShuffle',
				'ngg_ir_LinkFromDisplay' 	=> 'irLinkfromdisplay',
				'ngg_ir_ShowNavigation'		=> 'irShownavigation',
				'ngg_ir_ShowWatermark' 		=> 'irWatermark',
				'ngg_ir_Kenburns' 			=> 'irKenburns'
			)
		);

		foreach ($meta_tags as $typ => $meta_keys){
			foreach ($meta_keys as $key => $db_value){
				// if the kex exist overwrite it with the custom field
				if (array_key_exists($key, $meta_array)){
					switch ($typ) {
					case 'string':
						$options[$db_value] = (string) esc_attr($meta_array[$key][0]);
						break;
					case 'int':
						$options[$db_value] = (int) $meta_array[$key][0];
						break;
					case 'bool':
						$options[$db_value] = (bool) $meta_array[$key][0];
						break;
					}
				}
			}
		}

		return $options;
	}

	/**
	* Renders a section of user display code.  The code is first checked for in the current theme display directory
	* before defaulting to the plugin
	* Call the function :	nggGallery::render ('template_name', array ('var1' => $var1, 'var2' => $var2));
	*
	* @autor John Godley
	* @param string $template_name Name of the template file (without extension)
	* @param string $vars Array of variable name=>value that is available to the display code (optional)
	* @param bool $callback In case we check we didn't find template we tested it one time more (optional)
	* @return void
	**/
	static function render($template_name, $vars = array (), $callback = false)
	{
		$vars['template'] = $template_name;
		echo C_Displayed_Gallery_Renderer::get_instance()->display_images($vars);


	}

	/**
	* Captures an section of user display code.
	*
	* @autor John Godley
	* @param string $template_name Name of the template file (without extension)
	* @param string $vars Array of variable name=>value that is available to the display code (optional)
	* @deprecated Use C_Displayed_Gallery_Renderer class
	* @return string
	**/
	static function capture ($template_name, $vars = array ())
	{
		$vars['template'] = $template_name;
		return C_Displayed_Gallery_Renderer::get_instance()->display_images($vars);
	}

	/**
	 * Returns the path to lib/gd.thumbnail.inc.php
	 *
	 * @return string Path to the selected library
	 */
	static function graphic_library()
    {
        return NGGALLERY_ABSPATH . '/lib/gd.thumbnail.inc.php';
	}

	/**
	 * Support for i18n with wpml, polyglot or qtrans
	 *
	 * @param string $in
	 * @param string $name (optional) required for wpml to determine the type of translation
	 * @return string $in localized
	 */
	static function i18n($in, $name = null) {

		if ( function_exists( 'langswitch_filter_langs_with_message' ) )
			$in = langswitch_filter_langs_with_message($in);

		if ( function_exists( 'polyglot_filter' ))
			$in = polyglot_filter($in);

		if ( function_exists( 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ))
			$in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($in);

        if (is_string($name) && !empty($name) && function_exists('icl_translate'))
            $in = icl_translate('plugin_ngg', $name, $in, true);

		$in = apply_filters('localization', $in);

		return $in;
	}

	/**
	 * Check the memory_limit and calculate a recommended memory size
	 *
	 * @since V1.2.0
	 * @return string message about recommended image size
	 */
	static function check_memory_limit() {

		if ( (function_exists('memory_get_usage')) && (ini_get('memory_limit')) ) {

			// get memory limit
			$memory_limit = ini_get('memory_limit');
			if ($memory_limit != '')
				$memory_limit = substr($memory_limit, 0, -1) * 1024 * 1024;

			// calculate the free memory
			$freeMemory = $memory_limit - memory_get_usage();

			// build the test sizes
			$sizes = array();
			$sizes[] = array ( 'width' => 800,  'height' => 600);
			$sizes[] = array ( 'width' => 1024, 'height' => 768);
			$sizes[] = array ( 'width' => 1280, 'height' => 960);  // 1MP
			$sizes[] = array ( 'width' => 1600, 'height' => 1200); // 2MP
			$sizes[] = array ( 'width' => 2016, 'height' => 1512); // 3MP
			$sizes[] = array ( 'width' => 2272, 'height' => 1704); // 4MP
			$sizes[] = array ( 'width' => 2560, 'height' => 1920); // 5MP

			// test the classic sizes
			foreach ($sizes as $size){
				// very, very rough estimation
				if ($freeMemory < round( $size['width'] * $size['height'] * 5.09 )) {
                	$result = sprintf(  __( 'Note : Based on your server memory limit you should not upload larger images then <strong>%d x %d</strong> pixel', 'nggallery' ), $size['width'], $size['height']);
					return $result;
				}
			}
		}
		return '';
	}

	/**
	 * Check for extended capabilites. Must previously registers with add_ngg_capabilites()
	 *
	 * @since 1.5.0
	 * @param string $capability
	 * @return bool $result of capability check
	 */
	static function current_user_can( $capability ) {

		global $_ngg_capabilites;

		if ( is_array($_ngg_capabilites) )
			if ( in_array($capability , $_ngg_capabilites) )
				return current_user_can( $capability );

		return true;
	}

    /**
     * Show NextGEN Version in header
     * @since 1.9.0
     *
     * @return void
     */
    static function nextgen_version() {
        global $ngg;
        echo apply_filters('show_nextgen_version', '<!-- <meta name="NextGEN" version="'. $ngg->version . '" /> -->' . "\n");
    }
}
