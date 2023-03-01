/**
 * This JS file was auto-generated via Terser.
 *
 * Contributors should avoid editing this file, but instead edit the associated
 * non minified file file. For more information, check out our engineering docs
 * on how we handle JS minification in our engineering docs.
 *
 * @see: https://evnt.is/dev-docs-minification
 */

"function"==typeof jQuery&&jQuery((function($){var mapHolder,position,venueObject,venueAddress,venueCoords,venueTitle;function prepare(){!1!==venueCoords?function useCoords(){position=new google.maps.LatLng(venueCoords[0],venueCoords[1]),initialize()}():function useAddress(){(new google.maps.Geocoder).geocode({address:venueAddress},(function(results,status){status==google.maps.GeocoderStatus.OK&&(position=results[0].geometry.location,initialize())}))}()}function initialize(){var mapOptions={zoom:parseInt(tribeEventsSingleMap.zoom),center:position,mapTypeId:google.maps.MapTypeId.ROADMAP};venueObject.map=new google.maps.Map(mapHolder,mapOptions);var marker={map:venueObject.map,title:venueTitle,position:position};$("body").trigger("map-created.tribe",[venueObject.map,mapHolder,mapOptions]),"undefined"!==tribeEventsSingleMap.pin_url&&tribeEventsSingleMap.pin_url&&(marker.icon=tribeEventsSingleMap.pin_url),new google.maps.Marker(marker)}"undefined"!=typeof tribeEventsSingleMap&&$.each(tribeEventsSingleMap.addresses,(function(index,venue){null!==(mapHolder=document.getElementById("tribe-events-gmap-"+index))&&(venueObject=void 0!==venue?venue:{},venueAddress=void 0!==venue.address&&venue.address,venueCoords=void 0!==venue.coords&&venue.coords,venueTitle=venue.title,prepare())}))}));