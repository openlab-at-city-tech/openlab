/**
 * Plugin Name: WP Lightbox 2
 * Plugin URI: http://yepinol.com/lightbox-2-plugin-wordpress/
 * Description: This plugin used to add the lightbox (overlay) effect to the current page images on your WordPress blog.
 * Version:       2.28.9.2.1
 * Author:        Pankaj Jha
 * Author URI:    http://onlinewebapplication.com/
 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
/*  Copyright 2011 Pankaj Jha (onlinewebapplication.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation using version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
/**
 * jQuery Lightbox
 * Version 0.5 - 11/29/2007
 * @author Warren Krewenki
 *
 * This package is distributed under the BSD license.
 * For full license information, see LICENSE.TXT
 *
 * Based on Lightbox 2 by Lokesh Dhakar (http://www.huddletogether.com/projects/lightbox2/)
 * Originally written to make use of the Prototype framework, and Script.acalo.us, now altered to use jQuery.
 **/
 /** toyNN: davidtg@comtrya.com: fixed IE7-8 incompatabilities in 1.3.* branch **/ 
(function($){
    $.fn.lightbox = function(options) {
        var opts = $.extend({}, $.fn.lightbox.defaults, options);
		function onClick() {
            initialize();
            start(this);
            return false;
        }	
		if(parseFloat($().jquery) >= 1.7){
			return $(this).on("click", onClick);
        }else{
			return $(this).live("click", onClick); //deprecated since 1.7
		}		
		
		function initialize() {
            $(window).bind('orientationchange', resizeListener);
            $(window).bind('resize', resizeListener);
            // if (opts.followScroll) { $(window).bind('scroll', orientListener); }
            $('#overlay').remove();
            $('#lightbox').remove();
            opts.isIE8 = isIE8(); // //http://www.grayston.net/2011/internet-explorer-v8-and-opacity-issues/
            opts.inprogress = false;
            // if jsonData, build the imageArray from data provided in JSON format
            if (opts.jsonData && opts.jsonData.length > 0) {
                var parser = opts.jsonDataParser ? opts.jsonDataParser : $.fn.lightbox.parseJsonData;
                opts.imageArray = [];
                opts.imageArray = parser(opts.jsonData);
            }
            var outerImage = '<div id="outerImageContainer"><div id="imageContainer"><iframe id="lightboxIframe" /><img id="lightboxImage"><div id="hoverNav"><a href="javascript://" title="' + opts.strings.prevLinkTitle + '" id="prevLink"></a><a href="javascript://" id="nextLink" title="' + opts.strings.nextLinkTitle + '"></a></div><div id="loading"><a href="javascript://" id="loadingLink"><div id="jqlb_loading"></div></a></div></div></div>';
            var imageData = '<div id="imageDataContainer" class="clearfix"><div id="imageData"><div id="imageDetails"><span id="caption"></span><span id="numberDisplay"></span></div><div id="bottomNav">';
            if (opts.displayHelp) {
                imageData += '<span id="helpDisplay">' + opts.strings.help + '</span>';
            }
			if(JQLBSettings['jqlb_show_close_button']=='1'){
            	imageData += '<a href="javascript://" id="bottomNavClose" title="' + opts.strings.closeTitle + '"><img src="'+JQLBSettings['jqlb_image_for_close_lightbox']+'" id="jqlb_closelabel"/></a>';
			}
			imageData +='</div></div></div>';
            var string;
            if (opts.navbarOnTop) {
                string = '<div id="overlay"></div><div id="lightbox">' + imageData + outerImage + '</div>';
                $("body").append(string);
                $("#imageDataContainer").addClass('ontop');
            } else {
                string = '<div id="overlay"></div><div id="lightbox">' + outerImage + imageData + '</div>';
                $("body").append(string);
            }
			var gago=1;
            $("#overlay").click(function () { end(); }).hide();	
			if(JQLBSettings['jqlb_overlay_close']=='1')		
            $("#lightbox").click(function () { if(gago){end();} gago=1 }).hide().children('#imageDataContainer').click(function(e) { gago=0; console.log(e)  });;
            $("#loadingLink").click(function () { end(); return false; });
            $("#bottomNavClose").click(function () { end(); return false; });
            $('#outerImageContainer').width(opts.widthCurrent).height(opts.heightCurrent);
            $('#imageDataContainer').width(opts.widthCurrent);
            if (!opts.imageClickClose) {
                $("#lightboxImage").click(function () { return false; });
                $("#hoverNav").click(function () { return false; });
            }
        };
        //allow image to reposition & scale if orientation change or resize occurs.
		/*2.21 - Image Map, Shrink large images to fit smaller screens*/
		/*2.23 - Updated jQuery calls for faster load*/
		/*2.25 - Fixed PHP 5 bug*/
		/*2.27 - Compatible with wordpress 3.5.1.*/
		/*2.28.6.1 - Fixed navigation issue (minor release)*/
		/*2.28.8 - Compatible with wordpress 3.8.*/
		/*2.28.8.1 - Fixed navigation issue.*/
		/*2.28.8.2 - Compatible with wordpress 3.8.1*/
		/*2.28.8.3 - Fixed full screen image close issue*/
	        /*2.28.8.4 - Compatible with wordpress 3.9*/
                /*2.28.8.5 - Fixed Responsiveness Issue */
                /*2.28.8.6 - Compatible with wordpress 3.9.1*/
		/*2.28.8.7 - Fixed Image Galary and other HTML issue minor fix*/
		/*2.28.8.8 - Compatible with wordpress 3.9.2*/
		/*2.28.8.9 - Compatible with wordpress 4.0*/
		/*2.28.9.0 - Optimize: content grouping support and exclusion performance*/
		/*2.28.9.1 - Compatible with wordpress 4.0.1*/
		/*2.28.9.2 - Compatible with wordpress 4.1*/
		/*2.28.9.2.1 - Fixed: Broken shortcodes with WordPress 4.1*/
        function resizeListener(e) {
            if (opts.resizeTimeout) {
                clearTimeout(opts.resizeTimeout);
                opts.resizeTimeout = false;
            }
            opts.resizeTimeout = setTimeout(function () { doScale(false); }, 50); //a delay to avoid duplicate event calls.		
        }
        function getPageSize(){           
            var pgDocHeight = $(document).height();
            if (opts.isIE8 && pgDocHeight > 4096) {
                pgDocHeight = 4096;
            }
			var viewportHeight = $(window).height() - opts.adminBarHeight;			
			//$(document).width() returns width of HTML document 
            return new Array($(document).width(), pgDocHeight, $(window).width(), viewportHeight, $(document).height());
        };
        //code for IE8 check provided by http://kangax.github.com/cft/
        function isIE8() {
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
        function getPageScroll() {
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
			if(opts.adminBarHeight && parseInt($('#wpadminbar').css('top'), 10) === 0){
				yScroll += opts.adminBarHeight;
			}	
            return new Array(xScroll, yScroll);
        };
// JQuery Call
		function start(imageLink) {
            $("select, embed, object").hide();
            var arrayPageSize = getPageSize();
            var arrayPagePos = getPageScroll();
            var newTop = 0;
            $("#overlay").hide().css({width: arrayPageSize[0] + 'px', height: arrayPageSize[1] + 'px', opacity: opts.overlayOpacity}).fadeIn(400);
            if (opts.isIE8 && arrayPageSize[1] == 4096) {
                if (arrayPagePos[1] >= 1000) {
                    newTop = arrayPagePos[1] - 1000;
                    if ((arrayPageSize[4] - (arrayPagePos[1] + 3096)) < 0) {
                        newTop -= (arrayPagePos[1] + 3096) - arrayPageSize[4];
                    }
                    $("#overlay").css({ top: newTop + 'px' });
                }
            }
            imageNum = 0;
            // if data is not provided by jsonData parameter
            if (!opts.jsonData) {
                opts.imageArray = [];
                // if image is NOT part of a set..				
                if (!imageLink.rel || (imageLink.rel == '')) {
                    // add single image to Lightbox.imageArray
                    var s = '';
                    if (imageLink.title) {
                        s = imageLink.title;
                    } else if ($(this).children(':first-child').attr('title')) {
                        s = $(this).children(':first-child').attr('title');
                    }
                    opts.imageArray.push(new Array(imageLink.href, opts.displayTitle ? s : ''));
                } else {
                    // if image is part of a set..
                    $("a").each(function () {
                        if (this.href && (this.rel == imageLink.rel)) {
                            var title = '';
                            var caption = '';
                            var captionText = '';
                            var jqThis = $(this);
                            if (this.title) {
                                title = this.title;
                            } else if (jqThis.children('img:first-child').attr('title')) {
                                title = jqThis.children('img:first-child').attr('title'); //grab the title from the image if the link lacks one
                            }
                            if (jqThis.parent().next('.gallery-caption').html()) {
                                var jq = jqThis.parent().next('.gallery-caption');
                                caption = jq.html();
                                captionText = jq.text();
                            } else if (jqThis.next('.wp-caption-text').html()) {
                                caption = jqThis.next('.wp-caption-text').html();
                                captionText = jqThis.next('.wp-caption-text').text();
                            }
                            title = $.trim(title);
                            captionText = $.trim(captionText);
                            if (title.toLowerCase() == captionText.toLowerCase()) {
                                title = caption; //to keep linked captions
                                caption = ''; //but not duplicate the text								
                            }
							var s = '';
							if (title != '') {
								s = '<span id="titleText">' + title + '</span>';
							} 
							if (caption != '') {
								if (title != ''){
									s += '<br />';
								} 
								s += '<span id="captionText">' + caption +'</span>';
							}
                            opts.imageArray.push(new Array(this.href, opts.displayTitle ? s : ''));
                        }
                    });
                }
            }
            if (opts.imageArray.length > 1) {
                for (i = 0; i < opts.imageArray.length; i++) {
                    for (j = opts.imageArray.length - 1; j > i; j--) {
                        if (opts.imageArray[i][0] == opts.imageArray[j][0]) {
                            opts.imageArray.splice(j, 1);
                        }
                    }
                }
                while (opts.imageArray[imageNum][0] != imageLink.href) { imageNum++; }
            }
            // calculate top and left offset for the lightbox
			if(JQLBSettings['jqlb_popup_size_fix']=='1')
			setLightBoxPos(10, arrayPagePos[0]).show();
			else
            	setLightBoxPos(arrayPagePos[1], arrayPagePos[0]).show();
            changeImage(imageNum);
        };
		
		function setLightBoxPos(newTop, newLeft) {        
            if (opts.resizeSpeed > 0) {			
                $('#lightbox').animate({ top: newTop }, 250, 'linear');
                return $('#lightbox').animate({ left: newLeft }, 250, 'linear');
            }
            return $('#lightbox').css({ top: newTop + 'px', left: newLeft + 'px' });
        }
		
        function changeImage(imageNum) {
            if (opts.inprogress == false) {
                opts.inprogress = true;
                opts.activeImage = imageNum;
                // hide elements during transition
                $('#loading').show();
                $('#lightboxImage').hide();
                $('#hoverNav').hide();
                $('#prevLink').hide();
                $('#nextLink').hide();
                doChangeImage();
            }
        };

        function doChangeImage() {
            opts.imgPreloader = new Image();
            opts.imgPreloader.onload = function () {
                $('#lightboxImage').attr('src', opts.imageArray[opts.activeImage][0]);
                doScale();  // once image is preloaded, resize image container
                preloadNeighborImages();
            };
            opts.imgPreloader.src = opts.imageArray[opts.activeImage][0];
        };

        function doScale() {
            if (!opts.imgPreloader) {
                return;
            }
            var newWidth = opts.imgPreloader.width;
            var newHeight = opts.imgPreloader.height;
			var seted_widt_max=parseInt(JQLBSettings['jqlb_maximum_width']);
			var seted_height_max=parseInt(JQLBSettings['jqlb_maximum_height']);
            var arrayPageSize = getPageSize();  
			var noScrollWidth = (arrayPageSize[2] < arrayPageSize[0]) ? arrayPageSize[0] : arrayPageSize[2]; //if viewport is smaller than page, use page width.
			$("#overlay").css({ width: noScrollWidth + 'px', height: arrayPageSize[1] + 'px' });  
            var maxHeight = (arrayPageSize[3]) - ($("#imageDataContainer").height() + (2 * opts.borderSize));
            var maxWidth = (arrayPageSize[2]) - (2*opts.borderSize);	
			if (opts.fitToScreen){
				var displayWidth = maxWidth-opts.marginSize;	
				if(seted_widt_max<maxWidth && seted_widt_max>0)
					displayWidth = seted_widt_max-opts.marginSize;
				var displayHeight = maxHeight-opts.marginSize;	
				if(seted_height_max<maxHeight && seted_height_max>0)
					displayHeight = seted_height_max-opts.marginSize;	
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
			var arrayPageScroll = getPageScroll();
			var centerY = arrayPageScroll[1] + (maxHeight * 0.5);
			if(JQLBSettings['jqlb_popup_size_fix']=='1')
				var newTop =(maxHeight-newHeight) * 0.5;
			else
				var newTop = centerY - newHeight * 0.5;
			var newLeft = arrayPageScroll[0];
			$('#lightboxImage').width(newWidth).height(newHeight);
			resizeImageContainer(newWidth, newHeight, newTop, newLeft);           
        }
/*2.28.4 -  Compatible with wordpress 3.6.*/
        function resizeImageContainer(imgWidth, imgHeight, lightboxTop, lightboxLeft) {
            opts.widthCurrent = $("#outerImageContainer").outerWidth();
			if(parseInt(JQLBSettings['jqlb_maximum_width']) && parseInt(JQLBSettings['jqlb_maximum_width'])>0){
				if(parseInt(JQLBSettings['jqlb_maximum_width'])<opts.widthCurrent){
					opts.widthCurrent=parseInt(JQLBSettings['jqlb_maximum_width']);
				}
			}
			
            opts.heightCurrent = $("#outerImageContainer").outerHeight();
			if(parseInt(JQLBSettings['jqlb_maximum_height']) && parseInt(JQLBSettings['jqlb_maximum_height'])>0){
				if(parseInt(JQLBSettings['jqlb_maximum_height'])<opts.heightCurrent){
					opts.heightCurrent=parseInt(JQLBSettings['jqlb_maximum_height']);
				}
			}
            var widthNew = Math.max(350, imgWidth + (opts.borderSize * 2));
            var heightNew = (imgHeight + (opts.borderSize * 2));
            // scalars based on change from old to new
            opts.xScale = (widthNew / opts.widthCurrent) * 100;
            opts.yScale = (heightNew / opts.heightCurrent) * 100;           
            setLightBoxPos(lightboxTop, lightboxLeft);                   
            updateDetails(); //toyNN: moved updateDetails() here, seems to work fine.    
			$('#imageDataContainer').animate({ width: widthNew }, opts.resizeSpeed, 'linear');
			$('#outerImageContainer').animate({ width: widthNew }, opts.resizeSpeed, 'linear', function () {
				$('#outerImageContainer').animate({ height: heightNew }, opts.resizeSpeed, 'linear', function () {
					showImage();
				});
			});
			updateNav();
            $('#prevLink').height(imgHeight);
            $('#nextLink').height(imgHeight);
        };

        function showImage() {
            //assumes updateDetails have been called earlier!
            $("#imageData").show();
            $('#caption').show();
            //$('#imageDataContainer').slideDown(400);
            //$("#imageDetails").hide().fadeIn(400);		
            $('#loading').hide();
            if (opts.resizeSpeed > 0) {
                $('#lightboxImage').fadeIn("fast");
            } else {
                $('#lightboxImage').show();
            }
            opts.inprogress = false;
        };
		
		function preloadNeighborImages() {
            if (opts.loopImages && opts.imageArray.length > 1) {
                preloadNextImage = new Image();
                preloadNextImage.src = opts.imageArray[(opts.activeImage == (opts.imageArray.length - 1)) ? 0 : opts.activeImage + 1][0]
                preloadPrevImage = new Image();
                preloadPrevImage.src = opts.imageArray[(opts.activeImage == 0) ? (opts.imageArray.length - 1) : opts.activeImage - 1][0]
            } else {
                if ((opts.imageArray.length - 1) > opts.activeImage) {
                    preloadNextImage = new Image();
                    preloadNextImage.src = opts.imageArray[opts.activeImage + 1][0];
                }
                if (opts.activeImage > 0) {
                    preloadPrevImage = new Image();
                    preloadPrevImage.src = opts.imageArray[opts.activeImage - 1][0];
                }
            }
        };


        function updateDetails() {
            $('#numberDisplay').html('');
            $('#caption').html('').hide();
            if (opts.imageArray[opts.activeImage][1]) {
                $('#caption').html(opts.imageArray[opts.activeImage][1]).show();
            }
            var nav_html = '';
            var prev = '';
            var pos = (opts.imageArray.length > 1) ? opts.strings.image + (opts.activeImage + 1) + opts.strings.of + opts.imageArray.length : '';
            var link = (opts.displayDownloadLink) ? '<a href="' + opts.imageArray[opts.activeImage][0] + '" id="downloadLink" download="'+opts.imageArray[opts.activeImage][0].replace(/^.*\/|\.[^.]*$/g, '')+'.'+opts.imageArray[opts.activeImage][0].split('.').pop()+'" target="'+opts.linkTarget+'">' + opts.strings.download + '</a>' : '';
            var next = '';
            if (opts.imageArray.length > 1 && !opts.disableNavbarLinks) {	 // display previous / next text links   			           
                if ((opts.activeImage) > 0 || opts.loopImages) {
                    prev = '<a title="' + opts.strings.prevLinkTitle + '" href="#" id="prevLinkText">' + opts.strings.prevLinkText + "</a>";
                }
                if (((opts.activeImage + 1) < opts.imageArray.length) || opts.loopImages) {
                    next += '<a title="' + opts.strings.nextLinkTitle + '" href="#" id="nextLinkText">' + opts.strings.nextLinkText + "</a>";
                }
            }
            nav_html = prev + nav_html + pos + link + next;
            if (nav_html != '' && JQLBSettings['jqlb_show_text_for_image']=='1') {
                $('#numberDisplay').html(nav_html).show();
            }
        };

        function updateNav() {
            if (opts.imageArray.length > 1) {
                $('#hoverNav').show();
                // if loopImages is true, always show next and prev image buttons 
                if (opts.loopImages) {
                    $('#prevLink,#prevLinkText').show().click(function () {
                        changeImage((opts.activeImage == 0) ? (opts.imageArray.length - 1) : opts.activeImage - 1); return false;
                    });
                    $('#nextLink,#nextLinkText').show().click(function () {
                        changeImage((opts.activeImage == (opts.imageArray.length - 1)) ? 0 : opts.activeImage + 1); return false;
                    });
                } else {
                    // if not first image in set, display prev image button
                    if (opts.activeImage != 0) {
                        $('#prevLink,#prevLinkText').show().click(function () {
                            changeImage(opts.activeImage - 1); return false;
                        });
                    }
                    // if not last image in set, display next image button
                    if (opts.activeImage != (opts.imageArray.length - 1)) {
                        $('#nextLink,#nextLinkText').show().click(function () {
                            changeImage(opts.activeImage + 1); return false;
                        });
                    }
                }
				if(JQLBSettings ['jqlb_keyboard_navigation']=='1'){
                	enableKeyboardNav();
				}
            }
        };

        function end() {
            disableKeyboardNav();
            $('#lightbox').hide();
            $('#overlay').fadeOut();
            $('select, object, embed').show();
        };

        function keyboardAction(e) {
            var o = e.data.opts;
            var keycode = e.keyCode;
            var escapeKey = 27;
            var key = String.fromCharCode(keycode).toLowerCase();
            if ((key == 'x') || (key == 'o') || (key == 'c') || (keycode == escapeKey)) { // close lightbox
                end();
            } else if ((key == 'p') || (keycode == 37)) { // display previous image
                if (o.loopImages) {
                    disableKeyboardNav();
                    changeImage((o.activeImage == 0) ? (o.imageArray.length - 1) : o.activeImage - 1);
                }
                else if (o.activeImage != 0) {
                    disableKeyboardNav();
                    changeImage(o.activeImage - 1);
                }
            } else if ((key == 'n') || (keycode == 39)) { // display next image
                if (opts.loopImages) {
                    disableKeyboardNav();
                    changeImage((o.activeImage == (o.imageArray.length - 1)) ? 0 : o.activeImage + 1);
                }
                else if (o.activeImage != (o.imageArray.length - 1)) {
                    disableKeyboardNav();
                    changeImage(o.activeImage + 1);
                }
            }          
            return false;
        };
        function enableKeyboardNav() {			
			$(document).bind('keydown', {opts: opts}, keyboardAction);
        };
        function disableKeyboardNav() {
            $(document).unbind('keydown');
        };
    };
    $.fn.lightbox.parseJsonData = function(data) {
        var imageArray = [];
        $.each(data, function () {
            imageArray.push(new Array(this.url, this.title));
        });
        return imageArray;
    };
    $.fn.lightbox.defaults = {
		adminBarHeight:28,
        overlayOpacity: (JQLBSettings['jqlb_overlay_opacity']/100),
        borderSize: (JQLBSettings['jqlb_border_width']),
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
			prevLinkTitle: JQLBSettings ['jqlb_previous_image_title'],
			nextLinkTitle:  JQLBSettings ['jqlb_next_image_title'],
			prevLinkText:  '&laquo; Previous',
			nextLinkText:  'Next &raquo;',
			closeTitle: JQLBSettings ['jqlb_close_image_title'],
			image: 'Image ',
			of: ' of ',
			download: 'Download'
		};
		$('a[rel^="lightbox"]').lightbox({
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