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