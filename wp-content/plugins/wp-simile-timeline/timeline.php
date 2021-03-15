<?php
/*
Plugin Name:  WP SIMILE Timeline
Plugin URI: freshlabs.de
Description: Integrates the <a href="http://simile.mit.edu/timeline/">SIMILE Timeline</a> with WordPress and provides an option interface for the various timeline settings. With this plugin you can display posts from a specific category in the Timeline Widget. Simply include the <strong>[similetimeline]</strong> shortcode in your page or post and specify the category on the <a href="options-general.php?page=timeline.php">admin page</a>.
Author: freshlabs
Author URI: freshlabs.de
Version: 0.5.2
/*
	SIMILE Timeline for WordPress
    Copyright 2006-2019 freshlabs

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

include('inc/adodb-time.inc.php');  // AdoDB Time+Date Library
include('inc/WPSimileTimeline.class.php');	// class for timeline database functions
include('inc/WPSimileTimelineDatabase.class.php');	// class for timeline database functions
include('inc/WPSimileTimelineAdmin.class.php'); // class for helper functions
include('inc/WPSimileTimelinePost.class.php'); // class for post related functions
include('inc/WPSimileTimelineTerm.class.php'); // class for term functions
include('inc/WPSimileTimelineToolbox.class.php'); // class for helper functions
include('inc/WPSimileTimelineBand.class.php'); // class for timeline band
include('inc/WPSimileTimelineHotzone.class.php'); // class for timeline hotzone
include('inc/WPSimileTimelineDecorator.class.php'); // class for timeline decorator

@define('STL_TIMELINE_PLUGIN_DATESTRING', '20140920');
@define('STL_TIMELINE_PLUGIN_VERSION', '0.5.2');
@define('STL_TIMELINE_FOLDER', WP_PLUGIN_URL.'/wp-simile-timeline');
@define('STL_TIMELINE_DATA_FOLDER', STL_TIMELINE_FOLDER.'/data');
@define('STL_TIMELINE_IMAGE_FOLDER', STL_TIMELINE_FOLDER.'/src/img');
@define('STL_TIMELINE_API_URL', STL_TIMELINE_FOLDER.'/src/timeline_js/timeline-api.js?bundle=true'); // use &defaultLocale to override detected locale
@define('STL_TIMELINE_NONCE_NAME', 'stl_timeline_update_options');

/**
 * Responsible for initing the plugin and creating options and plugin tables in the database
 */
class WPSimileTimelineLoader{
	/*
	 * Initializing WordPress hooks for plugin
	 */
	function init(){
		// uninstall hook
		register_uninstall_hook(__FILE__, array('WPSimileTimelineLoader','uninstallPlugin'));
		// add custom post boxes in post interface
		add_action('admin_menu', array('WPSimileTimelineLoader','addPostPanelEventDates'));
		// add options page
		add_action('admin_menu', array('WPSimileTimelineLoader','registerOptionsPage'));
		// save event dates on post edit/save/publish
		add_action('wp_insert_post', array('WPSimileTimelinePost','updateEventDates'));
		// update term relations on category creation/editing
		add_action('create_term', array('WPSimileTimelineTerm','addTerm'));
		// and deletion
		add_action('delete_term', array('WPSimileTimelineTerm','deleteTerm'));
		// contextual help links
		add_filter('contextual_help', array('WPSimileTimelineLoader', 'showContextualHelp'), 10, 3);
		
		// filters and shortcode for the frontend
		add_filter('wp_head', array('WPSimileTimeline', 'outputFrontendHeaderMarkup'), 5);
		add_shortcode('similetimeline', array('WPSimileTimelineLoader', 'parseShortcodeCall'));
	}
	
	/*
	 * load dependent class files and init the main plugin
	 */
	function loadPlugin(){
		// init the actual plugin worker class
		WPSimileTimeline::construct();
		return true;
	}
	
	/*
	 * Install the plugin with basic parameters and necessary database tables
	 */
	function installPlugin(){
		// write default options
		$stl_timeline_default_options = WPSimileTimeline::getDefaultOptions();
		foreach($stl_timeline_default_options as $option=>$v){
			if(!get_option($option))
				add_option($option, $v);
		}
		// add database columns for start and end dates
		WPSimileTimelinePost::createColumns();
		// add database table for category settings
		WPSimileTimelineTerm::createTable();	
		// update possibly new categories
		WPSimileTimelineTerm::syncTerms();
		// add database table for timeline bands
		WPSimileTimelineBand::createTable();
		// add database table for timeline hotzones
		WPSimileTimelineHotzone::createTable();
		// add database table for timeline decorators
		WPSimileTimelineDecorator::createTable();
		
		// execute version specific updates to database
		WPSimileTimelineLoader::doVersionUpdates();
	}
	
	/*
	 * Execute version specific updates
	 */
	function doVersionUpdates(){

		// process database updates
		WPSimileTimelineDatabase::doUpdates();
		
		// Update to new version string
		update_option('stl_timeline_plugin_version', STL_TIMELINE_PLUGIN_VERSION);
	}
	
	/*
	 * Plugin uninstaller. Removes all plugin related data from database
	 */
	function uninstallPlugin(){
		$stl_timeline_default_options = WPSimileTimeline::getDefaultOptions();
		// remove entries from option table
		foreach($stl_timeline_default_options as $option=>$v){
			delete_option($option);
		}
		delete_option('stl_timeline_plugin_version');

		// remove columns in wp_posts
		WPSimileTimelinePost::deleteColumns();

		// remove timeline specific configuration tables
		WPSimileTimelineDatabase::deleteTable('stl_timeline_terms');
		WPSimileTimelineDatabase::deleteTable('stl_timeline_bands');
		WPSimileTimelineDatabase::deleteTable('stl_timeline_hotzones');
		WPSimileTimelineDatabase::deleteTable('stl_timeline_decorators');
	}

	/*
	 * Add options page to WordPress admin menu and hook the needed scripts
	 * Submission actions from the admin form are also processed here.
	 */
	function registerOptionsPage(){
		if (function_exists('add_options_page')) {
			$plugin_page = add_menu_page( 'SIMILE Timeline', 'SIMILE Timeline', 'activate_plugins', 'wp-simile-timeline', array('WPSimileTimelineLoader', 'showHtmlOptionsPage'), STL_TIMELINE_IMAGE_FOLDER.'/icon.png', 76.4 );
			// register plugin's own scripts
			add_action('admin_init', array('WPSimileTimelineAdmin', 'registerAdminScripts'));
			// plugin admin styles
			add_action('admin_print_scripts', array('WPSimileTimelineAdmin', 'outputAdminCss'));
			// only add plugin related HTML head to option page
			add_action('admin_print_scripts-'. $plugin_page, array('WPSimileTimelineAdmin', 'outputAdminScripts'));
		} 

		// safety upgrade in case plugin wasn't inited by mechanism or user
		$version = get_option('stl_timeline_plugin_version');
		if(isset($version) && $version != STL_TIMELINE_PLUGIN_VERSION){
			WPSimileTimelineLoader::installPlugin();
		}

		// uninstall plugin
		if (!empty($_POST['delete-action']) && $_POST['delete-action'] == 'purgedb'){
			WPSimileTimelineLoader::uninstallPlugin();
			// deactivate and redirect to plugin index page
			deactivate_plugins(array('wp-simile-timeline/timeline.php'));
			wp_redirect('plugins.php?deactivate=true');
			die();
		}
	}
	
	/*
	 * Displays admin option page after loading plugin basics
	 */
	function showHtmlOptionsPage(){
		if(WPSimileTimelineLoader::loadPlugin()){
			$wpstl = WPSimileTimeline::singleton();
			$wpstl->init();
			$wpstl->processOptions();
			WPSimileTimelineAdmin::renderOptionsPage();
		}
	}	
	
	/*
	 * Gather options from shortcode parameters and call output function
	 */
	function parseShortcodeCall($attributes){
		if(WPSimileTimelineLoader::loadPlugin()){
			$wpstl = WPSimileTimeline::singleton();
			// make attributes available as variables (see http://codex.wordpress.org/Shortcode_API)
			extract( shortcode_atts( $wpstl->api_parameters, $attributes ) );
			// call template function
			return stl_simile_timeline($cats, $id, $scriptfile, $theme, $start, $stop, true);
		}
	}
	
	/*
	 * Custom box hook for post and page interface adds custom option box
	 */
	function addPostPanelEventDates() {
		if(WPSimileTimelineLoader::loadPlugin()){
			$wpstl = WPSimileTimeline::singleton();
			$wpstl->init();
			if( function_exists('add_meta_box')) {
				add_meta_box( 'stl-timeline-event-data', __( 'SIMILE Timeline', 'stl_timeline' ), array('WPSimileTimelineAdmin', 'outputCustomPostDateOptions'), 'post', 'advanced' );
				add_meta_box( 'stl-timeline-event-data', __( 'SIMILE Timeline', 'stl_timeline' ), array('WPSimileTimelineAdmin', 'outputCustomPostDateOptions'), 'page', 'advanced' );
				
				// Adding meta boxes for all registered custom posts
				$post_types = WPSimileTimelineToolbox::getCustomPostTypes();
				foreach($post_types as $post_type):
					add_meta_box( 'stl-timeline-event-data', __( 'SIMILE Timeline', 'stl_timeline' ), array('WPSimileTimelineAdmin', 'outputCustomPostDateOptions'), $post_type, 'advanced' );
				endforeach;
			}
		}
	}
	
	/*
	 * Adds help links to WordPress help tab
	 */
	function showContextualHelp($contextual_help, $screen_id, $screen){
		if($screen_id=='toplevel_page_wp-simile-timeline'){
			$links = array(
				__('WP SIMILE Timeline Support Group', 'stl_timeline') => 'http://groups.google.com/group/wp-simile-timeline/',
				__('SIMILE Widgets Timeline Documentation', 'stl_timeline') => 'http://code.google.com/p/simile-widgets/wiki/Timeline'
			);
			$i=0;
			foreach($links as $title=>$url){
				$contextual_help .= '<a href="'.$url.'">' . $title . '</a>' . ($i<count($links)-1 ? '<br />' : '');
				$i++;
			}
		}
		return $contextual_help;
	}
}

// Init plugin if WordPress is loaded
if(defined('ABSPATH') && defined('WPINC')) {
	// install new database tables & columns on plugin activation
	// activation_hook does not work inside class file
	register_activation_hook(__FILE__, array('WPSimileTimelineLoader','installPlugin'));
	// init plugin executing all other hooks
	add_action('init', array('WPSimileTimelineLoader','init'), 1000,0);
}

/*
 * Template function to build the timeline
 */
function stl_simile_timeline($cats=null, $id='stl-mytimeline', $scriptfile='timeline.js.php', $theme='dynamic-theme', $start=0, $stop=0, $return=false){
	$timeline = new WPSimileTimeline();
	// load categories set in admin options if no override parameter is set
	if(!isset($cats)){
		$cats = WPSimileTimelineTerm::getActiveTerms();
	}
	$markup=$timeline->getFrontendBodyMarkup(array('cats'=>$cats, 'id'=>$id, 'scriptfile'=>$scriptfile, 'theme'=>$theme, 'start'=>$start, 'stop'=>$stop));

	if(!$return){
		echo $markup;
	}
	else{
		return $markup;
	}
}

?>