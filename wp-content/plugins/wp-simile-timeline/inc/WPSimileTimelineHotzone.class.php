<?php
/**
 * Description: For managing relations and database operations of a timeline hotzone
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
class WPSimileTimelineHotzone{
	
	var $id;
	var $name;
	var $unit;
	var $start_date;
	var $end_date;
	var $magnify;
	var $multiple;
	
	var $table_name = "stl_timeline_hotzones";
	
	function WPSimileTimelineHotzone(){
		// empty constructor
	}
	
	/*
	 * Installs table for Timeline hotzones
	 */
	function createTable(){
			
		global $wpdb;
		
		$hotzone_object = new WPSimileTimelineHotzone();
		$table_name = $wpdb->prefix . $hotzone_object->table_name;
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	
			$sql = "CREATE TABLE " . $table_name . " (
				  `id` int(11) NOT NULL auto_increment,
				  `name` varchar(210) NOT NULL,
				  `stl_timeline_band_id` int(11) NOT NULL,
				  `start_date` datetime NOT NULL,
				  `end_date` datetime NOT NULL,
				  `magnify` int(11) NOT NULL,
				  `multiple` int(11) NOT NULL,
				  `unit` int(11) NOT NULL,
				  PRIMARY KEY  (`id`)
				);";
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
	   }
	}
	
	function create($data){
            foreach($data as $key=>$value){
                    $this->{$key} = $value;
            }
            if(is_array($data)){
                if(is_array($data['start_date']) && sizeof($data['start_date']) > 1){
                    $ds1 = $data['start_date']['year'].'-'.$data['start_date']['month'].'-'.$data['start_date']['day'].' '.$data['start_date']['hour'].':'.$data['start_date']['minute'].':'.$data['start_date']['second'];
                    $ds2 = $data['end_date']['year']  .'-'.$data['end_date']['month']  .'-'.$data['end_date']['day']  .' '.$data['end_date']['hour']  .':'.$data['end_date']['minute']  .':'.$data['end_date']['second'];
                }
                else{
                    $ds1 = $data['start_date'];
                    $ds2 = $data['end_date'];
                }
                $this->start_date = adodb_date2('Y-m-d H:i:s', $ds1);
                $this->end_date = adodb_date2('Y-m-d H:i:s', $ds2);
            }
	}
	
	/**
	 * Saves to DB
	 */
	function save(){
		global $wpdb;

		if(isset($this->id) && $this->id != 'new'){
			// update existing hotzone
			$query = "UPDATE ".$wpdb->prefix. $this->table_name .
			" SET name=%s, unit=%d,
			start_date=%s,
			end_date=%s,
			magnify=%d,
			multiple=%d
			WHERE id=%d";
			$wpdb->query($wpdb->prepare($query, $this->name, $this->unit, $this->start_date, $this->end_date, $this->magnify, $this->multiple, $this->id));
		}
		else{
			// insert new hotzone entry
			$wpdb->insert(
				$wpdb->prefix. $this->table_name,
				array(
					'name' => $this->name,
					'unit' => $this->unit,
					'start_date' => $this->start_date,
					'end_date' => $this->end_date,
					'magnify' => $this->magnify,
					'multiple' => $this->multiple,
					'stl_timeline_band_id' => $this->stl_timeline_band_id
				),
				array('%s','%d','%s','%s','%d','%d','%d')
			);
		}
	}
	
	function delete($id){
		global $wpdb;

		if(isset($id)){
			$query = $wpdb->prepare('DELETE FROM '. $wpdb->prefix. $this->table_name .' WHERE id=%d', $id);
			$wpdb->query($query);
		}
	}
	
	
	function read($id) {
		global $wpdb;

		$table_name = $wpdb->prefix .  $this->table_name;

		$get = "SELECT * FROM " . $table_name .  " WHERE stl_timeline_band_id = %d";
		$res = $wpdb->get_results($wpdb->prepare($get, $id));
		
		$h = array();
		foreach($res as $r){
			$hotzone_object = new WPSimileTimelineHotzone();
			$hotzone_object->create($r);
		 	$h[] = $hotzone_object;
		}
		return $h;
	}

}
?>