/*  (c) Copyright 2022  MiKa (http://wp-osm-plugin.hyumika.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


  jQuery(document).ready(function(){

	jQuery('ul.osm-tabs li').click(function(){
		var tab_id = jQuery(this).attr('data-tab');

		jQuery('ul.osm-tabs li').removeClass('current');
		jQuery('.osm-tab-content').removeClass('current');

		jQuery(this).addClass('current');
		jQuery("#"+tab_id).addClass('current');
               
                AddMarker_map.updateSize();
		FileSC_map.updateSize();	
		TaggedSC_map.updateSize();
		AddGeotag_map.updateSize();
                
	})

	jQuery('ul.osm-marker-tabs li').click(function(){
		var tab_id = jQuery(this).attr('marker-tab');

		jQuery('ul.osm-marker-tabs li').removeClass('current');
		jQuery('.marker-tab-content').removeClass('current');

		jQuery(this).addClass('current');
		jQuery("#"+tab_id).addClass('current');
                
	})
	jQuery('ul.osm-geo-marker-tabs li').click(function(){
		var tab_id = jQuery(this).attr('geo-marker-tab');

		jQuery('ul.osm-geo-marker-tabs li').removeClass('current');
		jQuery('.geo-marker-tab-content').removeClass('current');

		jQuery(this).addClass('current');
		jQuery("#"+tab_id).addClass('current');
                
	})
	AddMarker_map.updateSize();
	FileSC_map.updateSize();	
	TaggedSC_map.updateSize();
	AddGeotag_map.updateSize();

    });

  jQuery(window).on('load', function(){
      AddMarker_map.updateSize();
      FileSC_map.updateSize();	
      TaggedSC_map.updateSize();
      AddGeotag_map.updateSize();
  });
  
  
