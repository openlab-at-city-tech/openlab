(function(g,f){'use strict';var h=function(e){if("object"!==typeof e.document)throw Error("Cookies.js requires a `window` with a `document` object");var b=function(a,d,c){return 1===arguments.length?b.get(a):b.set(a,d,c)};b._document=e.document;b._cacheKeyPrefix="cookey.";b._maxExpireDate=new Date("Fri, 31 Dec 9999 23:59:59 UTC");b.defaults={path:"/",secure:!1};b.get=function(a){b._cachedDocumentCookie!==b._document.cookie&&b._renewCache();a=b._cache[b._cacheKeyPrefix+a];return a===f?f:decodeURIComponent(a)};
b.set=function(a,d,c){c=b._getExtendedOptions(c);c.expires=b._getExpiresDate(d===f?-1:c.expires);b._document.cookie=b._generateCookieString(a,d,c);return b};b.expire=function(a,d){return b.set(a,f,d)};b._getExtendedOptions=function(a){return{path:a&&a.path||b.defaults.path,domain:a&&a.domain||b.defaults.domain,expires:a&&a.expires||b.defaults.expires,secure:a&&a.secure!==f?a.secure:b.defaults.secure}};b._isValidDate=function(a){return"[object Date]"===Object.prototype.toString.call(a)&&!isNaN(a.getTime())};
b._getExpiresDate=function(a,d){d=d||new Date;"number"===typeof a?a=Infinity===a?b._maxExpireDate:new Date(d.getTime()+1E3*a):"string"===typeof a&&(a=new Date(a));if(a&&!b._isValidDate(a))throw Error("`expires` parameter cannot be converted to a valid Date instance");return a};b._generateCookieString=function(a,b,c){a=a.replace(/[^#$&+\^`|]/g,encodeURIComponent);a=a.replace(/\(/g,"%28").replace(/\)/g,"%29");b=(b+"").replace(/[^!#$&-+\--:<-\[\]-~]/g,encodeURIComponent);c=c||{};a=a+"="+b+(c.path?";path="+
c.path:"");a+=c.domain?";domain="+c.domain:"";a+=c.expires?";expires="+c.expires.toUTCString():"";return a+=c.secure?";secure":""};b._getCacheFromString=function(a){var d={};a=a?a.split("; "):[];for(var c=0;c<a.length;c++){var e=b._getKeyValuePairFromCookieString(a[c]);d[b._cacheKeyPrefix+e.key]===f&&(d[b._cacheKeyPrefix+e.key]=e.value)}return d};b._getKeyValuePairFromCookieString=function(a){var b=a.indexOf("="),b=0>b?a.length:b,c=a.substr(0,b),e;try{e=decodeURIComponent(c)}catch(f){console&&"function"===
typeof console.error&&console.error('Could not decode cookie with key "'+c+'"',f)}return{key:e,value:a.substr(b+1)}};b._renewCache=function(){b._cache=b._getCacheFromString(b._document.cookie);b._cachedDocumentCookie=b._document.cookie};b._areEnabled=function(){var a="1"===b.set("cookies.js",1).get("cookies.js");b.expire("cookies.js");return a};b.enabled=b._areEnabled();return b},e="object"===typeof g.document?h(g):h;"function"===typeof define&&define.amd?define(function(){return e}):"object"===typeof exports?
("object"===typeof module&&"object"===typeof module.exports&&(exports=module.exports=e),exports.Cookies=e):g.Cookies=e})("undefined"===typeof window?this:window);

window.Frame_Event_Publisher = {
	id: window.name,
	cookie_name: 'X-Frame-Events',
	received: [],
	initialized: false,
	children: {},
    window: false,
    ajax_handlers_setup: false,

	is_parent: function() {
		return window.parent.document === window.document;
	},

	is_child: function(){
		return !this.is_parent();
	},

	setup_ajax_handlers: function() {
        if (!this.ajax_handlers_setup) {
            var publisher = this;
            jQuery(document).ajaxComplete(function(e, xhr, settings) {
                setTimeout(function() {
                    publisher.ajax_handler();
                }, 0);
            });
        }
	},

    ajax_handler: function() {
        this.broadcast(this.get_events(document.cookie));
    },

	initialize: function(){
		this.setup_ajax_handlers();

		// Provided by wp_localize_script() this lets us delete cookies set by the server
		if (typeof window.frame_event_publisher_domain !== 'undefined') {
			Cookies.defaults.domain = window.frame_event_publisher_domain;
		}

        this.window = window;
        if (typeof(this.window.id) != 'undefined' && this.window.id.length != null && this.window.id.length > 0) this.id = this.window.id;
        else this.id == 'Unknown';
		this.received = this.get_events(document.cookie);
		this.initialized = true;
		if (this.is_parent()) this.emit(this.received, true);
		return this.received;
	},

	register_child: function(child) {
		this.children[child.id] = child;
	},

	broadcast: function(events, child){
		if (!this.initialized) events = this.initialize();

        if (this.id == "Unknown") {
            this.initialized = false;
            setTimeout(function(){
                this.broadcast(events, child);
            }, 100);
        }
        // Broad cast events
        else {
            if (this.is_child()) {
                if (arguments.length <= 1) child = window;
                this.find_parent(child).register_child(child.Frame_Event_Publisher);
                this.notify_parent(events, child);
            }
            else {
                if (arguments.length == 0) events = this.received;
                this.notify_children(events);
            }
        }


	},

	/**
	 * Notifies the parent with a list of events to broadcast
	 */
	notify_parent: function(events, child){
		this.find_parent(child).broadcast(events, child);
	},

	/**
	 * Notifies (broadcasts) to children the list of available events
	 */
	notify_children: function(events){
		this.emit(events);
		for (var index in this.children) {
			var child = this.children[index];
			try {
				child.emit(events);
			}
			catch (ex) {
				if (typeof(console) != "undefined") console.log(ex);
				delete this.children.index;
			}
		}
	},

	/**
	 * Finds the parent window for the current child window
	 */
	find_parent: function(child){
		var retval = child;
		try {
			while (retval.document !== retval.parent.document) retval = retval.parent;
		}
		catch (ex){
			if (typeof(console) != "undefined") console.log(ex);
		}
		return retval.Frame_Event_Publisher;
	},

	/**
	 * Emits all known events to all children
	 */
	emit: function(events, forced){
		if (typeof(forced) == "undefined") forced = false;
		for (var event_id in events) {
			var event = events[event_id];
			if (!forced && !this.has_received_event(event_id)) {
				if (typeof(console) != "undefined") console.log("Emitting "+event_id+":"+event.event+" to "+this.id);
				this.trigger_event(event_id, events[event_id]);
			}
		}
	},

	has_received_event: function(id){
		return this.received[id] != undefined;
	},

	trigger_event: function(id, event){
		var signal = event.context+':'+event.event;
		event.id = id;
		if (typeof(window) != "undefined") jQuery(window).trigger(signal, event);
		this.received[id] = event;
	},

	/**
	 * Parses the events found in the cookie
	 */
	get_events: function(cookie){
		var frame_events = {};
		var cookies = unescape(cookie).split(' ');
		for (var i=0; i<cookies.length; i++) {
			var current_cookie = cookies[i];
			var parts = current_cookie.match(/X-Frame-Events_([^=]+)=(.*)/);
			if (parts) {
				var event_id = parts[1];
				var event_data = parts[2].replace(/;$/, '');
				try {
					frame_events[event_id] = JSON.parse(event_data);
				}
				catch (ex) {}
				var cookie_name = 'X-Frame-Events_'+event_id;
				this.delete_cookie(cookie_name);
			}
		}
		return frame_events;
	},

	delete_cookie: function(cookie) {
		 Cookies.expire(cookie);
	},

	listen_for: function(signal, callback){
		var publisher = this;
		jQuery(window).on(signal, function(e, event){
			var context = event.context;
			var event_id = event.id;
			if (!publisher.has_received_event(event_id)) {
				callback.call(publisher, event);
				publisher.received[event_id] = event;
			}
		});
	}
};

jQuery(function($){
    Frame_Event_Publisher.broadcast();
});
