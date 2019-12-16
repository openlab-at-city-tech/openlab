<?php
/**
 * WPSimileTimeline.class.php
 * Description: Main class for the plugin. Does most of the frontend work.
 * Plugin URI: http://wordpress.org/extend/plugins/wp-simile-timeline/
 * Author: freshlabs
 * 
	===========================================================================
	SIMILE Timeline for WordPress
	
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
	===========================================================================
*/
class WPSimileTimeline{
	var $_inited = false;
	
	// Parameters that can be used for the template tag and shortcode
	var $api_parameters = array(
		'cats' => null,
		'id' => 'stl-mytimeline',
		'scriptfile'=>'timeline.js.php',
		'theme'=>'dynamic-theme',
		'start'=>0,
		'stop'=>0
	);
	
	function WPSimileTimeline(){
		// empty constructor
	}
	
	/*
	 * Default options for timeline settings
	 */
	function getDefaultOptions(){
		$stl_timeline_default_options = array(
			'stl_timelinepageids' => 0,
			'stl_timeline_showfutureposts' => 0,
			'stl_timeline_startdate' => 0,
			'stl_timeline_usestartstop' => 0,
			'stl_timeline_start' => 0,
			'stl_timeline_stop' => 0,
			'stl_timeline_linkhandling' => 0,
			'stl_timeline_useattachments' => 0,
			'stl_timeline_showbubbledate' => 0
		);
		return $stl_timeline_default_options;
	}
	
	function construct(){
		if(!isset($GLOBALS["wpsimiletimeline_instance"])) {
			$GLOBALS["wpsimiletimeline_instance"]=new WPSimileTimeline();
		}
	}
	
	function &singleton() {
		if(isset($GLOBALS["wpsimiletimeline_instance"])) {
			return $GLOBALS["wpsimiletimeline_instance"];
		} else return null;
	}
	
	function init(){
		if(!$this->_inited){
			// localize plugin
			$currentLocale = get_locale();
			if(!empty($currentLocale)) {
				$moFile = dirname(dirname(__FILE__)) . "/locale/" . $currentLocale . ".mo";
				if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('stl_timeline', $moFile);
			}
			
			$this->_inited = true;
		}
		return $this->_inited;
	}
	
	/**
	 * Updates options when option page is submitted
	 */
	function processOptions(){
		if ( is_array($_POST) && !empty($_POST['action']) && $_POST['action'] == 'update'){
			if(check_admin_referer(STL_TIMELINE_NONCE_NAME)){
				$postdata = $_POST;
				$saved = WPSimileTimelineAdmin::saveAdminOptions($postdata);
				
				if(function_exists('add_settings_error')){
					if($saved){
						add_settings_error('stl_timeline_options', 'settings-updated', __('Settings saved.'), 'updated');
					}
					else{
						add_settings_error('stl_timeline_options', 'settings-failed', __('An error occured while saving.'), 'error');
					}
				}
			}
		}
		
		// process delete requests (for hotzones and decorators)
		if(!empty($_GET) && isset($_GET['action'])){
			$nonce=$_REQUEST['_wpnonce'];
			if(wp_verify_nonce($nonce, STL_TIMELINE_NONCE_NAME)){
				switch($_GET['action']){
					case 'delete-hotzone':
						$stl_timeline_hotzone = new WPSimileTimelineHotzone();
						$stl_timeline_hotzone->delete($_GET['id']);
						break;
					case 'delete-decorator':
						$stl_timeline_decorator = new WPSimileTimelineDecorator();
						$stl_timeline_decorator->delete($_GET['id']);
						break;
					default:
						break;
				}
				
				if(function_exists('add_settings_error')){
					add_settings_error('stl_timeline_options', 'settings-updated', __('Entry was successfully deleted.'), 'updated');
				}	
			}
			else{
				die("Security check");
			}
		}
	}
	
	/** -----------------------------------------------------------------------------
	 * outputFrontendHeaderMarkup
	 * outputs the SIMILE JavaScript and CSS in the <head> element
	 * ONLY when post_id is set in the SIMILE Timeline options
	 * ---------------------------------------------------------------------------*/
	function outputFrontendHeaderMarkup(){
		global $post;
	
		if( WPSimileTimeline::isTimelinePage($post->ID)):
			// directly include SIMILE Ajax API (prototype/jQuery issues)
			echo '<script src="'.STL_TIMELINE_FOLDER.'/src/timeline_ajax/simile-ajax-api.js" type="text/javascript"></script>';
			// load API from SIMILE server
			echo '<script src="' . STL_TIMELINE_API_URL . '" type="text/javascript"></script>'.
			// include local CSS template for timeline rules
			'<link rel="stylesheet" href="' . STL_TIMELINE_DATA_FOLDER . '/custom.css" type="text/css" />' . "\n" .
			'<link rel="stylesheet" href="' . STL_TIMELINE_DATA_FOLDER . '/timeline.css.php" type="text/css" />' . "\n";
		endif;
	}
	
	/** -----------------------------------------------------------------------------
	 * print the necessary timeline markup (called by shortcode API) and Template Tag
	 * ---------------------------------------------------------------------------*/
	function getFrontendBodyMarkup($attributes){
		$r = '<div id="'.$attributes['id'].'" class="stl-timeline '.$attributes['theme'].'"></div>';
		/*
		 * TODO: Move this to the <head> section with the correct filter (wp_print_scripts)
		 * but still with the dynamic attribute array.
		 */
		$script = '<script src="' . STL_TIMELINE_DATA_FOLDER . '/'.$attributes['scriptfile'];
		$script .= '?v=' . STL_TIMELINE_PLUGIN_DATESTRING . '&amp;id='.$attributes['id'];
		if(!empty($attributes['cats'])) $script .= '&amp;cat='.$attributes['cats'];
		$script .= '&start='.$attributes['start'].'&stop='.$attributes['stop'].'';
		$script .= '" type="text/javascript"></script>';
		return $r . $script;
	}
	
	/** -----------------------------------------------------------------------------
	 * loops all page ids given in the timeline options
	 * returns true when the current page has a timeline
	 * TODO: Recognize a timeline page programatically. But how to trigger printing something in the head section of the HTML when a template function is called?
	 * ---------------------------------------------------------------------------*/
	function isTimelinePage($stl_currentpageid){
		$stl_istimelinepage = false;
		$stl_timelinepages = get_option('stl_timelinepageids');
		// Set timeline for all pages when the option is 0
		if($stl_timelinepages == 0 || $stl_timelinepages == 'all'){
			$stl_istimelinepage = true;
		}
		else{
			$stl_timelinepagesarray = split(",", $stl_timelinepages);
			foreach($stl_timelinepagesarray as $stl_timelinepageid):
				if($stl_currentpageid == $stl_timelinepageid){
					$stl_istimelinepage = true;
				}
			endforeach;
		}
		return $stl_istimelinepage;
	}
}
?>