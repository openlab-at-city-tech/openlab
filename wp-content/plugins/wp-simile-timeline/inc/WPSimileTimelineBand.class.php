<?php
/**
 * Description: For managing relations and database operations of a timeline band
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
class WPSimileTimelineBand{
	
	var $id;
	var $name;
	var $height;
	var $unit;
	var $interval_size;
	var $bg_color;
	var $interval_color;
	var $ether_highlight_color;
	var $highlight_label_color;
	var $show_labels;

	var $hotzones;
	var $decorators;
	
	var $table_name = "stl_timeline_bands";
	
	function __construct(){
		/* empty constructor */
	}
	
	/* ---------------------------------------------------------------------------------
	 * install table for Timeline bandds
	 * --------------------------------------------------------------------------------*/
	function createTable(){
			
		global $wpdb;
	
		$table_name = $wpdb->prefix . "stl_timeline_bands";
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	
			$sql = "CREATE TABLE " . $table_name . " (
				  `id` int(11) NOT NULL auto_increment,
				  `name` varchar(210) NOT NULL,
				  `height` varchar(10) NOT NULL,
				  `unit` int(11) NOT NULL,
				  `interval_size` int(11) NOT NULL,
				  `bg_color` varchar(7) NOT NULL,
				  `interval_color` varchar(7) NOT NULL,
				  `ether_highlight_color` varchar(7) NOT NULL,
				  `highlight_label_color` varchar(7) NOT NULL,
				  `show_labels` int(11) NOT NULL,
				  PRIMARY KEY  (`id`)
				);";
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			
			// fill with standard timeline bands
			$insert = "INSERT INTO " . $table_name . " (name, height, unit, interval_size, bg_color, interval_color, ether_highlight_color, highlight_label_color, show_labels) " .
				  	  "VALUES (%s, %s, %d, %d, %s, %s, %s, %s, %d)";
			$wpdb->query($wpdb->prepare($insert, 'Band 1', '70%', 4, 200, '#eee', '#aaa', '#aaa', '#444', 1));
			$wpdb->query($wpdb->prepare($insert, 'Band 2', '30%', 6, 100, '#ddd', '#aaa', '#aaa', '#444', 0));
	   }
	}

	function set($data){
		foreach($data as $key=>$value){
			$this->{$key} = $value;
		}
	}

	/**
	 * Saves to DB
	 */
	function save(){
		global $wpdb;
		
		// Update existing band (id is set)
		if(isset($this->id) && $this->id != 'new'){
			$query = "UPDATE ".$wpdb->prefix. $this->table_name .
			" SET name=%s, height=%s, unit=%d,
			interval_size=%d,
			bg_color=%s,
			interval_color=%s,
			ether_highlight_color=%s,
			highlight_label_color=%s,
			show_labels=%d
			WHERE id=%d";
			// save band
			$wpdb->query(
				$wpdb->prepare(
					$query,
					$this->name,
					$this->height,
					$this->unit,
					$this->interval_size,
					$this->bg_color,
					$this->interval_color,
					$this->ether_highlight_color,
					$this->highlight_label_color,
					$this->show_labels,
					$this->id
			));
		}
		// Insert new band
		else{
			$wpdb->insert(
				$wpdb->prefix. $this->table_name,
				array(
					'name' => $this->name,
					'height' => $this->height,
					'unit' => $this->unit,
					'bg_color' => $this->bg_color,
					'interval_size' => $this->interval_size,
					'interval_color' => $this->interval_color,
					'ether_highlight_color' => $this->ether_highlight_color,
					'highlight_label_color' => $this->highlight_label_color,
					'show_labels' => $this->show_labels
				),
				array(
					'%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d'
				)
			);
		}
		
		// save related hotzones
		if(!empty($this->hotzones)){
			foreach($this->hotzones as $hotzone){
				if($hotzone['stl_timeline_band_id'] == 'new'){
					$hotzone['stl_timeline_band_id'] = $wpdb->insert_id; 
				}
				if(!empty($hotzone['name'])){
					$hotzone_object = new WPSimileTimelineHotzone();
					$hotzone_object->create($hotzone);
					$hotzone_object->save();
				}
			}
		}
		// save related decorators
		if(!empty($this->decorators)){
			foreach($this->decorators as $decorator){
				if($decorator['stl_timeline_band_id'] == 'new'){
					$decorator['stl_timeline_band_id'] = $wpdb->insert_id; 
				}
				if(!empty($decorator['name'])){
					$decorator_obj = new WPSimileTimelineDecorator();
					$decorator_obj->create($decorator);
					$decorator_obj->save();
				}
			}
		}
	}
	
	/**
	 * Read from DB
	 */
	function read($id) {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->table_name;

		$get = "SELECT * FROM " . $table_name . " WHERE id=$id";
		$res = $wpdb->get_row($get);
		return $res;
	}
	
	/**
	 * Delete band and related hotzones and decorators
	 */
	function delete($id){
		
	}

	/**
	 * Get all timeline bands and associated extras from DB
	 */
	function find_all($fields='*') {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->table_name;
		$stl_timeline_hotzone = new WPSimileTimelineHotzone();
		$stl_timeline_decorator = new WPSimileTimelineDecorator();
		$get = "SELECT $fields FROM " . $table_name;
		$res = $wpdb->get_results($get);
		for($i=0;$i<sizeof($res); $i++){
			$res[$i]->hotzones = $stl_timeline_hotzone->read($res[$i]->id);
			$res[$i]->decorators = $stl_timeline_decorator->read($res[$i]->id);
		}
		return $res;
	}
}
?>