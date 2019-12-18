<?php
/**
 * Description: Class for managing event dates in WordPress posts
 * @author freshlabs
 * @link http://wordpress.org/extend/plugins/wp-simile-timeline/
 * @package wp-simile-timeline
 * 
	===========================================================================
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

class WPSimileTimelinePost {
    
    private static $postEventDates = array(
        'stl_timeline_event_start',
        'stl_timeline_event_latest_start',
        'stl_timeline_event_end',
        'stl_timeline_event_earliest_end'
    );

    /**
     * Constructor
     */
    function __construct() {
        // empty
    }
    
    /**
     * 
     * @return type Array of event types for posts
     */
    function getPostEventTypes(){
        return self::$postEventDates;
    }
    
    /**
     * Install post types in posts database table
     */
    function createColumns(){
        global $wpdb;
        foreach(self::$postEventDates as $column) {
           if(!WPSimileTimelineDatabase::columnExists($wpdb->posts, $column)) {
                $wpdb->query(
                    $wpdb->prepare("ALTER TABLE $wpdb->posts ADD COLUMN $column datetime NOT NULL DEFAULT %s", array("0000-00-00 00:00:00") )
                );
            }
        }
    }
    
    /**
     * removes the database column on uninstalling
     * This deletes all event dates set for posts
     */
    function deleteColumns() {
        global $wpdb;
        foreach(self::$postEventDates as $column) {
            if(WPSimileTimelineDatabase::columnExists($wpdb->posts, $column)) {
                $wpdb->query("ALTER TABLE $wpdb->posts DROP COLUMN $column");
            }
        }
    }

    /**
     * Save time value of an event(post) to the database
     * used by both, start and end update functions
     */
    function saveEventDate($pID, $p, $type='start') {
        global $wpdb;
        // Database column
        $column = 'stl_timeline_event_'.$type;
        // Post array index
        $index  = 'stl_timeline_'.$type;
        // Edit checkbox selected
        $edit   = isset($p[$index]['edit']) ? $p['stl_timeline_'.$type]['edit'] : 0;
        $reset  = isset($p[$index]['reset']) ? $p['stl_timeline_'.$type]['reset'] : 0;
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
                
                // Format years before 1000 to four digits with leading zeroes
                #$stl_aa = sprintf("%04d", $stl_aa);
                
                // Handle dates B.C. - if checkbox is set, add prefix 'B', else 'A' for A.D.
                #$prefix = 'A'; // Assume date is A.D. per default
                $prefix = '';
                #if($p[$index]['is_bc'] == 1) $prefix = 'B';
                $postdata = "$prefix$stl_aa-$stl_mm-$stl_jj $stl_hh:$stl_mn:$stl_ss";
                #echo '<pre>';
                #print_r($postdata);
                #echo '</pre>';
                #exit();
                $wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET $column = '%s' WHERE ID = %d", array($postdata, $pID)));
        }
        // Reset timestamp to 0
        if ($reset==1) {
                $wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET $column = 'A0000-00-00 00:00:00' WHERE ID = %d", $pID));
        }
    }
    
    /**
     * Save start and end dates of an event(post) to the database
     */
    function updateEventDates($post_id){
        if(!empty($_POST)){
            self::saveEventDate($post_id, $_POST, 'start');
            self::saveEventDate($post_id, $_POST, 'latest_start');
            self::saveEventDate($post_id, $_POST, 'end');
            self::saveEventDate($post_id, $_POST, 'earliest_end');
        }
    }
    
    /**
     * Get the minimum or maximum date of wpdb->posts.$column in $categories
     */
    function queryEventDate($minmax, $column, $categories) {
        global $wpdb;

        $relation = "SELECT object_id FROM $wpdb->term_relationships " .
                    "INNER JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id AND $wpdb->term_taxonomy.term_id IN ($categories) )";

        $query = "SELECT $minmax($wpdb->posts.$column) from $wpdb->posts INNER JOIN ($relation) t1 ON (t1.object_id=$wpdb->posts.ID) " .
                 "WHERE $column != '0000-00-00 00:00:00' AND post_status='publish' AND post_type='post'";

        return $wpdb->get_var($query);
    }
    
    /**
     * Get the date of the very first or last post dependant from the start date
     * @param type $minmax
     * @param type $type
     * @param type $format
     * @param type $categories
     * @return type
     */
    function getMinMaxEventDate($minmax, $type, $format = 'r', $categories) {
        global $wpdb;

        $column = 'stl_timeline_event_' . $type;

        // try to get the extreme post date from stl-columns
        $date = self::queryEventDate($minmax, $column, $categories);

        if (empty($date)) {
            $column = 'post_date';
            // get usual post date when start or end date isn't set
            $date = self::queryEventDate($minmax, $column, $categories);
        }
        if ($format != null) {
            $date = adodb_date2($format, $date);
        }
        return $date;
    }

}

?>