/*!
 * jQuery Mousewheel 3.1.12
 *
 * Copyright 2014 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */

(function (factory) {
    if ( typeof define === 'function' && define.amd ) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory;
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var toFix  = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'],
        toBind = ( 'onwheel' in document || document.documentMode >= 9 ) ?
                    ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'],
        slice  = Array.prototype.slice,
        nullLowestDeltaTimeout, lowestDelta;

    if ( $.event.fixHooks ) {
        for ( var i = toFix.length; i; ) {
            $.event.fixHooks[ toFix[--i] ] = $.event.mouseHooks;
        }
    }

    var special = $.event.special.mousewheel = {
        version: '3.1.12',

        setup: function() {
            if ( this.addEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.addEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = handler;
            }
            // Store the line height and page height for this particular element
            $.data(this, 'mousewheel-line-height', special.getLineHeight(this));
            $.data(this, 'mousewheel-page-height', special.getPageHeight(this));
        },

        teardown: function() {
            if ( this.removeEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.removeEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = null;
            }
            // Clean up the data we added to the element
            $.removeData(this, 'mousewheel-line-height');
            $.removeData(this, 'mousewheel-page-height');
        },

        getLineHeight: function(elem) {
            var $elem = $(elem),
                $parent = $elem['offsetParent' in $.fn ? 'offsetParent' : 'parent']();
            if (!$parent.length) {
                $parent = $('body');
            }
            return parseInt($parent.css('fontSize'), 10) || parseInt($elem.css('fontSize'), 10) || 16;
        },

        getPageHeight: function(elem) {
            return $(elem).height();
        },

        settings: {
            adjustOldDeltas: true, // see shouldAdjustOldDeltas() below
            normalizeOffset: true  // calls getBoundingClientRect for each event
        }
    };

    $.fn.extend({
        mousewheel: function(fn) {
            return fn ? this.bind('mousewheel', fn) : this.trigger('mousewheel');
        },

        unmousewheel: function(fn) {
            return this.unbind('mousewheel', fn);
        }
    });


    function handler(event) {
        var orgEvent   = event || window.event,
            args       = slice.call(arguments, 1),
            delta      = 0,
            deltaX     = 0,
            deltaY     = 0,
            absDelta   = 0,
            offsetX    = 0,
            offsetY    = 0;
        event = $.event.fix(orgEvent);
        event.type = 'mousewheel';

        // Old school scrollwheel delta
        if ( 'detail'      in orgEvent ) { deltaY = orgEvent.detail * -1;      }
        if ( 'wheelDelta'  in orgEvent ) { deltaY = orgEvent.wheelDelta;       }
        if ( 'wheelDeltaY' in orgEvent ) { deltaY = orgEvent.wheelDeltaY;      }
        if ( 'wheelDeltaX' in orgEvent ) { deltaX = orgEvent.wheelDeltaX * -1; }

        // Firefox < 17 horizontal scrolling related to DOMMouseScroll event
        if ( 'axis' in orgEvent && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
            deltaX = deltaY * -1;
            deltaY = 0;
        }

        // Set delta to be deltaY or deltaX if deltaY is 0 for backwards compatabilitiy
        delta = deltaY === 0 ? deltaX : deltaY;

        // New school wheel delta (wheel event)
        if ( 'deltaY' in orgEvent ) {
            deltaY = orgEvent.deltaY * -1;
            delta  = deltaY;
        }
        if ( 'deltaX' in orgEvent ) {
            deltaX = orgEvent.deltaX;
            if ( deltaY === 0 ) { delta  = deltaX * -1; }
        }

        // No change actually happened, no reason to go any further
        if ( deltaY === 0 && deltaX === 0 ) { return; }

        // Need to convert lines and pages to pixels if we aren't already in pixels
        // There are three delta modes:
        //   * deltaMode 0 is by pixels, nothing to do
        //   * deltaMode 1 is by lines
        //   * deltaMode 2 is by pages
        if ( orgEvent.deltaMode === 1 ) {
            var lineHeight = $.data(this, 'mousewheel-line-height');
            delta  *= lineHeight;
            deltaY *= lineHeight;
            deltaX *= lineHeight;
        } else if ( orgEvent.deltaMode === 2 ) {
            var pageHeight = $.data(this, 'mousewheel-page-height');
            delta  *= pageHeight;
            deltaY *= pageHeight;
            deltaX *= pageHeight;
        }

        // Store lowest absolute delta to normalize the delta values
        absDelta = Math.max( Math.abs(deltaY), Math.abs(deltaX) );

        if ( !lowestDelta || absDelta < lowestDelta ) {
            lowestDelta = absDelta;

            // Adjust older deltas if necessary
            if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
                lowestDelta /= 40;
            }
        }

        // Adjust older deltas if necessary
        if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
            // Divide all the things by 40!
            delta  /= 40;
            deltaX /= 40;
            deltaY /= 40;
        }

        // Get a whole, normalized value for the deltas
        delta  = Math[ delta  >= 1 ? 'floor' : 'ceil' ](delta  / lowestDelta);
        deltaX = Math[ deltaX >= 1 ? 'floor' : 'ceil' ](deltaX / lowestDelta);
        deltaY = Math[ deltaY >= 1 ? 'floor' : 'ceil' ](deltaY / lowestDelta);

        // Normalise offsetX and offsetY properties
        if ( special.settings.normalizeOffset && this.getBoundingClientRect ) {
            var boundingRect = this.getBoundingClientRect();
            offsetX = event.clientX - boundingRect.left;
            offsetY = event.clientY - boundingRect.top;
        }

        // Add information to the event object
        event.deltaX = deltaX;
        event.deltaY = deltaY;
        event.deltaFactor = lowestDelta;
        event.offsetX = offsetX;
        event.offsetY = offsetY;
        // Go ahead and set deltaMode to 0 since we converted to pixels
        // Although this is a little odd since we overwrite the deltaX/Y
        // properties with normalized deltas.
        event.deltaMode = 0;

        // Add event and delta to the front of the arguments
        args.unshift(event, delta, deltaX, deltaY);

        // Clearout lowestDelta after sometime to better
        // handle multiple device types that give different
        // a different lowestDelta
        // Ex: trackpad = 3 and mouse wheel = 120
        if (nullLowestDeltaTimeout) { clearTimeout(nullLowestDeltaTimeout); }
        nullLowestDeltaTimeout = setTimeout(nullLowestDelta, 200);

        return ($.event.dispatch || $.event.handle).apply(this, args);
    }

    function nullLowestDelta() {
        lowestDelta = null;
    }

    function shouldAdjustOldDeltas(orgEvent, absDelta) {
        // If this is an older event and the delta is divisable by 120,
        // then we are assuming that the browser is treating this as an
        // older mouse wheel event and that we should divide the deltas
        // by 40 to try and get a more usable deltaFactor.
        // Side note, this actually impacts the reported scroll distance
        // in older browsers and can cause scrolling to be slower than native.
        // Turn this off by setting $.event.special.mousewheel.settings.adjustOldDeltas to false.
        return special.settings.adjustOldDeltas && orgEvent.type === 'mousewheel' && absDelta % 120 === 0;
    }

}));
;/**
 * @author trixta
 * @version 1.2
 */
(function($){

var mwheelI = {
			pos: [-260, -260]
		},
	minDif 	= 3,
	doc 	= document,
	root 	= doc.documentElement,
	body 	= doc.body,
	longDelay, shortDelay
;

function unsetPos(){
	if(this === mwheelI.elem){
		mwheelI.pos = [-260, -260];
		mwheelI.elem = false;
		minDif = 3;
	}
}

$.event.special.mwheelIntent = {
	setup: function(){
		var jElm = $(this).bind('mousewheel', $.event.special.mwheelIntent.handler);
		if( this !== doc && this !== root && this !== body ){
			jElm.bind('mouseleave', unsetPos);
		}
		jElm = null;
        return true;
    },
	teardown: function(){
        $(this)
			.unbind('mousewheel', $.event.special.mwheelIntent.handler)
			.unbind('mouseleave', unsetPos)
		;
        return true;
    },
    handler: function(e, d){
		var pos = [e.clientX, e.clientY];
		if( this === mwheelI.elem || Math.abs(mwheelI.pos[0] - pos[0]) > minDif || Math.abs(mwheelI.pos[1] - pos[1]) > minDif ){
            mwheelI.elem = this;
			mwheelI.pos = pos;
			minDif = 250;
			
			clearTimeout(shortDelay);
			shortDelay = setTimeout(function(){
				minDif = 10;
			}, 200);
			clearTimeout(longDelay);
			longDelay = setTimeout(function(){
				minDif = 3;
			}, 1500);
			e = $.extend({}, e, {type: 'mwheelIntent'});
            return ($.event.dispatch || $.event.handle).apply(this, arguments);
		}
    }
};
$.fn.extend({
	mwheelIntent: function(fn) {
		return fn ? this.bind("mwheelIntent", fn) : this.trigger("mwheelIntent");
	},
	
	unmwheelIntent: function(fn) {
		return this.unbind("mwheelIntent", fn);
	}
});

$(function(){
	body = doc.body;
	//assume that document is always scrollable, doesn't hurt if not
	$(doc).bind('mwheelIntent.mwheelIntentDefault', $.noop);
});
})(jQuery);
;!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof exports?module.exports=e(jQuery||require("jquery")):e(jQuery)}(function(be){be.fn.jScrollPane=function(o){function s(w,e){var y,b,k,T,C,S,x,D,B,H,P,z,A,W,Y,M,X,L,R,t,E,I,F,V,q,O,G,N,K,Q,U,$,J,Z,_=this,r=!0,a=!0,l=!1,c=!1,o=w.clone(!1,!1).empty(),ee=!1,te=be.fn.mwheelIntent?"mwheelIntent.jsp":"mousewheel.jsp",oe=function(){0<y.resizeSensorDelay?setTimeout(function(){se(y)},y.resizeSensorDelay):se(y)};function se(e){var t,o,s,i,n,r,a,l,c,p,u,d,f,h,g,j,v=!1,m=!1;if(y=e,void 0===b)n=w.scrollTop(),r=w.scrollLeft(),w.css({overflow:"hidden",padding:0}),k=w.innerWidth()+J,T=w.innerHeight(),w.width(k),b=be('<div class="jspPane" />').css("padding",$).append(w.children()),C=be('<div class="jspContainer" />').css({width:k+"px",height:T+"px"}).append(b).appendTo(w);else{if(w.css("width",""),C.css({width:"auto",height:"auto"}),b.css("position","static"),a=w.innerWidth()+J,l=w.innerHeight(),b.css("position","absolute"),v=y.stickToBottom&&(20<(p=x-T)&&p-we()<10),m=y.stickToRight&&(20<(c=S-k)&&c-me()<10),i=a!==k||l!==T,k=a,T=l,C.css({width:k,height:T}),!i&&Z==S&&b.outerHeight()==x)return void w.width(k);Z=S,b.css("width",""),w.width(k),C.find(">.jspVerticalBar,>.jspHorizontalBar").remove().end()}b.css("overflow","auto"),S=e.contentWidth?e.contentWidth:b[0].scrollWidth,x=b[0].scrollHeight,b.css("overflow",""),D=S/k,H=1<(B=x/T)||y.alwaysShowVScroll,(P=1<D||y.alwaysShowHScroll)||H?(w.addClass("jspScrollable"),(t=y.maintainPosition&&(W||X))&&(o=me(),s=we()),H&&(C.append(be('<div class="jspVerticalBar" />').append(be('<div class="jspCap jspCapTop" />'),be('<div class="jspTrack" />').append(be('<div class="jspDrag" />').append(be('<div class="jspDragTop" />'),be('<div class="jspDragBottom" />'))),be('<div class="jspCap jspCapBottom" />'))),L=C.find(">.jspVerticalBar"),R=L.find(">.jspTrack"),z=R.find(">.jspDrag"),y.showArrows&&(F=be('<a class="jspArrow jspArrowUp" />').on("mousedown.jsp",le(0,-1)).on("click.jsp",ye),V=be('<a class="jspArrow jspArrowDown" />').on("mousedown.jsp",le(0,1)).on("click.jsp",ye),y.arrowScrollOnHover&&(F.on("mouseover.jsp",le(0,-1,F)),V.on("mouseover.jsp",le(0,1,V))),ae(R,y.verticalArrowPositions,F,V)),E=T,C.find(">.jspVerticalBar>.jspCap:visible,>.jspVerticalBar>.jspArrow").each(function(){E-=be(this).outerHeight()}),z.on("mouseenter",function(){z.addClass("jspHover")}).on("mouseleave",function(){z.removeClass("jspHover")}).on("mousedown.jsp",function(e){be("html").on("dragstart.jsp selectstart.jsp",ye),z.addClass("jspActive");var t=e.pageY-z.position().top;return be("html").on("mousemove.jsp",function(e){ue(e.pageY-t,!1)}).on("mouseup.jsp mouseleave.jsp",pe),!1}),ne()),P&&(C.append(be('<div class="jspHorizontalBar" />').append(be('<div class="jspCap jspCapLeft" />'),be('<div class="jspTrack" />').append(be('<div class="jspDrag" />').append(be('<div class="jspDragLeft" />'),be('<div class="jspDragRight" />'))),be('<div class="jspCap jspCapRight" />'))),q=C.find(">.jspHorizontalBar"),O=q.find(">.jspTrack"),Y=O.find(">.jspDrag"),y.showArrows&&(K=be('<a class="jspArrow jspArrowLeft" />').on("mousedown.jsp",le(-1,0)).on("click.jsp",ye),Q=be('<a class="jspArrow jspArrowRight" />').on("mousedown.jsp",le(1,0)).on("click.jsp",ye),y.arrowScrollOnHover&&(K.on("mouseover.jsp",le(-1,0,K)),Q.on("mouseover.jsp",le(1,0,Q))),ae(O,y.horizontalArrowPositions,K,Q)),Y.on("mouseenter",function(){Y.addClass("jspHover")}).on("mouseleave",function(){Y.removeClass("jspHover")}).on("mousedown.jsp",function(e){be("html").on("dragstart.jsp selectstart.jsp",ye),Y.addClass("jspActive");var t=e.pageX-Y.position().left;return be("html").on("mousemove.jsp",function(e){fe(e.pageX-t,!1)}).on("mouseup.jsp mouseleave.jsp",pe),!1}),G=C.innerWidth(),re()),function(){if(P&&H){var e=O.outerHeight(),t=R.outerWidth();E-=e,be(q).find(">.jspCap:visible,>.jspArrow").each(function(){G+=be(this).outerWidth()}),G-=t,T-=t,k-=e,O.parent().append(be('<div class="jspCorner" />').css("width",e+"px")),ne(),re()}P&&b.width(C.outerWidth()-J+"px");x=b.outerHeight(),B=x/T,P&&((N=Math.ceil(1/D*G))>y.horizontalDragMaxWidth?N=y.horizontalDragMaxWidth:N<y.horizontalDragMinWidth&&(N=y.horizontalDragMinWidth),Y.css("width",N+"px"),M=G-N,he(X));H&&((I=Math.ceil(1/B*E))>y.verticalDragMaxHeight?I=y.verticalDragMaxHeight:I<y.verticalDragMinHeight&&(I=y.verticalDragMinHeight),z.css("height",I+"px"),A=E-I,de(W))}(),t&&(je(m?S-k:o,!1),ge(v?x-T:s,!1)),b.find(":input,a").off("focus.jsp").on("focus.jsp",function(e){ve(e.target,!1)}),C.off(te).on(te,function(e,t,o,s){X||(X=0),W||(W=0);var i=X,n=W,r=e.deltaFactor||y.mouseWheelSpeed;return _.scrollBy(o*r,-s*r,!1),i==X&&n==W}),j=!1,C.off("touchstart.jsp touchmove.jsp touchend.jsp click.jsp-touchclick").on("touchstart.jsp",function(e){var t=e.originalEvent.touches[0];u=me(),d=we(),f=t.pageX,h=t.pageY,j=!(g=!1)}).on("touchmove.jsp",function(e){if(j){var t=e.originalEvent.touches[0],o=X,s=W;return _.scrollTo(u+f-t.pageX,d+h-t.pageY),g=g||5<Math.abs(f-t.pageX)||5<Math.abs(h-t.pageY),o==X&&s==W}}).on("touchend.jsp",function(e){j=!1}).on("click.jsp-touchclick",function(e){if(g)return g=!1}),y.enableKeyboardNavigation&&function(){var s,i,n=[];P&&n.push(q[0]);H&&n.push(L[0]);b.on("focus.jsp",function(){w.focus()}),w.attr("tabindex",0).off("keydown.jsp keypress.jsp").on("keydown.jsp",function(e){if(e.target===this||n.length&&be(e.target).closest(n).length){var t=X,o=W;switch(e.keyCode){case 40:case 38:case 34:case 32:case 33:case 39:case 37:s=e.keyCode,r();break;case 35:ge(x-T),s=null;break;case 36:ge(0),s=null}return!(i=e.keyCode==s&&t!=X||o!=W)}}).on("keypress.jsp",function(e){if(e.keyCode==s&&r(),e.target===this||n.length&&be(e.target).closest(n).length)return!i}),y.hideFocus?(w.css("outline","none"),"hideFocus"in C[0]&&w.attr("hideFocus",!0)):(w.css("outline",""),"hideFocus"in C[0]&&w.attr("hideFocus",!1));function r(){var e=X,t=W;switch(s){case 40:_.scrollByY(y.keyboardSpeed,!1);break;case 38:_.scrollByY(-y.keyboardSpeed,!1);break;case 34:case 32:_.scrollByY(T*y.scrollPagePercent,!1);break;case 33:_.scrollByY(-T*y.scrollPagePercent,!1);break;case 39:_.scrollByX(y.keyboardSpeed,!1);break;case 37:_.scrollByX(-y.keyboardSpeed,!1)}return i=e!=X||t!=W}}(),y.clickOnTrack&&function(){ce(),H&&R.on("mousedown.jsp",function(i){if(void 0===i.originalTarget||i.originalTarget==i.currentTarget){var n,r=be(this),e=r.offset(),a=i.pageY-e.top-W,l=!0,c=function(){var e=r.offset(),t=i.pageY-e.top-I/2,o=T*y.scrollPagePercent,s=A*o/(x-T);if(a<0)t<W-s?_.scrollByY(-o):ue(t);else{if(!(0<a))return void p();W+s<t?_.scrollByY(o):ue(t)}n=setTimeout(c,l?y.initialDelay:y.trackClickRepeatFreq),l=!1},p=function(){n&&clearTimeout(n),n=null,be(document).off("mouseup.jsp",p)};return c(),be(document).on("mouseup.jsp",p),!1}});P&&O.on("mousedown.jsp",function(i){if(void 0===i.originalTarget||i.originalTarget==i.currentTarget){var n,r=be(this),e=r.offset(),a=i.pageX-e.left-X,l=!0,c=function(){var e=r.offset(),t=i.pageX-e.left-N/2,o=k*y.scrollPagePercent,s=M*o/(S-k);if(a<0)t<X-s?_.scrollByX(-o):fe(t);else{if(!(0<a))return void p();X+s<t?_.scrollByX(o):fe(t)}n=setTimeout(c,l?y.initialDelay:y.trackClickRepeatFreq),l=!1},p=function(){n&&clearTimeout(n),n=null,be(document).off("mouseup.jsp",p)};return c(),be(document).on("mouseup.jsp",p),!1}})}(),function(){if(location.hash&&1<location.hash.length){var e,t,o=escape(location.hash.substr(1));try{e=be("#"+o+', a[name="'+o+'"]')}catch(e){return}e.length&&b.find(o)&&(0===C.scrollTop()?t=setInterval(function(){0<C.scrollTop()&&(ve(e,!0),be(document).scrollTop(C.position().top),clearInterval(t))},50):(ve(e,!0),be(document).scrollTop(C.position().top)))}}(),y.hijackInternalLinks&&function(){if(be(document.body).data("jspHijack"))return;be(document.body).data("jspHijack",!0),be(document.body).delegate('a[href*="#"]',"click",function(e){var t,o,s,i,n,r=this.href.substr(0,this.href.indexOf("#")),a=location.href;if(-1!==location.href.indexOf("#")&&(a=location.href.substr(0,location.href.indexOf("#"))),r===a){t=escape(this.href.substr(this.href.indexOf("#")+1));try{o=be("#"+t+', a[name="'+t+'"]')}catch(e){return}o.length&&(s=o.closest(".jspScrollable"),s.data("jsp").scrollToElement(o,!0),s[0].scrollIntoView&&(i=be(window).scrollTop(),((n=o.offset().top)<i||n>i+be(window).height())&&s[0].scrollIntoView()),e.preventDefault())}})}()):(w.removeClass("jspScrollable"),b.css({top:0,left:0,width:C.width()-J}),C.off(te),b.find(":input,a").off("focus.jsp"),w.attr("tabindex","-1").removeAttr("tabindex").off("keydown.jsp keypress.jsp"),b.off(".jsp"),ce()),y.resizeSensor||!y.autoReinitialise||U?y.resizeSensor||y.autoReinitialise||!U||clearInterval(U):U=setInterval(function(){se(y)},y.autoReinitialiseDelay),y.resizeSensor&&!ee&&(ie(b,oe),ie(w,oe),ie(w.parent(),oe),window.addEventListener("resize",oe),ee=!0),n&&w.scrollTop(0)&&ge(n,!1),r&&w.scrollLeft(0)&&je(r,!1),w.trigger("jsp-initialised",[P||H])}function ie(e,t){var o,s,i=document.createElement("div"),n=document.createElement("div"),r=document.createElement("div"),a=document.createElement("div"),l=document.createElement("div");i.style.cssText="position: absolute; left: 0; top: 0; right: 0; bottom: 0; overflow: scroll; z-index: -1; visibility: hidden;",n.style.cssText="position: absolute; left: 0; top: 0; right: 0; bottom: 0; overflow: scroll; z-index: -1; visibility: hidden;",a.style.cssText="position: absolute; left: 0; top: 0; right: 0; bottom: 0; overflow: scroll; z-index: -1; visibility: hidden;",r.style.cssText="position: absolute; left: 0; top: 0;",l.style.cssText="position: absolute; left: 0; top: 0; width: 200%; height: 200%;";var c=function(){r.style.width=n.offsetWidth+10+"px",r.style.height=n.offsetHeight+10+"px",n.scrollLeft=n.scrollWidth,n.scrollTop=n.scrollHeight,a.scrollLeft=a.scrollWidth,a.scrollTop=a.scrollHeight,o=e.width(),s=e.height()};n.addEventListener("scroll",function(){(e.width()>o||e.height()>s)&&t.apply(this,[]),c()}.bind(this)),a.addEventListener("scroll",function(){(e.width()<o||e.height()<s)&&t.apply(this,[]),c()}.bind(this)),n.appendChild(r),a.appendChild(l),i.appendChild(n),i.appendChild(a),e.append(i),"static"===window.getComputedStyle(e[0],null).getPropertyValue("position")&&(e[0].style.position="relative"),c()}function ne(){R.height(E+"px"),W=0,t=y.verticalGutter+R.outerWidth(),b.width(k-t-J);try{0===L.position().left&&b.css("margin-left",t+"px")}catch(e){}}function re(){C.find(">.jspHorizontalBar>.jspCap:visible,>.jspHorizontalBar>.jspArrow").each(function(){G-=be(this).outerWidth()}),O.width(G+"px"),X=0}function ae(e,t,o,s){var i,n="before",r="after";"os"==t&&(t=/Mac/.test(navigator.platform)?"after":"split"),t==n?r=t:t==r&&(n=t,i=o,o=s,s=i),e[n](o)[r](s)}function le(e,t,o){return function(){return function(e,t,o,s){o=be(o).addClass("jspActive");var i,n,r=!0,a=function(){0!==e&&_.scrollByX(e*y.arrowButtonSpeed),0!==t&&_.scrollByY(t*y.arrowButtonSpeed),n=setTimeout(a,r?y.initialDelay:y.arrowRepeatFreq),r=!1};a(),i=s?"mouseout.jsp":"mouseup.jsp",(s=s||be("html")).on(i,function(){o.removeClass("jspActive"),n&&clearTimeout(n),n=null,s.off(i)})}(e,t,this,o),this.blur(),!1}}function ce(){O&&O.off("mousedown.jsp"),R&&R.off("mousedown.jsp")}function pe(){be("html").off("dragstart.jsp selectstart.jsp mousemove.jsp mouseup.jsp mouseleave.jsp"),z&&z.removeClass("jspActive"),Y&&Y.removeClass("jspActive")}function ue(e,t){if(H){e<0?e=0:A<e&&(e=A);var o=new be.Event("jsp-will-scroll-y");if(w.trigger(o,[e]),!o.isDefaultPrevented()){var s=e||0,i=0===s,n=s==A,r=-(e/A)*(x-T);void 0===t&&(t=y.animateScroll),t?_.animate(z,"top",e,de,function(){w.trigger("jsp-user-scroll-y",[-r,i,n])}):(z.css("top",e),de(e),w.trigger("jsp-user-scroll-y",[-r,i,n]))}}}function de(e){void 0===e&&(e=z.position().top),C.scrollTop(0);var t,o,s=0===(W=e||0),i=W==A,n=-(e/A)*(x-T);r==s&&l==i||(r=s,l=i,w.trigger("jsp-arrow-change",[r,l,a,c])),t=s,o=i,y.showArrows&&(F[t?"addClass":"removeClass"]("jspDisabled"),V[o?"addClass":"removeClass"]("jspDisabled")),b.css("top",n),w.trigger("jsp-scroll-y",[-n,s,i]).trigger("scroll")}function fe(e,t){if(P){e<0?e=0:M<e&&(e=M);var o=new be.Event("jsp-will-scroll-x");if(w.trigger(o,[e]),!o.isDefaultPrevented()){var s=e||0,i=0===s,n=s==M,r=-(e/M)*(S-k);void 0===t&&(t=y.animateScroll),t?_.animate(Y,"left",e,he,function(){w.trigger("jsp-user-scroll-x",[-r,i,n])}):(Y.css("left",e),he(e),w.trigger("jsp-user-scroll-x",[-r,i,n]))}}}function he(e){void 0===e&&(e=Y.position().left),C.scrollTop(0);var t,o,s=0===(X=e||0),i=X==M,n=-(e/M)*(S-k);a==s&&c==i||(a=s,c=i,w.trigger("jsp-arrow-change",[r,l,a,c])),t=s,o=i,y.showArrows&&(K[t?"addClass":"removeClass"]("jspDisabled"),Q[o?"addClass":"removeClass"]("jspDisabled")),b.css("left",n),w.trigger("jsp-scroll-x",[-n,s,i]).trigger("scroll")}function ge(e,t){ue(e/(x-T)*A,t)}function je(e,t){fe(e/(S-k)*M,t)}function ve(e,t,o){var s,i,n,r,a,l,c,p,u,d=0,f=0;try{s=be(e)}catch(e){return}for(i=s.outerHeight(),n=s.outerWidth(),C.scrollTop(0),C.scrollLeft(0);!s.is(".jspPane");)if(d+=s.position().top,f+=s.position().left,s=s.offsetParent(),/^body|html$/i.test(s[0].nodeName))return;l=(r=we())+T,d<r||t?p=d-y.horizontalGutter:l<d+i&&(p=d-T+i+y.horizontalGutter),isNaN(p)||ge(p,o),c=(a=me())+k,f<a||t?u=f-y.horizontalGutter:c<f+n&&(u=f-k+n+y.horizontalGutter),isNaN(u)||je(u,o)}function me(){return-b.position().left}function we(){return-b.position().top}function ye(){return!1}"border-box"===w.css("box-sizing")?J=$=0:($=w.css("paddingTop")+" "+w.css("paddingRight")+" "+w.css("paddingBottom")+" "+w.css("paddingLeft"),J=(parseInt(w.css("paddingLeft"),10)||0)+(parseInt(w.css("paddingRight"),10)||0)),be.extend(_,{reinitialise:function(e){se(e=be.extend({},y,e))},scrollToElement:function(e,t,o){ve(e,t,o)},scrollTo:function(e,t,o){je(e,o),ge(t,o)},scrollToX:function(e,t){je(e,t)},scrollToY:function(e,t){ge(e,t)},scrollToPercentX:function(e,t){je(e*(S-k),t)},scrollToPercentY:function(e,t){ge(e*(x-T),t)},scrollBy:function(e,t,o){_.scrollByX(e,o),_.scrollByY(t,o)},scrollByX:function(e,t){fe((me()+Math[e<0?"floor":"ceil"](e))/(S-k)*M,t)},scrollByY:function(e,t){ue((we()+Math[e<0?"floor":"ceil"](e))/(x-T)*A,t)},positionDragX:function(e,t){fe(e,t)},positionDragY:function(e,t){ue(e,t)},animate:function(e,t,o,s,i){var n={};n[t]=o,e.animate(n,{duration:y.animateDuration,easing:y.animateEase,queue:!1,step:s,complete:i})},getContentPositionX:function(){return me()},getContentPositionY:function(){return we()},getContentWidth:function(){return S},getContentHeight:function(){return x},getPercentScrolledX:function(){return me()/(S-k)},getPercentScrolledY:function(){return we()/(x-T)},getIsScrollableH:function(){return P},getIsScrollableV:function(){return H},getContentPane:function(){return b},scrollToBottom:function(e){ue(A,e)},hijackInternalLinks:be.noop,destroy:function(){var e,t;e=we(),t=me(),w.removeClass("jspScrollable").off(".jsp"),b.off(".jsp"),w.replaceWith(o.append(b.children())),o.scrollTop(e),o.scrollLeft(t),U&&clearInterval(U)}}),se(e)}return o=be.extend({},be.fn.jScrollPane.defaults,o),be.each(["arrowButtonSpeed","trackClickSpeed","keyboardSpeed"],function(){o[this]=o[this]||o.speed}),this.each(function(){var e=be(this),t=e.data("jsp");t?t.reinitialise(o):(be("script",e).filter('[type="text/javascript"],:not([type])').remove(),t=new s(e,o),e.data("jsp",t))})},be.fn.jScrollPane.defaults={showArrows:!1,maintainPosition:!0,stickToBottom:!1,stickToRight:!1,clickOnTrack:!0,autoReinitialise:!1,autoReinitialiseDelay:500,verticalDragMinHeight:0,verticalDragMaxHeight:99999,horizontalDragMinWidth:0,horizontalDragMaxWidth:99999,contentWidth:void 0,animateScroll:!1,animateDuration:300,animateEase:"linear",hijackInternalLinks:!1,verticalGutter:4,horizontalGutter:4,mouseWheelSpeed:3,arrowButtonSpeed:0,arrowRepeatFreq:50,arrowScrollOnHover:!1,trackClickSpeed:0,trackClickRepeatFreq:70,verticalArrowPositions:"split",horizontalArrowPositions:"split",enableKeyboardNavigation:!0,hideFocus:!1,keyboardSpeed:0,initialDelay:300,speed:30,scrollPagePercent:.8,alwaysShowVScroll:!1,alwaysShowHScroll:!1,resizeSensor:!1,resizeSensorDelay:0}});