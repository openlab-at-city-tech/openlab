<?php
/**
 * Description: SIMILE Timeline admin functions for the WordPress backend
 * @author freshlabs
 * @link http://wordpress.org/extend/plugins/wp-simile-timeline/
 * @package wp-simile-timeline
 * 
	===========================================================================
	SIMILE Timeline for WordPress
	Copyright (C) 2006-2019 freshlabs
	
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
class WPSimileTimelineAdmin{
	function __construct(){

	}
	
	/**
     * Saves user options selected in the Admin panel.
	 */
	function saveAdminOptions($postdata){
		$saved = true;
		#echo '<pre>';
		#print_r($postdata);
		#echo '</pre>';
		#exit();		
		// Save options for bands, hotzones and highlight decorators
		foreach($postdata['stl_timeline']['bands'] as $band){		
			$band_obj = new WPSimileTimelineBand();
			$band_obj->set($band);
			$band_obj->save();
		}
		
		// Convert date arrays to date strings
		if(!empty($postdata['stl_timeline_start']))
			$postdata['stl_timeline_start'] = WPSimileTimelineToolbox::implodeDate($postdata['stl_timeline_start']);
		if(!empty($postdata['stl_timeline_stop']))
			$postdata['stl_timeline_stop'] = WPSimileTimelineToolbox::implodeDate($postdata['stl_timeline_stop']);

		// Custom date for the timeline to focus on load
		if($postdata['stl_timeline_startdate'] == -1){
			$postdata['stl_timeline_startdate'] = adodb_date2('Y-m-d', $postdata['stl_timeline_startdate_custom']);
		}
		#print_r($postdata);exit();
		// Get default options
		$stl_timeline_default_options = WPSimileTimeline::getDefaultOptions();
		// try to get posted value for options
		foreach($stl_timeline_default_options as $option=>$v){
			if(array_key_exists($option, $postdata)){
				update_option($option, $postdata[$option]);
			}
			else{
				// use default value if no post var was found
				update_option($option, $v);
			}
		}
	
		// Save timeline category settings
		WPSimileTimelineAdmin::saveCategoryOptions($postdata);
		
		return $saved;
	}
	
	/* ---------------------------------------------------------------------------------
	 * update categories that are displayed by the timeline
	 * --------------------------------------------------------------------------------*/
	function saveCategoryOptions($postdata) {
	
	    $categories='';
	    $wpst_term = new WPSimileTimelineTerm();
	
		if (!$categories)
			$categories = $wpst_term->getAllTerms();
	
		if ($categories) {
			$stl_cs = array();
			foreach ($categories as $category) {
				$active = 0;
	
				# save category color to database
				if (!empty($postdata['stl_timeline']['categories'][$category->term_id]['color'])){
					$wpst_term->updateTermColor($category->term_id, $postdata["stl_timeline"]['categories'][$category->term_id]['color']);
				}
				# save category icon to database
				if (!empty($postdata['stl_timeline']['categories'][$category->term_id]['icon'])){
					$wpst_term->updateTermIcon($category->term_id, $postdata["stl_timeline"]['categories'][$category->term_id]['icon']);
				}
				# save active state to database	
				if ( isset($postdata['stl_timeline']['categories'][$category->term_id]['status'])) {
					array_push($stl_cs, $category->term_id);
					// Update category active/inactive
					$wpst_term->updateTermStatus($category->term_id, 1);
				} else {
					$wpst_term->updateTermStatus($category->term_id, 0);
				}
			}
		}
	}

	/** -----------------------------------------------------------------------------
	     register admin options JavaScript
	 * ---------------------------------------------------------------------------*/
	function registerAdminScripts(){
		wp_register_script('stl_timeline_admin_script', WP_PLUGIN_URL . '/wp-simile-timeline/inc/timeline-admin.js');
	}
		
	/** -----------------------------------------------------------------------------
	     Outputs admin JavaScript
	 * ---------------------------------------------------------------------------*/
	function outputAdminScripts(){
		wp_enqueue_script('colorpicker');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('stl_timeline_admin_script');
	}
	
	/** -----------------------------------------------------------------------------
     Outputs necessary admin CSS (for post + options page)
	 * ---------------------------------------------------------------------------*/
	function outputAdminCss(){
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".WP_PLUGIN_URL."/wp-simile-timeline/inc/timeline-admin.css\" />\n";
	}
	
	/** -----------------------------------------------------------------------------
	 * stl_timeline_postdate_inner_custom_box
	 * outputs content for custom option box in post/page interface
	 * ---------------------------------------------------------------------------*/
	function outputCustomPostDateOptions() {
		global $post, $action;
		// Use nonce for verification
		echo '<input type="hidden" name="st_timeline_nonce" id="st_timeline_nonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
		echo '<table>
			<thead>
				<tr>
					<th>'.__('Event Start Date', 'stl_timeline').'</th>
					<th>'.__('Event End Date', 'stl_timeline').'</th>
				</tr>
			</thead>';
		echo '<tbody>';
		echo '<tr>';
		echo '<td>';
		echo '<h4>'.__('Start Date', 'stl_timeline') . '</h4>';
		WPSimileTimelineAdmin::touchPostDateTime(($action == 'edit'), 1, $post->stl_timeline_event_start, 'start');
		echo '</td>';
		echo '<td>';
		echo '<h4>'.__('End Date', 'stl_timeline') . '</h4>';
		WPSimileTimelineAdmin::touchPostDateTime(($action == 'edit'), 1, $post->stl_timeline_event_end, 'end');
		echo '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>';
		echo '<h4>'.__('Latest Start Date', 'stl_timeline') . '</h4>';
		WPSimileTimelineAdmin::touchPostDateTime(($action == 'edit'), 1, $post->stl_timeline_event_latest_start, 'latest_start');
		echo '</td>';
		echo '<td>';
		echo '<h4>'.__('Earliest End Date', 'stl_timeline') . '</h4>';
		WPSimileTimelineAdmin::touchPostDateTime(($action == 'edit'), 1, $post->stl_timeline_event_earliest_end, 'earliest_end');
		echo '</td>';
		echo '</tr>';
		echo '</tbody>
		</table>';
	}
	
	/** -----------------------------------------------------------------------------
	 * touchPostDateTime
	 * output date selection elements
	 * ---------------------------------------------------------------------------*/
	function touchPostDateTime($edit = 1, $for_post = 1, $value, $name) {
		global $month, $post, $comment;

		if ( $for_post )
			// do not save the event date when post is saved as draft TODO: well, DO!
			$edit = ( ('draft' == $post->post_status) && (!$value || '0000-00-00 00:00:00' == $value) ) ? false : true;
	 
		echo '<legend>';
		// edit confirmation checkbox
		echo '<input type="checkbox" class="checkbox" name="stl_timeline_'.$name.'[edit]" value="1" id="stl_timeline_'.$name.'_timestamp" /> ';
		// ..and its label
		echo '<label for="stl_timeline_'.$name.'_timestamp">'. __('Edit timestamp','stl_timeline').'</label>';
		echo '&nbsp;&nbsp;<input type="checkbox" class="checkbox" name="stl_timeline_'.$name.'[reset]" value="1" id="stl_timeline_'.$name.'_timestamp_reset" /> ';
		echo '<label for="stl_timeline_'.$name.'_timestamp_reset">'. __('Reset timestamp','stl_timeline').'</label>';
		echo '</legend><br />';
		
		$post_date = ($for_post) ? $value : $comment->comment_date;
		
		WPSimileTimelineAdmin::outputDatepicker('stl_timeline_'.$name, $post_date, true, $edit);
	}
	
	/** -----------------------------------------------------------------------------
	 * WPSimileTimelineAdmin::outputDatepicker
	 * Create and output Date Picker input elements
         * TODO: Implement dates BC
	 * ---------------------------------------------------------------------------*/
	function outputDatepicker($name, $date, $show_existing=true, $edit=1) {
		global $month;
                /*
                echo $date;
                echo '<br />';
                $prefix     = substr($date, 0, 1);  // Get date prefix A/B
                $date       = substr($date, 1);     // Handle date without prefix
                $is_date_bc = ($prefix == 'B');     // Date BC existent with prefix 'B'
                $bc_checked = $is_date_bc ? ' checked' : '';
                */
		$stl_time_adj = time() + (get_option('gmt_offset') * 3600);
		
		$stl_jj = ($edit) ? adodb_date2('d', $date) : adodb_gmdate('d', $stl_time_adj);
		$stl_mm = ($edit) ? adodb_date2('m', $date) : adodb_gmdate('m', $stl_time_adj);
		$stl_aa = ($edit) ? adodb_date2('Y', $date) : adodb_gmdate('Y', $stl_time_adj);
		$stl_hh = ($edit) ? adodb_date2('H', $date) : adodb_gmdate('H', $stl_time_adj);
		$stl_mn = ($edit) ? adodb_date2('i', $date) : adodb_gmdate('i', $stl_time_adj);
		$stl_ss = ($edit) ? adodb_date2('s', $date) : adodb_gmdate('s', $stl_time_adj);
                
                

		if ($show_existing && $edit && $date != '0000-00-00 00:00:00' ) {
			echo '<small>' . __('Existing timestamp', 'stl_timeline') . ': ';
                        #if($is_date_bc){
                            // Only show year for BC dates (TODO: reference)
                        #    echo "$stl_aa B.C.";
                        #}
                        #else{
                            echo "{$month[$stl_mm]} $stl_jj, $stl_aa, $stl_hh:$stl_mn";
                        #}
                        echo '</small>';
		}
		                                                
		echo "<select name=\"".($name)."[month]\">\n";
		for ($i = 1; $i < 13; $i = $i +1) {
			echo "\t\t\t<option value=\"$i\"";
			if ($i == $stl_mm)
				echo " selected='selected'";
			if ($i < 10) {
				$stl_ii = "0".$i;
			} else {
				$stl_ii = "$i";
			}
			echo ">".$month["$stl_ii"]."</option>\n";
		}
		
		echo '</select>';
		echo '<input type="text" class="stl_timeline_2di" id="stl_jj_' . WPSimileTimelineToolbox::filterDomString($name) .'" name="' . $name . '[day]" value="' . $stl_jj . '" size="2" maxlength="2" />';
		echo '<input type="text" class="stl_timeline_4di" id="stl_aa_' . WPSimileTimelineToolbox::filterDomString($name) .'" name="' . $name .'[year]" value="' . $stl_aa .'" size="4" maxlength="4" />';
                #echo ', ';
		echo '<input type="text" class="stl_timeline_2di" id="stl_hh_' . WPSimileTimelineToolbox::filterDomString($name) .'" name="' . $name .'[hour]" value="' . $stl_hh .'" size="2" maxlength="2" /> : ';
		echo '<input type="text" class="stl_timeline_2di" id="stl_mn_' . WPSimileTimelineToolbox::filterDomString($name) .'" name="' . $name .'[minute]" value="' . $stl_mn .'" size="2" maxlength="2" />';
                // Is date B.C. checkbox
                #echo ' <input type="checkbox" id="stl_is_bc_' . WPSimileTimelineToolbox::filterDomString($name) .'" name="' . $name . '[is_bc]" value="1" ' . $bc_checked . ' />';
                #echo ' <label for="stl_is_bc_' . WPSimileTimelineToolbox::filterDomString($name) .'">' . _e('B.C.', 'stl_timeline') . '</label>';
		echo '<input type="hidden" id="stl_ss_' . WPSimileTimelineToolbox::filterDomString($name) .'" name="' . $name .'[second]" value="' . $stl_ss .'" size="2" maxlength="2" />';
	}
	
	/**
	 * Prints a select element with time units
	 */
	function outputUnitSelectElement($name, $id, $value){
		$units = array(
			 0 => __('Millisecond', 'stl_timeline'),
			 1 => __('Second', 'stl_timeline'),
			 2 => __('Minute', 'stl_timeline'),
			 3 => __('Hour', 'stl_timeline'),
			 4 => __('Day', 'stl_timeline'),
			 5 => __('Week', 'stl_timeline'),
			 6 => __('Month', 'stl_timeline'),
			 7 => __('Year', 'stl_timeline'),
			 8 => __('Decade', 'stl_timeline'),
			 9 => __('Century', 'stl_timeline'),
			10 => __('Millenium', 'stl_timeline'),
		);
		
		echo '<select name="' . $name . '" id="' . $id . '">';
		foreach($units as $index=>$unit):
		echo '<option' . WPSimileTimelineToolbox::outputOptionValue($index, $value) . '>' . $unit . '</option>';
		endforeach;
		echo '</select>';
	}
	
	/**
	 * Prints a select element for highlight decorator types
	 */
	function outputDecoratorTypeSelect($name, $active){
		$m = '<select name="'. $name . '" class="stl_timeline_decorator_type_picker">';
		$m .= '<option' . WPSimileTimelineToolbox::outputOptionValue(0, $active) . '>' . __('Point in time', 'stl_timeline') . '</option>';
		$m .= '<option' . WPSimileTimelineToolbox::outputOptionValue(1, $active) . '>' . __('Period in time', 'stl_timeline') . '</option>';
		$m .= '</select>';
		echo $m;
	}
	
	/**
	 * Build markup for JavaScript colorpicker
	 */
	function buildColorpickInput($field, $id, $palette, $hexvalue){
		$onclick = 'onclick="cp.select(document.getElementById(\''.WPSimileTimelineToolbox::filterDomString($field).'\'), \''.$palette.'\');return false;" ';
		$attribs  = 'style="background: '.$hexvalue.';" class="stl-colorfield" name="' . $palette . '" id="' . $palette .'"';
		$link = '<a href="#"' . $onclick . $attribs .'><span>' . __('Pick color', 'stl_timeline') . '</span></a>' . "\n";
		$input = '		<input type="text" class="stl-colorvalue" value="'.$hexvalue.'" size="7" id="' . WPSimileTimelineToolbox::filterDomString($field) . '" name="' . $field . '" />';
		return $link . $input;
	}
	
	/**
	 * Build markup for circle marker selector
	 */
	function buildIconSelector($field, $current){
		// Preset icons from SIMILE in Name=>URL format
		$icons = array(
			__('Default', 'stl_timeline')			=> 'null',
			__('Gray Circle', 'stl_timeline') 		=> 'http://www.simile-widgets.org/timeline/api/images/gray-circle.png',
			__('Blue Circle', 'stl_timeline') 		=> 'http://www.simile-widgets.org/timeline/api/images/blue-circle.png',
			__('Dull Blue Circle', 'stl_timeline') 	=> 'http://www.simile-widgets.org/timeline/api/images/dull-blue-circle.png',
			__('Dark Blue Circle', 'stl_timeline') 	=> 'http://www.simile-widgets.org/timeline/api/images/dark-blue-circle.png',
			__('Green Circle', 'stl_timeline') 		=> 'http://www.simile-widgets.org/timeline/api/images/green-circle.png',
			__('Dull Green Circle', 'stl_timeline') => 'http://www.simile-widgets.org/timeline/api/images/dull-green-circle.png',
			__('Dark Green Circle', 'stl_timeline') => 'http://www.simile-widgets.org/timeline/api/images/dark-green-circle.png',
			__('Red Circle', 'stl_timeline') 		=> 'http://www.simile-widgets.org/timeline/api/images/red-circle.png',
			__('Dull Red Circle', 'stl_timeline') 	=> 'http://www.simile-widgets.org/timeline/api/images/dull-red-circle.png',
			__('Dark Red Circle', 'stl_timeline')	=> 'http://www.simile-widgets.org/timeline/api/images/dark-red-circle.png'
		);
		$input  = '<select name="'.$field.'">';
		foreach($icons as $name => $url){
			$val = WPSimileTimelineToolbox::outputOptionValue($url, $current);
			$input .= '<option' . $val . '>'  . $name  . '</option>';
		}
		$input .= '</select>';
		return $input;
	}
	
	/**
	 * Set terms to display in the timeline
	 */
	function outputTermsTable($term_type='category') {
		$row = '<tr>
		        <th scope="col" style="width:150px;text-align:center;">' . __('Show in Timeline?', 'stl_timeline') . '</th> 
		        <th scope="col" style="width:60px;">' . __('ID', 'stl_timeline') . '</th>
		        <th scope="col" align="left" class="row-title">' . __('Name', 'stl_timeline') . '</th>
		        <th scope="col" width="130" align="center">' . __('Color', 'stl_timeline') . '</th>
		        <th scope="col" width="130" align="left">' . __('Icon', 'stl_timeline') . '</th>
		        </tr>';

		echo '<table width="100%" cellpadding="3" cellspacing="3" class="widefat">
				<thead>'
				. $row .
			    '</thead>
			    <tfoot>'
				. $row .
			    '</tfoot>';
	
		$wpst_term = new WPSimileTimelineTerm();
		$wpst_term->outputCategoryRows($term_type);
	
		echo '</table>';
	}
	
	/* ---------------------------------------------------------------------------------
	     Display User Options
	 * --------------------------------------------------------------------------------*/
	function renderOptionsPage(){
		$stl_timeline_band = new WPSimileTimelineBand();		

		$stl_timelinepageids = get_option('stl_timelinepageids');
		$stl_timeline_showfutureposts = get_option('stl_timeline_showfutureposts');
		$stl_timeline_startdate = get_option('stl_timeline_startdate');
		$stl_timeline_linkhandling = get_option('stl_timeline_linkhandling');
		$stl_timeline_useattachments = get_option('stl_timeline_useattachments');
		$stl_timeline_usestartstop = get_option('stl_timeline_usestartstop');
		$stl_timeline_start = get_option('stl_timeline_start');
		$stl_timeline_stop = get_option('stl_timeline_stop');
		$stl_timeline_showbubbledate = get_option('stl_timeline_showbubbledate');
		
		$stl_timeline_start = $stl_timeline_start == 0 ? '' : $stl_timeline_start;
		$stl_timeline_stop = $stl_timeline_stop == 0 ? '' : $stl_timeline_stop;
		
		$stl_bands = $stl_timeline_band->find_all();
		
		$custom_taxonomies = WPSimileTimelineTerm::getCustomTaxonomies();

	?>
		<div class="wrap" id="wp_simile_timeline_options">
		<h2><?php _e('SIMILE Timeline Options', 'stl_timeline') ?></h2>
		<?php
		if(function_exists('settings_errors')){
			settings_errors('stl_timeline_options');
		}
		?>
		<div id="stl-timeline-option-container">
	    <ul id="stl-timeline-options-tabs">
	        <li><a href="#stl-configuration"><span><?php _e('Configuration', 'stl_timeline') ?></span></a></li>
	        <li><a href="#stl-content"><span><?php _e('Content', 'stl_timeline') ?></span></a></li>
			<li><a href="#stl-design"><span><?php _e('Design', 'stl_timeline') ?></span></a></li>
	        <li><a href="#stl-uninstall"><span><?php _e('Uninstall', 'stl_timeline') ?></span></a></li>
	    </ul>
			<form name="stl_options" method="post" action="<?php echo wp_nonce_url('admin.php?page=wp-simile-timeline&updated=true', STL_TIMELINE_NONCE_NAME); ?>"><?php /*  */ ?>
			<?php wp_nonce_field(STL_TIMELINE_NONCE_NAME); ?>
			<input type="hidden" name="action" value="update" />
			<div id="colorPickerDiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;visibility:hidden;"> </div>
	<!--
	******************************************************
	Timeline Integration
	******************************************************
	-->	               
	            <div id="stl-configuration">				
	                <fieldset class="options">
					<h3><?php _e('Timeline Configuration', 'stl_timeline'); ?></h3>
					<table width="90%" cellpadding="8" class="form-table">
					<tr valign="middle">
					<th scope="row" style="width:340px"><?php _e('Pages with a timeline:', 'stl_timeline') ?><br /><small><?php _e('Enter the page or post IDs, seperated by comma. Set to 0 or all to include the Timeline script on all pages (default).', 'stl_timeline'); ?></small></th>
					<td><input name="stl_timelinepageids" type="text" id="stl_timelinepageids" value="<?php echo $stl_timelinepageids; ?>" size="30" />
					</td>
					</tr>
					
					<tr valign="middle">
					<th scope="row"><?php _e('Display future posts', 'stl_timeline') ?>:<br /><small><?php _e('Select this option if you want to show future posts in the timeline.', 'stl_timeline'); ?></small></th>
					<td>
					<fieldset>
					<label for="stl_timeline_showfutureposts">
					<input name="stl_timeline_showfutureposts" id="stl_timeline_showfutureposts" value="1" type="checkbox"<?php if($stl_timeline_showfutureposts==1) echo ' checked="checked" '; ?> />
					<?php _e('Display future posts', 'stl_timeline'); ?></label><br />
					</fieldset>
					</td>
					</tr>
					
					<tr valign="middle">
					<th scope="row"><?php _e('Timeline start and end', 'stl_timeline') ?>:<br /><small><?php _e('Sets start and end date for the scroll boundaries of the timeline.', 'stl_timeline'); ?></small></th>
					<td>
					<fieldset>
					<label for="stl_timeline_usestartstop">
					<input name="stl_timeline_usestartstop" id="stl_timeline_usestartstop" value="1" type="checkbox"<?php if($stl_timeline_usestartstop==1) echo ' checked="checked" '; ?> />
					<?php _e('Activate', 'stl_timeline'); ?></label><br />
					<div id="stl_timeline_usestartstop_inputs">
					<label><?php _e('Start date', 'stl_timeline'); ?>:</label>
					<?php WPSimileTimelineAdmin::outputDatepicker('stl_timeline_start', $stl_timeline_start, false, 1); ?>
					<br />
					<label><?php _e('End date', 'stl_timeline'); ?>:&nbsp;</label>
					<?php WPSimileTimelineAdmin::outputDatepicker('stl_timeline_stop', $stl_timeline_stop, false, 1); ?>
					</div>
					</fieldset>
					</td>
					</tr>
					
					<tr valign="middle">
					<th scope="row"><?php _e('Center timeline', 'stl_timeline') ?>:<br /><small><?php _e('Define where the timeline should be focused on load.<br />The default publish date will be used if the start or end date isn\'t set.', 'stl_timeline'); ?></small></th>
					<td>
					<fieldset>
					<label for="stl_timeline_startdate_cd">
					<input name="stl_timeline_startdate" id="stl_timeline_startdate_cd" value="0" type="radio"<?php if($stl_timeline_startdate==0) echo ' checked="checked" '; ?> />
					<?php _e('Current date', 'stl_timeline'); ?></label><br />
					<label for="stl_timeline_startdate_fp">
					<input name="stl_timeline_startdate" id="stl_timeline_startdate_fp" value="1" type="radio"<?php if($stl_timeline_startdate==1) echo ' checked="checked" '; ?> />
					<?php _e('Start date of first post', 'stl_timeline'); ?></label><br />
					<label for="stl_timeline_startdate_lp">
					<input name="stl_timeline_startdate" id="stl_timeline_startdate_lp" value="2" type="radio"<?php if($stl_timeline_startdate==2) echo ' checked="checked" '; ?> />
					<?php _e('End date of last post', 'stl_timeline'); ?></label><br />
					<label for="stl_timeline_startdate_ct">
					<input name="stl_timeline_startdate" id="stl_timeline_startdate_ct" value="3" type="radio"<?php if($stl_timeline_startdate==3) echo ' checked="checked" '; ?> />
					<?php _e('Center between first and last post', 'stl_timeline'); ?></label><br />
					<?php /* TODO: Custom focus date 
					<input name="stl_timeline_startdate" id="stl_timeline_startdate_sp" value="-1" type="radio"<?php if($stl_timeline_startdate < 0 || $stl_timeline_startdate>3) echo ' checked="checked" '; ?> />
					<label><?php _e('Specific', 'stl_timeline'); ?></label>:
					<input name="stl_timeline_startdate_custom" type="text" id="stl_timeline_startdate" value="<?php echo $stl_timeline_startdate; ?>" size="20" />
					*/ ?>
					</fieldset>
					</td>
					</tr>
					
					<tr valign="middle">
					<th scope="row"><?php _e('Image attachments (experimental)', 'stl_timeline') ?>:<br /><small><?php _e('Select how images that are attached to posts should be handled. Makes experimental use of the CompactPainter', 'stl_timeline'); ?></small></th>
					<td>
					<fieldset>
					<label for="stl_timeline_useattachments0">
					<input name="stl_timeline_useattachments" id="stl_timeline_useattachments0" value="0" type="radio"<?php if($stl_timeline_useattachments==0) echo ' checked="checked" '; ?> />
					<?php _e('Do nothing', 'stl_timeline'); ?></label><br />
					<label for="stl_timeline_useattachments1">
					<input name="stl_timeline_useattachments" id="stl_timeline_useattachments1" value="1" type="radio"<?php if($stl_timeline_useattachments==1) echo ' checked="checked" '; ?> />
					<?php _e('Display attached image in timeline', 'stl_timeline'); ?></label><br />
					<label for="stl_timeline_useattachments2">
					<input name="stl_timeline_useattachments" id="stl_timeline_useattachments2" value="2" type="radio"<?php if($stl_timeline_useattachments==2) echo ' checked="checked" '; ?> />
					<?php _e('Display attached image in bubble', 'stl_timeline'); ?></label><br />
					</fieldset>
					</td>
					</tr>
					
					<tr valign="middle">
					<th scope="row"><?php _e('Link Handling', 'stl_timeline') ?>:<br /><small><?php _e('Sets whether clicking links from the timeline will open an info bubble or jump directly to the linked post.', 'stl_timeline'); ?></small></th>
					<td>
					<fieldset>
					<label for="stl_timeline_handlelink_sb">
					<input name="stl_timeline_linkhandling" id="stl_timeline_handlelink_sb" value="0" type="radio"<?php if($stl_timeline_linkhandling==0) echo ' checked="checked" '; ?> />
					<?php _e('Show info bubble', 'stl_timeline'); ?></label><br />
					<label for="stl_timeline_handlelink_dl">
					<input name="stl_timeline_linkhandling" id="stl_timeline_handlelink_dl" value="1" type="radio"<?php if($stl_timeline_linkhandling==1) echo ' checked="checked" '; ?> />
					<?php _e('Jump to link location', 'stl_timeline'); ?></label>
					</fieldset>
					</td>
					</tr>

					<tr valign="middle">
					<th scope="row"><?php _e('Info bubble', 'stl_timeline') ?>:<br /><small><?php _e('Select this option if you want to include the event date inside the info bubble.', 'stl_timeline'); ?></small></th>
					<td>
					<fieldset>
					<label for="stl_timeline_showbubbledate">
					<input name="stl_timeline_showbubbledate" id="stl_timeline_showbubbledate" value="1" type="checkbox"<?php if($stl_timeline_showbubbledate==1) echo ' checked="checked" '; ?> />
					<?php _e('Display event date in popup bubble', 'stl_timeline'); ?></label><br />
					</fieldset>
					</td>
					</tr>

					</table>
					<p class="submit"><input type="submit" class="button-primary" name="Submit" value="<?php _e('Update Options', 'stl_timeline') ?>" /></p>
				</fieldset>
	            </div>
	<!--
	******************************************************
	Timeline Categories
	******************************************************
	-->
				<div id="stl-content">
					<fieldset class="options">
					<h3><?php _e('Categories', 'stl_timeline'); ?></h3>
						<p><small><?php printf(__('Select the categories the Timeline should display posts from.','stl_timeline') ); ?></small></p>
						<?php WPSimileTimelineAdmin::outputTermsTable('category'); ?>
						<p class="submit"><input type="submit" class="button-primary" name="Submit" value="<?php _e('Update Options', 'stl_timeline') ?>" /></p>
					</fieldset>
					
					<fieldset class="options">
					<h3><?php _e('Tags', 'stl_timeline'); ?></h3>
						<p><small><?php printf(__('Select the tags the Timeline should display posts from.','stl_timeline') ); ?></small></p>
						<?php WPSimileTimelineAdmin::outputTermsTable('post_tag'); ?>
						<p class="submit"><input type="submit" class="button-primary" name="Submit" value="<?php _e('Update Options', 'stl_timeline') ?>" /></p>
					</fieldset>
					
					<?php // custom taxonomies
					foreach($custom_taxonomies as $custom_taxonomy):
					?>
					<fieldset class="options">
					<h3><?php _e($custom_taxonomy->labels->name); ?></h3>
						<p><small><?php printf(__('Select the tags the Timeline should display posts from.','stl_timeline') ); ?></small></p>
						<?php /* TODO: Name or query_var? */ WPSimileTimelineAdmin::outputTermsTable($custom_taxonomy->name); ?>
						<p class="submit"><input type="submit" class="button-primary" name="Submit" value="<?php _e('Update Options', 'stl_timeline') ?>" /></p>
					</fieldset>
					<?php endforeach; ?>
				</div>
	<!--
	******************************************************
	Timeline Appearance
	******************************************************
	-->		
				<div id="stl-design">
				<fieldset class="options">
				<h3><?php _e('Timeline Appearance', 'stl_timeline'); ?></h3>
					<p><?php printf(__('These options define the default timeline theme. To use a custom theme, use the parameter %s with the shortcode or template tag.', 'stl_timeline'), '<code>theme</code>'); ?></p>
					
					<?php foreach($stl_bands as $band): ?>
					<?php echo WPSimileTimelineAdmin::outputOptionsForBand($band); ?>
					<input type="submit" class="button-primary" name="Submit" value="<?php _e('Update Options', 'stl_timeline') ?>" />
					<br /><br />
					<?php endforeach; ?>
					
					<?php
					$newband = new WPSimileTimelineBand();
					$newband->set(array(
						'id' => 'new',
						'name' => ''
					));
					#echo WPSimileTimelineAdmin::outputOptionsForBand($newband); ?>
					
				</fieldset>
				</div>
	<!--
	******************************************************
	Uninstall
	******************************************************
	-->
		<div id="stl-uninstall">
			<fieldset class="options">
				<h3><?php _e('Uninstall plugin', 'stl_timeline') ?></h3>
					<p>
					<?php
					_e("With this option you can remove all database entries related to this plugin.", 'stl_timeline');
					echo '<br />';
					_e("This might also work as re-initialization in case of unexpected behavior.", 'stl_timeline');
					echo '<br />';
					_e("The plugin will be deactivated after all plugin-related data has been removed.", 'stl_timeline');
					?></p>
					<p><?php _e("Sorry to hear that you have to do that for whatever reasons.", 'stl_timeline'); ?></p>
	
					<p>
					<input type="submit" id="stl-delete-plugindata-button" value="<?php _e('Delete Plugin Data', 'stl_timeline'); ?>" class="button-secondary" />
					<input type="checkbox" name="delete-action" id="stl-timeline-delete-confirm" value="purgedb" /> <label for="stl-timeline-delete-confirm"><?php _e('Yes, please purge plugin database entries', 'stl_timeline'); ?></label>
					</p>
					<script type="text/javascript">
						jQuery(document).ready( function($){
							// Delete Database table user check
							$('#stl-delete-plugindata-button').click(function(){
								if($('#stl-timeline-delete-confirm').attr('checked')){
									if ( confirm('<?php _e('You are about to delete all timeline settings and selected categories.\nDo you really want that?', 'stl_timeline'); ?>') ){
										return true;
									}
								}
								else{
									alert('<?php _e('For security reasons, you have to confirm the deletion of plugin-related database entries.', 'stl_timeline'); ?>');
								}
								return false;
							});
						});
					</script>
			</fieldset>
		</div>	
	<!--
	******************************************************
	******************************************************
	-->
		</form>
		</div><!-- #stl-stimeline-options-container -->
		</div><!-- .wrap -->
	<?php
	}
	
	/* ---------------------------------------------------------------------------------
	     Output option interface for Timeline bands
	 * --------------------------------------------------------------------------------*/
	function outputOptionsForBand($band){
		$show_labels_checked = '';
		if($band->show_labels == 1):
			$show_labels_checked = 'checked="checked"';
		endif;
		?>
		<table width="100%" class="widefat">
		<thead>
			<tr>
			<th><h3 class="stl-collapsible-handle"><?php echo $band->name; ?></h3><div class="handlediv"></div>
			<input type="hidden" value="<?php echo $band->id; ?>" name="<?php echo "stl_timeline[bands][$band->id][id]"; ?>" />
			</th>
			</tr>
		</thead>
		<tbody class="stl-collapsible">
			<tr><td class="nofoot bandoptions">
				<div class="inside">
				<table width="100%">
				<thead>
					<tr>
					<th colspan="2"><h3 class="stl-collapsible-handle"><?php _e('Attributes', 'stl_timeline'); ?></h3>
					</th>
					</tr>
				</thead>
				<tbody class="stl-collapsible">
				<tr>
						<td><label><?php _e('Name', 'stl_timeline'); ?></label></td>
						<td><input name="<?php echo "stl_timeline[bands][$band->id][name]"; ?>" type="text" id="<?php echo "stl_timelineband_name_".$band->id; ?>" value="<?php echo $band->name; ?>" size="20" />
						</td>
				</tr>
				<tr class="alternate">
						<td><label><?php _e('Height in % or px (add unit)', 'stl_timeline'); ?></label></td>
						<td><input name="<?php echo "stl_timeline[bands][$band->id][height]"; ?>" type="text" id="<?php echo "stl_timelineband_height_".$band->id; ?>" value="<?php echo $band->height; ?>" size="10" /></td>
				</tr>
				<tr>
					<td><label><?php _e('Resolution', 'stl_timeline'); ?></label></td>
					<td>
						<?php echo WPSimileTimelineAdmin::outputUnitSelectElement("stl_timeline[bands][$band->id][unit]", "stl_timelineband_unit_".$band->id, $band->unit); ?>
					</td>
				</tr>
				<tr class="alternate">
					<td><label><?php _e('Interval size', 'stl_timeline'); ?></label></td>
					<td><input name="<?php echo "stl_timeline[bands][$band->id][interval_size]"; ?>" type="text" id="<?php echo "stl_timelineband_interval_".$band->id; ?>" value="<?php echo $band->interval_size; ?>" size="10" /> px</td>
				</tr>
				<tr>
					<td><label><?php _e('Marker color', 'stl_timeline'); ?></label></td>
					<td><?php echo WPSimileTimelineAdmin::buildColorpickInput("stl_timeline[bands][$band->id][ether_highlight_color]",$band->id,'stl_band'.$band->id.'_cp_ethighlight',$band->ether_highlight_color); ?></td>
				</tr>
				<tr>
					<td><label><?php _e('Background color', 'stl_timeline'); ?></label></td>
					<td><?php echo WPSimileTimelineAdmin::buildColorpickInput("stl_timeline[bands][$band->id][bg_color]",$band->id,'stl_band'.$band->id.'_cp_bg',$band->bg_color); ?></td>
				</tr>
				<tr class="alternate">
					<td><label><?php _e('Interval line color', 'stl_timeline'); ?></label></td>
					<td><?php echo WPSimileTimelineAdmin::buildColorpickInput("stl_timeline[bands][$band->id][interval_color]",$band->id,'stl_band'.$band->id.'_cp_intline',$band->interval_color); ?></td>
				</tr>
				<tr class="alternate">
					<td><label><?php _e('Label color for Highlight Decorator', 'stl_timeline'); ?></label></td>
					<td><?php echo WPSimileTimelineAdmin::buildColorpickInput("stl_timeline[bands][$band->id][highlight_label_color]",$band->id,'stl_band'.$band->id.'_highlight_label_color',$band->highlight_label_color); ?></td>
				</tr>
				<tr class="nofoot">
					<td><label for="<?php echo "stl_band_showlabels_".$band->id; ?>"><?php _e('Show Labels', 'stl_timeline'); ?></label></td>
					<td><input type="checkbox" value="1" name="<?php echo "stl_timeline[bands][$band->id][show_labels]"; ?>" id="<?php echo "stl_band_showlabels_".$band->id; ?>" <?php echo $show_labels_checked; ?> /></td>
				</tr>
				</tbody>
				</table>
				
				<!--
				******************************************************
				Hotzones
				******************************************************
				-->
				<table width="100%">
				<thead>
					<tr>
					<th colspan="5"><h3 class="stl-collapsible-handle"><?php echo __('Hotzones', 'stl_timeline') . ' ('.sizeof($band->hotzones).')'; ?></h3>
					</th>
					</tr>
				</thead>
				<tbody class="stl-collapsible">
				<?php if(!empty($band->hotzones)):
				foreach($band->hotzones as $hotzone):
					WPSimileTimelineAdmin::outputHotzoneMarkup($band->id, $hotzone);
				endforeach;
				endif; ?>

				<?php WPSimileTimelineAdmin::outputHotzoneMarkup($band->id); ?>
				
				<tr class="nofoot stl-addrow">
					<td colspan="5"><input type="button" value="<?php _e('Add new Hotzone', 'stl_timeline'); ?>" class="stl-addsubentry button-secondary" />
					</td>
				</tr>
				</tbody>
				</table>
				<!--
				******************************************************
				Highlight Decorators
				******************************************************
				-->
				<table width="100%">
				<thead>
					<tr>
					<th colspan="5"><h3 class="stl-collapsible-handle"><?php echo __('Highlight Decorators', 'stl_timeline') . ' ('.sizeof($band->decorators).')'; ?></h3>
					</th>
					</tr>
				</thead>
				<tbody class="stl-collapsible">
				<?php
				// List existing decorators *********************************** 
				if(!empty($band->decorators)):
					foreach($band->decorators as $decorator):
						WPSimileTimelineAdmin::outputDecoratorMarkup($band->id, $decorator);
					endforeach;
				endif; ?>
				
				<?php
				// Construct for new decorator ********************************
				WPSimileTimelineAdmin::outputDecoratorMarkup($band->id);
				?>
				
				<tr class="nofoot stl-addrow">
					<td colspan="5"><input type="button" value="<?php _e('Add new Decorator', 'stl_timeline'); ?>" class="stl-addsubentry button-secondary" />
					</td>
				</tr>
	
				</tbody>
				</table>
				<!-- -->
				</div>
			</td></tr>
			</tbody>
		</table>
		<?php
	}
	
	/**
	 * Output markup for a hotzone object
	 * @param band_id Database ID of the related band object, default: new
	 * @param hotzone Hotzone object that should be printed
	 */	
	function outputHotzoneMarkup($band_id='new', $hotzone=null){
		if($hotzone == null){
			$hotzone = new WPSimileTimelineHotzone();
			$hotzone->create(array(
				'id' => 'new',
				'name' => '',
				'unit' => 3,
				'start_date' => adodb_date2('Y-m-d H:i:s'),
				'end_date' => adodb_date2('Y-m-d H:i:s'),
				'magnify' => '1',
				'multiple' => '1'
			));
		}
	?>
	<tr<?php if( $hotzone->id == 'new' ) echo ' class="stl-newentry"'; ?>>
		<td>
		<?php
		if( $hotzone->id != 'new' ){
		?>
		<h4 class="stl-suboption-handle"><?php echo $hotzone->name; ?></h4>
		<div class="stl-delete-link">
			<a id="stl-delete-hotzone<?php echo $hotzone->id; ?>" href="<?php echo wp_nonce_url('admin.php?page=wp-simile-timeline&amp;action=delete-hotzone&amp;id='.$hotzone->id.'#stl-design', STL_TIMELINE_NONCE_NAME); ?>"><?php _e('Delete', 'stl_timeline'); ?></a>
			<script type="text/javascript">
			jQuery(document).ready( function($){
				$('#stl-delete-hotzone<?php echo $hotzone->id; ?>').click(function(){
					if ( confirm('<?php _e('Do you really want to delete this hotzone?', 'stl_timeline'); ?>') ){
						return true;
					}
					return false;
				});
			});
			</script>
		</div>
		<?php 
		}else{
		?>
		<h4><?php _e('New Hotzone', 'stl_timeline'); ?></h4>
		<?php } ?>
		<div class="suboptions">
		<input type="hidden" value="<?php echo $hotzone->id; ?>" name="<?php echo "stl_timeline[bands][$band_id][hotzones][$hotzone->id][id]"; ?>" />
		<input type="hidden" value="<?php echo $band_id; ?>" name="<?php echo "stl_timeline[bands][$band_id][hotzones][$hotzone->id][stl_timeline_band_id]"; ?>" />
		
		<label for="<?php echo "stl_timelinehotzone_name_".$band_id.$hotzone->id; ?>"><?php _e('Name', 'stl_timeline'); ?></label>
		<input type="text" value="<?php echo $hotzone->name; ?>" id="<?php echo "stl_timelinehotzone_name_".$band_id.$hotzone->id; ?>" name="<?php echo "stl_timeline[bands][$band_id][hotzones][$hotzone->id][name]"; ?>" />
		<br />
		<label for="<?php echo "stl_timelinehotzone_unit_".$band_id.$hotzone->id; ?>"><?php _e('Resolution', 'stl_timeline'); ?></label>
		<?php echo WPSimileTimelineAdmin::outputUnitSelectElement("stl_timeline[bands][$band_id][hotzones][$hotzone->id][unit]", "stl_timelinehotzone_unit_".$band_id.$hotzone->id, $hotzone->unit); ?>
		<br />
		<label><?php _e('Start Date', 'stl_timeline'); ?></label>
		<?php WPSimileTimelineAdmin::outputDatepicker("stl_timeline[bands][$band_id][hotzones][$hotzone->id][start_date]", $hotzone->start_date, false); ?>
		<br />
		<label><?php _e('End Date', 'stl_timeline'); ?></label>
		<?php WPSimileTimelineAdmin::outputDatepicker("stl_timeline[bands][$band_id][hotzones][$hotzone->id][end_date]", $hotzone->end_date, false); ?>
		<br />
		<label for="<?php echo "stl_timelinehotzone_magnify_".$band_id.$hotzone->id; ?>"><?php _e('Magnify', 'stl_timeline'); ?></label>
		<input type="text" size="3" value="<?php echo $hotzone->magnify; ?>" id="<?php echo "stl_timelinehotzone_magnify_".$band_id.$hotzone->id; ?>" name="<?php echo "stl_timeline[bands][$band_id][hotzones][$hotzone->id][magnify]"; ?>" />
		<br />
		<label for="<?php echo "stl_timelinehotzone_multiple_".$band_id.$hotzone->id; ?>"><?php _e('Multiple', 'stl_timeline'); ?></label>
		<input type="text" size="3" value="<?php echo $hotzone->multiple; ?>" id="<?php echo "stl_timelinehotzone_multiple_".$band_id.$hotzone->id; ?>" name="<?php echo "stl_timeline[bands][$band_id][hotzones][$hotzone->id][multiple]"; ?>" />
		<small>(<?php _e('show every n-th unit marker', 'stl_timeline'); ?></small>
		</div>
		
		</td>
	</tr>
	<?php
	}
	
	/**
	 * Output markup for a decorator object
	 * @param band_id Database ID of the related band object, default: new
	 * @param decorator Decorator object that should be printed
	 */
	function outputDecoratorMarkup($band_id='new', $decorator=null){
		if($decorator == null){
			$decorator = new WPSimileTimelineDecorator();
			$decorator->create(array(
				'id' => 'new',
				'name' => '',
				'type' => 1,
				'start_date' => adodb_date2('Y-m-d H:i:s'),
				'end_date' => adodb_date2('Y-m-d H:i:s'),
				'start_label' => '',
				'end_label' => '',
				'css_opacity' => '70',
				'color' => '#aaaaaa'
			));
		}
	?>
	<tr<?php if( $decorator->id == 'new' ) echo ' class="stl-newentry"'; ?>>
		<td>
		<?php
		if( $decorator->id != 'new' ){
		?>
		<h4 class="stl-suboption-handle"><?php echo $decorator->name; ?></h4>
		<div class="stl-delete-link">
			<a id="stl-delete-decorator<?php echo $decorator->id; ?>" href="<?php echo wp_nonce_url('admin.php?page=wp-simile-timeline&amp;action=delete-decorator&amp;id='.$decorator->id.'#stl-design', STL_TIMELINE_NONCE_NAME); ?>"><?php _e('Delete', 'stl_timeline'); ?></a>
			<script type="text/javascript">
			jQuery(document).ready( function($){
				$('#stl-delete-decorator<?php echo $decorator->id; ?>').click(function(){
					if ( confirm('<?php _e('Do you really want to delete this decorator?', 'stl_timeline'); ?>') ){
						return true;
					}
					return false;
				});
			});
			</script>
		</div>
		<?php 
		}else{
		?>
		<h4><?php _e('New Highlight Decorator', 'stl_timeline'); ?></h4>
		<?php } ?>
		
		<div class="suboptions">
		<input type="hidden" value="<?php echo $decorator->id; ?>" name="<?php echo "stl_timeline[bands][$band_id][decorators][$decorator->id][id]"; ?>" />
		<input type="hidden" value="<?php echo $band_id; ?>" name="<?php echo "stl_timeline[bands][$band_id][decorators][$decorator->id][stl_timeline_band_id]"; ?>" />
		
			<div>
				<label for="<?php echo "stl_timelinedecorator_name_".$band_id.$decorator->id; ?>"><?php _e('Name', 'stl_timeline'); ?></label>
				<input type="text" value="<?php echo $decorator->name; ?>" id="<?php echo "stl_timelinedecorator_name_".$band_id.$decorator->id; ?>" name="<?php echo "stl_timeline[bands][$band_id][decorators][$decorator->id][name]"; ?>" />
			</div>
			<div>
				<label><?php _e('Type', 'stl_timeline'); ?></label>
				<?php WPSimileTimelineAdmin::outputDecoratorTypeSelect("stl_timeline[bands][$band_id][decorators][$decorator->id][type]", $decorator->type); ?>
			</div>
			<div>
				<label><?php _e('Start Date', 'stl_timeline'); ?></label>
				<?php WPSimileTimelineAdmin::outputDatepicker("stl_timeline[bands][$band_id][decorators][$decorator->id][start_date]", $decorator->start_date, false); ?>
			</div>
			<div class="stl_timeline_decorator_optional"<?php if($decorator->type==0) echo ' style="display:none;"'; ?>>
				<label><?php _e('End Date', 'stl_timeline'); ?></label>
				<?php WPSimileTimelineAdmin::outputDatepicker("stl_timeline[bands][$band_id][decorators][$decorator->id][end_date]", $decorator->end_date, false); ?>
			</div>
			<div class="stl_timeline_decorator_optional"<?php if($decorator->type==0) echo ' style="display:none;"'; ?>>
				<label for="<?php echo "stl_timelinedecorator_startlabel_".$band_id.$decorator->id; ?>"><?php _e('Start Label', 'stl_timeline'); ?></label>
				<input type="text" value="<?php echo $decorator->start_label; ?>" id="<?php echo "stl_timelinedecorator_startlabel_".$band_id.$decorator->id; ?>" name="<?php echo "stl_timeline[bands][$band_id][decorators][$decorator->id][start_label]"; ?>" />
			</div>
			<div class="stl_timeline_decorator_optional"<?php if($decorator->type==0) echo ' style="display:none;"'; ?>>
				<label for="<?php echo "stl_timelinedecorator_endlabel_".$band_id.$decorator->id; ?>"><?php _e('End Label', 'stl_timeline'); ?></label>
				<input type="text" value="<?php echo $decorator->end_label; ?>" id="<?php echo "stl_timelinedecorator_endlabel_".$band_id.$decorator->id; ?>" name="<?php echo "stl_timeline[bands][$band_id][decorators][$decorator->id][end_label]"; ?>" />
			</div>
			<div>
				<label for="<?php echo "stl_timelinedecorator_cssclass_".$band_id.$decorator->id; ?>"><?php _e('CSS class', 'stl_timeline'); ?></label>
				<input type="text" value="<?php echo $decorator->css_class; ?>" id="<?php echo "stl_timelinedecorator_cssclass_".$band_id.$decorator->id; ?>" name="<?php echo "stl_timeline[bands][$band_id][decorators][$decorator->id][css_class]"; ?>" />
			</div>
			<div>
				<label><?php _e('Color', 'stl_timeline'); ?></label>
				<?php echo WPSimileTimelineAdmin::buildColorpickInput("stl_timeline[bands][$band_id][decorators][$decorator->id][color]",$band_id,'stl_band'.$band_id.$decorator->id.'_color',$decorator->color); ?>
			</div>
			<div>
				<label for="<?php echo "stl_timelinedecorator_opacity_".$band_id.$decorator->id; ?>"><?php _e('Opacity', 'stl_timeline'); ?></label>
				<input type="text" size="3" value="<?php echo $decorator->opacity; ?>" id="<?php echo "stl_timelinedecorator_opacity_".$band_id.$decorator->id; ?>" name="<?php echo "stl_timeline[bands][$band_id][decorators][$decorator->id][opacity]"; ?>" />
			</div>
		</div>
		
		</td>
	</tr>
	<?php
	}
}