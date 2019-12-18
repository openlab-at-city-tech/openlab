<?php
/**
 * WPSimileTimelineDatabase.class.php
 * Description: Database functions for the SIMILE Timline Plugin
 * Plugin URI: http://wordpress.org/extend/plugins/wp-simile-timeline/
 * Author: freshlabs
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
class WPSimileTimelineDatabase{
    
    function __construct(){
        // empty
    }

    /**
     * Process version updates
     */
    function doUpdates(){
        global $wpdb;

        $terms_table = WPSimileTimelineTerm::getTableName();

        // @since: 0.4.9
        // Alter posts columns for event dates to CHAR(20) to store dates B.C.
        // 20 Characters: A/B für A.D/B.C + 19 for YYYY-MM-DD HH:ii:ss
        /*
        $post_event_dates = WPSimileTimelinePost::getPostEventTypes();
        foreach($post_event_dates as $column):
            if(WPSimileTimelineDatabase::columnExists($wpdb->posts, $column)){
                $wpdb->query("ALTER TABLE " .$wpdb->posts . " CHANGE `" . $column . "` `" . $column . "` VARCHAR(20) NOT NULL DEFAULT 'A0000-00-00 00:00:00'; ");
                // TODO: Loop through existing datetimes and add 'A' as default prefix
            }
        endforeach;
        */

        // Add column for icon in terms table
        // @since: 0.4.8.5
        $column_name = 'icon';
        if(!WPSimileTimelineDatabase::columnExists($terms_table, $column_name)){
            $wpdb->query("ALTER TABLE $terms_table ADD COLUMN $column_name VARCHAR( 255 ) NOT NULL AFTER `color`");
        }
        
        $deprecated_options = array(
            'stl_timeline_band_options', 	// deprecated since 0.4.6.4
            'stl_timelinecategories',	// deprecated since 0.4.7
            'stl_timeline_locale'		// deprecated since 0.4.8.2
        );
        // remove deprecated options from database
        foreach($deprecated_options as $d){
            if(get_option($d)){
                delete_option($d);
            }
        }
    }

    /**
     * Checks if a given column exists in a database table
     * @param type $table
     * @param type $column
     * @return boolean
     */
    function columnExists($table, $column){
        global $wpdb;
        $column_exists = false;
        $q = $wpdb->query($wpdb->prepare("SHOW COLUMNS FROM $table LIKE %s", $column));
        if($q == 1){
                $column_exists = true;
        }
        return $column_exists;
    }

    /**
     * Delete a table
     * @param type $tn
     */
    function deleteTable($tn){		
        global $wpdb;
        $table_name = $wpdb->prefix . $tn;
        $wpdb->query("DROP TABLE $table_name");
    }	

}
?>