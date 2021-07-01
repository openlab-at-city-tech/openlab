<?php
/**
 * Description: Helper functions for the WP SIMILE Timeline plugin
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

class WPSimileTimelineToolbox {

    function __construct() {
    }

    /** -----------------------------------------------------------------------------
	 * WPSimileTimelineToolbox::filterHtml
	 * Hybrid html filtering for XML-output
	 * ---------------------------------------------------------------------------*/
	function filterHtml($content){
		$content = htmlspecialchars($content); // convert special chars in html entities
		$content = ent2ncr($content); // convert named entities into numbered entities
		return $content;
	}
	
	/** -----------------------------------------------------------------------------
	 * WPSimileTimelineToolbox::filterDomString
	 * strip brackets from string (used for HTML-friendly output of DOM-IDs)
	 * ---------------------------------------------------------------------------*/
	function filterDomString($string){
		return str_replace(array('[',']'), '', $string);
	}
	
	/**
	 * PHP getdate clone...the dirty way
	 */
	function myGetDate($date){
		$d = explode(' ', $date);
		$date = explode('-', $d[0]);
		$time = explode(':', $d[1]);
		$arr['year'] = $date[0];
		$arr['month'] = $date[1];
		$arr['day'] = $date[2];
		$arr['hour'] = $time[0];
		$arr['minute'] = $time[1];
		$arr['second'] = $time[2];
		return $arr;
	}
	
	function outputOptionValue($index, $cmp){
		if($index == $cmp){
			$s = ' value="'.$index.'" selected="selected"';
		}
		else{
			$s = ' value="'.$index.'"';
		}
		return $s;
	}
        
        /**
         * Parse and return date string. Check for date string prefix (A/B) and return elements accordingly
         * @param type $date
         * @return type
         */
        function parseDateString($rawdate) {
            $prefix     = substr($rawdate, 0, 1);  // Get date prefix A/B
            $date       = substr($rawdate, 1);     // Handle date without prefix
            $is_date_bc = ($prefix == 'B');     // Date BC existent with prefix 'B'
            
            if($is_date_bc){
                // custom date parsing for BC dates
                $string = adodb_date2('Y', $date) . ' BC';
            }
            else{
                // Regular date parsing
                $string = adodb_date2('r', $date);
            }
            
            return $string;  
        }
	
	function implodeDate($a){
		//$time = adodb_mktime($a['hour'], $a['minute'], $a['second'], $a['day'], $a['month'], $a['year']);
		//$time = adodb_date('Y', $time);
		return $a['year'] . '-' . $a['month'] . '-' . $a['day'] . ' ' . $a['hour'] . ':' . $a['minute'];
	}
	
	/**
	 * Get custom post types (exclude built-ins)
	 * @return Array of post type names
	 */
	function getCustomPostTypes(){
		$args = array(
			'public'   => true,
			'_builtin' => false
		);
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator);
		return $post_types;
	}
}
?>