jQuery(document).ready(function($) {

    var pdfembPagesViewer = PDFEMB_NS.pdfembPagesViewer;

    var pdfembPagesViewerPremium = function () {
        pdfembPagesViewer.apply(this, arguments);
    };

    pdfembPagesViewerPremium.prototype = new pdfembPagesViewer();

    if (pdfemb_trans.continousscroll) {

        pdfembPagesViewerPremium.prototype.scrollPageIntoView = function (pageNum) {
            var pagesContainer = this.divContainer.find('.pdfemb-pagescontainer');
            var innerdiv = pagesContainer.find('.pdfemb-page' + pageNum);
            if (innerdiv.length == 0) {
                return;
            }
            var offset = innerdiv.position();
            var cscrolltop = pagesContainer[0].scrollTop;
            pagesContainer[0].scrollTop = cscrolltop + offset.top;
        };

        pdfembPagesViewerPremium.prototype.getTopVisiblePageNum = function () {
            var pagesContainer = this.divContainer.find('.pdfemb-pagescontainer');
            var innerdivs = pagesContainer.find('.pdfemb-inner-div');

            var max = innerdivs.length,
                min = 0, c = 0;

            var marginOfError = (this.divContainer.height() / 10) * (this.zoom / 100);

            while (max > min && c < 50) {
                ++c;

                var i = min + Math.floor((max - min) / 2);

                var innerdiv = innerdivs[i];
                var top = Math.floor($(innerdiv).position().top);

                if (top < marginOfError) {
                    min = i;
                }
                else if (top > marginOfError) {
                    max = i;
                }
                else {
                    return i + 1; // Adjust back to 1-based page nums
                }
            }

            return min + 1; // Adjust back to 1-based page nums
        };

    }

    pdfembPagesViewerPremium.prototype.pdfembWantMobile = function(wantWidth, wantHeight) {
        var divContainer = this.divContainer;

        if (divContainer.data('fullScreen') == 'on') {
            return false;
        }

        var mobileWidth = parseInt(divContainer.data('mobile-width'), 10);
        if (isNaN(mobileWidth) || mobileWidth < 0) {
            mobileWidth = 500;
        }

        return wantWidth < mobileWidth;
    };

    pdfembPagesViewerPremium.prototype.pdfembMakeMobile = function() {
        var wantMobile = this.wantMobile;

        var divContainer = this.divContainer;
        var pagesContainer = divContainer.find('div.pdfemb-pagescontainer');
        if (wantMobile) {
            if (divContainer.find('.pdfemb-wantmobile').length == 0) {

                var wantMobileWrapper = $('<div></div>', {'class': 'pdfemb-inner-div-wantmobile pdfemb-wantmobile'});

                var wantMobileFS = $('<div></div>', {'class': 'pdfemb-wantmobile-fsarea'});
                var wantMobileFSWrapper = $('<div></div>', {'class': 'pdfemb-inner-div-wantmobile-fswrap pdfemb-wantmobile'})
                    .append(wantMobileFS
                        .append(document.createTextNode(pdfemb_trans.objectL10n.viewinfullscreen)));

                this.bindFullScreen(wantMobileFS);

                divContainer.prepend(wantMobileWrapper);
                divContainer.prepend(wantMobileFSWrapper);

                // Hide toolbars
                divContainer.find('.pdfemb-toolbar-fixed').hide();
                // Tell floating toolbars not to show
                divContainer.find('.pdfemb-toolbar-hover').data('no-hover', true);
            }
        }
        else {
            divContainer.find('.pdfemb-wantmobile').remove();

            divContainer.find('.pdfemb-toolbar-fixed').show();
            divContainer.find('.pdfemb-toolbar-hover').data('no-hover', false);
        }
    };

    pdfembPagesViewer.prototype.addMoreToolbar = function(toolbar) {
        var self = this;
        var divContainer = self.divContainer;

        if (divContainer.data('download') == 'on') {
            var downloadbtn = $('<button class="pdfemb-download" title="'+pdfemb_trans.objectL10n.download+'" type="button"></button>');
            var downloadurl = divContainer.data('pdf-url');
            if (downloadurl) {
                downloadbtn.on('click', function (e) {
                    self.download(downloadurl, divContainer.data('download-nonce'), divContainer.data('tracking'));
                });
                toolbar.append(downloadbtn);
            }
        }

        var fsbtn = $('<button class="pdfemb-fs" title="'+pdfemb_trans.objectL10n.fullscreen+'" type="button"></button>');

        if (divContainer.data('fullScreen') == 'on') {
            fsbtn.addClass('pdfemb-toggled');
            fsbtn.on('click', function (e){
                // Close full screen window
                divContainer.closest('.pdfemb-fsp-wrapper').trigger('closePopup');
                divContainer.data('fullScreenClosed', 'true');
            });
        }
        else {
            self.bindFullScreen(fsbtn);
        }
        toolbar.append(fsbtn);


        // Change Page area so you can jump

        var oldPageArea = toolbar.find('div.pdfemb-page-area');
        var oldPageNum = oldPageArea.find('span.pdfemb-page-num');

        var changePageTextBox = function() {
            var newPageInput = $("<input />", {'type': 'text', 'class': 'pdfemb-page-num'});

            newPageInput.on('keyup', function(e) {
                if (keyCode = e.keyCode ? e.keyCode : e.which == 13) {
                    var newPage = parseInt(newPageInput.val());
                    if (!isNaN(newPage) && newPage > 0) {
                        divContainer.trigger('pdfembGotopage', newPage);
                    }
                }
            });

            newPageInput.val(oldPageNum.text());
            oldPageNum.replaceWith(newPageInput);
            oldPageArea.off('click');
        };

        if (divContainer.data('pagetextbox') == 'on') {
            // Switch to text box version of page number now
            changePageTextBox();
        }
        else {
            // Wait until it's clicked on
            oldPageArea.on('click', changePageTextBox);
        }

    };

    pdfembPagesViewer.prototype.download = function (downloadurl, downloadnonce, tracking) {

        if (downloadnonce !== undefined && downloadnonce != ''){
            downloadurl += '&pdfemb-nonce=' + downloadnonce;
        }

        setTimeout( function() {
                window.open(downloadurl);
            },
            100)

        if (tracking == 'on') {
            // Count the download
            var $ = jQuery;
            var data = {
                action: 'pdfemb_count_download',
                pdf_url: downloadurl
            };
            // the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
            $.post(pdfemb_trans.ajaxurl, data, function(response) {
                // Count was recorded
            });
        }
    };


    pdfembPagesViewer.prototype.bindFullScreen = function(fsbtn) {
        var self = this;
        var dC = self.divContainer;

        fsbtn.fullScreenPopup({
            popup: function () {
                var _pdfDoc = self.pdfDoc;
                var _pageNum = self.currentPageNum;
                var _showIsSecure = dC.data('showIsSecure');
                var _pdfurl = dC.data('pdf-url');
                var _scrollbar = dC.data('scrollbar');
                var _download = dC.data('download');
                var _tracking = dC.data('tracking');
                var _newwindow = dC.data('newwindow');
                var _scrolltotop = dC.data('scrolltotop');
                var _downloadNonce = dC.data('download-nonce');
                var _disablerightclick = dC.data('disablerightclick');
                var _pagetextbox = dC.data('pagetextbox');
                var _startzoom = dC.data('startzoom');
                var _startfpzoom = dC.data('startfpzoom');

                var fsDivContainer = $('<div class="pdfemb-viewer"></div>');
                fsDivContainer.data('pdfDoc', _pdfDoc);
                fsDivContainer.data('pagenum', _pageNum);
                fsDivContainer.data('showIsSecure', _showIsSecure);
                fsDivContainer.data('pdf-url', _pdfurl);
                fsDivContainer.data('scrollbar', _scrollbar);
                fsDivContainer.data('download', _download);
                fsDivContainer.data('tracking', _tracking);
                fsDivContainer.data('newwindow', _newwindow);
                fsDivContainer.data('scrolltotop', _scrolltotop);
                fsDivContainer.data('disablerightclick', _disablerightclick);
                fsDivContainer.data('pagetextbox', _pagetextbox);
                fsDivContainer.data('startzoom', _startzoom);
                fsDivContainer.data('startfpzoom', _startfpzoom);
                fsDivContainer.data('width', 'max');
                fsDivContainer.data('height', 'auto');
                fsDivContainer.data('toolbar', 'bottom');
                fsDivContainer.data('toolbar-fixed', 'on');
                fsDivContainer.data('fullScreen', 'on');
                if (_downloadNonce !== undefined) {
                    fsDivContainer.data('download-nonce', _downloadNonce);
                }
                fsDivContainer.pdfEmbedder();

                $(document).on("keyup", function(e){
                    var pagesContainer = fsDivContainer.find('.pdfemb-pagescontainer');

                    var keyCode = e.keyCode ? e.keyCode : e.which;
                    if (keyCode == 33) {
                        // Page UP
                        pagesContainer.trigger('pdfembGotoAction', 'PrevPage');
                    }
                    else if (keyCode == 34) {
                        // Page Down
                        pagesContainer.trigger('pdfembGotoAction', 'NextPage');
                    }
                    else if (keyCode >= 37 && keyCode <= 40) {
                        // Left 37, Up 38, Right 39, Down 40

                        var event = document.createEvent('Event');
                        //event.initCustomEvent( "pdfembKeyboardPan", true, false, {data: {'keyCode': keyCode}});
                        event.initEvent( "pdfembKeyboardPan", true, false);
                        event.detail = {'keyCode': keyCode};

                        fsDivContainer.find('.grab-to-pan-grab').each(function(i, elt) {elt.dispatchEvent(event)});
                    }
                    else if (keyCode == 187 || keyCode == 109 || keyCode == 61 || keyCode == 43) {
                        // Zoom In
                        pagesContainer.trigger('changeZoom', 10);
                    }
                    else if (keyCode == 189 || keyCode == 107 || keyCode == 45 || keyCode == 173) {
                        // Zoom Out
                        pagesContainer.trigger('changeZoom', -10);
                    }

                });

                return fsDivContainer;
            },
            close: false,
            mainWrapperClass: 'pdfemb-fsp-wrapper',
            contentWrapperClass: 'pdfemb-fsp-content',
            inlineStyles: false
        });
    };

    pdfembPagesViewer.prototype.jumpToTop = function() {
        var self = this;
        var divContainer = self.divContainer;
        if (divContainer.length > 0 && 'scrollIntoView' in divContainer[0]) {
            if (divContainer.data('scrolltotop') == 'on') {
                divContainer[0].scrollIntoView();
            }
        }
    };


    pdfembPagesViewer.prototype.preRenderCanvas = function(ctx, pageNum, zoom) {
        var divContainer = this.divContainer;
        var watermark_map = pdfemb_trans.watermark_map;

        if (!$.isArray(watermark_map)
            || watermark_map.length == 0) {
            return;
        }

        var url = divContainer.data('pdf-url');
        if (url.search("/?pdfemb-serveurl=") === -1) {
            return;
        }

        if (typeof(pdfemb_trans.watermark_evenpagesonly) != 'undefined' && (pdfemb_trans.watermark_evenpagesonly && pageNum % 2 !== 0)) {
            return;
        }

        var xmargin=5, y=10;
        var xalign = 'center';
        var rotate = 45;
        var fontsize = 20;
        for (var i=0 ; i < watermark_map.length ; ++i) {
            var thisMap = watermark_map[i];
            if ($.isArray(thisMap) && thisMap.length >= 1) {
                var text = thisMap[0];
                ctx.save();

                ctx.lineWidth = 1;
                ctx.fillStyle = "#444444";
                ctx.lineStyle = "#111111";
                ctx.textAlign = 'left';
                ctx.font = '20pt sans-serif';
                ctx.globalAlpha = 0.1;
                if (thisMap.length >= 2) {
                    xmargin = thisMap[1];

                    if (thisMap.length >= 3) {
                        y = thisMap[2];

                        if (thisMap.length >= 4) {
                            xalign = thisMap[3];

                            if (thisMap.length >= 5) {
                                rotate = thisMap[4];

                                if (thisMap.length >= 6) {
                                    fontsize = thisMap[5];

                                    ctx.font = (Math.round(100*fontsize*(ctx.canvas.width/1000))/100)+'pt sans-serif';

                                    if (thisMap.length >= 7 && typeof(thisMap[6]) == 'object') {
                                        var styleList = thisMap[6];
                                        for (var prop in styleList) {
                                            ctx[prop] = styleList[prop];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                var mtwidth = ctx.measureText(text).width;
                var mtheight = 10;

                var drawx, drawy = ctx.canvas.height * y / 100;
                var textx, texty;

                if (xalign == 'left') {
                    drawx = ctx.canvas.width * xmargin / 100;
                    textx = 0;
                    texty = 0;
                }
                else if (xalign == 'right') {
                    drawx = ctx.canvas.width * ( 1 - xmargin / 100 );
                    textx = -mtwidth;
                    texty = 0;
                }
                else {
                    drawx = (ctx.canvas.width) / 2;
                    textx =  -mtwidth / 2;
                    texty = 0;
                }

                ctx.translate(drawx, drawy);

                ctx.rotate(-Math.PI * rotate / 360);

                ctx.fillText(text, textx, texty);

                ctx.restore();

                y += 10;
            }
        }
    }



    PDFEMB_NS.pdfembPagesViewerUsable = pdfembPagesViewerPremium;

    // Premium-specific 'global' libs


    var pdfembAnnotationsLayerBuilder = (function AnnotationsLayerBuilderClosure() {
        /**
         * @param {AnnotationsLayerBuilderOptions} options
         * @constructs AnnotationsLayerBuilder
         */
        function AnnotationsLayerBuilder(options) {
            this.pageDiv = options.pageDiv;
            this.pdfPage = options.pdfPage;
            this.linkService = options.linkService;
            this.div = null;
        }
        AnnotationsLayerBuilder.prototype =
            /** @lends AnnotationsLayerBuilder.prototype */ {

            /**
             * @param {PageViewport} viewport
             */
            setupAnnotations:
                function AnnotationsLayerBuilder_setupAnnotations(viewport, newwindow) {

                    var pdfPage = this.pdfPage;
                    var self = this;
                    var pageDiv = this.pageDiv;

                    var getannotations_parameters = {
                        intent: 'display'
                    };

                    pdfPage.getAnnotations(getannotations_parameters).then(function (annotationsData) {
                        viewport = viewport.clone({ dontFlip: true });
                        var transform = viewport.transform;

                        parameters = {
                            viewport: viewport,
                            div: self.pageDiv,
                            annotations: annotationsData,
                            page: pdfPage,
                            linkService: self.linkService
                        };

                        if (self.div) {
                            // If an annotationLayer already exists, refresh its children's
                            // transformation matrices.
                            PDFJS.AnnotationLayer.update(parameters);
                        } else {
                            // Create an annotation layer div and render the annotations
                            // if there is at least one annotation.
                            if (annotationsData.length === 0) {
                                return;
                            }

                            self.div = document.createElement('div');
                            self.div.className = 'annotationLayer pdfembAnnotationLayer';

                            self.pageDiv.appendChild(self.div);
                            parameters.div = self.div;

                            PDFJS.AnnotationLayer.render(parameters);
                            if (typeof mozL10n !== 'undefined') {
                                mozL10n.translate(self.div);
                            }

                            // Get canvas
                            var canvas = self.pageDiv.getElementsByTagName("canvas")[0];

                            self.div.style.left = canvas.style.left;
                            self.div.style.top = canvas.style.top;

                            jQuery(self.div).find('a').each(function(i, link) {
                                if (newwindow == 'on') {
                                    link.target = '_blank';
                                }

                                link.title = '';

                                jQuery(link).on('touchstart', function(e){
                                    e.stopPropagation();
                                });
                            });

                        }

                    });
                },

            hide: function () {
                if (!this.div) {
                    return;
                }
                this.div.setAttribute('hidden', 'true');
            }
        };
        return AnnotationsLayerBuilder;
    })();

    /**
     * @constructor
     * @implements IPDFAnnotationsLayerFactory
     */
    PDFEMB_NS.pdfembPremiumAnnotationsLayerFactory = function() {}
    PDFEMB_NS.pdfembPremiumAnnotationsLayerFactory.prototype = {
        /**
         * @param {HTMLDivElement} pageDiv
         * @param {PDFPage} pdfPage
         * @returns {AnnotationsLayerBuilder}
         */
        createAnnotationsLayerBuilder: function (pageDiv, pdfPage) {
            return new pdfembAnnotationsLayerBuilder({
                pageDiv: pageDiv,
                pdfPage: pdfPage,
                linkService: new pdfembSimpleLinkService(pageDiv)
            });
        }
    };


    var pdfembSimpleLinkService = (function SimpleLinkServiceClosure() {
        function SimpleLinkService(pageDiv) {
            this.pageDiv = pageDiv;
        }

        SimpleLinkService.prototype = {
            /**
             * @param dest - The PDF destination object.
             */
            navigateTo: function (dest) {
                if (dest) {
                    jQuery(this.pageDiv).parent().trigger('pdfembGotoHash', {'dest': dest});
                }
            },
            /**
             * @param dest - The PDF destination object.
             * @returns {string} The hyperlink to the PDF object.
             */
            getDestinationHash: function (dest) {
                return '#';
            },
            /**
             * @param hash - The PDF parameters/hash.
             * @returns {string} The hyperlink to the PDF object.
             */
            getAnchorUrl: function (hash) {
                return '#';
            },
            /**
             * @param {string} hash
             */
            setHash: function (hash) {},
            /**
             * @param {string} action
             */
            executeNamedAction: function (action) {
                if (action) {
                    //linkService.navigateTo(dest);
                    jQuery(this.pageDiv).parent().trigger('pdfembGotoAction', action);
                }
            },
            /**
             * @param {number} pageNum - page number.
             * @param {Object} pageRef - reference to the page.
             */
            cachePageRef: function (pageNum, pageRef) {}
        };
        return SimpleLinkService;
    })();

    // Text Layer

    var pdfembTextLayerBuilder = (function TextLayerBuilderClosure() {
        /**
         * @param {TextLayerBuilderOptions} options
         * @constructs TextLayerBuilder
         */
        function TextLayerBuilder(options) {
            this.pageDiv = options.pageDiv;
            this.pdfPage = options.pdfPage;
            this.linkService = options.linkService;
            this.div = null;
        }
        TextLayerBuilder.prototype =
            /** @lends TextLayerBuilder.prototype */ {

            /**
             * @param {PageViewport} viewport
             */
            setupText:
                function TextLayerBuilder_setupText(viewport) {

                    var pdfPage = this.pdfPage;
                    var self = this;
                    var pageDiv = this.pageDiv;

                    var gettext_parameters = {
                        //intent: 'display'
                    };

                    pdfPage.getTextContent(gettext_parameters).then(function (textContent) {
                        viewport = viewport.clone({ dontFlip: false });

                        parameters = {
                            viewport: viewport,
                            textContent: textContent,
                            page: pdfPage,
                            enhanceTextSelection: true
                        };

                        // Create a text layer div and render the text layer
                        if (textContent.length === 0) {
                            return;
                        }

                        var tls = self.pageDiv.getElementsByClassName('pdfembTextLayer');

                        if (tls.length > 0) {
                            self.div = tls[0];
                            while (self.div.firstChild) { self.div.removeChild(self.div.firstChild); }
                        }
                        else {
                            self.div = document.createElement('div');
                            self.div.className = 'textLayer pdfembTextLayer';
                            self.pageDiv.appendChild(self.div);
                        }

                        parameters.container = self.div;

                        var tsk = PDFJS.renderTextLayer(parameters);

                        /*tsk.promise.then(function() {
                           tsk.expandTextDivs(true);
                        });*/

                        if (typeof mozL10n !== 'undefined') {
                            mozL10n.translate(self.div);
                        }

                        // Get canvas
                       /* var canvas = self.pageDiv.getElementsByTagName("canvas")[0];

                        self.div.style.left = canvas.style.left;
                        self.div.style.top = canvas.style.top;
*/

                    });
                },

            hide: function () {
                if (!this.div) {
                    return;
                }
                this.div.setAttribute('hidden', 'true');
            }
        };
        return TextLayerBuilder;
    })();

    /**
     * @constructor
     * @implements IPDFTextLayerFactory
     */
    PDFEMB_NS.pdfembPremiumTextLayerFactory = function() {}
    PDFEMB_NS.pdfembPremiumTextLayerFactory.prototype = {
        /**
         * @param {HTMLDivElement} pageDiv
         * @param {PDFPage} pdfPage
         * @returns {TextLayerBuilder}
         */
        createTextLayerBuilder: function (pageDiv, pdfPage) {
            return new pdfembTextLayerBuilder({
                pageDiv: pageDiv,
                pdfPage: pdfPage,
                linkService: new pdfembSimpleLinkService(pageDiv)
            });
        }
    };



// optimised CSS custom property getter/setter
    PDFEMB_NS.pdfembCustomStyle = (function CustomStyleClosure() {

        // As noted on: http://www.zachstronaut.com/posts/2009/02/17/
        //              animate-css-transforms-firefox-webkit.html
        // in some versions of IE9 it is critical that ms appear in this list
        // before Moz
        var prefixes = ['ms', 'Moz', 'Webkit', 'O'];
        var _cache = {};

        function CustomStyle() {}

        CustomStyle.getProp = function get(propName, element) {
            // check cache only when no element is given
            if (arguments.length === 1 && typeof _cache[propName] === 'string') {
                return _cache[propName];
            }

            element = element || document.documentElement;
            var style = element.style, prefixed, uPropName;

            // test standard property first
            if (typeof style[propName] === 'string') {
                return (_cache[propName] = propName);
            }

            // capitalize
            uPropName = propName.charAt(0).toUpperCase() + propName.slice(1);

            // test vendor specific properties
            for (var i = 0, l = prefixes.length; i < l; i++) {
                prefixed = prefixes[i] + uPropName;
                if (typeof style[prefixed] === 'string') {
                    return (_cache[propName] = prefixed);
                }
            }

            //if all fails then set to undefined
            return (_cache[propName] = 'undefined');
        };

        CustomStyle.setProp = function set(propName, element, str) {
            var prop = this.getProp(propName);
            if (prop !== 'undefined') {
                element.style[prop] = str;
            }
        };

        return CustomStyle;
    })();


});


PDFEMB_NS.pdfembGetPDF = function(url, callback) {
	    	
		// 	Get PDF directly
	
	if (url.search("/?pdfemb-serveurl=") == -1) {
		callback(url, false); // false = not secure
		return;
	}

    PDFEMB_NS.pdfembAddAjaxBufferTransport();

	jQuery.ajax({
		   dataType:'arraybuffer',
		   type:'POST',
		   url: url
		}).done(function(blob){

            PDFEMB_NS.pdfemb_rc4ab(pdfemb_trans.k, blob);
	
			var uia = new Uint8Array(blob);
	
		  callback(uia, pdfemb_trans.is_admin); // true = secure
		  
		}).fail(function(jqXHR, textStatus, errorThrown) {
          callback(null, pdfemb_trans.is_admin);
        });


};



PDFEMB_NS.pdfembAddAjaxBufferTransport_added = false;
PDFEMB_NS.pdfembAddAjaxBufferTransport = function () {

    if (PDFEMB_NS.pdfembAddAjaxBufferTransport_added) {
        return;
    }
    PDFEMB_NS.pdfembAddAjaxBufferTransport_added = true;
	
	// http://www.artandlogic.com/blog/2013/11/jquery-ajax-blobs-and-array-buffers/
	/**
	 * Register ajax transports for blob send/recieve and array buffer send/receive via XMLHttpRequest Level 2
	 * within the comfortable framework of the jquery ajax request, with full support for promises.
	 *
	 * Notice the +* in the dataType string? The + indicates we want this transport to be prepended to the list
	 * of potential transports (so it gets first dibs if the request passes the conditions within to provide the
	 * ajax transport, preventing the standard transport from hogging the request), and the * indicates that
	 * potentially any request with any dataType might want to use the transports provided herein.
	 *
	 * Remember to specify 'processData:false' in the ajax options when attempting to send a blob or arraybuffer -
	 * otherwise jquery will try (and fail) to convert the blob or buffer into a query string.
	 */
	jQuery.ajaxTransport("+*", function(options, originalOptions, jqXHR){
	    // Test for the conditions that mean we can/want to send/receive blobs or arraybuffers - we need XMLHttpRequest
	    // level 2 (so feature-detect against window.FormData), feature detect against window.Blob or window.ArrayBuffer,
	    // and then check to see if the dataType is blob/arraybuffer or the data itself is a Blob/ArrayBuffer
	    if (window.FormData && ((options.dataType && (options.dataType == 'blob' || options.dataType == 'arraybuffer'))
	        || (options.data && ((window.Blob && options.data instanceof Blob)
	            || (window.ArrayBuffer && options.data instanceof ArrayBuffer)))
	        ))
	    {
	        return {
	            /**
	             * Return a transport capable of sending and/or receiving blobs - in this case, we instantiate
	             * a new XMLHttpRequest and use it to actually perform the request, and funnel the result back
	             * into the jquery complete callback (such as the success function, done blocks, etc.)
	             *
	             * @param headers
	             * @param completeCallback
	             */
	            send: function(headers, completeCallback){
	                var xhr = new XMLHttpRequest(),
	                    url = options.url || window.location.href,
	                    type = options.type || 'GET',
	                    dataType = options.dataType || 'text',
	                    data = options.data || null,
	                    async = options.async || true;
	 
	                xhr.addEventListener('load', function(){
	                    var res = {};
	 
	                    res[dataType] = xhr.response;
	                    completeCallback(xhr.status, xhr.statusText, res, xhr.getAllResponseHeaders());
	                });
	 
	                xhr.open(type, url, async);
	                xhr.responseType = dataType;
	                xhr.send(data);
	            },
	            abort: function(){
	                jqXHR.abort();
	            }
	        };
	    }
	});
	
	
};

PDFEMB_NS.pdfemb_rc4ab = function(key, ab) {
	var s = [], j = 0, x, res = '';
	var dv = new DataView(ab);

    // Check for Unicode BOM and skip it
    var starty = 0;
    if (dv.getUint8(0) == 0xEF && dv.getUint8(1) == 0xBB && dv.getUint8(2) == 0xBF) {
        starty = 3;
    }

    // Decrypt
	for (var i = 0; i < 256; i++) {
		s[i] = i;
	}
	for (i = 0; i < 256; i++) {
		j = (j + s[i] + key.charCodeAt(i % key.length)) % 256;
		x = s[i];
		s[i] = s[j];
		s[j] = x;
	}
	i = 0;
	j = 0;
	for (var y = starty; y < ab.byteLength; y++) {
		i = (i + 1) % 256;
		j = (j + s[i]) % 256;
		x = s[i];
		s[i] = s[j];
		s[j] = x;
		input = dv.getUint8(y);
		output = input ^ s[(s[i] + s[j]) % 256];
		dv.setUint8(y, output);
	}
};


