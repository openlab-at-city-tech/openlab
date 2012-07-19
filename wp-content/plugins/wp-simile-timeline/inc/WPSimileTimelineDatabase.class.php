<?php
/**
 * WPSimileTimelineDatabase.class.php
 * Description: Database functions for the SIMILE Timline Plugin
 * Plugin URI: http://wordpress.org/extend/plugins/wp-simile-timeline/
 * Author: Tim Isenheim
 * 
	===========================================================================
	SIMILE Timeline for WordPress
	Copyright (C) 2009 Tim Isenheim
	
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
class WPSimileTimelineDatabase{
	function WPSimileTimelineDatabase(){
		
	}
	
	function doUpdates(){
		global $wpdb;
		
		$terms_table = WPSimileTimelineTerm::getTableName();
		$column_name = 'icon';
		// Add column for icon in terms table
		$wpdb->query($wpdb->prepare("ALTER TABLE $terms_table ADD COLUMN " . $column_name . " VARCHAR( 255 ) NOT NULL AFTER `color`"));
	}
	
	/* ---------------------------------------------------------------------------------
	 * checks if a given column exists in a database table
	 * --------------------------------------------------------------------------------*/
	function columnExists($col_name){
		global $wpdb;
		$column_exists = false;
		$q = $wpdb->query($wpdb->prepare("SHOW COLUMNS FROM $wpdb->posts LIKE '$col_name'"));
		if($q == 1){
			$column_exists = true;
		}
		return $column_exists;
	}
	
	/* -------------------------------------------------------------------------
	 * Functions to ADD tables and columns
	 * ----------------------------------------------------------------------*/
	
	/*
	 * Adds extra column in posts table for start and end dates
	 */
	function addEventColumn($column_name){
		global $wpdb;
		if(!WPSimileTimelineDatabase::columnExists($column_name)) {
			$wpdb->query($wpdb->prepare("ALTER TABLE $wpdb->posts ADD COLUMN " . $column_name . " datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"));
		}
	}
	
	/* -------------------------------------------------------------------------
	 * Functions to DELETE tables and columns
	 * ----------------------------------------------------------------------*/

	/*
	 * Delete a table
	 */
	function deleteTable($tn){		
		global $wpdb;
		$table_name = $wpdb->prefix . $tn;
		$wpdb->query($wpdb->prepare("DROP TABLE " . $table_name));
	}

	/*
	 * removes the database column on uninstalling
	 * This deletes all event dates set for posts
	 */
	function removeEventColumn($column_name) {
		global $wpdb;
		if(WPSimileTimelineDatabase::columnExists($column_name)) {
			$wpdb->query($wpdb->prepare("ALTER TABLE $wpdb->posts DROP COLUMN $column_name"));
		}
	}
	
	/*
	 * Save start and end dates of an event(post) to the database
	 */
	function updateEventDates($post_id){
		if(!empty($_POST)){
			WPSimileTimelineDatabase::saveEventDate($post_id, $_POST, 'start');
			WPSimileTimelineDatabase::saveEventDate($post_id, $_POST, 'latest_start');
			WPSimileTimelineDatabase::saveEventDate($post_id, $_POST, 'end');
			WPSimileTimelineDatabase::saveEventDate($post_id, $_POST, 'earliest_end');
		}
	}
	
	/*
	 * Save time value of an event(post) to the database
	 * used by both, start and end update functions
	 */
	function saveEventDate($pID, $p, $type='start'){
		global $wpdb;
		$column = 'stl_timeline_event_'.$type;
		$index = 'stl_timeline_'.$type;
		$edit = isset($p[$index]['edit']) ? $p['stl_timeline_'.$type]['edit'] : 0;
		$reset = isset($p[$index]['reset']) ? $p['stl_timeline_'.$type]['reset'] : 0;
		// if "Edit timestamp" is selected
		if ($edit==1 && $reset==0) {
			$stl_aa = $p[$index]['year'];
			$stl_mm = $p[$index]['month'];
			$stl_jj = $p[$index]['day'];
			$stl_hh = $p[$index]['hour'];
			$stl_mn = $p[$index]['minute'];
			$stl_ss = $p[$index]['second'];
			$stl_jj = ($stl_jj > 31) ? 31 : $stl_jj;
			$stl_hh = ($stl_hh > 23) ? $stl_hh -24 : $stl_hh;
			$stl_mn = ($stl_mn > 59) ? $stl_mn -60 : $stl_mn;
			$stl_ss = ($stl_ss > 59) ? $stl_ss -60 : $stl_ss;
			$postdata = "$stl_aa-$stl_mm-$stl_jj $stl_hh:$stl_mn:$stl_ss";
			$stl_tee = $postdata;
			$wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET $column = '%s' WHERE ID = %d", $stl_tee, $pID));
		}
		// Reset timestamp to 0
		if ($reset==1) {
			$wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET $column = '0000-00-00 00:00:00' WHERE ID = $pID"));
		}
	}
	
	/**
	 * Get the minimum or maximum date of wpdb->posts.$column in $categories
	 */
	function queryEventDate($minmax, $column, $categories){
		global $wpdb;
		
		$relation = "SELECT object_id FROM $wpdb->term_relationships " .
					"INNER JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id AND	$wpdb->term_taxonomy.term_id IN ($categories) )";
					
		$query = 	"SELECT $minmax($wpdb->posts.$column) from $wpdb->posts INNER JOIN ($relation) t1 ON (t1.object_id=$wpdb->posts.ID) " .
					"WHERE $column != '0000-00-00 00:00:00' AND post_status='publish' AND post_type='post'";
	
		return $wpdb->get_var($query);
	}
	
	/* ---------------------------------------------------------------------------------
	 * stl_get_extreme_event_date
	 * Get the date of the very first or last post dependant from the start date
	 * --------------------------------------------------------------------------------*/
	function getMinMaxEventDate($minmax, $type, $format='r', $categories){
		global $wpdb;
		
		$column = 'stl_timeline_event_' . $type;
		
		// try to get the extreme post date from stl-columns
		$date = WPSimileTimelineDatabase::queryEventDate($minmax, $column, $categories);
		
		if(empty($date)){
			$column = 'post_date';
			// get usual post date when start or end date isn't set
			$date = WPSimileTimelineDatabase::queryEventDate($minmax, $column, $categories);
		}
		if($format != null){
			$date = adodb_date2($format, $date);
		}
		return $date;
	}
}
?>