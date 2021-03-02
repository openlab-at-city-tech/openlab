(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
const Lightbox = require("./lightbox");

},{"./lightbox":2}],2:[function(require,module,exports){
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
},{"./lightbox/Lightbox":6}],3:[function(require,module,exports){
const $ = window.jQuery;


class Display {
    constructor(config, helper) {
        this.config = config;
        this.helper = helper;
    }
    changeImage(imageNum) {
        if (this.config.inprogress == false) {
            this.config.inprogress = true;
            this.config.activeImage = imageNum;
            // hide elements during transition
            const loading = document.getElementById('loading');
            const lightboxImage = document.getElementById('lightboxImage');
            const hoverNav = document.getElementById('hoverNav');
            const prevLink = document.getElementById('prevLink');
            const nextLink = document.getElementById('nextLink');
            if (loading) {
                this.helper.show(loading);
            }
            if (lightboxImage) {
                this.helper.hide(lightboxImage);
            }
            if (hoverNav) {
                this.helper.hide(hoverNav);
            }
            if (prevLink) {
                this.helper.hide(prevLink);
            }
            if (nextLink) {
                this.helper.hide(nextLink);
            }
            this.doChangeImage();
        }
    };


    doChangeImage() {
        this.config.imgPreloader = new Image();
        this.config.imgPreloader.onload = _ => {
            const lightboxImage = document.getElementById('lightboxImage');
            if (lightboxImage) {
                lightboxImage.src = this.config.imageArray[this.config.activeImage][0];
            }
            this.doScale();  // once image is preloaded, resize image container
            this.preloadNeighborImages();    
        };
        this.config.imgPreloader.src = this.config.imageArray[this.config.activeImage][0];
    };

    doScale() {
        this.updateDetails(); //Kevin: moved updateDetails() here, seems to work fine.    
        const overlay = document.getElementById('overlay');
        if (!overlay || !this.config.imgPreloader) {
            return;
        }
        var newWidth = this.config.imgPreloader.width;
        var newHeight = this.config.imgPreloader.height;
        var arrayPageSize = this.helper.getPageSize();
        var noScrollWidth = (arrayPageSize.pageWindowWidth < arrayPageSize.pageDocumentWidth) ? arrayPageSize.pageDocumentWidth : arrayPageSize.pageWindowWidth; //if viewport is smaller than page, use page width.
        overlay.style.width = noScrollWidth + 'px';
        overlay.style.height = arrayPageSize.pageDocumentHeight + 'px';
        const imageDataContainer = document.getElementById('imageDataContainer');
        var maxHeight = (arrayPageSize.viewportHeight) - (imageDataContainer.offsetHeight + (2 * this.config.borderSize));
        var maxWidth = (arrayPageSize.pageWindowWidth) - (2 * this.config.borderSize);
        if (this.config.fitToScreen) {
            var displayHeight = maxHeight - this.config.marginSize;
            var displayWidth = maxWidth - this.config.marginSize;
            var ratio = 1;
            if (newHeight > displayHeight) {
                ratio = displayHeight / newHeight; //ex. 600/1024 = 0.58					
            }
            newWidth = newWidth * ratio;
            newHeight = newHeight * ratio;
            ratio = 1;
            if (newWidth > displayWidth) {
                ratio = displayWidth / newWidth; //ex. 800/1280 == 0.62					
            }
            newWidth = Math.round(newWidth * ratio);
            newHeight = Math.round(newHeight * ratio);
        }
        var arrayPageScroll = this.helper.getPageScroll();
        var centerY = arrayPageScroll.yScroll + (maxHeight * 0.5);
        var newTop = centerY - newHeight * 0.5;
        var newLeft = arrayPageScroll.xScroll;
        const lightbox = document.getElementById('lightboxImage');
        lightbox.style.width = newWidth;
        lightbox.style.height = newHeight;
        this.resizeImageContainer(newWidth, newHeight, newTop, newLeft);
    }
    /*2.28.4 -  Compatible with wordpress 3.6.*/
    resizeImageContainer(imgWidth, imgHeight, lightboxTop, lightboxLeft) {
        const outerImageContainer = document.getElementById("outerImageContainer");
        const imageDataContainer = document.getElementById("imageDataContainer");
        if (!outerImageContainer || !imageDataContainer) {
            return;
        }
        this.config.widthCurrent = outerImageContainer.offsetWidth;
        this.config.heightCurrent = outerImageContainer.offsetHeight;
        var widthNew = Math.max(350, imgWidth + ((this.config.borderSize || 0) * 2));
        var heightNew = (imgHeight + ((this.config.borderSize || 0) * 2));
        // scalars based on change from old to new
        this.config.xScale = (widthNew / this.config.widthCurrent) * 100;
        this.config.yScale = (heightNew / this.config.heightCurrent) * 100;
        this.helper.setLightBoxPos(lightboxTop, lightboxLeft);
        
        $('#imageDataContainer').animate({ width: widthNew }, this.config.resizeSpeed, 'linear');
        $('#outerImageContainer').animate({ width: widthNew }, this.config.resizeSpeed, 'linear', _ => {
            $('#outerImageContainer').animate({ height: heightNew }, this.config.resizeSpeed, 'linear', _=> {
                this.showImage();
            });
        });

        this.showNavigationElements();
        if (document.getElementById("prevLink"))
            document.getElementById("prevLink").style.height = imgHeight;
        if (document.getElementById("nextLink"))
            document.getElementById("nextLink").style.height = imgHeight;
    };

    showImage() {
        //assumes updateDetails have been called earlier!
        this.helper.show(document.getElementById("imageData"));
        this.helper.show(document.getElementById('caption'));
        //$('#imageDataContainer').slideDown(400);
        //$("#imageDetails").hide().fadeIn(400);	
        this.helper.hide(document.getElementById("loading"));
        if (this.config.resizeSpeed > 0) {
            $('#lightboxImage').fadeIn("fast");
        } else {
            this.helper.show(document.getElementById("lightboxImage"));
        }
        this.config.inprogress = false;
    };

    preloadNeighborImages() {
        if (this.config.loopImages && this.config.imageArray.length > 1) {
            let preloadNextImage = new Image();
            preloadNextImage.src = this.config.imageArray[(this.config.activeImage == (this.config.imageArray.length - 1)) ? 0 : this.config.activeImage + 1][0]
            let preloadPrevImage = new Image();
            preloadPrevImage.src = this.config.imageArray[(this.config.activeImage == 0) ? (this.config.imageArray.length - 1) : this.config.activeImage - 1][0]
        } else {
            if ((this.config.imageArray.length - 1) > this.config.activeImage) {
                let preloadNextImage = new Image();
                preloadNextImage.src = this.config.imageArray[this.config.activeImage + 1][0];
            }
            if (this.config.activeImage > 0) {
                let preloadPrevImage = new Image();
                preloadPrevImage.src = this.config.imageArray[this.config.activeImage - 1][0];
            }
        }
    };


    updateDetails() {
        const numberDisplay = document.getElementById('numberDisplay');
        if (numberDisplay) {
            numberDisplay.innerHTML = '';
        }
        const caption = document.getElementById('caption');
        if (caption) {
            caption.innerHTML = '';
            this.helper.hide(caption);
            if (this.config.imageArray[this.config.activeImage][1]) {
                caption.innerHTML = this.config.imageArray[this.config.activeImage][1];
                this.helper.show();
            }
        }
        var nav_html = '';
        var prev = '';
        var pos = (this.config.imageArray.length > 1) ? this.config.strings.image + (this.config.activeImage + 1) + this.config.strings.of + this.config.imageArray.length : '';
        var link = (this.config.displayDownloadLink) ? '<a href="' + this.config.imageArray[this.config.activeImage][0] + '" id="downloadLink" target="' + this.config.linkTarget + '">' + this.config.strings.download + '</a>' : '';
        var next = '';
        if (this.config.imageArray.length > 1 && !this.config.disableNavbarLinks) {	 // display previous / next text links   			           
            if ((this.config.activeImage) > 0 || this.config.loopImages) {
                prev = '<a title="' + this.config.strings.prevLinkTitle + '" href="#" id="prevLinkText">' + this.config.strings.prevLinkText + "</a>";
            }
            if (((this.config.activeImage + 1) < this.config.imageArray.length) || this.config.loopImages) {
                next += '<a title="' + this.config.strings.nextLinkTitle + '" href="#" id="nextLinkText">' + this.config.strings.nextLinkText + "</a>";
            }
        }
        nav_html = prev + nav_html + pos + link + next;
        if (nav_html != '') {
            if (document.getElementById("numberDisplay")) {
                document.getElementById("numberDisplay").innerHTML = nav_html;
                this.helper.show(document.getElementById("numberDisplay"));
            }
        }
    };


    showNavigationElements() {
        const prevLink = document.getElementById("prevLink");
        const prevLinkText = document.getElementById("prevLinkText");
        const nextLink = document.getElementById("nextLink");
        const nextLinkText = document.getElementById("nextLinkText");
        if (this.config.imageArray.length > 1) {
            this.helper.show(document.getElementById("hoverNav"));
            // if loopImages is true, always show next and prev image buttons 
            if (this.config.loopImages) {
                this.helper.show(prevLink);
                this.helper.show(prevLinkText);
                this.helper.show(nextLink);
                this.helper.show(nextLinkText);

            } else {
                // if not first image in set, display prev image button
                if (this.config.activeImage != 0) {
                    this.helper.show(prevLink);
                    this.helper.show(prevLinkText);
                }
                // if not last image in set, display next image button
                if (this.config.activeImage != (this.config.imageArray.length - 1)) {
                    this.helper.show(nextLink);
                    this.helper.show(nextLink);
                }
            }
            //this.enableKeyboardNav();
        }
    };
}

module.exports = Display;
},{}],4:[function(require,module,exports){
class Events {
    constructor(config, parent) {
        this.config = config;
        this.parent = parent;
    }

    bindNavigationButtons(callback) {
        const prevLink = document.getElementById("prevLink");
        const prevLinkText = document.getElementById("prevLinkText");
        const nextLink = document.getElementById("nextLink");
        const nextLinkText = document.getElementById("nextLinkText");
        if(prevLink) {
            prevLink.addEventListener('click', this.onNavigationButtonClick.bind(this, callback, 'prev'));
        }
        if(prevLinkText) {
            prevLinkText.addEventListener('click', this.onNavigationButtonClick.bind(this, callback, 'prev'));
        }
        if(nextLink) {
            nextLink.addEventListener('click', this.onNavigationButtonClick.bind(this, callback, 'next'));
        }
        if(nextLinkText) {
            nextLinkText.addEventListener('click', this.onNavigationButtonClick.bind(this, callback, 'next'));
        }
    }

    onNavigationButtonClick(callback, direction, e) {
        e.stopPropagation()
        e.preventDefault();
        if(direction === 'prev') {
            const index = (this.config.activeImage == 0) ? (this.config.imageArray.length - 1) : this.config.activeImage - 1;
            callback(index);
        }
        if(direction === 'next') {
            const index = (this.config.activeImage == (this.config.imageArray.length - 1)) ? 0 : this.config.activeImage + 1;
            callback(index);
        }
    }


    enableKeyboardNav(callback) {
        document.addEventListener('keydown', this.keyboardAction.bind(this, callback));
    }

    keyboardAction(callback, e) {
        var config = this.config;
        var keycode = e.keyCode;
        var escapeKey = 27;
        var key = String.fromCharCode(keycode).toLowerCase();
        if ((key == 'x') || (key == 'o') || (key == 'c') || (keycode == escapeKey)) { // close lightbox
            this.parent.end();
        } else if ((key == 'p') || (keycode == 37)) { // display previous image
            if (config.loopImages) {
                callback((config.activeImage == 0) ? (config.imageArray.length - 1) : config.activeImage - 1);
            }
            else if (config.activeImage != 0) {
                callback(config.activeImage - 1);
            }
        } else if ((key == 'n') || (keycode == 39)) { // display next image
            if (this.config.loopImages) {
                callback((config.activeImage == (config.imageArray.length - 1)) ? 0 : config.activeImage + 1);
            }
            else if (config.activeImage != (config.imageArray.length - 1)) {
                callback(config.activeImage + 1);
            }
        }
        return false;
    };

}

module.exports = Events;
},{}],5:[function(require,module,exports){
const $ = window.jQuery;

class Helper {
    constructor(config) {
        this.config = config;
    }
    hide(element) {
        if (element) {
            element.style.display = 'none';
        }
    };
    show(element) {
        if (element) {
            element.style.display = 'block';
        }
    };
    getPageSize() {
        const pageDocumentHeight = document.documentElement.scrollHeight;
        const pageDocumentWidth = document.documentElement.scrollWidth;
        if (this.config.isIE8 && pageDocumentHeight > 4096) {
            pageDocumentHeight = 4096;
        }
        var viewportHeight = document.documentElement.clientHeight - this.config.adminBarHeight;
        var pageWindowWidth = document.documentElement.clientWidth;
        const pageSize = {
            pageDocumentWidth: pageDocumentWidth,
            pageDocumentHeight: pageDocumentHeight,
            pageWindowWidth: pageWindowWidth,
            viewportHeight: viewportHeight,
            documentScrollHeight: document.documentElement.scrollHeight
        };
        return pageSize;
    };
    isIE8() {
        var isBuggy = false;
        if (document.createElement) {
            var el = document.createElement("div");
            if (el && el.querySelectorAll) {
                el.innerHTML = "<object><param name=\"\"></object>";
                isBuggy = el.querySelectorAll("param").length != 1;
            }
            el = null;
        }
        return isBuggy;
    };
    getPageScroll() {
        var xScroll = 0; var yScroll = 0;
        if (self.pageYOffset) {
            yScroll = self.pageYOffset;
            xScroll = self.pageXOffset;
        } else if (document.documentElement && document.documentElement.scrollTop) {  // Explorer 6 Strict
            yScroll = document.documentElement.scrollTop;
            xScroll = document.documentElement.scrollLeft;
        } else if (document.body) {// all other Explorers
            yScroll = document.body.scrollTop;
            xScroll = document.body.scrollLeft;
        }
        const wpadminbar = document.getElementById("wpadminbar");
        if (wpadminbar) {
            const style = window.getComputedStyle(wpadminbar);
            const top = style.getPropertyValue('top');
            if (this.config.adminBarHeight && parseInt(top, 10) === 0) {
                yScroll += this.config.adminBarHeight;
            }
        }
        return {xScroll, yScroll};
    };
    setLightBoxPos(newTop, newLeft) {
        if (this.config.resizeSpeed > 0) {
            $('#lightbox').animate({ top: newTop }, 250, 'linear');
            return $('#lightbox').animate({ left: newLeft }, 250, 'linear').show();
        }
        return $('#lightbox').css({ top: newTop + 'px', left: newLeft + 'px' }).show();
    }
}

module.exports = Helper;
},{}],6:[function(require,module,exports){
const $ = window.jQuery;
const Helper = require("./Helper");
const Display = require("./Display");
const Events = require("./Events");

class Lightbox {
    constructor(element, config) {
        this.config = $.extend({}, $.fn.lightbox.defaults, config);
        this.helper = new Helper(this.config);
        this.display = new Display(this.config, this.helper);
        this.events = new Events(this.config, this);
        this.loader(element);
    }
    loader(element) {
        for (let i = 0; i < element.length; i++) {
            element[i].addEventListener('click', this.onClick.bind(this, element[i]));
        }
    }
    onClick(element, event) {
        event.preventDefault();
        this.initialize();
        this.start(element);
    }
    initialize() {
        window.addEventListener('orientationchange', this.resizeListener.bind(this));
        window.addEventListener('resize', this.resizeListener.bind(this));
        //            $(window).bind('orientationchange', resizeListener);
        //          $(window).bind('resize', resizeListener);
        // if (opts.followScroll) { $(window).bind('scroll', orientListener); }
        document.getElementById('overlay') ? document.getElementById('overlay').remove() : false;
        document.getElementById('lightbox') ? document.getElementById('lightbox').remove() : false;
        this.config.isIE8 = this.helper.isIE8(); // //http://www.grayston.net/2011/internet-explorer-v8-and-opacity-issues/
        this.config.inprogress = false;
        // if jsonData, build the imageArray from data provided in JSON format
        if (this.config.jsonData && this.config.jsonData.length > 0) {
            var parser = this.config.jsonDataParser ? this.config.jsonDataParser : $.fn.lightbox.parseJsonData;
            this.config.imageArray = [];
            this.config.imageArray = parser(this.config.jsonData);
        }
        var outerImage = '<div id="outerImageContainer"><div id="imageContainer"><img id="lightboxImage"><div id="hoverNav"><a href="javascript:void(0);" title="' + this.config.strings.prevLinkTitle + '" id="prevLink"></a><a href="javascript:void(0);" id="nextLink" title="' + this.config.strings.nextLinkTitle + '"></a></div><div id="loading"><a href="javascript:void(0);" id="loadingLink"><div id="jqlb_loading"></div></a></div></div></div>';
        var imageData = '<div id="imageDataContainer" class="clearfix"><div id="imageData"><div id="imageDetails"><span id="caption"></span><span id="numberDisplay"></span></div><div id="bottomNav">';
        if (this.config.displayHelp) {
            imageData += '<span id="helpDisplay">' + this.config.strings.help + '</span>';
        }
        imageData += '<a href="javascript:void(0);" id="bottomNavClose" title="' + this.config.strings.closeTitle + '"><div id="jqlb_closelabel"></div></a></div></div></div>';
        var string;
        if (this.config.navbarOnTop) {
            string = '<div id="overlay"></div><div id="lightbox">' + imageData + outerImage + '</div>';
            $("body").append(string);
            $("#imageDataContainer").addClass('ontop');
        } else {
            string = '<div id="overlay"></div><div id="lightbox">' + outerImage + imageData + '</div>';
            $("body").append(string);
        }
        const overlay = document.getElementById('overlay');
        const lightbox = document.getElementById('lightbox');
        const loadingLink = document.getElementById('loadingLink');
        const bottomNavClose = document.getElementById('bottomNavClose');
        const outerImageContainer = document.getElementById('outerImageContainer');
        const imageDataContainer = document.getElementById('imageDataContainer');
        const lightboxImage = document.getElementById('lightboxImage');
        const hoverNav = document.getElementById('hoverNav');

        if (overlay) {
            overlay.addEventListener('click', _ => this.end());
            this.helper.hide(overlay);
        }
        if (lightbox) {
            lightbox.addEventListener('click', _ => this.end());
            this.helper.hide(lightbox);
        }
        if (loadingLink) {
            loadingLink.addEventListener('click', _ => this.end());
        }
        if (bottomNavClose) {
            bottomNavClose.addEventListener('click', _ => this.end());
        }

        this.events.bindNavigationButtons(this.display.changeImage.bind(this.display));
        this.events.enableKeyboardNav(this.display.changeImage.bind(this.display));

        if (outerImageContainer) {
            outerImageContainer.style.width = this.config.widthCurrent + "px";
            outerImageContainer.style.height = this.config.heightCurrent + "px";
        }
        if (imageDataContainer) {
            imageDataContainer.style.width = this.config.widthCurrent + "px";
        }
        /*
        if (!opts.imageClickClose) {
            if(lightboxImage) {
                lightboxImage.addEventListener('click', _ => end());
            }
            if(hoverNav) {
                hoverNav.addEventListener('click', _ => end());
            }
        }
        */
    };

    resizeListener(e) {
        if (this.config.resizeTimeout) {
            clearTimeout(this.config.resizeTimeout);
            this.config.resizeTimeout = false;
        }
        this.config.resizeTimeout = setTimeout(_ => { this.display.doScale(false); }, 50); //a delay to avoid duplicate event calls.		
    }
    
    //code for IE8 check provided by http://kangax.github.com/cft/
    
    // JQuery Call
    start(imageLink) {
        document.querySelectorAll("select, embed, object").forEach(element => {
            this.helper.hide(element);
        });
        var arrayPageSize = this.helper.getPageSize();
        var arrayPagePos = this.helper.getPageScroll();
        var newTop = 0;
        const overlay = document.getElementById("overlay");
        $("#overlay").hide().css({ width: arrayPageSize.pageDocumentWidth + 'px', height: arrayPageSize.pageDocumentHeight + 'px', opacity: this.config.overlayOpacity }).fadeIn(400);
        if (this.config.isIE8 && arrayPageSize.pageDocumentHeight == 4096) {
            if (arrayPagePos.yScroll >= 1000) {
                newTop = arrayPagePos.yScroll - 1000;
                if ((arrayPageSize.documentScrollHeight - (arrayPagePos.yScroll + 3096)) < 0) {
                    newTop -= (arrayPagePos.yScroll + 3096) - arrayPageSize.documentScrollHeight;
                }
                overlay.style.top = newTop + 'px';
            }
        }
        let imageNum = 0;
        // if data is not provided by jsonData parameter
        if (!this.config.jsonData) {
            this.config.imageArray = [];
            // if image is NOT part of a set..				
            if (!imageLink.rel || (imageLink.rel == '')) {
                // add single image to Lightbox.imageArray
                var s = imageLink.title || imageLink.parentElement.firstChild.title || '';
                /*
                                    if (imageLink.title) {
                                        s = imageLink.title;
                                    } else if ($(this).children(':first-child').attr('title')) {
                                        s = $(this).children(':first-child').attr('title');
                                    }
                                    */
                                   console.log(imageLink);
                this.config.imageArray.push([imageLink.href, this.config.displayTitle ? s : '']);
            } else {
                document.querySelectorAll("a").forEach(a => {
                    if (a.href && (a.rel === imageLink.rel)) {
                        let title = '';
                        let alternative_title = a.parentElement.querySelector("img:first-of-type");
                        if (a.title) {
                            title = a.title;
                        } else if (alternative_title) {
                            title = alternative_title.title;
                        }

                        let caption = '';
                        let captionText = '';
                        const galleryCaptionElement = a.parentElement.parentElement.querySelector('.gallery-caption');
                        const captionElement = a.parentElement.querySelector('.wp-caption-text');
                        if (galleryCaptionElement) {
                            caption = galleryCaptionElement.textContent;
                            captionText = galleryCaptionElement.innerHTML;
                        } else if (captionElement) {
                            caption = captionElement.textContent;
                            captionText = captionElement.innerHTML;
                        }
                        title = title.trim();
                        captionText = captionText.trim();
                        if (title.toLowerCase() === captionText.toLowerCase()) {
                            title = captionText;
                            caption = '';
                        }
                        let s = '';
                        if (title != '') {
                            s = '<span id="titleText">' + title + '</span>';
                        }
                        if (caption != '') {
                            if (title != '') {
                                s += '<br />';
                            }
                            s += '<span id="captionText">' + caption + '</span>';
                        }
                        this.config.imageArray.push([
                            a.href,
                            this.config.displayTitle ? s : ''
                        ]);
                    }
                });
            }
        }
        if (this.config.imageArray.length > 1) {
            for (let i = 0; i < this.config.imageArray.length; i++) {
                for (let j = this.config.imageArray.length - 1; j > i; j--) {
                    if (this.config.imageArray[i][0] == this.config.imageArray[j][0]) {
                        this.config.imageArray.splice(j, 1);
                    }
                }
            }
            while (this.config.imageArray[imageNum][0] != imageLink.href) { imageNum++; }
        }
        // calculate top and left offset for the lightbox
        this.helper.setLightBoxPos(arrayPagePos[1], arrayPagePos[0]);
        this.display.changeImage(imageNum);
    };

    end() {
        document.getElementById("lightbox").remove();
        $('#overlay').fadeOut(_ => {
            document.getElementById("overlay").remove();
        });
        document.querySelectorAll("select, embed, object").forEach(element => {
            this.helper.show(element);
        });
    };

}

module.exports = Lightbox;
},{"./Display":3,"./Events":4,"./Helper":5}]},{},[1]);
