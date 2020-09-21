<?php
/**
 * nggAdminPanel - Admin Section for NextGEN Gallery
 *
 * @package NextGEN Gallery
 * @author Alex Rabe
 *
 * @since 1.0.0
 */
class nggAdminPanel{

	// constructor
	function __construct() {

		// Buffer the output
		add_action('admin_init', array($this, 'start_buffer'));

		// Add the admin menu
		add_action( 'admin_menu', array ($this, 'add_menu') );
        add_action( 'admin_bar_menu', array($this, 'admin_bar_menu'), 99 );
		add_action( 'network_admin_menu', array ($this, 'add_network_admin_menu') );

		// Add the script and style files
		add_action('admin_print_scripts', array($this, 'load_scripts') );
		add_action('admin_print_styles', array($this, 'load_styles') );

		// Try to detect plugins that embed their own jQuery and jQuery UI
		// libraries and load them in NGG's admin pages
		add_action('admin_enqueue_scripts', array($this, 'buffer_scripts'), 0);
		add_action('admin_print_scripts', array($this, 'output_scripts'), PHP_INT_MAX);

        add_filter('current_screen', array($this, 'edit_current_screen'));

        add_action('ngg_admin_enqueue_scripts', array($this, 'enqueue_progress_bars'));
	}

	function enqueue_progress_bars()
	{
		// Enqueue the new Gritter-based progress bars
		wp_enqueue_style('ngg_progressbar');
		wp_enqueue_script('ngg_progressbar');

	}

	function start_buffer()
	{
		// Notify of page event
		if (isset($_REQUEST['page']) && $_POST) {
			$event = array(
				'event'	=>	str_replace('-', '_', str_replace('nggallery', '', $_REQUEST['page']))
			);

			// Do we have a list of galleries that are being affected?
			if (isset($_REQUEST['doaction'])) {
				$event['gallery_ids']	= $_REQUEST['doaction'];
			}

			// Do we have a particular gallery id?
			elseif (isset($_REQUEST['gid'])) {
				$event['gallery_id']	= $_REQUEST['gid'];
			}

			// Do we have an album id?
			elseif (isset($_REQUEST['act_album']) && $_REQUEST['act_album']) {
				$event['album_id']		= $_REQUEST['act_album'];
			}
			if (strpos($event['event'], '_') === 0) $event['event'] = substr($event['event'], 1);

			do_action('ngg_page_event', $event);
		}
		ob_start();
	}

	/**
	 * If a NGG page is being requested, we buffer any rendering of <script>
	 * tags to detect conflicts and remove them if need be
	 */
	function buffer_scripts()
	{
		// Is this a NGG admin page?
		if (isset($_REQUEST['page']) && strpos($_REQUEST['page'] ,'nggallery') !== FALSE) {
			ob_start();
		}
	}

	function output_scripts()
	{
		// Is this a NGG admin page?
		if (isset($_REQUEST['page']) && strpos($_REQUEST['page'] ,'nggallery') !== FALSE) {
			$plugin_folder		= NGGFOLDER;
			$skipjs_count		= 0;
			$html = ob_get_contents();
			ob_end_clean();

            if (!defined('NGG_JQUERY_CONFLICT_DETECTION')) {
				define('NGG_JQUERY_CONFLICT_DETECTION', TRUE);
			}

			if (NGG_JQUERY_CONFLICT_DETECTION) {
				// Detect custom jQuery script
				if (preg_match_all("/<script.*wp-content.*jquery[-_\.](min\.)?js.*<\script>/", $html, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						$old_script = array_shift($match);
						if (strpos($old_script, NGG_PLUGIN_DIR) === FALSE)
							$html = str_replace($old_script, '', $html);
					}
				}

				// Detect custom jQuery UI script and remove
				if (preg_match_all("/<script.*wp-content.*jquery[-_\.]ui.*<\/script>/", $html, $matches, PREG_SET_ORDER)) {
					$detected_jquery_ui = TRUE;
					foreach ($matches as $match) {
						$old_script = array_shift($match);
						if (strpos($old_script, NGG_PLUGIN_DIR) === FALSE)
							$html = str_replace($old_script, '', $html);
					}
				}

				if (isset($_REQUEST['skipjs'])) {
					foreach ($_REQUEST['skipjs'] as $js) {
						$js = preg_quote($js);
						if (preg_match_all("#<script.*{$js}.*</script>#", $html, $matches, PREG_SET_ORDER)) {
							foreach ($matches as $match) {
								$old_script = array_shift($match);
								if (strpos($old_script, NGGFOLDER) === FALSE)
									$html = str_replace($old_script, '', $html);
							}
						}
					}
					$skipjs_count = count($_REQUEST['skipjs']);
				}


				// Use WordPress built-in version of jQuery
				$jquery_url = includes_url('js/jquery/jquery.js');
				$html = implode('', array(
					"<script type='text/javascript' src='{$jquery_url}'></script>\n",
					"<script type='text/javascript'>
					window.onerror = function(msg, url, line){
						if (url.match(/\.js$|\.js\?/)) {
							if (window.location.search.length > 0) {
								if (window.location.search.indexOf(url) == -1)
									window.location.search += '&skipjs[{$skipjs_count}]='+url;
							}
							else {
								window.location.search = '?skipjs[{$skipjs_count}]='+url;
							}
						}
						console.error(msg)
						return true;
					};</script>\n",
					$html
				));
			}

			echo $html;
		}
	}

	// integrate the menu
	function add_menu()  {

		add_menu_page(
		    __('NextGEN Gallery', 'nggallery'),
            _n('NextGEN Gallery', 'NextGen Galleries', 1, 'nggallery'),
            'NextGEN Gallery overview',
            NGGFOLDER,
            array ($this, 'show_menu'),
            path_join(NGGALLERY_URLPATH, 'admin/images/imagely_icon.png'), 11
        );
	    add_submenu_page( NGGFOLDER , __('Overview', 'nggallery'), __('Overview', 'nggallery'), 'NextGEN Gallery overview', NGGFOLDER, array (&$this, 'show_menu'));
	    add_submenu_page( NGGFOLDER , __('Manage Galleries', 'nggallery'), __('Manage Galleries', 'nggallery'), 'NextGEN Manage gallery', 'nggallery-manage-gallery', array (&$this, 'show_menu'));
	    add_submenu_page( NGGFOLDER , _n( 'Manage Albums', 'Albums', 1, 'nggallery' ), _n( 'Manage Albums', 'Manage Albums', 1, 'nggallery' ), 'NextGEN Edit album', 'nggallery-manage-album', array (&$this, 'show_menu'));
	    add_submenu_page( NGGFOLDER , __('Manage Tags', 'nggallery'), __('Manage Tags', 'nggallery'), 'NextGEN Manage tags', 'nggallery-tags', array (&$this, 'show_menu'));

		//register the column fields
		$this->register_columns();
	}

	// integrate the network menu
	function add_network_admin_menu()  {

		add_menu_page( _n( 'Gallery', 'Galleries', 1, 'nggallery' ), _n( 'Gallery', 'Galleries', 1, 'nggallery' ), 'nggallery-wpmu', NGGFOLDER, array (&$this, 'show_network_settings'), path_join(NGGALLERY_URLPATH, 'admin/images/imagely_icon.png') );
		add_submenu_page( NGGFOLDER , __('Network settings', 'nggallery'), __('Network settings', 'nggallery'), 'nggallery-wpmu', NGGFOLDER,  array (&$this, 'show_network_settings'));
	}

    /**
     * Adding NextGEN Gallery to the Admin bar
     *
     * @since 1.9.0
     *
     * @return void
     */
    function admin_bar_menu() {
    	// If the current user can't write posts, this is all of no use, so let's not output an admin menu
    	if ( !current_user_can('NextGEN Gallery overview') )
    		return;

    	global $wp_admin_bar;

    	$wp_admin_bar->add_menu( array( 'id' => 'ngg-menu', 'title' => __( 'Gallery' ), 'href' => admin_url('admin.php?page='. NGGFOLDER) ) );
        $wp_admin_bar->add_menu( array( 'parent' => 'ngg-menu', 'id' => 'ngg-menu-overview', 'title' => __('Overview', 'nggallery'), 'href' => admin_url('admin.php?page='. NGGFOLDER) ) );
        if ( current_user_can('NextGEN Upload images') )
            $wp_admin_bar->add_menu( array( 'parent' => 'ngg-menu', 'id' => 'ngg-menu-add-gallery', 'title' => __('Add Gallery / Images', 'nggallery'), 'href' => admin_url('admin.php?page=ngg_addgallery') ) );
        if ( current_user_can('NextGEN Manage gallery') )
            $wp_admin_bar->add_menu( array( 'parent' => 'ngg-menu', 'id' => 'ngg-menu-manage-gallery', 'title' => __('Manage Galleries', 'nggallery'), 'href' => admin_url('admin.php?page=nggallery-manage-gallery') ) );
        if ( current_user_can('NextGEN Edit album') )
            $wp_admin_bar->add_menu( array( 'parent' => 'ngg-menu', 'id' => 'ngg-menu-manage-album', 'title' => _n( 'Manage Albums', 'Manage Albums', 1, 'nggallery' ), 'href' => admin_url('admin.php?page=nggallery-manage-album') ) );
        if ( current_user_can('NextGEN Manage tags') )
            $wp_admin_bar->add_menu( array( 'parent' => 'ngg-menu', 'id' => 'ngg-menu-tags', 'title' => __('Manage Tags', 'nggallery'), 'href' => admin_url('admin.php?page=nggallery-tags') ) );
    }

    // show the network page
    function show_network_settings() {
		include_once ( dirname (__FILE__) . '/wpmu.php' );
		nggallery_wpmu_setup();
    }

	// load the script for the defined page and load only this code
	function show_menu() {
		global $ngg;

		// Set installation date
		if( empty($ngg->options['installDate']) ) {
			$ngg->options['installDate'] = time();
			update_option('ngg_options', $ngg->options);
		}

		// Show donation message only one time.
		if (isset ( $_GET['hide_donation']) ) {
			$ngg->options['hideDonation'] = true;
			update_option('ngg_options', $ngg->options);
		}

		echo '<div id="ngg_page_content">';
  		switch ($_GET['page']){
			case "nggallery-manage-gallery" :
				include_once ( dirname (__FILE__) . '/functions.php' );	// admin functions
				include_once ( dirname (__FILE__) . '/manage.php' );	// nggallery_admin_manage_gallery
				// Initate the Manage Gallery page
				$ngg->manage_page = new nggManageGallery ();
				// Render the output now, because you cannot access a object during the constructor is not finished
				$ngg->manage_page->controller();
				break;
			case "nggallery-manage-album" :
				include_once ( dirname (__FILE__) . '/album.php' );		// nggallery_admin_manage_album
				$ngg->manage_album = new nggManageAlbum ();
				$ngg->manage_album->controller();
				break;
			case "nggallery-tags" :
				include_once ( dirname (__FILE__) . '/tags.php' );		// nggallery_admin_tags
				break;
			case "nggallery-roles" :
				include_once ( dirname (__FILE__) . '/roles.php' );		// nggallery_admin_roles
				nggallery_admin_roles();
				break;
			case "nggallery-import" :
				include_once ( dirname (__FILE__) . '/myimport.php' );	// nggallery_admin_import
				nggallery_admin_import();
				break;
			case "nggallery-about" :
				include_once ( dirname (__FILE__) . '/about.php' );		// nggallery_admin_about
				nggallery_admin_about();
				break;
			case "nggallery" :
			default :
				include_once ( dirname (__FILE__) . '/overview.php' ); 	// nggallery_admin_overview
				nggallery_admin_overview();
				break;
		}
		echo "</div>";
	}

	function load_scripts() {
		global $wp_version;

		// no need to go on if it's not a plugin page
		if( !isset($_GET['page']) )
			return;

        // used to retrieve the uri of some module resources
        $router = C_Router::get_instance();

		wp_register_script('ngg-ajax', NGGALLERY_URLPATH . 'admin/js/ngg.ajax.js', array('jquery'), NGG_SCRIPT_VERSION);
		wp_localize_script('ngg-ajax', 'nggAjaxSetup', array(
					'url' => admin_url('admin-ajax.php'),
					'action' => 'ngg_ajax_operation',
					'operation' => '',
					'nonce' => wp_create_nonce( 'ngg-ajax' ),
					'ids' => '',
					'permission' => __('You do not have the correct permission', 'nggallery'),
					'error' => __('Unexpected Error', 'nggallery'),
					'failure' => __('A failure occurred', 'nggallery')
		) );
		wp_register_script('ngg-progressbar', NGGALLERY_URLPATH .'admin/js/ngg.progressbar.js', array('jquery'), NGG_SCRIPT_VERSION);

		wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('imagely-admin-font', 'https://fonts.googleapis.com/css?family=Lato:300,400,700,900', array(), NGG_SCRIPT_VERSION );

		switch ($_GET['page']) {
			case NGGFOLDER :
				wp_enqueue_script( 'ngg_overview', $router->get_static_url('photocrati-nextgen-legacy#overview.js'), array('jquery'), NGG_SCRIPT_VERSION);
			break;
			case "nggallery-manage-gallery" :
				wp_enqueue_script( 'postbox' );
				wp_enqueue_script( 'ngg-ajax' );
				wp_enqueue_script( 'ngg-progressbar' );
				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_register_script('shutter', $router->get_static_url('photocrati-lightbox#shutter/shutter.js'), array(), NGG_SCRIPT_VERSION);
				wp_localize_script('shutter', 'shutterSettings', array(
    						'msgLoading' => __('L O A D I N G', 'nggallery'),
    						'msgClose' => __('Click to Close', 'nggallery'),
    						'imageCount' => '1'
    			) );
    			wp_enqueue_script( 'shutter' );
    			add_thickbox();
			break;
			case "nggallery-manage-album" :
                wp_enqueue_script( 'jquery-ui-dialog' );
                wp_enqueue_script( 'jquery-ui-sortable' );
                wp_enqueue_script( 'ngg_select2' );
				wp_enqueue_style( 'ngg_select2' );
			break;
		}
	}


	function enqueue_jquery_ui_theme()
	{
		$settings = C_NextGen_Settings::get_instance();
		wp_enqueue_style(
			$settings->jquery_ui_theme,
			$settings->jquery_ui_theme_url,
			array(),
			$settings->jquery_ui_theme_version
		);
	}

	function load_styles() {
		global $ngg;

		wp_register_style( 'nggadmin', NGGALLERY_URLPATH .'admin/css/nggadmin.css', array(), NGG_SCRIPT_VERSION, 'screen' );
		wp_register_style( 'ngg-jqueryui', NGGALLERY_URLPATH .'admin/css/jquery.ui.css', array(), NGG_SCRIPT_VERSION, 'screen' );

        // no need to go on if it's not a plugin page
		if( !isset($_GET['page']) )
			return;

        // used to retrieve the uri of some module resources
        $router = C_Router::get_instance();

		switch ($_GET['page']) {
			case NGGFOLDER :
				wp_add_inline_style('nggadmin', file_get_contents(M_Static_Assets::get_static_abspath('photocrati-nextgen-legacy#overview.css')));
			case "nggallery-about" :
				wp_enqueue_style( 'nggadmin' );
                //TODO:Remove after WP 3.3 release
                if ( !defined('IS_WP_3_3') )
                    wp_admin_css( 'css/dashboard' );
			break;
			case "nggallery-manage-gallery" :
                wp_enqueue_script('jquery-ui-tooltip');
			case "nggallery-roles" :
			case "nggallery-manage-album" :
				$this->enqueue_jquery_ui_theme();
				wp_enqueue_style( 'nggadmin' );
			break;
			case "nggallery-tags" :
				wp_enqueue_style( 'nggtags', NGGALLERY_URLPATH .'admin/css/tags-admin.css', array(), NGG_SCRIPT_VERSION, 'screen' );
				break;
		}
	}

	/**
	 * We need to manipulate the current_screen name so that we can show the correct column screen options
	 *
     * @since 1.8.0
	 * @param object $screen
	 * @return object $screen
	 */
	function edit_current_screen($screen) {

    	if ( is_string($screen) )
    		$screen = convert_to_screen($screen);

		// menu title is localized, so we need to change the toplevel name
		$i18n = strtolower  ( _n( 'Gallery', 'Galleries', 1, 'nggallery' ) );

		switch ($screen->id) {
			case "{$i18n}_page_nggallery-manage-gallery" :
				// we would like to have screen option only at the manage images / gallery page
				if ( isset ($_POST['sortGallery']) )
					$screen = $screen;
				else if ( (isset($_GET['mode']) && $_GET['mode'] == 'edit') || isset ($_POST['backToGallery']) )
					$screen->base = $screen->id = 'nggallery-manage-images';
				else if ( (isset($_GET['mode']) && $_GET['mode'] == 'sort') )
					$screen = $screen;
				else
					$screen->base = $screen->id = 'nggallery-manage-gallery';
			break;
		}

		if ( 	strpos($screen->id, 'ngg') !== FALSE ||
				strpos($screen->id, 'nextgen') !== FALSE ||
				strpos($screen->id, 'ngg') === 0 )
				{ $screen->ngg = TRUE; }	

		return $screen;
	}

	/**
	 * We need to register the columns at a very early point
	 *
	 * @return void
	 */
	function register_columns() {
		include_once ( dirname (__FILE__) . '/manage-images.php' );

		$wp_list_table = new _NGG_Images_List_Table('nggallery-manage-images');

		include_once ( dirname (__FILE__) . '/manage-galleries.php' );

		$wp_list_table = new _NGG_Galleries_List_Table('nggallery-manage-gallery');
	}
}

function wpmu_site_admin() {
	// Check for site admin
	if ( function_exists('is_super_admin') )
		if ( is_super_admin() )
			return true;

	return false;
}

function wpmu_enable_function($value) {
	if (is_multisite()) {
		$ngg_options = get_site_option('ngg_options');
		return $ngg_options[$value];
	}
	// if this is not WPMU, enable it !
	return true;
}
