const Lightbox = require("./lightbox/Lightbox");

(function($){
    $.fn.lightbox = {};
    $.fn.lightbox.parseJsonData = function(data) {
        var imageArray = [];
        $.each(data, function () {
            imageArray.push(new Array(this.url, this.title));
        });
        return imageArray;
    };
    $.fn.lightbox.defaults = {
		adminBarHeight:28,
        overlayOpacity: 0.8,
        borderSize: 10,
        imageArray: new Array,
        activeImage: null,
        inprogress: false, //this is an internal state variable. don't touch.
        widthCurrent: 250,
        heightCurrent: 250,
        xScale: 1,
        yScale: 1,
        displayTitle: true,
        disableNavbarLinks: true,
        loopImages: true,
        imageClickClose: true,
        jsonData: null,
        jsonDataParser: null,
        followScroll: false,
        isIE8: false  //toyNN:internal value only
    };
	$(document).ready(function($){
		var haveConf = (typeof JQLBSettings == 'object');
		if(haveConf && JQLBSettings.resizeSpeed) {
			JQLBSettings.resizeSpeed = parseInt(JQLBSettings.resizeSpeed);
		}
		if(haveConf && JQLBSettings.marginSize){
			JQLBSettings.marginSize = parseInt(JQLBSettings.marginSize);
		}
		var default_strings = {
			help: ' Browse images with your keyboard: Arrows or P(revious)/N(ext) and X/C/ESC for close.',
			prevLinkTitle: 'previous image',
			nextLinkTitle: 'next image',
			prevLinkText:  '&laquo; Previous',
			nextLinkText:  'Next &raquo;',
			closeTitle: 'close image gallery',
			image: 'Image ',
			of: ' of ',
			download: 'Download'
        };
        new Lightbox($('a[rel^="lightbox"]'), {
			adminBarHeight: $('#wpadminbar').height() || 0,
			linkTarget: (haveConf && JQLBSettings.linkTarget.length) ? JQLBSettings.linkTarget : '_self',
			displayHelp: (haveConf && JQLBSettings.help.length) ? true : false,
			marginSize: (haveConf && JQLBSettings.marginSize) ? JQLBSettings.marginSize : 0,
			fitToScreen: (haveConf && JQLBSettings.fitToScreen == '1') ? true : false,
			resizeSpeed: (haveConf && JQLBSettings.resizeSpeed >= 0) ? JQLBSettings.resizeSpeed : 400,
			displayDownloadLink: (haveConf && JQLBSettings.displayDownloadLink == '0') ? false : true,
			navbarOnTop: (haveConf && JQLBSettings.navbarOnTop == '0') ? false : true,
			//followScroll: (haveConf && JQLBSettings.followScroll == '0') ? false : true,
			strings: (haveConf && typeof JQLBSettings.help == 'string') ? JQLBSettings : default_strings
        });
	});	
})(jQuery);