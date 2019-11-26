/*  Copyright (C) 2019  Matthias Greiling (https://westrad.de)

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

	
/** 
 * get method for stored GET-Parameters
 * @param {string} _attribut - key of array
 * @returns {string} val of array at given index or 'undefined'
 */
function getGET(_attribut) {
	if(!HTTP_GET_VARS[_attribut]){
		return 'undefined';
	}

	return HTTP_GET_VARS[_attribut];
}

/** fix IE - add methode "includes" to string */
if (!String.prototype.includes) {
	String.prototype.includes = function(search, start) {
		if (typeof start !== 'number') {
			start = 0;
		}

		if (start + search.length > this.length) {
			return false;
		} else {
			return this.indexOf(search, start) !== -1;
		}	
	};
}

/** 
 * put layer on top of map 
 * @param {string} _map - the #id of the map | data-map ^data-map_name
 * @param {string} _layers - comma separated values
 */
function activateLayers(_map, _layers, _startUp) {

	/** _layers comma separated values? layers is an array - even with one layer */
	layers = [];
	comma = ',';
	if (typeof _layers.includes === "function" && !_startUp) {
		if (_layers.includes(comma)) {
			layers = _layers.split(',');
		}	
	} else {
		layers[0] = _layers;
	}	
	
	// via GET given param at startup - just layer 0
	if (_layers == 0) {
		layers[0] = _layers;
	}

	
	/** every layer stored in array switch its layerBox 'visible' and added global layer to global map */
	jQuery(layers).each(function(i,e){
	
		/** visibility and fancy looking */
		jQuery('#layerBox' + e + _map).children('i').removeClass('fa-eye-slash'); 
		jQuery('#layerBox' + e + _map).children('i').addClass('fa-eye'); 
	    jQuery('#layerBox' + e + _map).children('span.layerColor').removeClass('layerColorHidden'); 
		jQuery('#layerBox' + e + _map).css({'background-color':'rgb(250,255,255)' });
		jQuery('#layerBox' + e + _map).data('active', true);
		
		/** real action */
		window[_map].addLayer(window.vectorM[_map][e]);

		/** tracking stuff */
		if (typeof _paq !== "undefined") {
			if (!_startUp) { 
				_paq.push(['trackEvent', translations['openlayer'], jQuery('#layerBox' + e + _map).data('layer_title')]);
			} else {
				_paq.push(['trackEvent', translations['openlayerAtStartup'], jQuery('#layerBox' + e + _map).data('layer_title')]);
			}	
		}
	});
}	
		
/** 
 * for the link to map with choosen layers - collect which are active
 * @param {string} _map - the #id of the map | data-map ^data-map_name
 * @returns {string} of comma separated values of active layers of map
 */
function checkChoosedLayers(_map) {
	layers = [];
	
	jQuery('.layerOf' + _map).each(function(){
		if (jQuery(this).data('active') == true) {
			layers.push( jQuery(this).data('layer'));
		}
	});
	
	layers.join(); 
	
	// because 0 is not NULL and 0 is not 0 and so on ...
	if (layers != "") {

		return layers;
	} else {

		return 'none';
	}
}

/** 
 * removes layer from map by click on #layerBox_n tag - also sets visibility features
 * @param {object} chosen #layerBox
 */		
function switchLayerOff(_e) {
	_e.data('active', false);
	_e.children('i').removeClass('fa-eye'); 
	_e.children('i').addClass('fa-eye-slash'); 
	_e.children('span.layerColor').addClass('layerColorHidden'); 
	
	/** window and vectorM are both global */
	window[_e.data('map')].removeLayer(window.vectorM[_e.data('map')][_e.data('layer')]);
	
	/** tracking stuff */
	if (typeof _paq !== "undefined") {
		_paq.push(['trackEvent', translations['closeLayer'], _e.text()]);
	}	
}	
	
/** 
 * add layer on top of the map by click on #layerBox_n tag - also sets visibility features
 * @param {object} chosen #layerBox
 */		
function switchLayerOn(_e) {
	_e.data('active', true);
	_e.children('i').removeClass('fa-eye-slash'); 
	_e.children('i').addClass('fa-eye'); 
	_e.children('span.layerColor').removeClass('layerColorHidden'); 
	
	/** window and vectorM are both global */
	window[_e.data('map')].addLayer(window.vectorM[_e.data('map')][_e.data('layer')]);
	
	/** tracking stuff */
	if (typeof _paq !== "undefined") {
		_paq.push(['trackEvent', translations['openlayer'], _e.text()]);
	}	
}

/** after DOM has been rendered this jQuery will be executed */
jQuery(document).ready(function() { 

	/** 
	 * event handler 
	 * @fires prompt with link to page and map and choosen layers, center and zoom
	 * link contains GET-Parameters in an array with map_name as an index  
	 */	
	jQuery('.generatedLink').click(function() {
		if (jQuery(this).data('map_name') == '') {
			jQuery(this).data('map_name', jQuery(this).data('map'));
		}
		window.prompt(translations['generateLink'], location.protocol + '//' + location.host + location.pathname + '?map=' + jQuery(this).data('map_name') + '&mapCenter[' + jQuery(this).data('map_name') + ']=' + window[jQuery(this).data('map')].getView().getCenter() + '&mapZoom[' + jQuery(this).data('map_name') + ']=' + window[jQuery(this).data('map')].getView().getZoom() + '&mapLayers[' + jQuery(this).data('map_name') + ']=' + checkChoosedLayers(jQuery(this).data('map')));
	});
	
	/** 
	 * event handler 
	 * @fires prompt with wordpress short code to map_name map and choosen layers, center, zoom and a example string for trigger
	 */	
	jQuery('a.generatedShortCode').click(function() {
		
		mapName =  jQuery(this).data('map_name');
		mapCenter = window[jQuery(this).data('map')].getView().getCenter();
		mapZoom = window[jQuery(this).data('map')].getView().getZoom();
		mapLayer = checkChoosedLayers(jQuery(this).data('map')) ;
		
		string2show = '[osm_map_v3 setup_map_name="' + mapName + '" setup_center="' + mapCenter + '" setup_zoom="' + mapZoom + '" setup_layer="' + mapLayer + '" setup_trigger="' + translations['shortDescription'] + '"]';				
	
		window.prompt(translations['generatedShortCode'], string2show);
	});

	/** 
	 * event handler 
	 * @fires prompt with random name of map - it doesn't matter - it just have to be the same in active and passive short code
	 */	 	
	jQuery('a.cantGenerateShortCode').click(function() {
	
		var randomize = 'map_' + Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);
		window.prompt(translations['cantGenerateLink'], 'setup_map_name="' + randomize + '"');
	});
		
	/** 
	 * event handler for text link to control map
	 * set visibility an layers of map as well as zoom and center
	 */			
	jQuery('.setupChange').click(function() {
	
		/** the name of the map have to be unique to be controlled via text link - but have to be translated to the real id in the document */
		var controlledMap = jQuery('*[data-map_name="' + jQuery(this).data('map_name') + '"]').data('map');

		/** reads from clicked anchor tag data attributes: zoom - get from global: map */
		window[controlledMap].getView().setZoom(jQuery(this).data('zoom'));
	
		/** reads from clicked anchor tag data attributes: center - get from global: map */
		mapCenter = jQuery(this).data('center').split(',');

		/** this is the difference between OL 3.0 and 4.0 **/
		mapCenter[0] = parseFloat(mapCenter[0]);
		mapCenter[1] = parseFloat(mapCenter[1]);

		window[controlledMap].getView().setCenter(mapCenter);
	
		/** layers	- via class addressed seams not to be the same as via id ... */
		/** switch off every shown layer on specific map */
		jQuery('.layerBoxes').each( function(i){
			/** keep in mind: multiple maps per page */
			if (jQuery('#layerBox' + i + controlledMap).length > 0) {
				switchLayerOff(jQuery('#layerBox' + i + controlledMap));
			}	
		});
		
		/** switch on layer(s) in given order on specific map */
		if (jQuery(this).data('layer') != 'none') {
			activateLayers(controlledMap, jQuery(this).data('layer'), 0) ;
		}	
	});

	/** 
	 * event handler for text link to control map
	 * decide which function to call by looking at active status of tag
	 */
	jQuery('.layerBoxes').click(function() {
	
		/** data-attribut active represents visibility */
		if (jQuery(this).data('active') ==  true) {
			/** hide (remove layer from map) */
			switchLayerOff(jQuery(this));
		
		} else {
			/** show (add layer on maps top level) */
			switchLayerOn(jQuery(this));
		}
	});		
		
	/** 
	 * code part for evaluate incoming control data
	 */ 

	/** decode GET-Parameters, remove leading '?' */
	var strGET = decodeURIComponent(document.location.search.substr(1, document.location.search.length));

	/** splits by '&' fill array HTTP_GET_VARS with key => val */
	if (strGET != ''){

		var pairings = strGET.split('&');

		for (var i = 0; i < pairings.length; ++i) {
			var value = '';
			pairing = pairings[i].split('=');

			if (pairing.length > 1){
				value = pairing[1];
			}
	
			HTTP_GET_VARS[unescape(pairing[0])] = unescape(value);
		}
	}	

	/** if GET-Parameter set control for map - set values for map -- if mapCenter is set, script supposes everything is set in right order */
	if (getGET('mapCenter[' + getGET('map') + ']') != 'undefined') {
	
		/** mapCenter is an array of 2 values longitude, latitude (different format as long, lat in the entire plugin) */
		mapCenter = getGET('mapCenter[' + getGET('map') + ']').split(',');
		mapCenter[0] = parseFloat(mapCenter[0]);
		mapCenter[1] = parseFloat(mapCenter[1]);
		
		/** link between map_name and map id on page, because id is not map specific enough on different content positions */
		controlledMap = jQuery('*[data-map_name="' + getGET('map') + '"]').data('map');
		
		/** setup_map_name not set, content position is hopefully the same (standard at single view) */
		if (typeof controlledMap == "undefined") {
			controlledMap = getGET('map');
		}

		/** set center of GET given map */
		window[controlledMap].getView().setCenter(mapCenter);
	}

	/** set zoom of GET given map */
	if (getGET('mapZoom[' + getGET('map') + ']') != 'undefined') {
		window[controlledMap].getView().setZoom(getGET('mapZoom[' + getGET('map') + ']') );
	}
	
			
	/** set layers of GET given map */		
	if (getGET('mapLayers[' + getGET('map') + ']') != 'undefined') {
	
		/** show no layers at all - intentionally */
		if (getGET('mapLayers[' + getGET('map') + ']') != 'none') {

			activateLayers(controlledMap, getGET('mapLayers[' + getGET('map') + ']'), 0);
		}
	}  else {
		
		/** no map control is given via GET - make first layer visible - if there is any to choose from */
		jQuery('.map').each(function() {
			if (jQuery('.layerOf' + jQuery(this).data('map')).length > 0) {
				activateLayers(jQuery(this).data('map'), '0', 1);
			}
		});	
	}	
});		
