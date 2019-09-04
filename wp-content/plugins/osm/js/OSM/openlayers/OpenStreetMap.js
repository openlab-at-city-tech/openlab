/**
 * Class: OpenLayers.Layer.OSM.Mapnik
 *
 * Inherits from:
 *  - <OpenLayers.Layer.OSM>
 */
OpenLayers.Layer.OSM.Mapnik = OpenLayers.Class(OpenLayers.Layer.OSM, {
    /**
     * Constructor: OpenLayers.Layer.OSM.Mapnik
     *
     * Parameters:
     * name - {String}
     * options - {Object} Hashtable of extra options to tag onto the layer
     */
    initialize: function(name, options) {
        var url = [
            "https://a.tile.openstreetmap.org/${z}/${x}/${y}.png",
            "https://b.tile.openstreetmap.org/${z}/${x}/${y}.png",
            "https://c.tile.openstreetmap.org/${z}/${x}/${y}.png"
        ];
        options = OpenLayers.Util.extend({
            numZoomLevels: 20,
            attribution: "&copy; <a href='http://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors; <a href='https://www.HanBlog.Net'>OSM Plugin</a>",
            buffer: 0,
            tileOptions: {crossOriginKeyword: null},
            transitionEffect: "resize"
        }, options);
        var newArguments = [name, url, options];
        OpenLayers.Layer.OSM.prototype.initialize.apply(this, newArguments);
    },

    CLASS_NAME: "OpenLayers.Layer.OSM.Mapnik"
});

/**
 * Class: OpenLayers.Layer.OSM.CycleMap
 *
 * Inherits from:
 *  - <OpenLayers.Layer.OSM>
 */
OpenLayers.Layer.OSM.CycleMap = OpenLayers.Class(OpenLayers.Layer.OSM, {
    /**
     * Constructor: OpenLayers.Layer.OSM.CycleMap
     *
     * Parameters:
     * name - {String}
     * options - {Object} Hashtable of extra options to tag onto the layer
     */
    initialize: function(name, options) {
        var url = [
            "https://a.tile.opencyclemap.org/cycle/${z}/${x}/${y}.png",
            "https://b.tile.opencyclemap.org/cycle/${z}/${x}/${y}.png",
            "https://c.tile.opencyclemap.org/cycle/${z}/${x}/${y}.png"
        ];
        options = OpenLayers.Util.extend({
            numZoomLevels: 19,
            attribution: "&copy; <a href='http://www.openstreetmap.org/copyright'>OpenStreetMap</a>  contributors, Tiles courtesy of <a href='http://www.opencyclemap.org'>Andy Allan</a>; <a href='https://www.HanBlog.Net'>OSM Plugin</a>",
            buffer: 0,
            tileOptions: {crossOriginKeyword: null},
            transitionEffect: "resize"
        }, options);
        var newArguments = [name, url, options];
        OpenLayers.Layer.OSM.prototype.initialize.apply(this, newArguments);
    },

    CLASS_NAME: "OpenLayers.Layer.OSM.CycleMap"
});


/**
 * Class: OpenLayers.Layer.OSM.StamenWC
 *
 * Inherits from:
 *  - <OpenLayers.Layer.OSM>
 */
OpenLayers.Layer.OSM.StamenWC = OpenLayers.Class(OpenLayers.Layer.OSM, {
    /**
     * Constructor: OpenLayers.Layer.OSM.StamenWC
     *
     * Parameters:
     * name - {String}
     * options - {Object} Hashtable of extra options to tag onto the layer
     */
    initialize: function(name, options) {
        var url = [
            "https://a.tile.stamen.com/watercolor/${z}/${x}/${y}.jpg",
            "https://b.tile.stamen.com/watercolor/${z}/${x}/${y}.jpg",
            "https://c.tile.stamen.com/watercolor/${z}/${x}/${y}.jpg"
        ];
        options = OpenLayers.Util.extend({
            numZoomLevels: 19,
            attribution: "<a href=\"http://map.stamen.com\">Map tiles</a> by <a href=\"http://stamen.com\">Stamen Design</a>, under <a href=\"http://creativecommons.org/licenses/by/3.0\">CC BY 3.0</a>. Data by <a href=\"http://openstreetmap.org\">OpenStreetMap</a>  and <a href=\"https://www.HanBlog.net\">OSM Plugin</a>",
            buffer: 0,
            tileOptions: {crossOriginKeyword: null},
            transitionEffect: "resize"
        }, options);
        var newArguments = [name, url, options];
        OpenLayers.Layer.OSM.prototype.initialize.apply(this, newArguments);
    },

    CLASS_NAME: "OpenLayers.Layer.OSM.StamenWC"
});
/**
 * Class: OpenLayers.Layer.OSM.StamenToner
 *
 * Inherits from:
 *  - <OpenLayers.Layer.OSM>
 */
OpenLayers.Layer.OSM.StamenToner = OpenLayers.Class(OpenLayers.Layer.OSM, {
    /**
     * Constructor: OpenLayers.Layer.OSM.StamenToner
     *
     * Parameters:
     * name - {String}
     * options - {Object} Hashtable of extra options to tag onto the layer
     */
    initialize: function(name, options) {
        var url = [
            "https://a.tile.stamen.com/toner/${z}/${x}/${y}.jpg",
            "https://b.tile.stamen.com/toner/${z}/${x}/${y}.jpg",
            "https://c.tile.stamen.com/toner/${z}/${x}/${y}.jpg"
        ];
        options = OpenLayers.Util.extend({
            numZoomLevels: 19,
            attribution: "<a href=\"http://map.stamen.com\">Map tiles</a> by <a href=\"http://stamen.com\">Stamen Design</a>, under <a href=\"http://creativecommons.org/licenses/by/3.0\">CC BY 3.0</a>. Data by <a href=\"http://openstreetmap.org\">OpenStreetMap</a> and <a href=\"https://www.HanBlog.net\">OSM Plugin</a>",
            buffer: 0,
            tileOptions: {crossOriginKeyword: null},
            transitionEffect: "resize"
        }, options);
        var newArguments = [name, url, options];
        OpenLayers.Layer.OSM.prototype.initialize.apply(this, newArguments);
    },

    CLASS_NAME: "OpenLayers.Layer.OSM.StamenToner"
});
/**
 * Class: OpenLayers.Layer.OSM.TransportMap
 *
 * Inherits from:
 *  - <OpenLayers.Layer.OSM>
 */
OpenLayers.Layer.OSM.TransportMap = OpenLayers.Class(OpenLayers.Layer.OSM, {
    /**
     * Constructor: OpenLayers.Layer.OSM.TransportMap
     *
     * Parameters:
     * name - {String}
     * options - {Object} Hashtable of extra options to tag onto the layer
     */
    initialize: function(name, options) {
        var url = [
            "https://a.tile2.opencyclemap.org/transport/${z}/${x}/${y}.png",
            "https://b.tile2.opencyclemap.org/transport/${z}/${x}/${y}.png",
            "https://c.tile2.opencyclemap.org/transport/${z}/${x}/${y}.png"
        ];
        options = OpenLayers.Util.extend({
            numZoomLevels: 19,
            attribution: "&copy; <a href='http://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors, Tiles courtesy of <a href='http://www.opencyclemap.org'>Andy Allan</a>,  and <a href=\"https://www.HanBlog.net\">OSM Plugin</a>",
            buffer: 0,
            transitionEffect: "resize"
        }, options);
        var newArguments = [name, url, options];
        OpenLayers.Layer.OSM.prototype.initialize.apply(this, newArguments);
    },

    CLASS_NAME: "OpenLayers.Layer.OSM.TransportMap"
});
