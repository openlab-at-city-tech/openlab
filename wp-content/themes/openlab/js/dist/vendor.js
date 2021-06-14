/*!
 * Bootstrap v3.2.0 (http://getbootstrap.com)
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */
if("undefined"==typeof jQuery)throw new Error("Bootstrap's JavaScript requires jQuery");+function(a){"use strict";function b(){var a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var c in b)if(void 0!==a.style[c])return{end:b[c]};return!1}a.fn.emulateTransitionEnd=function(b){var c=!1,d=this;a(this).one("bsTransitionEnd",function(){c=!0});var e=function(){c||a(d).trigger(a.support.transition.end)};return setTimeout(e,b),this},a(function(){a.support.transition=b(),a.support.transition&&(a.event.special.bsTransitionEnd={bindType:a.support.transition.end,delegateType:a.support.transition.end,handle:function(b){return a(b.target).is(this)?b.handleObj.handler.apply(this,arguments):void 0}})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var c=a(this),e=c.data("bs.alert");e||c.data("bs.alert",e=new d(this)),"string"==typeof b&&e[b].call(c)})}var c='[data-dismiss="alert"]',d=function(b){a(b).on("click",c,this.close)};d.VERSION="3.2.0",d.prototype.close=function(b){function c(){f.detach().trigger("closed.bs.alert").remove()}var d=a(this),e=d.attr("data-target");e||(e=d.attr("href"),e=e&&e.replace(/.*(?=#[^\s]*$)/,""));var f=a(e);b&&b.preventDefault(),f.length||(f=d.hasClass("alert")?d:d.parent()),f.trigger(b=a.Event("close.bs.alert")),b.isDefaultPrevented()||(f.removeClass("in"),a.support.transition&&f.hasClass("fade")?f.one("bsTransitionEnd",c).emulateTransitionEnd(150):c())};var e=a.fn.alert;a.fn.alert=b,a.fn.alert.Constructor=d,a.fn.alert.noConflict=function(){return a.fn.alert=e,this},a(document).on("click.bs.alert.data-api",c,d.prototype.close)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.button"),f="object"==typeof b&&b;e||d.data("bs.button",e=new c(this,f)),"toggle"==b?e.toggle():b&&e.setState(b)})}var c=function(b,d){this.$element=a(b),this.options=a.extend({},c.DEFAULTS,d),this.isLoading=!1};c.VERSION="3.2.0",c.DEFAULTS={loadingText:"loading..."},c.prototype.setState=function(b){var c="disabled",d=this.$element,e=d.is("input")?"val":"html",f=d.data();b+="Text",null==f.resetText&&d.data("resetText",d[e]()),d[e](null==f[b]?this.options[b]:f[b]),setTimeout(a.proxy(function(){"loadingText"==b?(this.isLoading=!0,d.addClass(c).attr(c,c)):this.isLoading&&(this.isLoading=!1,d.removeClass(c).removeAttr(c))},this),0)},c.prototype.toggle=function(){var a=!0,b=this.$element.closest('[data-toggle="buttons"]');if(b.length){var c=this.$element.find("input");"radio"==c.prop("type")&&(c.prop("checked")&&this.$element.hasClass("active")?a=!1:b.find(".active").removeClass("active")),a&&c.prop("checked",!this.$element.hasClass("active")).trigger("change")}a&&this.$element.toggleClass("active")};var d=a.fn.button;a.fn.button=b,a.fn.button.Constructor=c,a.fn.button.noConflict=function(){return a.fn.button=d,this},a(document).on("click.bs.button.data-api",'[data-toggle^="button"]',function(c){var d=a(c.target);d.hasClass("btn")||(d=d.closest(".btn")),b.call(d,"toggle"),c.preventDefault()})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.carousel"),f=a.extend({},c.DEFAULTS,d.data(),"object"==typeof b&&b),g="string"==typeof b?b:f.slide;e||d.data("bs.carousel",e=new c(this,f)),"number"==typeof b?e.to(b):g?e[g]():f.interval&&e.pause().cycle()})}var c=function(b,c){this.$element=a(b).on("keydown.bs.carousel",a.proxy(this.keydown,this)),this.$indicators=this.$element.find(".carousel-indicators"),this.options=c,this.paused=this.sliding=this.interval=this.$active=this.$items=null,"hover"==this.options.pause&&this.$element.on("mouseenter.bs.carousel",a.proxy(this.pause,this)).on("mouseleave.bs.carousel",a.proxy(this.cycle,this))};c.VERSION="3.2.0",c.DEFAULTS={interval:5e3,pause:"hover",wrap:!0},c.prototype.keydown=function(a){switch(a.which){case 37:this.prev();break;case 39:this.next();break;default:return}a.preventDefault()},c.prototype.cycle=function(b){return b||(this.paused=!1),this.interval&&clearInterval(this.interval),this.options.interval&&!this.paused&&(this.interval=setInterval(a.proxy(this.next,this),this.options.interval)),this},c.prototype.getItemIndex=function(a){return this.$items=a.parent().children(".item"),this.$items.index(a||this.$active)},c.prototype.to=function(b){var c=this,d=this.getItemIndex(this.$active=this.$element.find(".item.active"));return b>this.$items.length-1||0>b?void 0:this.sliding?this.$element.one("slid.bs.carousel",function(){c.to(b)}):d==b?this.pause().cycle():this.slide(b>d?"next":"prev",a(this.$items[b]))},c.prototype.pause=function(b){return b||(this.paused=!0),this.$element.find(".next, .prev").length&&a.support.transition&&(this.$element.trigger(a.support.transition.end),this.cycle(!0)),this.interval=clearInterval(this.interval),this},c.prototype.next=function(){return this.sliding?void 0:this.slide("next")},c.prototype.prev=function(){return this.sliding?void 0:this.slide("prev")},c.prototype.slide=function(b,c){var d=this.$element.find(".item.active"),e=c||d[b](),f=this.interval,g="next"==b?"left":"right",h="next"==b?"first":"last",i=this;if(!e.length){if(!this.options.wrap)return;e=this.$element.find(".item")[h]()}if(e.hasClass("active"))return this.sliding=!1;var j=e[0],k=a.Event("slide.bs.carousel",{relatedTarget:j,direction:g});if(this.$element.trigger(k),!k.isDefaultPrevented()){if(this.sliding=!0,f&&this.pause(),this.$indicators.length){this.$indicators.find(".active").removeClass("active");var l=a(this.$indicators.children()[this.getItemIndex(e)]);l&&l.addClass("active")}var m=a.Event("slid.bs.carousel",{relatedTarget:j,direction:g});return a.support.transition&&this.$element.hasClass("slide")?(e.addClass(b),e[0].offsetWidth,d.addClass(g),e.addClass(g),d.one("bsTransitionEnd",function(){e.removeClass([b,g].join(" ")).addClass("active"),d.removeClass(["active",g].join(" ")),i.sliding=!1,setTimeout(function(){i.$element.trigger(m)},0)}).emulateTransitionEnd(1e3*d.css("transition-duration").slice(0,-1))):(d.removeClass("active"),e.addClass("active"),this.sliding=!1,this.$element.trigger(m)),f&&this.cycle(),this}};var d=a.fn.carousel;a.fn.carousel=b,a.fn.carousel.Constructor=c,a.fn.carousel.noConflict=function(){return a.fn.carousel=d,this},a(document).on("click.bs.carousel.data-api","[data-slide], [data-slide-to]",function(c){var d,e=a(this),f=a(e.attr("data-target")||(d=e.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,""));if(f.hasClass("carousel")){var g=a.extend({},f.data(),e.data()),h=e.attr("data-slide-to");h&&(g.interval=!1),b.call(f,g),h&&f.data("bs.carousel").to(h),c.preventDefault()}}),a(window).on("load",function(){a('[data-ride="carousel"]').each(function(){var c=a(this);b.call(c,c.data())})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.collapse"),f=a.extend({},c.DEFAULTS,d.data(),"object"==typeof b&&b);!e&&f.toggle&&"show"==b&&(b=!b),e||d.data("bs.collapse",e=new c(this,f)),"string"==typeof b&&e[b]()})}var c=function(b,d){this.$element=a(b),this.options=a.extend({},c.DEFAULTS,d),this.transitioning=null,this.options.parent&&(this.$parent=a(this.options.parent)),this.options.toggle&&this.toggle()};c.VERSION="3.2.0",c.DEFAULTS={toggle:!0},c.prototype.dimension=function(){var a=this.$element.hasClass("width");return a?"width":"height"},c.prototype.show=function(){if(!this.transitioning&&!this.$element.hasClass("in")){var c=a.Event("show.bs.collapse");if(this.$element.trigger(c),!c.isDefaultPrevented()){var d=this.$parent&&this.$parent.find("> .panel > .in");if(d&&d.length){var e=d.data("bs.collapse");if(e&&e.transitioning)return;b.call(d,"hide"),e||d.data("bs.collapse",null)}var f=this.dimension();this.$element.removeClass("collapse").addClass("collapsing")[f](0),this.transitioning=1;var g=function(){this.$element.removeClass("collapsing").addClass("collapse in")[f](""),this.transitioning=0,this.$element.trigger("shown.bs.collapse")};if(!a.support.transition)return g.call(this);var h=a.camelCase(["scroll",f].join("-"));this.$element.one("bsTransitionEnd",a.proxy(g,this)).emulateTransitionEnd(350)[f](this.$element[0][h])}}},c.prototype.hide=function(){if(!this.transitioning&&this.$element.hasClass("in")){var b=a.Event("hide.bs.collapse");if(this.$element.trigger(b),!b.isDefaultPrevented()){var c=this.dimension();this.$element[c](this.$element[c]())[0].offsetHeight,this.$element.addClass("collapsing").removeClass("collapse").removeClass("in"),this.transitioning=1;var d=function(){this.transitioning=0,this.$element.trigger("hidden.bs.collapse").removeClass("collapsing").addClass("collapse")};return a.support.transition?void this.$element[c](0).one("bsTransitionEnd",a.proxy(d,this)).emulateTransitionEnd(350):d.call(this)}}},c.prototype.toggle=function(){this[this.$element.hasClass("in")?"hide":"show"]()};var d=a.fn.collapse;a.fn.collapse=b,a.fn.collapse.Constructor=c,a.fn.collapse.noConflict=function(){return a.fn.collapse=d,this},a(document).on("click.bs.collapse.data-api",'[data-toggle="collapse"]',function(c){var d,e=a(this),f=e.attr("data-target")||c.preventDefault()||(d=e.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,""),g=a(f),h=g.data("bs.collapse"),i=h?"toggle":e.data(),j=e.attr("data-parent"),k=j&&a(j);h&&h.transitioning||(k&&k.find('[data-toggle="collapse"][data-parent="'+j+'"]').not(e).addClass("collapsed"),e[g.hasClass("in")?"addClass":"removeClass"]("collapsed")),b.call(g,i)})}(jQuery),+function(a){"use strict";function b(b){b&&3===b.which||(a(e).remove(),a(f).each(function(){var d=c(a(this)),e={relatedTarget:this};d.hasClass("open")&&(d.trigger(b=a.Event("hide.bs.dropdown",e)),b.isDefaultPrevented()||d.removeClass("open").trigger("hidden.bs.dropdown",e))}))}function c(b){var c=b.attr("data-target");c||(c=b.attr("href"),c=c&&/#[A-Za-z]/.test(c)&&c.replace(/.*(?=#[^\s]*$)/,""));var d=c&&a(c);return d&&d.length?d:b.parent()}function d(b){return this.each(function(){var c=a(this),d=c.data("bs.dropdown");d||c.data("bs.dropdown",d=new g(this)),"string"==typeof b&&d[b].call(c)})}var e=".dropdown-backdrop",f='[data-toggle="dropdown"]',g=function(b){a(b).on("click.bs.dropdown",this.toggle)};g.VERSION="3.2.0",g.prototype.toggle=function(d){var e=a(this);if(!e.is(".disabled, :disabled")){var f=c(e),g=f.hasClass("open");if(b(),!g){"ontouchstart"in document.documentElement&&!f.closest(".navbar-nav").length&&a('<div class="dropdown-backdrop"/>').insertAfter(a(this)).on("click",b);var h={relatedTarget:this};if(f.trigger(d=a.Event("show.bs.dropdown",h)),d.isDefaultPrevented())return;e.trigger("focus"),f.toggleClass("open").trigger("shown.bs.dropdown",h)}return!1}},g.prototype.keydown=function(b){if(/(38|40|27)/.test(b.keyCode)){var d=a(this);if(b.preventDefault(),b.stopPropagation(),!d.is(".disabled, :disabled")){var e=c(d),g=e.hasClass("open");if(!g||g&&27==b.keyCode)return 27==b.which&&e.find(f).trigger("focus"),d.trigger("click");var h=" li:not(.divider):visible a",i=e.find('[role="menu"]'+h+', [role="listbox"]'+h);if(i.length){var j=i.index(i.filter(":focus"));38==b.keyCode&&j>0&&j--,40==b.keyCode&&j<i.length-1&&j++,~j||(j=0),i.eq(j).trigger("focus")}}}};var h=a.fn.dropdown;a.fn.dropdown=d,a.fn.dropdown.Constructor=g,a.fn.dropdown.noConflict=function(){return a.fn.dropdown=h,this},a(document).on("click.bs.dropdown.data-api",b).on("click.bs.dropdown.data-api",".dropdown form",function(a){a.stopPropagation()}).on("click.bs.dropdown.data-api",f,g.prototype.toggle).on("keydown.bs.dropdown.data-api",f+', [role="menu"], [role="listbox"]',g.prototype.keydown)}(jQuery),+function(a){"use strict";function b(b,d){return this.each(function(){var e=a(this),f=e.data("bs.modal"),g=a.extend({},c.DEFAULTS,e.data(),"object"==typeof b&&b);f||e.data("bs.modal",f=new c(this,g)),"string"==typeof b?f[b](d):g.show&&f.show(d)})}var c=function(b,c){this.options=c,this.$body=a(document.body),this.$element=a(b),this.$backdrop=this.isShown=null,this.scrollbarWidth=0,this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,a.proxy(function(){this.$element.trigger("loaded.bs.modal")},this))};c.VERSION="3.2.0",c.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},c.prototype.toggle=function(a){return this.isShown?this.hide():this.show(a)},c.prototype.show=function(b){var c=this,d=a.Event("show.bs.modal",{relatedTarget:b});this.$element.trigger(d),this.isShown||d.isDefaultPrevented()||(this.isShown=!0,this.checkScrollbar(),this.$body.addClass("modal-open"),this.setScrollbar(),this.escape(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',a.proxy(this.hide,this)),this.backdrop(function(){var d=a.support.transition&&c.$element.hasClass("fade");c.$element.parent().length||c.$element.appendTo(c.$body),c.$element.show().scrollTop(0),d&&c.$element[0].offsetWidth,c.$element.addClass("in").attr("aria-hidden",!1),c.enforceFocus();var e=a.Event("shown.bs.modal",{relatedTarget:b});d?c.$element.find(".modal-dialog").one("bsTransitionEnd",function(){c.$element.trigger("focus").trigger(e)}).emulateTransitionEnd(300):c.$element.trigger("focus").trigger(e)}))},c.prototype.hide=function(b){b&&b.preventDefault(),b=a.Event("hide.bs.modal"),this.$element.trigger(b),this.isShown&&!b.isDefaultPrevented()&&(this.isShown=!1,this.$body.removeClass("modal-open"),this.resetScrollbar(),this.escape(),a(document).off("focusin.bs.modal"),this.$element.removeClass("in").attr("aria-hidden",!0).off("click.dismiss.bs.modal"),a.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",a.proxy(this.hideModal,this)).emulateTransitionEnd(300):this.hideModal())},c.prototype.enforceFocus=function(){a(document).off("focusin.bs.modal").on("focusin.bs.modal",a.proxy(function(a){this.$element[0]===a.target||this.$element.has(a.target).length||this.$element.trigger("focus")},this))},c.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keyup.dismiss.bs.modal",a.proxy(function(a){27==a.which&&this.hide()},this)):this.isShown||this.$element.off("keyup.dismiss.bs.modal")},c.prototype.hideModal=function(){var a=this;this.$element.hide(),this.backdrop(function(){a.$element.trigger("hidden.bs.modal")})},c.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},c.prototype.backdrop=function(b){var c=this,d=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var e=a.support.transition&&d;if(this.$backdrop=a('<div class="modal-backdrop '+d+'" />').appendTo(this.$body),this.$element.on("click.dismiss.bs.modal",a.proxy(function(a){a.target===a.currentTarget&&("static"==this.options.backdrop?this.$element[0].focus.call(this.$element[0]):this.hide.call(this))},this)),e&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!b)return;e?this.$backdrop.one("bsTransitionEnd",b).emulateTransitionEnd(150):b()}else if(!this.isShown&&this.$backdrop){this.$backdrop.removeClass("in");var f=function(){c.removeBackdrop(),b&&b()};a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one("bsTransitionEnd",f).emulateTransitionEnd(150):f()}else b&&b()},c.prototype.checkScrollbar=function(){document.body.clientWidth>=window.innerWidth||(this.scrollbarWidth=this.scrollbarWidth||this.measureScrollbar())},c.prototype.setScrollbar=function(){var a=parseInt(this.$body.css("padding-right")||0,10);this.scrollbarWidth&&this.$body.css("padding-right",a+this.scrollbarWidth)},c.prototype.resetScrollbar=function(){this.$body.css("padding-right","")},c.prototype.measureScrollbar=function(){var a=document.createElement("div");a.className="modal-scrollbar-measure",this.$body.append(a);var b=a.offsetWidth-a.clientWidth;return this.$body[0].removeChild(a),b};var d=a.fn.modal;a.fn.modal=b,a.fn.modal.Constructor=c,a.fn.modal.noConflict=function(){return a.fn.modal=d,this},a(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',function(c){var d=a(this),e=d.attr("href"),f=a(d.attr("data-target")||e&&e.replace(/.*(?=#[^\s]+$)/,"")),g=f.data("bs.modal")?"toggle":a.extend({remote:!/#/.test(e)&&e},f.data(),d.data());d.is("a")&&c.preventDefault(),f.one("show.bs.modal",function(a){a.isDefaultPrevented()||f.one("hidden.bs.modal",function(){d.is(":visible")&&d.trigger("focus")})}),b.call(f,g,this)})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tooltip"),f="object"==typeof b&&b;(e||"destroy"!=b)&&(e||d.data("bs.tooltip",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.type=this.options=this.enabled=this.timeout=this.hoverState=this.$element=null,this.init("tooltip",a,b)};c.VERSION="3.2.0",c.DEFAULTS={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1,viewport:{selector:"body",padding:0}},c.prototype.init=function(b,c,d){this.enabled=!0,this.type=b,this.$element=a(c),this.options=this.getOptions(d),this.$viewport=this.options.viewport&&a(this.options.viewport.selector||this.options.viewport);for(var e=this.options.trigger.split(" "),f=e.length;f--;){var g=e[f];if("click"==g)this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this));else if("manual"!=g){var h="hover"==g?"mouseenter":"focusin",i="hover"==g?"mouseleave":"focusout";this.$element.on(h+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(i+"."+this.type,this.options.selector,a.proxy(this.leave,this))}}this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.getOptions=function(b){return b=a.extend({},this.getDefaults(),this.$element.data(),b),b.delay&&"number"==typeof b.delay&&(b.delay={show:b.delay,hide:b.delay}),b},c.prototype.getDelegateOptions=function(){var b={},c=this.getDefaults();return this._options&&a.each(this._options,function(a,d){c[a]!=d&&(b[a]=d)}),b},c.prototype.enter=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);return c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),clearTimeout(c.timeout),c.hoverState="in",c.options.delay&&c.options.delay.show?void(c.timeout=setTimeout(function(){"in"==c.hoverState&&c.show()},c.options.delay.show)):c.show()},c.prototype.leave=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);return c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),clearTimeout(c.timeout),c.hoverState="out",c.options.delay&&c.options.delay.hide?void(c.timeout=setTimeout(function(){"out"==c.hoverState&&c.hide()},c.options.delay.hide)):c.hide()},c.prototype.show=function(){var b=a.Event("show.bs."+this.type);if(this.hasContent()&&this.enabled){this.$element.trigger(b);var c=a.contains(document.documentElement,this.$element[0]);if(b.isDefaultPrevented()||!c)return;var d=this,e=this.tip(),f=this.getUID(this.type);this.setContent(),e.attr("id",f),this.$element.attr("aria-describedby",f),this.options.animation&&e.addClass("fade");var g="function"==typeof this.options.placement?this.options.placement.call(this,e[0],this.$element[0]):this.options.placement,h=/\s?auto?\s?/i,i=h.test(g);i&&(g=g.replace(h,"")||"top"),e.detach().css({top:0,left:0,display:"block"}).addClass(g).data("bs."+this.type,this),this.options.container?e.appendTo(this.options.container):e.insertAfter(this.$element);var j=this.getPosition(),k=e[0].offsetWidth,l=e[0].offsetHeight;if(i){var m=g,n=this.$element.parent(),o=this.getPosition(n);g="bottom"==g&&j.top+j.height+l-o.scroll>o.height?"top":"top"==g&&j.top-o.scroll-l<0?"bottom":"right"==g&&j.right+k>o.width?"left":"left"==g&&j.left-k<o.left?"right":g,e.removeClass(m).addClass(g)}var p=this.getCalculatedOffset(g,j,k,l);this.applyPlacement(p,g);var q=function(){d.$element.trigger("shown.bs."+d.type),d.hoverState=null};a.support.transition&&this.$tip.hasClass("fade")?e.one("bsTransitionEnd",q).emulateTransitionEnd(150):q()}},c.prototype.applyPlacement=function(b,c){var d=this.tip(),e=d[0].offsetWidth,f=d[0].offsetHeight,g=parseInt(d.css("margin-top"),10),h=parseInt(d.css("margin-left"),10);isNaN(g)&&(g=0),isNaN(h)&&(h=0),b.top=b.top+g,b.left=b.left+h,a.offset.setOffset(d[0],a.extend({using:function(a){d.css({top:Math.round(a.top),left:Math.round(a.left)})}},b),0),d.addClass("in");var i=d[0].offsetWidth,j=d[0].offsetHeight;"top"==c&&j!=f&&(b.top=b.top+f-j);var k=this.getViewportAdjustedDelta(c,b,i,j);k.left?b.left+=k.left:b.top+=k.top;var l=k.left?2*k.left-e+i:2*k.top-f+j,m=k.left?"left":"top",n=k.left?"offsetWidth":"offsetHeight";d.offset(b),this.replaceArrow(l,d[0][n],m)},c.prototype.replaceArrow=function(a,b,c){this.arrow().css(c,a?50*(1-a/b)+"%":"")},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fade in top bottom left right")},c.prototype.hide=function(){function b(){"in"!=c.hoverState&&d.detach(),c.$element.trigger("hidden.bs."+c.type)}var c=this,d=this.tip(),e=a.Event("hide.bs."+this.type);return this.$element.removeAttr("aria-describedby"),this.$element.trigger(e),e.isDefaultPrevented()?void 0:(d.removeClass("in"),a.support.transition&&this.$tip.hasClass("fade")?d.one("bsTransitionEnd",b).emulateTransitionEnd(150):b(),this.hoverState=null,this)},c.prototype.fixTitle=function(){var a=this.$element;(a.attr("title")||"string"!=typeof a.attr("data-original-title"))&&a.attr("data-original-title",a.attr("title")||"").attr("title","")},c.prototype.hasContent=function(){return this.getTitle()},c.prototype.getPosition=function(b){b=b||this.$element;var c=b[0],d="BODY"==c.tagName;return a.extend({},"function"==typeof c.getBoundingClientRect?c.getBoundingClientRect():null,{scroll:d?document.documentElement.scrollTop||document.body.scrollTop:b.scrollTop(),width:d?a(window).width():b.outerWidth(),height:d?a(window).height():b.outerHeight()},d?{top:0,left:0}:b.offset())},c.prototype.getCalculatedOffset=function(a,b,c,d){return"bottom"==a?{top:b.top+b.height,left:b.left+b.width/2-c/2}:"top"==a?{top:b.top-d,left:b.left+b.width/2-c/2}:"left"==a?{top:b.top+b.height/2-d/2,left:b.left-c}:{top:b.top+b.height/2-d/2,left:b.left+b.width}},c.prototype.getViewportAdjustedDelta=function(a,b,c,d){var e={top:0,left:0};if(!this.$viewport)return e;var f=this.options.viewport&&this.options.viewport.padding||0,g=this.getPosition(this.$viewport);if(/right|left/.test(a)){var h=b.top-f-g.scroll,i=b.top+f-g.scroll+d;h<g.top?e.top=g.top-h:i>g.top+g.height&&(e.top=g.top+g.height-i)}else{var j=b.left-f,k=b.left+f+c;j<g.left?e.left=g.left-j:k>g.width&&(e.left=g.left+g.width-k)}return e},c.prototype.getTitle=function(){var a,b=this.$element,c=this.options;return a=b.attr("data-original-title")||("function"==typeof c.title?c.title.call(b[0]):c.title)},c.prototype.getUID=function(a){do a+=~~(1e6*Math.random());while(document.getElementById(a));return a},c.prototype.tip=function(){return this.$tip=this.$tip||a(this.options.template)},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},c.prototype.validate=function(){this.$element[0].parentNode||(this.hide(),this.$element=null,this.options=null)},c.prototype.enable=function(){this.enabled=!0},c.prototype.disable=function(){this.enabled=!1},c.prototype.toggleEnabled=function(){this.enabled=!this.enabled},c.prototype.toggle=function(b){var c=this;b&&(c=a(b.currentTarget).data("bs."+this.type),c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c))),c.tip().hasClass("in")?c.leave(c):c.enter(c)},c.prototype.destroy=function(){clearTimeout(this.timeout),this.hide().$element.off("."+this.type).removeData("bs."+this.type)};var d=a.fn.tooltip;a.fn.tooltip=b,a.fn.tooltip.Constructor=c,a.fn.tooltip.noConflict=function(){return a.fn.tooltip=d,this}}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.popover"),f="object"==typeof b&&b;(e||"destroy"!=b)&&(e||d.data("bs.popover",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.init("popover",a,b)};if(!a.fn.tooltip)throw new Error("Popover requires tooltip.js");c.VERSION="3.2.0",c.DEFAULTS=a.extend({},a.fn.tooltip.Constructor.DEFAULTS,{placement:"right",trigger:"click",content:"",template:'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}),c.prototype=a.extend({},a.fn.tooltip.Constructor.prototype),c.prototype.constructor=c,c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle(),c=this.getContent();a.find(".popover-title")[this.options.html?"html":"text"](b),a.find(".popover-content").empty()[this.options.html?"string"==typeof c?"html":"append":"text"](c),a.removeClass("fade top bottom left right in"),a.find(".popover-title").html()||a.find(".popover-title").hide()},c.prototype.hasContent=function(){return this.getTitle()||this.getContent()},c.prototype.getContent=function(){var a=this.$element,b=this.options;return a.attr("data-content")||("function"==typeof b.content?b.content.call(a[0]):b.content)},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".arrow")},c.prototype.tip=function(){return this.$tip||(this.$tip=a(this.options.template)),this.$tip};var d=a.fn.popover;a.fn.popover=b,a.fn.popover.Constructor=c,a.fn.popover.noConflict=function(){return a.fn.popover=d,this}}(jQuery),+function(a){"use strict";function b(c,d){var e=a.proxy(this.process,this);this.$body=a("body"),this.$scrollElement=a(a(c).is("body")?window:c),this.options=a.extend({},b.DEFAULTS,d),this.selector=(this.options.target||"")+" .nav li > a",this.offsets=[],this.targets=[],this.activeTarget=null,this.scrollHeight=0,this.$scrollElement.on("scroll.bs.scrollspy",e),this.refresh(),this.process()}function c(c){return this.each(function(){var d=a(this),e=d.data("bs.scrollspy"),f="object"==typeof c&&c;e||d.data("bs.scrollspy",e=new b(this,f)),"string"==typeof c&&e[c]()})}b.VERSION="3.2.0",b.DEFAULTS={offset:10},b.prototype.getScrollHeight=function(){return this.$scrollElement[0].scrollHeight||Math.max(this.$body[0].scrollHeight,document.documentElement.scrollHeight)},b.prototype.refresh=function(){var b="offset",c=0;a.isWindow(this.$scrollElement[0])||(b="position",c=this.$scrollElement.scrollTop()),this.offsets=[],this.targets=[],this.scrollHeight=this.getScrollHeight();var d=this;this.$body.find(this.selector).map(function(){var d=a(this),e=d.data("target")||d.attr("href"),f=/^#./.test(e)&&a(e);return f&&f.length&&f.is(":visible")&&[[f[b]().top+c,e]]||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){d.offsets.push(this[0]),d.targets.push(this[1])})},b.prototype.process=function(){var a,b=this.$scrollElement.scrollTop()+this.options.offset,c=this.getScrollHeight(),d=this.options.offset+c-this.$scrollElement.height(),e=this.offsets,f=this.targets,g=this.activeTarget;if(this.scrollHeight!=c&&this.refresh(),b>=d)return g!=(a=f[f.length-1])&&this.activate(a);if(g&&b<=e[0])return g!=(a=f[0])&&this.activate(a);for(a=e.length;a--;)g!=f[a]&&b>=e[a]&&(!e[a+1]||b<=e[a+1])&&this.activate(f[a])},b.prototype.activate=function(b){this.activeTarget=b,a(this.selector).parentsUntil(this.options.target,".active").removeClass("active");var c=this.selector+'[data-target="'+b+'"],'+this.selector+'[href="'+b+'"]',d=a(c).parents("li").addClass("active");d.parent(".dropdown-menu").length&&(d=d.closest("li.dropdown").addClass("active")),d.trigger("activate.bs.scrollspy")};var d=a.fn.scrollspy;a.fn.scrollspy=c,a.fn.scrollspy.Constructor=b,a.fn.scrollspy.noConflict=function(){return a.fn.scrollspy=d,this},a(window).on("load.bs.scrollspy.data-api",function(){a('[data-spy="scroll"]').each(function(){var b=a(this);c.call(b,b.data())})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tab");e||d.data("bs.tab",e=new c(this)),"string"==typeof b&&e[b]()})}var c=function(b){this.element=a(b)};c.VERSION="3.2.0",c.prototype.show=function(){var b=this.element,c=b.closest("ul:not(.dropdown-menu)"),d=b.data("target");if(d||(d=b.attr("href"),d=d&&d.replace(/.*(?=#[^\s]*$)/,"")),!b.parent("li").hasClass("active")){var e=c.find(".active:last a")[0],f=a.Event("show.bs.tab",{relatedTarget:e});if(b.trigger(f),!f.isDefaultPrevented()){var g=a(d);this.activate(b.closest("li"),c),this.activate(g,g.parent(),function(){b.trigger({type:"shown.bs.tab",relatedTarget:e})})}}},c.prototype.activate=function(b,c,d){function e(){f.removeClass("active").find("> .dropdown-menu > .active").removeClass("active"),b.addClass("active"),g?(b[0].offsetWidth,b.addClass("in")):b.removeClass("fade"),b.parent(".dropdown-menu")&&b.closest("li.dropdown").addClass("active"),d&&d()}var f=c.find("> .active"),g=d&&a.support.transition&&f.hasClass("fade");g?f.one("bsTransitionEnd",e).emulateTransitionEnd(150):e(),f.removeClass("in")};var d=a.fn.tab;a.fn.tab=b,a.fn.tab.Constructor=c,a.fn.tab.noConflict=function(){return a.fn.tab=d,this},a(document).on("click.bs.tab.data-api",'[data-toggle="tab"], [data-toggle="pill"]',function(c){c.preventDefault(),b.call(a(this),"show")})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.affix"),f="object"==typeof b&&b;e||d.data("bs.affix",e=new c(this,f)),"string"==typeof b&&e[b]()})}var c=function(b,d){this.options=a.extend({},c.DEFAULTS,d),this.$target=a(this.options.target).on("scroll.bs.affix.data-api",a.proxy(this.checkPosition,this)).on("click.bs.affix.data-api",a.proxy(this.checkPositionWithEventLoop,this)),this.$element=a(b),this.affixed=this.unpin=this.pinnedOffset=null,this.checkPosition()};c.VERSION="3.2.0",c.RESET="affix affix-top affix-bottom",c.DEFAULTS={offset:0,target:window},c.prototype.getPinnedOffset=function(){if(this.pinnedOffset)return this.pinnedOffset;this.$element.removeClass(c.RESET).addClass("affix");var a=this.$target.scrollTop(),b=this.$element.offset();return this.pinnedOffset=b.top-a},c.prototype.checkPositionWithEventLoop=function(){setTimeout(a.proxy(this.checkPosition,this),1)},c.prototype.checkPosition=function(){if(this.$element.is(":visible")){var b=a(document).height(),d=this.$target.scrollTop(),e=this.$element.offset(),f=this.options.offset,g=f.top,h=f.bottom;"object"!=typeof f&&(h=g=f),"function"==typeof g&&(g=f.top(this.$element)),"function"==typeof h&&(h=f.bottom(this.$element));var i=null!=this.unpin&&d+this.unpin<=e.top?!1:null!=h&&e.top+this.$element.height()>=b-h?"bottom":null!=g&&g>=d?"top":!1;if(this.affixed!==i){null!=this.unpin&&this.$element.css("top","");var j="affix"+(i?"-"+i:""),k=a.Event(j+".bs.affix");this.$element.trigger(k),k.isDefaultPrevented()||(this.affixed=i,this.unpin="bottom"==i?this.getPinnedOffset():null,this.$element.removeClass(c.RESET).addClass(j).trigger(a.Event(j.replace("affix","affixed"))),"bottom"==i&&this.$element.offset({top:b-this.$element.height()-h}))}}};var d=a.fn.affix;a.fn.affix=b,a.fn.affix.Constructor=c,a.fn.affix.noConflict=function(){return a.fn.affix=d,this},a(window).on("load",function(){a('[data-spy="affix"]').each(function(){var c=a(this),d=c.data();d.offset=d.offset||{},d.offsetBottom&&(d.offset.bottom=d.offsetBottom),d.offsetTop&&(d.offset.top=d.offsetTop),b.call(c,d)})})}(jQuery);;/*!
 * jCarousel Lite - v1.9.3 - 2015-02-16
 * http://kswedberg.github.com/jquery-carousel-lite/
 * Copyright (c) 2015 Karl Swedberg
 * based on the original by Ganeshji Marwaha (gmarwaha.com)
 * Licensed MIT (http://kswedberg.github.com/jquery-carousel-lite/blob/master/LICENSE-MIT)
 */


(function($) {
  $.jCarouselLite = {
    version: '1.9.3',
    curr: 0
  };

  $.fn.anim = typeof $.fn.velocity !== 'undefined' ? $.fn.velocity : $.fn.animate;

  $.fn.jCarouselLite = function(options) {
    var o = $.extend(true, {}, $.fn.jCarouselLite.defaults, options),
        ceil = Math.ceil,
        mabs = Math.abs;

    this.each(function() {

      var beforeCirc, afterCirc, pageNav, pageNavCount, resize,
          li, itemLength, curr,
          prepResize, touchEvents, $btnsGo,
          isTouch = 'ontouchend' in document,
          styles = { div: {}, ul: {}, li: {} },
          // firstCss = true,
          running = false,
          animCss = o.vertical ? 'top': 'left',
          aniProps = {},
          sizeProp = o.vertical ? 'height': 'width',
          outerMethod = o.vertical ? 'outerHeight': 'outerWidth',
          self = this,
          div = $(this),
          ul = div.find(o.containerSelector).eq(0),
          tLi = ul.children(o.itemSelector),
          tl = tLi.length,
          visibleNum = o.visible,
          // need visibleCeil and visibleFloor in case we want a fractional number of visible items at a time
          visibleCeil = ceil(visibleNum),
          visibleFloor = Math.floor(visibleNum),
          start = Math.min(o.start, tl - 1),
          direction = 1,
          activeBtnOffset = 0,
          activeBtnTypes = {},
          startTouch = {},
          endTouch = {},
          axisPrimary = o.vertical ? 'y' : 'x',
          axisSecondary = o.vertical ? 'x' : 'y';


      var init = o.init.call(this, o, tLi);
      // bail out for this carousel if the o.init() callback returns `false`
      if ( init === false ) {
        return;
      }

      var makeCircular = function() {
        if (beforeCirc && beforeCirc.length) {
          beforeCirc.remove();
          afterCirc.remove();
        }
        tLi = ul.children(o.liSelector);
        tl = tLi.length;
        beforeCirc = tLi.slice( tl - visibleCeil ).clone(true).each(fixIds);
        afterCirc = tLi.slice( 0, visibleCeil ).clone(true).each(fixIds);
        ul.prepend( beforeCirc )
          .append( afterCirc );
        li = ul.children(o.liSelector);
        itemLength = li.length;
      };

      div.data('dirjc', direction);
      div.data(animCss + 'jc', div.css(animCss));

      if (o.circular) {

        makeCircular();
        start += visibleCeil;
        activeBtnOffset = visibleCeil;

      } else {
        li = ul.children(o.liSelector);
        itemLength = li.length;
      }

      if (o.btnGo && o.btnGo.length) {

        if ( $.isArray(o.btnGo) && typeof o.btnGo[0] === 'string' ) {
          $btnsGo = $( o.btnGo.join() );
        } else {
          $btnsGo = $(o.btnGo);
        }

        $btnsGo.each(function(i) {
          $(this).bind('click.jc', function(event) {
            event.preventDefault();
            var btnInfo = {
              btnGo: this,
              btnGoIndex: i
            };
            return go(o.circular ? visibleNum + i : i, btnInfo);
          });
        });
        activeBtnTypes.go = 1;
      }

      var setActive = function(i, types) {
        i = ceil(i);

        // Set active class on the appropriate carousel item
        li.filter('.' + o.activeClass).removeClass(o.activeClass);
        li.eq(i).addClass(o.activeClass);

        var activeBtnIndex = (i - activeBtnOffset) % tl,
            visEnd = activeBtnIndex + visibleFloor;

        if ( types.go ) {
          // remove active and visible classes from all the go buttons
          $btnsGo.removeClass(o.activeClass).removeClass(o.visibleClass);
          // add active class to the go button corresponding to the first visible slide
          $btnsGo.eq(activeBtnIndex).addClass(o.activeClass);
          // add visible class to go buttons corresponding to all visible slides
          $btnsGo.slice(activeBtnIndex, activeBtnIndex + visibleFloor).addClass(o.visibleClass);

          if ( visEnd > $btnsGo.length ) {
            $btnsGo.slice(0, visEnd - $btnsGo.length).addClass(o.visibleClass);
          }
        }

        if ( types.pager ) {
          pageNav.removeClass(o.activeClass);
          pageNav.eq( ceil(activeBtnIndex / visibleNum) ).addClass(o.activeClass);
        }
        return activeBtnIndex;
      };

      curr = start;

      $.jCarouselLite.curr = curr;

      var getDimensions = function(reset) {
        var liSize, ulSize, divSize;

        if (reset) {

          styles.div[sizeProp] = '';
          styles.li = {
            width: '',
            height: ''
          };
          // bail out with the reset styles
          return styles;
        }

        // Full li size(incl margin)-Used for animation
        liSize = li[outerMethod](true);

        // size of full ul(total length, not just for the visible items)
        ulSize = liSize * itemLength;

        // size of entire div(total length for just the visible items)
        divSize = liSize * visibleNum;

        styles.div[sizeProp] = divSize + 'px';
        styles.ul[sizeProp] = ulSize + 'px';
        styles.ul[animCss] = -(curr * liSize) + 'px';
        styles.li = {
          width: li.width(),
          height: li.height()
        };
        styles.liSize = liSize;
        return styles;
      };


      var setDimensions = function(reset) {
        var css, tmpDivSize;
        var prelimCss = {
          div: {visibility: 'visible', position: 'relative', zIndex: 2, left: '0'},
          ul: {margin: '0', padding: '0', position: 'relative', listStyleType: 'none', zIndex: 1},
          li: {overflow: o.vertical ? 'hidden' : 'visible', 'float': o.vertical ? 'none' : 'left'}
        };

        if (reset) {
          css = getDimensions(true);
          div.css(css.div);
          ul.css(css.ul);
          li.css(css.li);
        }

        css = getDimensions();

        if (o.autoCSS) {
          $.extend(true, css, prelimCss);
          // firstCss = false;
        }

        if (o.autoWidth) {
          tmpDivSize = parseInt(div.css(sizeProp), 10);
          styles.liSize = tmpDivSize / o.visible;
          css.li[sizeProp] = styles.liSize - (li[outerMethod](true) - parseInt(li.css(sizeProp), 10));

          // Need to adjust other settings to fit with li width
          css.ul[sizeProp] = (styles.liSize * itemLength) + 'px';
          css.ul[animCss] = -(curr * styles.liSize) + 'px';
          css.div[sizeProp] = tmpDivSize;
        }

        if (o.autoCSS) {
          li.css(css.li);
          ul.css(css.ul);
          div.css(css.div);
        }
      };

      setDimensions();

      // set up timed advancer
      var advanceCounter = 0,
          autoStop = iterations(tl, o),
          autoScrollBy = typeof o.auto === 'number' ? o.auto : o.scroll;

      var advancer = function() {
        self.setAutoAdvance = setTimeout(function() {

          if (!autoStop || autoStop > advanceCounter) {
            direction = div.data('dirjc');
            go( curr + (direction * autoScrollBy), {auto: true} );
            advanceCounter++;
            advancer();
          }
        }, o.timeout);
      };

      // bind click handlers to prev and next buttons, if set
      $.each([ 'btnPrev', 'btnNext' ], function(index, btn) {
        if ( o[btn] ) {
          o['$' + btn] = $.isFunction( o[btn] ) ? o[btn].call( div[0] ) : $( o[btn] );
          o['$' + btn].bind('click.jc', function(event) {
            event.preventDefault();
            var step = index === 0 ? curr - o.scroll : curr + o.scroll;
            if (o.directional) {
              // set direction of subsequent scrolls to:
              //  1 if "btnNext" clicked
              // -1 if "btnPrev" clicked
              div.data( 'dirjc', (index ? 1 : -1) );
            }
            return go( step );
          });
        }
      });

      if (!o.circular) {
        if (o.btnPrev && start === 0) {
          o.$btnPrev.addClass(o.btnDisabledClass);
        }

        if ( o.btnNext && start + visibleFloor >= itemLength ) {
          o.$btnNext.addClass(o.btnDisabledClass);
        }
      }

      if (o.autoPager) {
        pageNavCount = ceil(tl / visibleNum);
        pageNav = [];
        for (var i=0; i < pageNavCount; i++) {
          pageNav.push('<li><a href="#">' + (i+1) + '</a></li>');
        }
        if (pageNav.length > 1) {
          pageNav = $('<ul>' + pageNav.join('') + '</ul>').appendTo(o.autoPager).find('li');
          pageNav.find('a').each(function(i) {
            $(this).bind('click.jc', function(event) {
              event.preventDefault();
              var slide = i * visibleNum;
              if (o.circular) {
                slide += visibleNum;
              }
              return go(slide);
            });
          });
          activeBtnTypes.pager = 1;
        }
      }

      // set the active class on the btn corresponding to the "start" li
      setActive(start, activeBtnTypes);

      if (o.mouseWheel && div.mousewheel) {
        div.bind('mousewheel.jc', function(e, d) {
          return d > 0 ? go(curr - o.scroll) : go(curr + o.scroll);
        });
      }

      if (o.pause && o.auto && !isTouch) {
        div.bind('mouseenter.jc', function() {
          div.trigger('pauseCarousel.jc');
        }).bind('mouseleave.jc', function() {
          div.trigger('resumeCarousel.jc');
        });
      }

      if (o.auto) {
        advancer();
      }

      function vis() {
        return li.slice(curr).slice(0, visibleCeil);
      }

      $.jCarouselLite.vis = vis;

      function go(to, settings) {
        if (running) { return false; }
        settings = settings || {};
        var prev = curr,
            direction = to > curr,
            speed = typeof settings.speed !== 'undefined' ? settings.speed : o.speed,
            // offset appears if touch moves slides
            offset = settings.offset || 0;

        if (o.beforeStart) {
          o.beforeStart.call(div, vis(), direction, settings);
        }

        // If circular and we are in first or last, then go to the other end
        if (o.circular) {
          if (to > curr && to > itemLength - visibleCeil) {

            // temporarily set "to" as the difference
            to = to - curr;
            curr = curr % tl;

            // use the difference to make "to" correct relative to curr
            to = curr + to;
            ul.css(animCss, (-curr * styles.liSize) - offset);
          } else if ( to < curr && to < 0) {
            curr += tl;
            to += tl;
            ul.css(animCss, (-curr * styles.liSize) - offset);
          }

          curr = to + (to % 1);

        // If non-circular and "to" points beyond first or last, we change to first or last.
        } else {
          if (to < 0) {
            to = 0;
          } else if  (to > itemLength - visibleFloor) {
            to = itemLength - visibleFloor;
          }

          curr = to;

          if (curr === 0 && o.first) {
            o.first.call(this, vis(), direction);
          }

          if (curr === itemLength - visibleFloor && o.last) {
            o.last.call(this, vis(), direction);
          }

          // Disable buttons when the carousel reaches the last/first, and enable when not
          if (o.btnPrev) {
            o.$btnPrev.toggleClass(o.btnDisabledClass, curr === 0);
          }
          if (o.btnNext) {
            o.$btnNext.toggleClass(o.btnDisabledClass, curr === itemLength - visibleFloor);
          }
        }

        // if btnGo, set the active class on the btnGo element corresponding to the first visible carousel li
        // if autoPager, set active class on the appropriate autopager element
        setActive(curr, activeBtnTypes);

        $.jCarouselLite.curr = curr;

        if (prev === curr && !settings.force) {
          if (o.afterEnd) {
            o.afterEnd.call(div, vis(), direction, settings);
          }
          return curr;
        }

        running = true;

        aniProps[animCss] = -(curr * styles.liSize);
        ul.anim(aniProps, speed, o.easing, function() {
          if (o.afterEnd) {
            o.afterEnd.call(div, vis(), direction, settings);
          }
          running = false;
        });

        return curr;
      } // end go function

      // bind custom events so they can be triggered by user
      div
      .bind('go.jc', function(e, to, settings) {

        if (typeof to === 'undefined') {
          to = '+=1';
        }

        var todir = typeof to === 'string' && /(\+=|-=)(\d+)/.exec(to);

        if ( todir ) {
          to = todir[1] === '-=' ? curr - todir[2] * 1 : curr + todir[2] * 1;
        } else {
          to += start;
        }
        go(to, settings);
      })
      .bind('startCarousel.jc', function() {
        clearTimeout(self.setAutoAdvance);
        self.setAutoAdvance = undefined;
        div.trigger('go', '+=' + o.scroll);
        advancer();
        div.removeData('pausedjc').removeData('stoppedjc');
      })
      .bind('resumeCarousel.jc', function(event, forceRun) {
        if (self.setAutoAdvance) { return; }
        clearTimeout(self.setAutoAdvance);
        self.setAutoAdvance = undefined;

        var stopped = div.data('stoppedjc');
        if ( forceRun || !stopped ) {
          advancer();
          div.removeData('pausedjc');
          if (stopped) {
            div.removeData('stoppedjc');
          }
        }
      })

      .bind('pauseCarousel.jc', function() {
        clearTimeout(self.setAutoAdvance);
        self.setAutoAdvance = undefined;
        div.data('pausedjc', true);
      })
      .bind('stopCarousel.jc', function() {
        clearTimeout(self.setAutoAdvance);
        self.setAutoAdvance = undefined;

        div.data('stoppedjc', true);
      })

      .bind('refreshCarousel.jc', function(event, all) {
        if (all && o.circular) {
          makeCircular();
        }
        setDimensions(o.autoCSS);
      })

      .bind('endCarousel.jc', function() {
        if (self.setAutoAdvance) {
          clearTimeout(self.setAutoAdvance);
          self.setAutoAdvance = undefined;
        }
        if (o.btnPrev) {
          o.$btnPrev.addClass(o.btnDisabledClass).unbind('.jc');
        }
        if (o.btnNext) {
          o.$btnNext.addClass(o.btnDisabledClass).unbind('.jc');
        }
        if (o.btnGo) {
          $.each(o.btnGo, function(i, val) {
            $(val).unbind('.jc');
          });
        }

        if (o.circular) {
          li.slice(0, visibleCeil).remove();
          li.slice(-visibleCeil).remove();
        }
        $.each([animCss + 'jc', 'pausedjc', 'stoppedjc', 'dirjc'], function(i, d) {
          div.removeData(d);
        });
        div.unbind('.jc');
      });

      // touch gesture support

      touchEvents = {

        touchmove: function(event) {
          var tlength = event.targetTouches.length;

          if (tlength === 1) {
            endTouch.x = event.targetTouches[0].pageX;
            endTouch.y = event.targetTouches[0].pageY;
            aniProps[animCss] = startTouch[animCss] + (endTouch[axisPrimary] - startTouch[axisPrimary]);
            ul.css(aniProps);
            if (o.preventTouchWindowScroll) {
              event.preventDefault();
            }
          } else {
            endTouch.x = startTouch.x;
            endTouch.y = startTouch.y;
          }
        },

        touchend: function() {
          // bail out early if there is no touch movement
          if (!endTouch.x) {
            return;
          }

          var pxDelta = startTouch[axisPrimary] - endTouch[axisPrimary],
              pxAbsDelta = mabs( pxDelta ),
              primaryAxisGood = pxAbsDelta > o.swipeThresholds[axisPrimary],
              secondaryAxisGood =  mabs(startTouch[axisSecondary] - endTouch[axisSecondary]) < o.swipeThresholds[axisSecondary],
              timeDelta = +new Date() - startTouch.time,
              quickSwipe = timeDelta < o.swipeThresholds.time,
              operator = pxDelta > 0 ? '+=' : '-=',
              to = operator + o.scroll,
              swipeInfo  = { force: true };

          // quick, clean swipe
          if ( quickSwipe && primaryAxisGood && secondaryAxisGood ) {
            // set animation speed to twice as fast as that set in speed option
            swipeInfo.speed = o.speed / 2;
          }
          else
          // slow swipe < 1/2 slide width, OR
          // not enough movement for swipe, OR
          // too much movement on secondary axis when quick swipe
          if ( (!quickSwipe && pxAbsDelta < styles.liSize / 2) ||
            !primaryAxisGood ||
            (quickSwipe && !secondaryAxisGood)
            ) {
            // revert to same slide
            to = '+=0';
          }
          else
          // slow swipe > 1/2 slide width
          if ( !quickSwipe && pxAbsDelta > styles.liSize / 2 ) {
            to = Math.round(pxAbsDelta / styles.liSize);
            to = operator + (to > o.visible ? o.visible : to);

            // send pxDelta along as offset in case carousel is circular and needs to reset
            swipeInfo.offset = pxDelta;
          }

          div.trigger('go.jc', [to, swipeInfo]);
          endTouch = {};
        },

        handle: function(event) {
          event = event.originalEvent;
          touchEvents[event.type](event);
        }
      };

      if ( isTouch && o.swipe ) {
        div.bind('touchmove.jc touchend.jc', touchEvents.handle);
      } // end swipe events

      // Responsive design handling:
      // Reset dimensions on window.resize
      if (o.responsive) {
        prepResize = o.autoCSS;
        $(window).bind('resize', function() {
          if (prepResize) {
            ul.width( ul.width() * 2 );
            prepResize = false;
          }

          clearTimeout(resize);
          resize = setTimeout(function() {
            div.trigger('refreshCarousel.jc', [true]);
            prepResize = o.autoCSS;
          }, 100);
        });
      }

    }); // end each

    return this;
  };

  $.fn.jCarouselLite.defaults = {

    // valid selector for the "ul" container containing the slides
    containerSelector: 'ul',

    // valid selector for the slide "li" items
    itemSelector: 'li',

    btnPrev: null,
    btnNext: null,

    // array (or jQuery object) of elements. When clicked, makes the corresponding carousel LI the first visible one
    btnGo: null,

    // selector (or jQuery object) indicating the containing element for pagination navigation.
    autoPager: null,
    btnDisabledClass: 'disabled',

    // class applied to the active slide and btnGo element
    activeClass: 'active',

    // class applied to the btnGo elements corresponding to the visible slides
    visibleClass: 'vis',
    mouseWheel: false,
    speed: 200,
    easing: null,

    // milliseconds between scrolls
    timeout: 4000,

    // true to enable auto scrolling; number to auto-scroll by different number at a time than that of scroll option
    auto: false,


    // true to enable changing direction of auto scrolling when user clicks prev or next button
    directional: false,

    // number of times before autoscrolling will stop. (if circular is false, won't iterate more than number of items)
    autoStop: false,

    // pause scrolling on hover
    pause: true,
    vertical: false,

    // continue scrolling when reach the last item
    circular: true,

    // the number to be visible at a given time.
    visible: 3,

    // index of item to show initially in the first posiition
    start: 0,

    // number of items to scroll at a time
    scroll: 1,

    // whether to set initial styles on the carousel elements. See readme for info
    autoCSS: true,

    // whether the dimensions should change on resize
    responsive: false,

    // whether to set width of <li>s (and left/top of <ul>) based on width of <div>
    autoWidth: false,

    // touch options
    swipe: true,
    swipeThresholds: {
      x: 80,
      y: 40,
      time: 150
    },

    // whether to prevent vertical scrolling of the document window when swiping
    preventTouchWindowScroll: true,

    // Function to be called for each matched carousel when .jCaourselLite() is called.
    // Inside the function, `this` is the carousel div.
    // The function can take 2 arguments:
        // 1. The merged options object
        // 2. A jQuery object containing the <li> items in the carousel
    // If the function returns `false`, the plugin will skip all the carousel magic for that carousel div
    init: function() {},

    // function to be called once the first slide is hit
    first: null,

    // function to be called once the last slide is hit
    last: null,

    // function to be called before each transition starts
    beforeStart: null,

    // function to be called after each transition ends
    afterEnd: null
  };

  function iterations(itemLength, options) {
    return options.autoStop && (options.circular ? options.autoStop : Math.min(itemLength, options.autoStop));
  }

  function fixIds(i) {
    if ( this.id ) {
      this.id += i;
    }
  }
})(jQuery);
;/*
 * 	easyAccordion 0.1 - jQuery plugin
 *	written by Andrea Cima Serniotti	
 *	http://www.madeincima.eu
 *
 *	Copyright (c) 2010 Andrea Cima Serniotti (http://www.madeincima.eu)
 *	Dual licensed under the MIT (MIT-LICENSE.txt) and GPL (GPL-LICENSE.txt) licenses.
 *	Built for jQuery library http://jquery.com
 */
 
(function(jQuery) {
	jQuery.fn.easyAccordion = function(options) {
	
	var defaults = {			
		slideNum: true,
		autoStart: false,
		slideInterval: 3000
	};
			
	this.each(function() {
		
		var settings = jQuery.extend(defaults, options);		
		jQuery(this).find('dl').addClass('easy-accordion');
		
		
		// -------- Set the variables ------------------------------------------------------------------------------
		
		jQuery.fn.setVariables = function() {
			dlWidth = jQuery(this).width();
			dlHeight = jQuery(this).height();
			dtWidth = jQuery(this).find('dt').outerHeight();
			if (jQuery.browser.msie){ dtWidth = $(this).find('dt').outerWidth();}
			dtHeight = dlHeight - (jQuery(this).find('dt').outerWidth()-jQuery(this).find('dt').width());
			slideTotal = jQuery(this).find('dt').size();
			ddWidth = dlWidth - (dtWidth*slideTotal) - (jQuery(this).find('dd').outerWidth(true)-jQuery(this).find('dd').width());
			ddHeight = dlHeight - (jQuery(this).find('dd').outerHeight(true)-jQuery(this).find('dd').height());
		};
		jQuery(this).setVariables();
	
		
		// -------- Fix some weird cross-browser issues due to the CSS rotation -------------------------------------

		if (jQuery.browser.safari){ var dtTop = (dlHeight-dtWidth)/2; var dtOffset = -dtTop;  /* Safari and Chrome */ }
		if (jQuery.browser.mozilla){ var dtTop = dlHeight - 20; var dtOffset = - 20; /* FF */ }
		if (jQuery.browser.msie){ var dtTop = 0; var dtOffset = 0; /* IE */ }
		
		
		// -------- Getting things ready ------------------------------------------------------------------------------
		
		var f = 1;
		jQuery(this).find('dt').each(function(){
			jQuery(this).css({'width':dtHeight,'top':dtTop,'margin-left':dtOffset});	
			if(settings.slideNum == true){
				jQuery('<span class="slide-number">'+0+f+'</span>').appendTo(this);
				if(jQuery.browser.msie){	
					var slideNumLeft = parseInt(jQuery(this).find('.slide-number').css('left')) - 14;
					jQuery(this).find('.slide-number').css({'left': slideNumLeft})
					if(jQuery.browser.version == 6.0 || jQuery.browser.version == 7.0){
						jQuery(this).find('.slide-number').css({'bottom':'auto'});
					}
					if(jQuery.browser.version == 8.0){
					var slideNumTop = jQuery(this).find('.slide-number').css('bottom');
					var slideNumTopVal = parseInt(slideNumTop) + parseInt(jQuery(this).css('padding-top'))  - 12; 
					jQuery(this).find('.slide-number').css({'bottom': slideNumTopVal}); 
					}
				} else {
					var slideNumTop = jQuery(this).find('.slide-number').css('bottom');
					var slideNumTopVal = parseInt(slideNumTop) + parseInt(jQuery(this).css('padding-top')); 
					jQuery(this).find('.slide-number').css({'bottom': slideNumTopVal}); 
				}
			}
			f = f + 1;
		});
		
		if(jQuery(this).find('.active').size()) { 
			jQuery(this).find('.active').next('dd').addClass('active');
		} else {
			jQuery(this).find('dt:first').addClass('active').next('dd').addClass('active');
		}
		
		jQuery(this).find('dt:first').css({'left':'0'}).next().css({'left':dtWidth});
		jQuery(this).find('dd').css({'width':ddWidth,'height':ddHeight});	

		
		// -------- Functions ------------------------------------------------------------------------------
		
		jQuery.fn.findActiveSlide = function() {
				var i = 1;
				this.find('dt').each(function(){
				if(jQuery(this).hasClass('active')){
					activeID = i; // Active slide
				} else if (jQuery(this).hasClass('no-more-active')){
					noMoreActiveID = i; // No more active slide
				}
				i = i + 1;
			});
		};
			
		jQuery.fn.calculateSlidePos = function() {
			var u = 2;
			jQuery(this).find('dt').not(':first').each(function(){	
				var activeDtPos = dtWidth*activeID;
				if(u <= activeID){
					var leftDtPos = dtWidth*(u-1);
					jQuery(this).animate({'left': leftDtPos});
					if(u < activeID){ // If the item sits to the left of the active element
						jQuery(this).next().css({'left':leftDtPos+dtWidth});	
					} else{ // If the item is the active one
						jQuery(this).next().animate({'left':activeDtPos});
					}
				} else {
					var rightDtPos = dlWidth-(dtWidth*(slideTotal-u+1));
					jQuery(this).animate({'left': rightDtPos});
					var rightDdPos = rightDtPos+dtWidth;
					jQuery(this).next().animate({'left':rightDdPos});	
				}
				u = u+ 1;
			});
			setTimeout( function() {
				jQuery('.easy-accordion').find('dd').not('.active').each(function(){ 
					jQuery(this).css({'display':'none'});
				});
			}, 400);
			
		};
	
		jQuery.fn.activateSlide = function() {
			this.parent('dl').setVariables();	
			this.parent('dl').find('dd').css({'display':'block'});
			this.parent('dl').find('dd.plus').removeClass('plus');
			this.parent('dl').find('.no-more-active').removeClass('no-more-active');
			this.parent('dl').find('.active').removeClass('active').addClass('no-more-active');
			this.addClass('active').next().addClass('active');	
			this.parent('dl').findActiveSlide();
			if(activeID < noMoreActiveID){
				this.parent('dl').find('dd.no-more-active').addClass('plus');
			}
			this.parent('dl').calculateSlidePos();	
		};
	
		jQuery.fn.rotateSlides = function(slideInterval, timerInstance) {
			var accordianInstance = jQuery(this);
			timerInstance.value = setTimeout(function(){accordianInstance.rotateSlides(slideInterval, timerInstance);}, slideInterval);
			jQuery(this).findActiveSlide();
			var totalSlides = jQuery(this).find('dt').size();
			var activeSlide = activeID;
			var newSlide = activeSlide + 1;
			if (newSlide > totalSlides) newSlide = 1;
			jQuery(this).find('dt:eq(' + (newSlide-1) + ')').activateSlide(); // activate the new slide
		}


		// -------- Let's do it! ------------------------------------------------------------------------------
		
		function trackerObject() {this.value = null}
		var timerInstance = new trackerObject();
		
		jQuery(this).findActiveSlide();
		jQuery(this).calculateSlidePos();
		
		if (settings.autoStart == true){
			var accordianInstance = jQuery(this);
			var interval = parseInt(settings.slideInterval);
			timerInstance.value = setTimeout(function(){
				accordianInstance.rotateSlides(interval, timerInstance);
				}, interval);
		} 

		jQuery(this).find('dt').not('active').click(function(){		
			jQuery(this).activateSlide();
			clearTimeout(timerInstance.value);
		});	
				
		if (!(jQuery.browser.msie && jQuery.browser.version == 6.0)){ 
			jQuery('dt').hover(function(){
				jQuery(this).addClass('hover');
			}, function(){
				jQuery(this).removeClass('hover');
			});
		}
	});
	};
})(jQuery);;/*
 * jQuery Easing v1.4.1 - http://gsgd.co.uk/sandbox/jquery/easing/
 * Open source under the BSD License.
 * Copyright © 2008 George McGinley Smith
 * All rights reserved.
 * https://raw.github.com/gdsmith/jquery.easing/master/LICENSE
*/

/* globals jQuery, define, module, require */
(function (factory) {
	if (typeof define === "function" && define.amd) {
		define(['jquery'], function ($) {
			return factory($);
		});
	} else if (typeof module === "object" && typeof module.exports === "object") {
		module.exports = factory(require('jquery'));
	} else {
		factory(jQuery);
	}
})(function($){

	// Preserve the original jQuery "swing" easing as "jswing"
	if (typeof $.easing !== 'undefined') {
		$.easing['jswing'] = $.easing['swing'];
	}

	var pow = Math.pow,
		sqrt = Math.sqrt,
		sin = Math.sin,
		cos = Math.cos,
		PI = Math.PI,
		c1 = 1.70158,
		c2 = c1 * 1.525,
		c3 = c1 + 1,
		c4 = ( 2 * PI ) / 3,
		c5 = ( 2 * PI ) / 4.5;

	// x is the fraction of animation progress, in the range 0..1
	function bounceOut(x) {
		var n1 = 7.5625,
			d1 = 2.75;
		if ( x < 1/d1 ) {
			return n1*x*x;
		} else if ( x < 2/d1 ) {
			return n1*(x-=(1.5/d1))*x + .75;
		} else if ( x < 2.5/d1 ) {
			return n1*(x-=(2.25/d1))*x + .9375;
		} else {
			return n1*(x-=(2.625/d1))*x + .984375;
		}
	}

	$.extend( $.easing, {
		def: 'easeOutQuad',
		swing: function (x) {
			return $.easing[$.easing.def](x);
		},
		easeInQuad: function (x) {
			return x * x;
		},
		easeOutQuad: function (x) {
			return 1 - ( 1 - x ) * ( 1 - x );
		},
		easeInOutQuad: function (x) {
			return x < 0.5 ?
				2 * x * x :
				1 - pow( -2 * x + 2, 2 ) / 2;
		},
		easeInCubic: function (x) {
			return x * x * x;
		},
		easeOutCubic: function (x) {
			return 1 - pow( 1 - x, 3 );
		},
		easeInOutCubic: function (x) {
			return x < 0.5 ?
				4 * x * x * x :
				1 - pow( -2 * x + 2, 3 ) / 2;
		},
		easeInQuart: function (x) {
			return x * x * x * x;
		},
		easeOutQuart: function (x) {
			return 1 - pow( 1 - x, 4 );
		},
		easeInOutQuart: function (x) {
			return x < 0.5 ?
				8 * x * x * x * x :
				1 - pow( -2 * x + 2, 4 ) / 2;
		},
		easeInQuint: function (x) {
			return x * x * x * x * x;
		},
		easeOutQuint: function (x) {
			return 1 - pow( 1 - x, 5 );
		},
		easeInOutQuint: function (x) {
			return x < 0.5 ?
				16 * x * x * x * x * x :
				1 - pow( -2 * x + 2, 5 ) / 2;
		},
		easeInSine: function (x) {
			return 1 - cos( x * PI/2 );
		},
		easeOutSine: function (x) {
			return sin( x * PI/2 );
		},
		easeInOutSine: function (x) {
			return -( cos( PI * x ) - 1 ) / 2;
		},
		easeInExpo: function (x) {
			return x === 0 ? 0 : pow( 2, 10 * x - 10 );
		},
		easeOutExpo: function (x) {
			return x === 1 ? 1 : 1 - pow( 2, -10 * x );
		},
		easeInOutExpo: function (x) {
			return x === 0 ? 0 : x === 1 ? 1 : x < 0.5 ?
				pow( 2, 20 * x - 10 ) / 2 :
				( 2 - pow( 2, -20 * x + 10 ) ) / 2;
		},
		easeInCirc: function (x) {
			return 1 - sqrt( 1 - pow( x, 2 ) );
		},
		easeOutCirc: function (x) {
			return sqrt( 1 - pow( x - 1, 2 ) );
		},
		easeInOutCirc: function (x) {
			return x < 0.5 ?
				( 1 - sqrt( 1 - pow( 2 * x, 2 ) ) ) / 2 :
				( sqrt( 1 - pow( -2 * x + 2, 2 ) ) + 1 ) / 2;
		},
		easeInElastic: function (x) {
			return x === 0 ? 0 : x === 1 ? 1 :
				-pow( 2, 10 * x - 10 ) * sin( ( x * 10 - 10.75 ) * c4 );
		},
		easeOutElastic: function (x) {
			return x === 0 ? 0 : x === 1 ? 1 :
				pow( 2, -10 * x ) * sin( ( x * 10 - 0.75 ) * c4 ) + 1;
		},
		easeInOutElastic: function (x) {
			return x === 0 ? 0 : x === 1 ? 1 : x < 0.5 ?
				-( pow( 2, 20 * x - 10 ) * sin( ( 20 * x - 11.125 ) * c5 )) / 2 :
				pow( 2, -20 * x + 10 ) * sin( ( 20 * x - 11.125 ) * c5 ) / 2 + 1;
		},
		easeInBack: function (x) {
			return c3 * x * x * x - c1 * x * x;
		},
		easeOutBack: function (x) {
			return 1 + c3 * pow( x - 1, 3 ) + c1 * pow( x - 1, 2 );
		},
		easeInOutBack: function (x) {
			return x < 0.5 ?
				( pow( 2 * x, 2 ) * ( ( c2 + 1 ) * 2 * x - c2 ) ) / 2 :
				( pow( 2 * x - 2, 2 ) *( ( c2 + 1 ) * ( x * 2 - 2 ) + c2 ) + 2 ) / 2;
		},
		easeInBounce: function (x) {
			return 1 - bounceOut( 1 - x );
		},
		easeOutBounce: bounceOut,
		easeInOutBounce: function (x) {
			return x < 0.5 ?
				( 1 - bounceOut( 1 - 2 * x ) ) / 2 :
				( 1 + bounceOut( 2 * x - 1 ) ) / 2;
		}
	});
	return $;
});
;// Camera slideshow v1.4.0 - a jQuery slideshow with many effects, transitions, easy to customize, using canvas and mobile ready, based on jQuery 1.9.1+
// Copyright (c) 2012 by Manuel Masia - www.pixedelic.com
// Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

/****This plugin is being customized, as it is no longer supported****/

;(function($){$.fn.camera = function(opts, callback) {
	
	var defaults = {
		alignment			: 'center', //topLeft, topCenter, topRight, centerLeft, center, centerRight, bottomLeft, bottomCenter, bottomRight
		
		autoAdvance			: true,	//true, false
		
		mobileAutoAdvance	: true, //true, false. Auto-advancing for mobile devices
		
		barDirection		: 'leftToRight',	//'leftToRight', 'rightToLeft', 'topToBottom', 'bottomToTop'
		
		barPosition			: 'bottom',	//'bottom', 'left', 'top', 'right'
		
		cols				: 6,
		
		easing				: 'easeInOutExpo',	//for the complete list http://jqueryui.com/demos/effect/easing.html
		
		mobileEasing		: '',	//leave empty if you want to display the same easing on mobile devices and on desktop etc.
		
		fx					: 'random',	//'random','simpleFade', 'curtainTopLeft', 'curtainTopRight', 'curtainBottomLeft', 'curtainBottomRight', 'curtainSliceLeft', 'curtainSliceRight', 'blindCurtainTopLeft', 'blindCurtainTopRight', 'blindCurtainBottomLeft', 'blindCurtainBottomRight', 'blindCurtainSliceBottom', 'blindCurtainSliceTop', 'stampede', 'mosaic', 'mosaicReverse', 'mosaicRandom', 'mosaicSpiral', 'mosaicSpiralReverse', 'topLeftBottomRight', 'bottomRightTopLeft', 'bottomLeftTopRight', 'bottomLeftTopRight'
										//you can also use more than one effect, just separate them with commas: 'simpleFade, scrollRight, scrollBottom'

		mobileFx			: '',	//leave empty if you want to display the same effect on mobile devices and on desktop etc.

		gridDifference		: 250,	//to make the grid blocks slower than the slices, this value must be smaller than transPeriod
		
		height				: '50%',	//here you can type pixels (for instance '300px'), a percentage (relative to the width of the slideshow, for instance '50%') or 'auto'
		
		imagePath			: 'images/',	//he path to the image folder (it serves for the blank.gif, when you want to display videos)
		
		hover				: true,	//true, false. Puase on state hover. Not available for mobile devices
				
		loader				: 'pie',	//pie, bar, none (even if you choose "pie", old browsers like IE8- can't display it... they will display always a loading bar)
		
		loaderColor			: '#eeeeee', 
		
		loaderBgColor		: '#222222', 
		
		loaderOpacity		: .8,	//0, .1, .2, .3, .4, .5, .6, .7, .8, .9, 1
		
		loaderPadding		: 2,	//how many empty pixels you want to display between the loader and its background
		
		loaderStroke		: 7,	//the thickness both of the pie loader and of the bar loader. Remember: for the pie, the loader thickness must be less than a half of the pie diameter
				
		minHeight			: '200px',	//you can also leave it blank
		
		navigation			: true,	//true or false, to display or not the navigation buttons
		
		navigationHover		: true,	//if true the navigation button (prev, next and play/stop buttons) will be visible on hover state only, if false they will be visible always
		
		mobileNavHover		: true,	//same as above, but only for mobile devices
		
		opacityOnGrid		: false,	//true, false. Decide to apply a fade effect to blocks and slices: if your slideshow is fullscreen or simply big, I recommend to set it false to have a smoother effect 
		
		overlayer			: true,	//a layer on the images to prevent the users grab them simply by clicking the right button of their mouse (.camera_overlayer)
		
		pagination			: true,
		
		playPause			: true,	//true or false, to display or not the play/pause buttons
		
		pauseOnClick		: true,	//true, false. It stops the slideshow when you click the sliders.
		
		pieDiameter			: 38,
		
		piePosition			: 'rightTop',	//'rightTop', 'leftTop', 'leftBottom', 'rightBottom'
		
		portrait			: false, //true, false. Select true if you don't want that your images are cropped
		
		rows				: 4,
		
		slicedCols			: 12,	//if 0 the same value of cols
		
		slicedRows			: 8,	//if 0 the same value of rows
		
		slideOn				: 'random',	//next, prev, random: decide if the transition effect will be applied to the current (prev) or the next slide
		
		thumbnails			: false,
		
		time				: 7000,	//milliseconds between the end of the sliding effect and the start of the nex one
		
		transPeriod			: 1500,	//lenght of the sliding effect in milliseconds
		
////////callbacks

		onEndTransition		: function() {  },	//this callback is invoked when the transition effect ends

		onLoaded			: function() {  },	//this callback is invoked when the image on a slide has completely loaded
		
		onStartLoading		: function() {  },	//this callback is invoked when the image on a slide start loading
		
		onStartTransition	: function() {  }	//this callback is invoked when the transition effect starts

    };
	
	
	function isMobile() {
		if( navigator.userAgent.match(/Android/i) ||
			navigator.userAgent.match(/webOS/i) ||
			navigator.userAgent.match(/iPad/i) ||
			navigator.userAgent.match(/iPhone/i) ||
			navigator.userAgent.match(/iPod/i)
			){
				return true;
		}
	}

	$.support.borderRadius = false;
	$.each(['borderRadius','BorderRadius','MozBorderRadius','WebkitBorderRadius','OBorderRadius','KhtmlBorderRadius'], function() {
		if(document.body.style[this] !== undefined) $.support.borderRadius = true;
	});

	var opts = $.extend({}, defaults, opts);
	
	var wrap = $(this).addClass('camera_wrap');
	
	wrap.wrapInner(
        '<div class="camera_src" />'
		).wrapInner(
	    '<div class="camera_fakehover" />'
		);
		
	var fakeHover = $('.camera_fakehover',wrap);
	var fakeHoverSelector = ('.camera_fakehover',wrap);
	
	fakeHover.append(
		'<div class="camera_target"></div>'
		);
	if(opts.overlayer == true){
		fakeHover.append(
			'<div class="camera_overlayer"></div>'
			)
	}
		fakeHover.append(
        '<div class="camera_target_content"></div>'
		);
		
	var loader;
	
	if(opts.loader=='pie' && !$.support.borderRadius){
		loader = 'bar';
	} else {
		loader = opts.loader;
	}
	
	if(loader == 'pie'){
		fakeHover.append(
			'<div class="camera_pie"></div>'
			)
	} else if (loader == 'bar') {
		fakeHover.append(
			'<div class="camera_bar"></div>'
			)
	} else {
		fakeHover.append(
			'<div class="camera_bar" style="display:none"></div>'
			)
	}
	
	if(opts.playPause==true){
		fakeHover.append(
        '<div class="camera_commands"></div>'
		)
	}
		
	if(opts.navigation==true){
		fakeHover.append(
			'<div class="camera_prev"><span></span></div>'
			).append(
			'<div class="camera_next"><span></span></div>'
			);
	}
		
	if(opts.thumbnails==true){
		wrap.append(
			'<div class="camera_thumbs_cont" />'
			);
	}
	
	if(opts.thumbnails==true && opts.pagination!=true){
		$('.camera_thumbs_cont',wrap).wrap(
			'<div />'
			).wrap(
				'<div class="camera_thumbs" />'
			).wrap(
				'<div />'
			).wrap(
				'<div class="camera_command_wrap" />'
			);
	}
		
	if(opts.pagination==true){
		wrap.append(
			'<div class="camera_pag"></div>'
			);
	}
		
	wrap.append(
		'<div class="camera_loader"></div>'
		);
		
	$('.camera_caption',wrap).each(function(){
		$(this).wrapInner('<div />');
	});
		
                
	var pieID = 'pie_'+wrap.index(),
		elem = $('.camera_src',wrap),
		target = $('.camera_target',wrap),
		content = $('.camera_target_content',wrap),
		pieContainer = $('.camera_pie',wrap),
		barContainer = $('.camera_bar',wrap),
		prevNav = $('.camera_prev',wrap),
		nextNav = $('.camera_next',wrap),
		commands = $('.camera_commands',wrap),
		pagination = $('.camera_pag',wrap),
		thumbs = $('.camera_thumbs_cont',wrap);	

	
	var w,
		h;


	var allImg = new Array();
	$('> div', elem).each( function() { 
		allImg.push($(this).attr('data-src'));
	});
	
	var allLinks = new Array();
	$('> div', elem).each( function() {
		if($(this).attr('data-link')){
			allLinks.push($(this).attr('data-link'));
		} else {
			allLinks.push('');
		}
	});
	
	var allTargets = new Array();
	$('> div', elem).each( function() {
		if($(this).attr('data-target')){
			allTargets.push($(this).attr('data-target'));
		} else {
			allTargets.push('');
		}
	});
	
	var allPor = new Array();
	$('> div', elem).each( function() {
		if($(this).attr('data-portrait')){
			allPor.push($(this).attr('data-portrait'));
		} else {
			allPor.push('');
		}
	});
	
	var allAlign= new Array();
	$('> div', elem).each( function() { 
		if($(this).attr('data-alignment')){
			allAlign.push($(this).attr('data-alignment'));
		} else {
			allAlign.push('');
		}
	});
        
        var allAlts= new Array();
	$('> div', elem).each( function() { 
		if($(this).attr('data-alt')){
			allAlts.push($(this).attr('data-alt'));
		} else {
			allAlts.push('');
		}
	});
		
	var allThumbs = new Array();
	$('> div', elem).each( function() { 
		if($(this).attr('data-thumb')){
			allThumbs.push($(this).attr('data-thumb'));
		} else {
			allThumbs.push('');
		}
	});
	
	var amountSlide = allImg.length;

	$(content).append('<div class="cameraContents" />');
	var loopMove;
	for (loopMove=0;loopMove<amountSlide;loopMove++)
	{
		$('.cameraContents',content).append('<div class="cameraContent" />');
		if(allLinks[loopMove]!=''){
			//only for Wordpress plugin
			var dataBox = $('> div ',elem).eq(loopMove).attr('data-box');
			if(typeof dataBox !== 'undefined' && dataBox !== false && dataBox != '') {
				dataBox = 'data-box="'+$('> div ',elem).eq(loopMove).attr('data-box')+'"';
			} else {
				dataBox = '';
			}
			//
			$('.camera_target_content .cameraContent:eq('+loopMove+')',wrap).append('<a class="camera_link" href="'+allLinks[loopMove]+'" '+dataBox+' target="'+allTargets[loopMove]+'"></a>');
		}

	}
	$('.camera_caption',wrap).each(function(){
		var ind = $(this).parent().index(),
			cont = wrap.find('.cameraContent').eq(ind);
		$(this).appendTo(cont);
	});
	
	target.append('<div class="cameraCont" />');
	var cameraCont = $('.cameraCont',wrap);
	

	
	var loop;
	for (loop=0;loop<amountSlide;loop++)
	{
		cameraCont.append('<div class="cameraSlide cameraSlide_'+loop+'" />');
		var div = $('> div:eq('+loop+')',elem);
		target.find('.cameraSlide_'+loop).clone(div);
	}
	
	
	function thumbnailVisible() {
		var wTh = $(thumbs).width();
		$('li', thumbs).removeClass('camera_visThumb');
		$('li', thumbs).each(function(){
			var pos = $(this).position(),
				ulW = $('ul', thumbs).outerWidth(),
				offUl = $('ul', thumbs).offset().left,
				offDiv = $('> div',thumbs).offset().left,
				ulLeft = offDiv-offUl;
				if(ulLeft>0){
					$('.camera_prevThumbs',camera_thumbs_wrap).removeClass('hideNav');
				} else {
					$('.camera_prevThumbs',camera_thumbs_wrap).addClass('hideNav');
				}
				if((ulW-ulLeft)>wTh){
					$('.camera_nextThumbs',camera_thumbs_wrap).removeClass('hideNav');
				} else {
					$('.camera_nextThumbs',camera_thumbs_wrap).addClass('hideNav');
				}
				var left = pos.left,
					right = pos.left+($(this).width());
				if(right-ulLeft<=wTh && left-ulLeft>=0){
					$(this).addClass('camera_visThumb');
				}
		});
	}
	
	$(window).bind('load resize pageshow',function(){
		thumbnailPos();
		thumbnailVisible();
	});


	cameraCont.append('<div class="cameraSlide cameraSlide_'+loop+'" />');
	
	
	var started;
	
	wrap.show();
	var w = target.width();
	var h = target.height();
	
	var setPause;
		
	$(window).bind('resize pageshow',function(){
		if(started == true) {
			resizeImage();
		}
		$('ul', thumbs).animate({'margin-top':0},0,thumbnailPos);
		if(!elem.hasClass('paused')){
			elem.addClass('paused');
			if($('.camera_stop',camera_thumbs_wrap).length){
				$('.camera_stop',camera_thumbs_wrap).hide()
				$('.camera_play',camera_thumbs_wrap).show();
				if(loader!='none'){
					$('#'+pieID).hide();
				}
			} else {
				if(loader!='none'){
					$('#'+pieID).hide();
				}
			}
			clearTimeout(setPause);
			setPause = setTimeout(function(){
				elem.removeClass('paused');
				if($('.camera_play',camera_thumbs_wrap).length){
					$('.camera_play',camera_thumbs_wrap).hide();
					$('.camera_stop',camera_thumbs_wrap).show();
					if(loader!='none'){
						$('#'+pieID).fadeIn();
					}
				} else {
					if(loader!='none'){
						$('#'+pieID).fadeIn();
					}
				}
			},1500);
		}
	});
	
	function resizeImage(){	
		var res;
		function resizeImageWork(){
			w = wrap.width();
			if(opts.height.indexOf('%')!=-1) {
				var startH = Math.round(w / (100/parseFloat(opts.height)));
				if(opts.minHeight != '' && startH < parseFloat(opts.minHeight)){
					h = parseFloat(opts.minHeight);
				} else {
					h = startH;
				}
				wrap.css({height:h});
			} else if (opts.height=='auto') {
				h = wrap.height();
			} else {
				h = parseFloat(opts.height);
				wrap.css({height:h});
			}
			$('.camerarelative',target).css({'width':w,'height':h});
			$('.imgLoaded',target).each(function(){
				var t = $(this),
					wT = t.attr('width'),
					hT = t.attr('height'),
					imgLoadIn = t.index(),
					mTop,
					mLeft,
					alignment = t.attr('data-alignment'),
					portrait =  t.attr('data-portrait');
					
					if(typeof alignment === 'undefined' || alignment === false || alignment === ''){
						alignment = opts.alignment;
					}
					
					if(typeof portrait === 'undefined' || portrait === false || portrait === ''){
						portrait = opts.portrait;
					}
										
					if(portrait==false||portrait=='false'){
						if((wT/hT)<(w/h)) {
							var r = w / wT;
							var d = (Math.abs(h - (hT*r)))*0.5;
							switch(alignment){
								case 'topLeft':
									mTop = 0;
									break;
								case 'topCenter':
									mTop = 0;
									break;
								case 'topRight':
									mTop = 0;
									break;
								case 'centerLeft':
									mTop = '-'+d+'px';
									break;
								case 'center':
									mTop = '-'+d+'px';
									break;
								case 'centerRight':
									mTop = '-'+d+'px';
									break;
								case 'bottomLeft':
									mTop = '-'+d*2+'px';
									break;
								case 'bottomCenter':
									mTop = '-'+d*2+'px';
									break;
								case 'bottomRight':
									mTop = '-'+d*2+'px';
									break;
							}
							t.css({
								'height' : hT*r,
								'margin-left' : 0,
								'margin-right' : 0,
								'margin-top' : mTop,
								'position' : 'absolute',
								'visibility' : 'visible',
								'width' : w
							});
						}
						else {
							var r = h / hT;
							var d = (Math.abs(w - (wT*r)))*0.5;
							switch(alignment){
								case 'topLeft':
									mLeft = 0;
									break;
								case 'topCenter':
									mLeft = '-'+d+'px';
									break;
								case 'topRight':
									mLeft = '-'+d*2+'px';
									break;
								case 'centerLeft':
									mLeft = 0;
									break;
								case 'center':
									mLeft = '-'+d+'px';
									break;
								case 'centerRight':
									mLeft = '-'+d*2+'px';
									break;
								case 'bottomLeft':
									mLeft = 0;
									break;
								case 'bottomCenter':
									mLeft = '-'+d+'px';
									break;
								case 'bottomRight':
									mLeft = '-'+d*2+'px';
									break;
							}
							t.css({
								'height' : h,
								'margin-left' : mLeft,
								'margin-right' : mLeft,
								'margin-top' : 0,
								'position' : 'absolute',
								'visibility' : 'visible',
								'width' : wT*r
							});
						}
					} else {
						if((wT/hT)<(w/h)) {
							var r = h / hT;
							var d = (Math.abs(w - (wT*r)))*0.5;
							switch(alignment){
								case 'topLeft':
									mLeft = 0;
									break;
								case 'topCenter':
									mLeft = d+'px';
									break;
								case 'topRight':
									mLeft = d*2+'px';
									break;
								case 'centerLeft':
									mLeft = 0;
									break;
								case 'center':
									mLeft = d+'px';
									break;
								case 'centerRight':
									mLeft = d*2+'px';
									break;
								case 'bottomLeft':
									mLeft = 0;
									break;
								case 'bottomCenter':
									mLeft = d+'px';
									break;
								case 'bottomRight':
									mLeft = d*2+'px';
									break;
							}
							t.css({
								'height' : h,
								'margin-left' : mLeft,
								'margin-right' : mLeft,
								'margin-top' : 0,
								'position' : 'absolute',
								'visibility' : 'visible',
								'width' : wT*r
							});
						}
						else {
							var r = w / wT;
							var d = (Math.abs(h - (hT*r)))*0.5;
							switch(alignment){
								case 'topLeft':
									mTop = 0;
									break;
								case 'topCenter':
									mTop = 0;
									break;
								case 'topRight':
									mTop = 0;
									break;
								case 'centerLeft':
									mTop = d+'px';
									break;
								case 'center':
									mTop = d+'px';
									break;
								case 'centerRight':
									mTop = d+'px';
									break;
								case 'bottomLeft':
									mTop = d*2+'px';
									break;
								case 'bottomCenter':
									mTop = d*2+'px';
									break;
								case 'bottomRight':
									mTop = d*2+'px';
									break;
							}
							t.css({
								'height' : hT*r,
								'margin-left' : 0,
								'margin-right' : 0,
								'margin-top' : mTop,
								'position' : 'absolute',
								'visibility' : 'visible',
								'width' : w
							});
						}
					}
			});
		}
		if (started == true) {
			clearTimeout(res);
			res = setTimeout(resizeImageWork,200);
		} else {
			resizeImageWork();
		}
		
		started = true;
	}
	
	
	var u,
		setT;

	var clickEv,
		autoAdv,
		navHover,
		commands,
		pagination;

	var videoHover,
		videoPresent;
		
	if(isMobile() && opts.mobileAutoAdvance!=''){
		autoAdv = opts.mobileAutoAdvance;
	} else {
		autoAdv = opts.autoAdvance;
	}
	
	if(autoAdv==false){
		elem.addClass('paused');
	}

	if(isMobile() && opts.mobileNavHover!=''){
		navHover = opts.mobileNavHover;
	} else {
		navHover = opts.navigationHover;
	}

	if(elem.length!=0){
			
		var selector = $('.cameraSlide',target);
		selector.wrapInner('<div class="camerarelative" />');
		
		var navSlide;
			
		var barDirection = opts.barDirection;
	
		var camera_thumbs_wrap = wrap;


		$('iframe',fakeHover).each(function(){
			var t = $(this);
			var src = t.attr('src');
			t.attr('data-src',src);
			var divInd = t.parent().index('.camera_src > div');
			$('.camera_target_content .cameraContent:eq('+divInd+')',wrap).append(t);
		});
		function imgFake() {
				$('iframe',fakeHover).each(function(){
					$('.camera_caption',fakeHover).show();
					var t = $(this);
					var cloneSrc = t.attr('data-src');
					t.attr('src',cloneSrc);
					var imgFakeUrl = opts.imagePath+'blank.gif';
					var imgFake = new Image();
					imgFake.src = imgFakeUrl;
					if(opts.height.indexOf('%')!=-1) {
						var startH = Math.round(w / (100/parseFloat(opts.height)));
						if(opts.minHeight != '' && startH < parseFloat(opts.minHeight)){
							h = parseFloat(opts.minHeight);
						} else {
							h = startH;
						}
					} else if (opts.height=='auto') {
						h = wrap.height();
					} else {
						h = parseFloat(opts.height);
					}
					t.after($(imgFake).attr({'class':'imgFake','width':w,'height':h}));
					var clone = t.clone();
					t.remove();
					$(imgFake).bind('click',function(){
						if($(this).css('position')=='absolute') {
							$(this).remove();
							if(cloneSrc.indexOf('vimeo') != -1 || cloneSrc.indexOf('youtube') != -1) {
								if(cloneSrc.indexOf('?') != -1){
									autoplay = '&autoplay=1';
								} else {
									autoplay = '?autoplay=1';
								}
							} else if(cloneSrc.indexOf('dailymotion') != -1) {
								if(cloneSrc.indexOf('?') != -1){
									autoplay = '&autoPlay=1';
								} else {
									autoplay = '?autoPlay=1';
								}
							}
							clone.attr('src',cloneSrc+autoplay);
							videoPresent = true;
						} else {
							$(this).css({position:'absolute',top:0,left:0,zIndex:10}).after(clone);
							clone.css({position:'absolute',top:0,left:0,zIndex:9});
						}
					});
				});
		}
		
		imgFake();
		
		
		if(opts.hover==true){
			if(!isMobile()){
				fakeHover.hover(function(){
					elem.addClass('hovered');
				},function(){
					elem.removeClass('hovered');
				});
			}
		}

		if(navHover==true){
			$(prevNav,wrap).animate({opacity:0},0);
			$(nextNav,wrap).animate({opacity:0},0);
			$(commands,wrap).animate({opacity:0},0);
			if(isMobile()){
				$(document).on('vmouseover',fakeHoverSelector,function(){
					$(prevNav,wrap).animate({opacity:1},200);
					$(nextNav,wrap).animate({opacity:1},200);
					$(commands,wrap).animate({opacity:1},200);
				});
				$(document).on('vmouseout',fakeHoverSelector,function(){
					$(prevNav,wrap).delay(500).animate({opacity:0},200);
					$(nextNav,wrap).delay(500).animate({opacity:0},200);
					$(commands,wrap).delay(500).animate({opacity:0},200);
				});
			} else {
				fakeHover.hover(function(){
					$(prevNav,wrap).animate({opacity:1},200);
					$(nextNav,wrap).animate({opacity:1},200);
					$(commands,wrap).animate({opacity:1},200);
				},function(){
					$(prevNav,wrap).animate({opacity:0},200);
					$(nextNav,wrap).animate({opacity:0},200);
					$(commands,wrap).animate({opacity:0},200);
				});
			}
		}
		
	
		camera_thumbs_wrap.on('click','.camera_stop',function(){
			autoAdv = false;
			elem.addClass('paused');
			if($('.camera_stop',camera_thumbs_wrap).length){
				$('.camera_stop',camera_thumbs_wrap).hide()
				$('.camera_play',camera_thumbs_wrap).show();
				if(loader!='none'){
					$('#'+pieID).hide();
				}
			} else {
				if(loader!='none'){
					$('#'+pieID).hide();
				}
			}
		});
	
		camera_thumbs_wrap.on('click','.camera_play',function(){
			autoAdv = true;
			elem.removeClass('paused');
			if($('.camera_play',camera_thumbs_wrap).length){
				$('.camera_play',camera_thumbs_wrap).hide();
				$('.camera_stop',camera_thumbs_wrap).show();
				if(loader!='none'){
					$('#'+pieID).show();
				}
			} else {
				if(loader!='none'){
					$('#'+pieID).show();
				}
			}
		});
	
		if(opts.pauseOnClick==true){
			$('.camera_target_content',fakeHover).mouseup(function(){
				autoAdv = false;
				elem.addClass('paused');
				$('.camera_stop',camera_thumbs_wrap).hide()
				$('.camera_play',camera_thumbs_wrap).show();
				$('#'+pieID).hide();
			});
		}
		$('.cameraContent, .imgFake',fakeHover).hover(function(){
			videoHover = true;
		},function(){
			videoHover = false;
		});
		
		$('.cameraContent, .imgFake',fakeHover).bind('click',function(){
			if(videoPresent == true && videoHover == true) {
				autoAdv = false;
				$('.camera_caption',fakeHover).hide();
				elem.addClass('paused');
				$('.camera_stop',camera_thumbs_wrap).hide()
				$('.camera_play',camera_thumbs_wrap).show();
				$('#'+pieID).hide();
			}
		});
		
		
	}
	
	
		function shuffle(arr) {
			for(
			  var j, x, i = arr.length; i;
			  j = parseInt(Math.random() * i),
			  x = arr[--i], arr[i] = arr[j], arr[j] = x
			);
			return arr;
		}
	
		function isInteger(s) {
			return Math.ceil(s) == Math.floor(s);
		}	
	
		if (loader != 'pie') {
			barContainer.append('<span class="camera_bar_cont" />');
			$('.camera_bar_cont',barContainer)
				.animate({opacity:opts.loaderOpacity},0)
				.css({'position':'absolute', 'left':0, 'right':0, 'top':0, 'bottom':0, 'background-color':opts.loaderBgColor})
				.append('<span id="'+pieID+'" />');
			$('#'+pieID).animate({opacity:0},0);
			var canvas = $('#'+pieID);
			canvas.css({'position':'absolute', 'background-color':opts.loaderColor});
			switch(opts.barPosition){
				case 'left':
					barContainer.css({right:'auto',width:opts.loaderStroke});
					break;
				case 'right':
					barContainer.css({left:'auto',width:opts.loaderStroke});
					break;
				case 'top':
					barContainer.css({bottom:'auto',height:opts.loaderStroke});
					break;
				case 'bottom':
					barContainer.css({top:'auto',height:opts.loaderStroke});
					break;
			}
			switch(barDirection){
				case 'leftToRight':
					canvas.css({'left':0, 'right':0, 'top':opts.loaderPadding, 'bottom':opts.loaderPadding});
					break;
				case 'rightToLeft':
					canvas.css({'left':0, 'right':0, 'top':opts.loaderPadding, 'bottom':opts.loaderPadding});
					break;
				case 'topToBottom':
					canvas.css({'left':opts.loaderPadding, 'right':opts.loaderPadding, 'top':0, 'bottom':0});
					break;
				case 'bottomToTop':
					canvas.css({'left':opts.loaderPadding, 'right':opts.loaderPadding, 'top':0, 'bottom':0});
					break;
			}
		} else {
			pieContainer.append('<canvas id="'+pieID+'"></canvas>');
			var G_vmlCanvasManager;
			var canvas = document.getElementById(pieID);
			canvas.setAttribute("width", opts.pieDiameter);
			canvas.setAttribute("height", opts.pieDiameter);
			var piePosition;
			switch(opts.piePosition){
				case 'leftTop' :
					piePosition = 'left:0; top:0;';
					break;
				case 'rightTop' :
					piePosition = 'right:0; top:0;';
					break;
				case 'leftBottom' :
					piePosition = 'left:0; bottom:0;';
					break;
				case 'rightBottom' :
					piePosition = 'right:0; bottom:0;';
					break;
			}
			canvas.setAttribute("style", "position:absolute; z-index:1002; "+piePosition);
			var rad;
			var radNew;
	
			if (canvas && canvas.getContext) {
				var ctx = canvas.getContext("2d");
				ctx.rotate(Math.PI*(3/2));
				ctx.translate(-opts.pieDiameter,0);
			}
		
		}
		if(loader=='none' || autoAdv==false) {
			$('#'+pieID).hide();
			$('.camera_canvas_wrap',camera_thumbs_wrap).hide();
		}
		
		if($(pagination).length) {
			$(pagination).append('<ul class="camera_pag_ul" />');
			var li;
			for (li = 0; li < amountSlide; li++){
				$('.camera_pag_ul',wrap).append('<li class="pag_nav_'+li+'" style="position:relative; z-index:1002"><span><span>'+li+'</span></span></li>');
			}
			$('.camera_pag_ul li',wrap).hover(function(){
				$(this).addClass('camera_hover');
				if($('.camera_thumb',this).length){
					var wTh = $('.camera_thumb',this).outerWidth(),
					hTh = $('.camera_thumb',this).outerHeight(),
					wTt = $(this).outerWidth();
					$('.camera_thumb',this).show().css({'top':'-'+hTh+'px','left':'-'+(wTh-wTt)/2+'px'}).animate({'opacity':1,'margin-top':'-3px'},200);
					$('.thumb_arrow',this).show().animate({'opacity':1,'margin-top':'-3px'},200);
				}
			},function(){
				$(this).removeClass('camera_hover');
				$('.camera_thumb',this).animate({'margin-top':'-20px','opacity':0},200,function(){
					$(this).css({marginTop:'5px'}).hide();
				});
				$('.thumb_arrow',this).animate({'margin-top':'-20px','opacity':0},200,function(){
					$(this).css({marginTop:'5px'}).hide();
				});
			});
		}
			
	
	
		if($(thumbs).length) {
			var thumbUrl;
			if(!$(pagination).length) {
				$(thumbs).append('<div />');
				$(thumbs).before('<div class="camera_prevThumbs hideNav"><div></div></div>').before('<div class="camera_nextThumbs hideNav"><div></div></div>');
				$('> div',thumbs).append('<ul />');
				$.each(allThumbs, function(i, val) {
					if($('> div', elem).eq(i).attr('data-thumb')!='') {
						var thumbUrl = $('> div', elem).eq(i).attr('data-thumb'),
							newImg = new Image();
						newImg.src = thumbUrl;
						$('ul',thumbs).append('<li class="pix_thumb pix_thumb_'+i+'" />');
						$('li.pix_thumb_'+i,thumbs).append($(newImg).attr('class','camera_thumb'));
					}
				});
			} else {
				$.each(allThumbs, function(i, val) {
					if($('> div', elem).eq(i).attr('data-thumb')!='') {
						var thumbUrl = $('> div', elem).eq(i).attr('data-thumb'),
							newImg = new Image();
						newImg.src = thumbUrl;
						$('li.pag_nav_'+i,pagination).append($(newImg).attr('class','camera_thumb').css({'position':'absolute'}).animate({opacity:0},0));
						$('li.pag_nav_'+i+' > img',pagination).after('<div class="thumb_arrow" />');
						$('li.pag_nav_'+i+' > .thumb_arrow',pagination).animate({opacity:0},0);
					}
				});
				wrap.css({marginBottom:$(pagination).outerHeight()});
			}
		} else if(!$(thumbs).length && $(pagination).length) {
			wrap.css({marginBottom:$(pagination).outerHeight()});
		}

	
		var firstPos = true;

		function thumbnailPos() {
			if($(thumbs).length && !$(pagination).length) {
				var wTh = $(thumbs).outerWidth(),
					owTh = $('ul > li',thumbs).outerWidth(),
					pos = $('li.cameracurrent', thumbs).length ? $('li.cameracurrent', thumbs).position() : '',
					ulW = ($('ul > li', thumbs).length * $('ul > li', thumbs).outerWidth()),
					offUl = $('ul', thumbs).offset().left,
					offDiv = $('> div', thumbs).offset().left,
					ulLeft;

					if(offUl<0){
						ulLeft = '-'+ (offDiv-offUl);
					} else {
						ulLeft = offDiv-offUl;
					}
					
					
					
				if(firstPos == true) {
					$('ul', thumbs).width($('ul > li', thumbs).length * $('ul > li', thumbs).outerWidth());
					if($(thumbs).length && !$(pagination).lenght) {
						wrap.css({marginBottom:$(thumbs).outerHeight()});
					}
					thumbnailVisible();
					/*I repeat this two lines because of a problem with iPhones*/
					$('ul', thumbs).width($('ul > li', thumbs).length * $('ul > li', thumbs).outerWidth());
					if($(thumbs).length && !$(pagination).lenght) {
						wrap.css({marginBottom:$(thumbs).outerHeight()});
					}
					/*...*/
				}
				firstPos = false;
				
					var left = $('li.cameracurrent', thumbs).length ? pos.left : '',
						right = $('li.cameracurrent', thumbs).length ? pos.left+($('li.cameracurrent', thumbs).outerWidth()) : '';
					if(left<$('li.cameracurrent', thumbs).outerWidth()) {
						left = 0;
					}
					if(right-ulLeft>wTh){
						if((left+wTh)<ulW){
							$('ul', thumbs).animate({'margin-left':'-'+(left)+'px'},500,thumbnailVisible);
						} else {
							$('ul', thumbs).animate({'margin-left':'-'+($('ul', thumbs).outerWidth()-wTh)+'px'},500,thumbnailVisible);
						}
					} else if(left-ulLeft<0) {
						$('ul', thumbs).animate({'margin-left':'-'+(left)+'px'},500,thumbnailVisible);
					} else {
						$('ul', thumbs).css({'margin-left':'auto', 'margin-right':'auto'});
						setTimeout(thumbnailVisible,100);
					}
					
			}
		}

		if($(commands).length) {
			$(commands).append('<div class="camera_play"></div>').append('<div class="camera_stop"></div>');
			if(autoAdv==true){
				$('.camera_play',camera_thumbs_wrap).hide();
				$('.camera_stop',camera_thumbs_wrap).show();
			} else {
				$('.camera_stop',camera_thumbs_wrap).hide();
				$('.camera_play',camera_thumbs_wrap).show();
			}
			
		}
			
			
		function canvasLoader() {
			rad = 0;
			var barWidth = $('.camera_bar_cont',camera_thumbs_wrap).width(),
				barHeight = $('.camera_bar_cont',camera_thumbs_wrap).height();

			if (loader != 'pie') {
				switch(barDirection){
					case 'leftToRight':
						$('#'+pieID).css({'right':barWidth});
						break;
					case 'rightToLeft':
						$('#'+pieID).css({'left':barWidth});
						break;
					case 'topToBottom':
						$('#'+pieID).css({'bottom':barHeight});
						break;
					case 'bottomToTop':
						$('#'+pieID).css({'top':barHeight});
						break;
				}
			} else {
				ctx.clearRect(0,0,opts.pieDiameter,opts.pieDiameter); 
			}
		}
		
		
		canvasLoader();
		
		
		$('.moveFromLeft, .moveFromRight, .moveFromTop, .moveFromBottom, .fadeIn, .fadeFromLeft, .fadeFromRight, .fadeFromTop, .fadeFromBottom',fakeHover).each(function(){
			$(this).css('visibility','hidden');
		});
		
		opts.onStartLoading.call(this);
		
		nextSlide();
		
	
	/*************************** FUNCTION nextSlide() ***************************/
	
	function nextSlide(navSlide){ 
		elem.addClass('camerasliding');
		
		videoPresent = false;
		var vis = parseFloat($('div.cameraSlide.cameracurrent',target).index());

		if(navSlide>0){ 
			var slideI = navSlide-1;
		} else if (vis == amountSlide-1) { 
			var slideI = 0;
		} else {
			var slideI = vis+1;
		}
		
				
		var slide = $('.cameraSlide:eq('+slideI+')',target);
		var slideNext = $('.cameraSlide:eq('+(slideI+1)+')',target).addClass('cameranext');
		if( vis != slideI+1 ) {
			slideNext.hide();
		}
		$('.cameraContent',fakeHover).fadeOut(600);
		$('.camera_caption',fakeHover).show();
		
		$('.camerarelative',slide).append($('> div ',elem).eq(slideI).find('> div.camera_effected'));

		$('.camera_target_content .cameraContent:eq('+slideI+')',wrap).append($('> div ',elem).eq(slideI).find('> div'));
		
		if(!$('.imgLoaded',slide).length){
			var imgUrl = allImg[slideI];
			var imgLoaded = new Image();
			imgLoaded.src = imgUrl +"?"+ new Date().getTime();
                        $(imgLoaded).prop('alt',allAlts[slideI]);
			slide.css('visibility','hidden');
			slide.prepend($(imgLoaded).attr('class','imgLoaded').css('visibility','hidden'));
			var wT, hT;
			if (!$(imgLoaded).get(0).complete || wT == '0' || hT == '0' || typeof wT === 'undefined' || wT === false || typeof hT === 'undefined' || hT === false) {
				$('.camera_loader',wrap).delay(500).fadeIn(400);
				imgLoaded.onload = function() {
					wT = imgLoaded.naturalWidth;
					hT = imgLoaded.naturalHeight;
					$(imgLoaded).attr('data-alignment',allAlign[slideI]).attr('data-portrait',allPor[slideI]);
					$(imgLoaded).attr('width',wT);
					$(imgLoaded).attr('height',hT);
					target.find('.cameraSlide_'+slideI).hide().css('visibility','visible');
					resizeImage();
					nextSlide(slideI+1);
				};
			}
		} else {
			if( allImg.length > (slideI+1) && !$('.imgLoaded',slideNext).length ){
				var imgUrl2 = allImg[(slideI+1)];
				var imgLoaded2 = new Image();
				imgLoaded2.src = imgUrl2 +"?"+ new Date().getTime();
                                $(imgLoaded2).prop('alt',allAlts[slideI]);
				slideNext.prepend($(imgLoaded2).attr('class','imgLoaded').css('visibility','hidden'));
				imgLoaded2.onload = function() {
					wT = imgLoaded2.naturalWidth;
					hT = imgLoaded2.naturalHeight;
					$(imgLoaded2).attr('data-alignment',allAlign[slideI+1]).attr('data-portrait',allPor[slideI+1]);
					$(imgLoaded2).attr('width',wT);
					$(imgLoaded2).attr('height',hT);
					resizeImage();
				};
			}
			opts.onLoaded.call(this);
			if($('.camera_loader',wrap).is(':visible')){
				$('.camera_loader',wrap).fadeOut(400);
			} else {
				$('.camera_loader',wrap).css({'visibility':'hidden'});
				$('.camera_loader',wrap).fadeOut(400,function(){
					$('.camera_loader',wrap).css({'visibility':'visible'});
				});
			}
			var rows = opts.rows,
				cols = opts.cols,
				couples = 1,
				difference = 0,
				dataSlideOn,
				time,
				transPeriod,
				fx,
				easing,
				randomFx = new Array('simpleFade','curtainTopLeft','curtainTopRight','curtainBottomLeft','curtainBottomRight','curtainSliceLeft','curtainSliceRight','blindCurtainTopLeft','blindCurtainTopRight','blindCurtainBottomLeft','blindCurtainBottomRight','blindCurtainSliceBottom','blindCurtainSliceTop','stampede','mosaic','mosaicReverse','mosaicRandom','mosaicSpiral','mosaicSpiralReverse','topLeftBottomRight','bottomRightTopLeft','bottomLeftTopRight','topRightBottomLeft','scrollLeft','scrollRight','scrollTop','scrollBottom','scrollHorz');
				marginLeft = 0,
				marginTop = 0,
				opacityOnGrid = 0;
				
				if(opts.opacityOnGrid==true){
					opacityOnGrid = 0;
				} else {
					opacityOnGrid = 1;
				}
 
			
			
			var dataFx = $(' > div',elem).eq(slideI).attr('data-fx');
				
			if(isMobile()&&opts.mobileFx!=''&&opts.mobileFx!='default'){
				fx = opts.mobileFx;
			} else {
				if(typeof dataFx !== 'undefined' && dataFx!== false && dataFx!== 'default'){
					fx = dataFx;
				} else {
					fx = opts.fx;
				}
			}
			
			if(fx=='random') {
				fx = shuffle(randomFx);
				fx = fx[0];
			} else {
				fx = fx;
				if(fx.indexOf(',')>0){
					fx = fx.replace(/ /g,'');
					fx = fx.split(',');
					fx = shuffle(fx);
					fx = fx[0];
				}
			}
			
			dataEasing = $(' > div',elem).eq(slideI).attr('data-easing');
			mobileEasing = $(' > div',elem).eq(slideI).attr('data-mobileEasing');

			if(isMobile()&&opts.mobileEasing!=''&&opts.mobileEasing!='default'){
				if(typeof mobileEasing !== 'undefined' && mobileEasing!== false && mobileEasing!== 'default') {
					easing = mobileEasing;
				} else {
					easing = opts.mobileEasing;
				}
			} else {
				if(typeof dataEasing !== 'undefined' && dataEasing!== false && dataEasing!== 'default') {
					easing = dataEasing;
				} else {
					easing = opts.easing;
				}
			}
	
			dataSlideOn = $(' > div',elem).eq(slideI).attr('data-slideOn');
			if(typeof dataSlideOn !== 'undefined' && dataSlideOn!== false){
				slideOn = dataSlideOn;
			} else {
				if(opts.slideOn=='random'){
					var slideOn = new Array('next','prev');
					slideOn = shuffle(slideOn);
					slideOn = slideOn[0];
				} else {
					slideOn = opts.slideOn;
				}
			}
				
			var dataTime = $(' > div',elem).eq(slideI).attr('data-time');
			if(typeof dataTime !== 'undefined' && dataTime!== false && dataTime!== ''){
				time = parseFloat(dataTime);
			} else {
				time = opts.time;
			}
				
			var dataTransPeriod = $(' > div',elem).eq(slideI).attr('data-transPeriod');
			if(typeof dataTransPeriod !== 'undefined' && dataTransPeriod!== false && dataTransPeriod!== ''){
				transPeriod = parseFloat(dataTransPeriod);
			} else {
				transPeriod = opts.transPeriod;
			}
				
			if(!$(elem).hasClass('camerastarted')){
				fx = 'simpleFade';
				slideOn = 'next';
				easing = '';
				transPeriod = 400;
				$(elem).addClass('camerastarted')
			}
	
			switch(fx){
				case 'simpleFade':
					cols = 1;
					rows = 1;
						break;
				case 'curtainTopLeft':
					if(opts.slicedCols == 0) {
						cols = opts.cols;
					} else {
						cols = opts.slicedCols;
					}
					rows = 1;
						break;
				case 'curtainTopRight':
					if(opts.slicedCols == 0) {
						cols = opts.cols;
					} else {
						cols = opts.slicedCols;
					}
					rows = 1;
						break;
				case 'curtainBottomLeft':
					if(opts.slicedCols == 0) {
						cols = opts.cols;
					} else {
						cols = opts.slicedCols;
					}
					rows = 1;
						break;
				case 'curtainBottomRight':
					if(opts.slicedCols == 0) {
						cols = opts.cols;
					} else {
						cols = opts.slicedCols;
					}
					rows = 1;
						break;
				case 'curtainSliceLeft':
					if(opts.slicedCols == 0) {
						cols = opts.cols;
					} else {
						cols = opts.slicedCols;
					}
					rows = 1;
						break;
				case 'curtainSliceRight':
					if(opts.slicedCols == 0) {
						cols = opts.cols;
					} else {
						cols = opts.slicedCols;
					}
					rows = 1;
						break;
				case 'blindCurtainTopLeft':
					if(opts.slicedRows == 0) {
						rows = opts.rows;
					} else {
						rows = opts.slicedRows;
					}
					cols = 1;
						break;
				case 'blindCurtainTopRight':
					if(opts.slicedRows == 0) {
						rows = opts.rows;
					} else {
						rows = opts.slicedRows;
					}
					cols = 1;
						break;
				case 'blindCurtainBottomLeft':
					if(opts.slicedRows == 0) {
						rows = opts.rows;
					} else {
						rows = opts.slicedRows;
					}
					cols = 1;
						break;
				case 'blindCurtainBottomRight':
					if(opts.slicedRows == 0) {
						rows = opts.rows;
					} else {
						rows = opts.slicedRows;
					}
					cols = 1;
						break;
				case 'blindCurtainSliceTop':
					if(opts.slicedRows == 0) {
						rows = opts.rows;
					} else {
						rows = opts.slicedRows;
					}
					cols = 1;
						break;
				case 'blindCurtainSliceBottom':
					if(opts.slicedRows == 0) {
						rows = opts.rows;
					} else {
						rows = opts.slicedRows;
					}
					cols = 1;
						break;
				case 'stampede':
					difference = '-'+transPeriod;
						break;
				case 'mosaic':
					difference = opts.gridDifference;
						break;
				case 'mosaicReverse':
					difference = opts.gridDifference;
						break;
				case 'mosaicRandom':
						break;
				case 'mosaicSpiral':
					difference = opts.gridDifference;
					couples = 1.7;
						break;
				case 'mosaicSpiralReverse':
					difference = opts.gridDifference;
					couples = 1.7;
						break;
				case 'topLeftBottomRight':
					difference = opts.gridDifference;
					couples = 6;
						break;
				case 'bottomRightTopLeft':
					difference = opts.gridDifference;
					couples = 6;
						break;
				case 'bottomLeftTopRight':
					difference = opts.gridDifference;
					couples = 6;
						break;
				case 'topRightBottomLeft':
					difference = opts.gridDifference;
					couples = 6;
						break;
				case 'scrollLeft':
					cols = 1;
					rows = 1;
						break;
				case 'scrollRight':
					cols = 1;
					rows = 1;
						break;
				case 'scrollTop':
					cols = 1;
					rows = 1;
						break;
				case 'scrollBottom':
					cols = 1;
					rows = 1;
						break;
				case 'scrollHorz':
					cols = 1;
					rows = 1;
						break;
			}
			
			var cycle = 0;
			var blocks = rows*cols;
			var leftScrap = w-(Math.floor(w/cols)*cols);
			var topScrap = h-(Math.floor(h/rows)*rows);
			var addLeft;
			var addTop;
			var tAppW = 0;	
			var tAppH = 0;
			var arr = new Array();
			var delay = new Array();
			var order = new Array();
			while(cycle < blocks){
				arr.push(cycle);
				delay.push(cycle);
				cameraCont.append('<div class="cameraappended" style="display:none; overflow:hidden; position:absolute; z-index:1000" />');
				var tApp = $('.cameraappended:eq('+cycle+')',target);
				if(fx=='scrollLeft' || fx=='scrollRight' || fx=='scrollTop' || fx=='scrollBottom' || fx=='scrollHorz'){
					selector.eq(slideI).clone().show().appendTo(tApp);
				} else {
					if(slideOn=='next'){
						selector.eq(slideI).clone().show().appendTo(tApp);
					} else {
						selector.eq(vis).clone().show().appendTo(tApp);
					}
				}

				if(cycle%cols<leftScrap){
					addLeft = 1;
				} else {
					addLeft = 0;
				}
				if(cycle%cols==0){
					tAppW = 0;
				}
				if(Math.floor(cycle/cols)<topScrap){
					addTop = 1;
				} else {
					addTop = 0;
				}
				tApp.css({
					'height': Math.floor((h/rows)+addTop+1),
					'left': tAppW,
					'top': tAppH,
					'width': Math.floor((w/cols)+addLeft+1)
				});
				$('> .cameraSlide', tApp).css({
					'height': h,
					'margin-left': '-'+tAppW+'px',
					'margin-top': '-'+tAppH+'px',
					'width': w
				});
				tAppW = tAppW+tApp.width()-1;
				if(cycle%cols==cols-1){
					tAppH = tAppH + tApp.height() - 1;
				}
				cycle++;
			}
			

			
			switch(fx){
				case 'curtainTopLeft':
						break;
				case 'curtainBottomLeft':
						break;
				case 'curtainSliceLeft':
						break;
				case 'curtainTopRight':
					arr = arr.reverse();
						break;
				case 'curtainBottomRight':
					arr = arr.reverse();
						break;
				case 'curtainSliceRight':
					arr = arr.reverse();
						break;
				case 'blindCurtainTopLeft':
						break;
				case 'blindCurtainBottomLeft':
					arr = arr.reverse();
						break;
				case 'blindCurtainSliceTop':
						break;
				case 'blindCurtainTopRight':
						break;
				case 'blindCurtainBottomRight':
					arr = arr.reverse();
						break;
				case 'blindCurtainSliceBottom':
					arr = arr.reverse();
						break;
				case 'stampede':
					arr = shuffle(arr);
						break;
				case 'mosaic':
						break;
				case 'mosaicReverse':
					arr = arr.reverse();
						break;
				case 'mosaicRandom':
					arr = shuffle(arr);
						break;
				case 'mosaicSpiral':
					var rows2 = rows/2, x, y, z, n=0;
						for (z = 0; z < rows2; z++){
							y = z;
							for (x = z; x < cols - z - 1; x++) {
								order[n++] = y * cols + x;
							}
							x = cols - z - 1;
							for (y = z; y < rows - z - 1; y++) {
								order[n++] = y * cols + x;
							}
							y = rows - z - 1;
							for (x = cols - z - 1; x > z; x--) {
								order[n++] = y * cols + x;
							}
							x = z;
							for (y = rows - z - 1; y > z; y--) {
								order[n++] = y * cols + x;
							}
						}
						
						arr = order;

						break;
				case 'mosaicSpiralReverse':
					var rows2 = rows/2, x, y, z, n=blocks-1;
						for (z = 0; z < rows2; z++){
							y = z;
							for (x = z; x < cols - z - 1; x++) {
								order[n--] = y * cols + x;
							}
							x = cols - z - 1;
							for (y = z; y < rows - z - 1; y++) {
								order[n--] = y * cols + x;
							}
							y = rows - z - 1;
							for (x = cols - z - 1; x > z; x--) {
								order[n--] = y * cols + x;
							}
							x = z;
							for (y = rows - z - 1; y > z; y--) {
								order[n--] = y * cols + x;
							}
						}

						arr = order;
						
						break;
				case 'topLeftBottomRight':
					for (var y = 0; y < rows; y++)
					for (var x = 0; x < cols; x++) {
						order.push(x + y);
					}
						delay = order;
						break;
				case 'bottomRightTopLeft':
					for (var y = 0; y < rows; y++)
					for (var x = 0; x < cols; x++) {
						order.push(x + y);
					}
						delay = order.reverse();
						break;
				case 'bottomLeftTopRight':
					for (var y = rows; y > 0; y--)
					for (var x = 0; x < cols; x++) {
						order.push(x + y);
					}
						delay = order;
						break;
				case 'topRightBottomLeft':
					for (var y = 0; y < rows; y++)
					for (var x = cols; x > 0; x--) {
						order.push(x + y);
					}
						delay = order;
						break;
			}
			
			
						
			$.each(arr, function(index, value) {

				if(value%cols<leftScrap){
					addLeft = 1;
				} else {
					addLeft = 0;
				}
				if(value%cols==0){
					tAppW = 0;
				}
				if(Math.floor(value/cols)<topScrap){
					addTop = 1;
				} else {
					addTop = 0;
				}
							
				switch(fx){
					case 'simpleFade':
						height = h;
						width = w;
						opacityOnGrid = 0;
							break;
					case 'curtainTopLeft':
						height = 0,
						width = Math.floor((w/cols)+addLeft+1),
						marginTop = '-'+Math.floor((h/rows)+addTop+1)+'px';
							break;
					case 'curtainTopRight':
						height = 0,
						width = Math.floor((w/cols)+addLeft+1),
						marginTop = '-'+Math.floor((h/rows)+addTop+1)+'px';
							break;
					case 'curtainBottomLeft':
						height = 0,
						width = Math.floor((w/cols)+addLeft+1),
						marginTop = Math.floor((h/rows)+addTop+1)+'px';
							break;
					case 'curtainBottomRight':
						height = 0,
						width = Math.floor((w/cols)+addLeft+1),
						marginTop = Math.floor((h/rows)+addTop+1)+'px';
							break;
					case 'curtainSliceLeft':
						height = 0,
						width = Math.floor((w/cols)+addLeft+1);
						if(value%2==0){
							marginTop = Math.floor((h/rows)+addTop+1)+'px';					
						} else {
							marginTop = '-'+Math.floor((h/rows)+addTop+1)+'px';					
						}
							break;
					case 'curtainSliceRight':
						height = 0,
						width = Math.floor((w/cols)+addLeft+1);
						if(value%2==0){
							marginTop = Math.floor((h/rows)+addTop+1)+'px';					
						} else {
							marginTop = '-'+Math.floor((h/rows)+addTop+1)+'px';					
						}
							break;
					case 'blindCurtainTopLeft':
						height = Math.floor((h/rows)+addTop+1),
						width = 0,
						marginLeft = '-'+Math.floor((w/cols)+addLeft+1)+'px';
							break;
					case 'blindCurtainTopRight':
						height = Math.floor((h/rows)+addTop+1),
						width = 0,
						marginLeft = Math.floor((w/cols)+addLeft+1)+'px';
							break;
					case 'blindCurtainBottomLeft':
						height = Math.floor((h/rows)+addTop+1),
						width = 0,
						marginLeft = '-'+Math.floor((w/cols)+addLeft+1)+'px';
							break;
					case 'blindCurtainBottomRight':
						height = Math.floor((h/rows)+addTop+1),
						width = 0,
						marginLeft = Math.floor((w/cols)+addLeft+1)+'px';
							break;
					case 'blindCurtainSliceBottom':
						height = Math.floor((h/rows)+addTop+1),
						width = 0;
						if(value%2==0){
							marginLeft = '-'+Math.floor((w/cols)+addLeft+1)+'px';
						} else {
							marginLeft = Math.floor((w/cols)+addLeft+1)+'px';
						}
							break;
					case 'blindCurtainSliceTop':
						height = Math.floor((h/rows)+addTop+1),
						width = 0;
						if(value%2==0){
							marginLeft = '-'+Math.floor((w/cols)+addLeft+1)+'px';
						} else {
							marginLeft = Math.floor((w/cols)+addLeft+1)+'px';
						}
							break;
					case 'stampede':
						height = 0;
						width = 0;					
						marginLeft = (w*0.2)*(((index)%cols)-(cols-(Math.floor(cols/2))))+'px';					
						marginTop = (h*0.2)*((Math.floor(index/cols)+1)-(rows-(Math.floor(rows/2))))+'px';	
							break;
					case 'mosaic':
						height = 0;
						width = 0;					
							break;
					case 'mosaicReverse':
						height = 0;
						width = 0;					
						marginLeft = Math.floor((w/cols)+addLeft+1)+'px';					
						marginTop = Math.floor((h/rows)+addTop+1)+'px';					
							break;
					case 'mosaicRandom':
						height = 0;
						width = 0;					
						marginLeft = Math.floor((w/cols)+addLeft+1)*0.5+'px';					
						marginTop = Math.floor((h/rows)+addTop+1)*0.5+'px';					
							break;
					case 'mosaicSpiral':
						height = 0;
						width = 0;
						marginLeft = Math.floor((w/cols)+addLeft+1)*0.5+'px';					
						marginTop = Math.floor((h/rows)+addTop+1)*0.5+'px';					
							break;
					case 'mosaicSpiralReverse':
						height = 0;
						width = 0;
						marginLeft = Math.floor((w/cols)+addLeft+1)*0.5+'px';					
						marginTop = Math.floor((h/rows)+addTop+1)*0.5+'px';					
							break;
					case 'topLeftBottomRight':
						height = 0;
						width = 0;					
							break;
					case 'bottomRightTopLeft':
						height = 0;
						width = 0;					
						marginLeft = Math.floor((w/cols)+addLeft+1)+'px';					
						marginTop = Math.floor((h/rows)+addTop+1)+'px';					
							break;
					case 'bottomLeftTopRight':
						height = 0;
						width = 0;					
						marginLeft = 0;					
						marginTop = Math.floor((h/rows)+addTop+1)+'px';					
							break;
					case 'topRightBottomLeft':
						height = 0;
						width = 0;					
						marginLeft = Math.floor((w/cols)+addLeft+1)+'px';					
						marginTop = 0;					
							break;
					case 'scrollRight':
						height = h;
						width = w;
						marginLeft = -w;					
							break;
					case 'scrollLeft':
						height = h;
						width = w;
						marginLeft = w;					
							break;
					case 'scrollTop':
						height = h;
						width = w;
						marginTop = h;					
							break;
					case 'scrollBottom':
						height = h;
						width = w;
						marginTop = -h;					
							break;
					case 'scrollHorz':
						height = h;
						width = w;
						if(vis==0 && slideI==amountSlide-1) {
							marginLeft = -w;	
						} else if(vis<slideI  || (vis==amountSlide-1 && slideI==0)) {
							marginLeft = w;	
						} else {
							marginLeft = -w;	
						}
							break;
					}
					
			
				var tApp = $('.cameraappended:eq('+value+')',target);
								
				if(typeof u !== 'undefined'){
					clearInterval(u);
					clearTimeout(setT);
					setT = setTimeout(canvasLoader,transPeriod+difference);
				}
				
				
				if($(pagination).length){
					$('.camera_pag li',wrap).removeClass('cameracurrent');
					$('.camera_pag li',wrap).eq(slideI).addClass('cameracurrent');
				}
						
				if($(thumbs).length){
					$('li', thumbs).removeClass('cameracurrent');
					$('li', thumbs).eq(slideI).addClass('cameracurrent');
					$('li', thumbs).not('.cameracurrent').find('img').animate({opacity:.5},0);
					$('li.cameracurrent img', thumbs).animate({opacity:1},0);
					$('li', thumbs).hover(function(){
						$('img',this).stop(true,false).animate({opacity:1},150);
					},function(){
						if(!$(this).hasClass('cameracurrent')){
							$('img',this).stop(true,false).animate({opacity:.5},150);
						}
					});
				}
								
						
				var easedTime = parseFloat(transPeriod)+parseFloat(difference);
				
				function cameraeased() {

					$(this).addClass('cameraeased');
					if($('.cameraeased',target).length>=0){
						$(thumbs).css({visibility:'visible'});
					}
					if($('.cameraeased',target).length==blocks){
						
						thumbnailPos();
						
						$('.moveFromLeft, .moveFromRight, .moveFromTop, .moveFromBottom, .fadeIn, .fadeFromLeft, .fadeFromRight, .fadeFromTop, .fadeFromBottom',fakeHover).each(function(){
							$(this).css('visibility','hidden');
						});
		
						selector.eq(slideI).show().css('z-index','999').removeClass('cameranext').addClass('cameracurrent');
						selector.eq(vis).css('z-index','1').removeClass('cameracurrent');
						$('.cameraContent',fakeHover).eq(slideI).addClass('cameracurrent');
						if (vis >= 0) {
							$('.cameraContent',fakeHover).eq(vis).removeClass('cameracurrent');
						}
						
						opts.onEndTransition.call(this);
						
						if($('> div', elem).eq(slideI).attr('data-video')!='hide' && $('.cameraContent.cameracurrent .imgFake',fakeHover).length ){
							$('.cameraContent.cameracurrent .imgFake',fakeHover).click();
						}

						
						var lMoveIn = selector.eq(slideI).find('.fadeIn').length;
						var lMoveInContent = $('.cameraContent',fakeHover).eq(slideI).find('.moveFromLeft, .moveFromRight, .moveFromTop, .moveFromBottom, .fadeIn, .fadeFromLeft, .fadeFromRight, .fadeFromTop, .fadeFromBottom').length;
						
						if (lMoveIn!=0){
							$('.cameraSlide.cameracurrent .fadeIn',fakeHover).each(function(){
								if($(this).attr('data-easing')!=''){
									var easeMove = $(this).attr('data-easing');
								} else {
									var easeMove = easing;
								}
								var t = $(this);
								if(typeof t.attr('data-outerWidth') === 'undefined' || t.attr('data-outerWidth') === false || t.attr('data-outerWidth') === '') {
									var wMoveIn = t.outerWidth();
									t.attr('data-outerWidth',wMoveIn);
								} else {
									var wMoveIn = t.attr('data-outerWidth');
								}
								if(typeof t.attr('data-outerHeight') === 'undefined' || t.attr('data-outerHeight') === false || t.attr('data-outerHeight') === '') {
									var hMoveIn = t.outerHeight();
									t.attr('data-outerHeight',hMoveIn);
								} else {
									var hMoveIn = t.attr('data-outerHeight');
								}
								//t.css('width',wMoveIn);
								var pos = t.position();
								var left = pos.left;
								var top = pos.top;
								var tClass = t.attr('class');
								var ind = t.index();
								var hRel = t.parents('.camerarelative').outerHeight();
								var wRel = t.parents('.camerarelative').outerWidth();
								if(tClass.indexOf("fadeIn") != -1) {
									t.animate({opacity:0},0).css('visibility','visible').delay((time/lMoveIn)*(0.1*(ind-1))).animate({opacity:1},(time/lMoveIn)*0.15,easeMove);
								} else {
									t.css('visibility','visible');
								}
							});
						}

						$('.cameraContent.cameracurrent',fakeHover).show();
						if (lMoveInContent!=0){
							
							$('.cameraContent.cameracurrent .moveFromLeft, .cameraContent.cameracurrent .moveFromRight, .cameraContent.cameracurrent .moveFromTop, .cameraContent.cameracurrent .moveFromBottom, .cameraContent.cameracurrent .fadeIn, .cameraContent.cameracurrent .fadeFromLeft, .cameraContent.cameracurrent .fadeFromRight, .cameraContent.cameracurrent .fadeFromTop, .cameraContent.cameracurrent .fadeFromBottom',fakeHover).each(function(){
								if($(this).attr('data-easing')!=''){
									var easeMove = $(this).attr('data-easing');
								} else {
									var easeMove = easing;
								}
								var t = $(this);
								var pos = t.position();
								var left = pos.left;
								var top = pos.top;
								var tClass = t.attr('class');
								var ind = t.index();
								var thisH = t.outerHeight();
								if(tClass.indexOf("moveFromLeft") != -1) {
									t.css({'left':'-'+(w)+'px','right':'auto'});
									t.css('visibility','visible').delay((time/lMoveInContent)*(0.1*(ind-1))).animate({'left':pos.left},(time/lMoveInContent)*0.15,easeMove);
								} else if(tClass.indexOf("moveFromRight") != -1) {
									t.css({'left':w+'px','right':'auto'});
									t.css('visibility','visible').delay((time/lMoveInContent)*(0.1*(ind-1))).animate({'left':pos.left},(time/lMoveInContent)*0.15,easeMove);
								} else if(tClass.indexOf("moveFromTop") != -1) {
									t.css({'top':'-'+h+'px','bottom':'auto'});
									t.css('visibility','visible').delay((time/lMoveInContent)*(0.1*(ind-1))).animate({'top':pos.top},(time/lMoveInContent)*0.15,easeMove,function(){
										t.css({top:'auto',bottom:0});
									});
								} else if(tClass.indexOf("moveFromBottom") != -1) {
									t.css({'top':h+'px','bottom':'auto'});
									t.css('visibility','visible').delay((time/lMoveInContent)*(0.1*(ind-1))).animate({'top':pos.top},(time/lMoveInContent)*0.15,easeMove);
								} else if(tClass.indexOf("fadeFromLeft") != -1) {
									t.animate({opacity:0},0).css({'left':'-'+(w)+'px','right':'auto'});
									t.css('visibility','visible').delay((time/lMoveInContent)*(0.1*(ind-1))).animate({'left':pos.left,opacity:1},(time/lMoveInContent)*0.15,easeMove);
								} else if(tClass.indexOf("fadeFromRight") != -1) {
									t.animate({opacity:0},0).css({'left':(w)+'px','right':'auto'});
									t.css('visibility','visible').delay((time/lMoveInContent)*(0.1*(ind-1))).animate({'left':pos.left,opacity:1},(time/lMoveInContent)*0.15,easeMove);
								} else if(tClass.indexOf("fadeFromTop") != -1) {
									t.animate({opacity:0},0).css({'top':'-'+(h)+'px','bottom':'auto'});
									t.css('visibility','visible').delay((time/lMoveInContent)*(0.1*(ind-1))).animate({'top':pos.top,opacity:1},(time/lMoveInContent)*0.15,easeMove,function(){
										t.css({top:'auto',bottom:0});
									});
								} else if(tClass.indexOf("fadeFromBottom") != -1) {
									t.animate({opacity:0},0).css({'bottom':'-'+thisH+'px'});
									t.css('visibility','visible').delay((time/lMoveInContent)*(0.1*(ind-1))).animate({'bottom':'0',opacity:1},(time/lMoveInContent)*0.15,easeMove);
								} else if(tClass.indexOf("fadeIn") != -1) {
									t.animate({opacity:0},0).css('visibility','visible').delay((time/lMoveInContent)*(0.1*(ind-1))).animate({opacity:1},(time/lMoveInContent)*0.15,easeMove);
								} else {
									t.css('visibility','visible');
								}
							});
						}

						
						$('.cameraappended',target).remove();
						elem.removeClass('camerasliding');	
							selector.eq(vis).hide();
							var barWidth = $('.camera_bar_cont',camera_thumbs_wrap).width(),
								barHeight = $('.camera_bar_cont',camera_thumbs_wrap).height(),
								radSum;
							if (loader != 'pie') {
								radSum = 0.05;
							} else {
								radSum = 0.005;
							}
							$('#'+pieID).animate({opacity:opts.loaderOpacity},200);
							u = setInterval(
								function(){
									if(elem.hasClass('stopped')){
										clearInterval(u);
									}
									if (loader != 'pie') {
										if(rad<=1.002 && !elem.hasClass('stopped') && !elem.hasClass('paused') && !elem.hasClass('hovered')){
											rad = (rad+radSum);
										} else if (rad<=1 && (elem.hasClass('stopped') || elem.hasClass('paused') || elem.hasClass('stopped') || elem.hasClass('hovered'))){
											rad = rad;
										} else {
											if(!elem.hasClass('stopped') && !elem.hasClass('paused') && !elem.hasClass('hovered')) {
												clearInterval(u);
												imgFake();
												$('#'+pieID).animate({opacity:0},200,function(){
													clearTimeout(setT);
													setT = setTimeout(canvasLoader,easedTime);
													nextSlide();
													opts.onStartLoading.call(this);
												});
											}
										}
										switch(barDirection){
											case 'leftToRight':
												$('#'+pieID).animate({'right':barWidth-(barWidth*rad)},(time*radSum),'linear');
												break;
											case 'rightToLeft':
												$('#'+pieID).animate({'left':barWidth-(barWidth*rad)},(time*radSum),'linear');
												break;
											case 'topToBottom':
												$('#'+pieID).animate({'bottom':barHeight-(barHeight*rad)},(time*radSum),'linear');
												break;
											case 'bottomToTop':
												$('#'+pieID).animate({'bottom':barHeight-(barHeight*rad)},(time*radSum),'linear');
												break;
										}
										
									} else {
										radNew = rad;
										ctx.clearRect(0,0,opts.pieDiameter,opts.pieDiameter);
										ctx.globalCompositeOperation = 'destination-over';
										ctx.beginPath();
										ctx.arc((opts.pieDiameter)/2, (opts.pieDiameter)/2, (opts.pieDiameter)/2-opts.loaderStroke,0,Math.PI*2,false);
										ctx.lineWidth = opts.loaderStroke;
										ctx.strokeStyle = opts.loaderBgColor;
										ctx.stroke();
										ctx.closePath();
										ctx.globalCompositeOperation = 'source-over';
										ctx.beginPath();
										ctx.arc((opts.pieDiameter)/2, (opts.pieDiameter)/2, (opts.pieDiameter)/2-opts.loaderStroke,0,Math.PI*2*radNew,false);
										ctx.lineWidth = opts.loaderStroke-(opts.loaderPadding*2);
										ctx.strokeStyle = opts.loaderColor;
										ctx.stroke();
										ctx.closePath();
												
										if(rad<=1.002 && !elem.hasClass('stopped') && !elem.hasClass('paused') && !elem.hasClass('hovered')){
											rad = (rad+radSum);
										} else if (rad<=1 && (elem.hasClass('stopped') || elem.hasClass('paused') || elem.hasClass('hovered'))){
											rad = rad;
										} else {
											if(!elem.hasClass('stopped') && !elem.hasClass('paused') && !elem.hasClass('hovered')) {
												clearInterval(u);
												imgFake();
												$('#'+pieID+', .camera_canvas_wrap',camera_thumbs_wrap).animate({opacity:0},200,function(){
													clearTimeout(setT);
													setT = setTimeout(canvasLoader,easedTime);
													nextSlide();
													opts.onStartLoading.call(this);
												});
											}
										}
									}
								},time*radSum
							);
						}

				}


				
				if(fx=='scrollLeft' || fx=='scrollRight' || fx=='scrollTop' || fx=='scrollBottom' || fx=='scrollHorz'){
					opts.onStartTransition.call(this);
					easedTime = 0;
					tApp.delay((((transPeriod+difference)/blocks)*delay[index]*couples)*0.5).css({
							'display' : 'block',
							'height': height,
							'margin-left': marginLeft,
							'margin-top': marginTop,
							'width': width
						}).animate({
							'height': Math.floor((h/rows)+addTop+1),
							'margin-top' : 0,
							'margin-left' : 0,
							'width' : Math.floor((w/cols)+addLeft+1)
						},(transPeriod-difference),easing,cameraeased);
					selector.eq(vis).delay((((transPeriod+difference)/blocks)*delay[index]*couples)*0.5).animate({
							'margin-left': marginLeft*(-1),
							'margin-top': marginTop*(-1)
						},(transPeriod-difference),easing,function(){
							$(this).css({'margin-top' : 0,'margin-left' : 0});
						});
				} else {
					opts.onStartTransition.call(this);
					easedTime = parseFloat(transPeriod)+parseFloat(difference);
					if(slideOn=='next'){
						tApp.delay((((transPeriod+difference)/blocks)*delay[index]*couples)*0.5).css({
								'display' : 'block',
								'height': height,
								'margin-left': marginLeft,
								'margin-top': marginTop,
								'width': width,
								'opacity' : opacityOnGrid
							}).animate({
								'height': Math.floor((h/rows)+addTop+1),
								'margin-top' : 0,
								'margin-left' : 0,
								'opacity' : 1,
								'width' : Math.floor((w/cols)+addLeft+1)
							},(transPeriod-difference),easing,cameraeased);
					} else {
						selector.eq(slideI).show().css('z-index','999').addClass('cameracurrent');
						selector.eq(vis).css('z-index','1').removeClass('cameracurrent');
						$('.cameraContent',fakeHover).eq(slideI).addClass('cameracurrent');
						$('.cameraContent',fakeHover).eq(vis).removeClass('cameracurrent');
						tApp.delay((((transPeriod+difference)/blocks)*delay[index]*couples)*0.5).css({
								'display' : 'block',
								'height': Math.floor((h/rows)+addTop+1),
								'margin-top' : 0,
								'margin-left' : 0,
								'opacity' : 1,
								'width' : Math.floor((w/cols)+addLeft+1)
							}).animate({
								'height': height,
								'margin-left': marginLeft,
								'margin-top': marginTop,
								'width': width,
								'opacity' : opacityOnGrid
							},(transPeriod-difference),easing,cameraeased);
					}
				}





			});
				
				
				
	 
		}
	}


				if($(prevNav).length){
					$(prevNav).click(function(){
						if(!elem.hasClass('camerasliding')){
							var idNum = parseFloat($('.cameraSlide.cameracurrent',target).index());
							clearInterval(u);
							imgFake();
							$('#'+pieID+', .camera_canvas_wrap',wrap).animate({opacity:0},0);
							canvasLoader();
							if(idNum!=0){
								nextSlide(idNum);
							} else {
								nextSlide(amountSlide);
						   }
						   opts.onStartLoading.call(this);
						}
					});
				}
			
				if($(nextNav).length){
					$(nextNav).click(function(){
						if(!elem.hasClass('camerasliding')){
							var idNum = parseFloat($('.cameraSlide.cameracurrent',target).index()); 
							clearInterval(u);
							imgFake();
							$('#'+pieID+', .camera_canvas_wrap',camera_thumbs_wrap).animate({opacity:0},0);
							canvasLoader();
							if(idNum==amountSlide-1){
								nextSlide(1);
							} else {
								nextSlide(idNum+2);
						   }
						   opts.onStartLoading.call(this);
						}
					});
				}


				if(isMobile()){
					fakeHover.bind('swipeleft',function(event){
						if(!elem.hasClass('camerasliding')){
							var idNum = parseFloat($('.cameraSlide.cameracurrent',target).index()); 
							clearInterval(u);
							imgFake();
							$('#'+pieID+', .camera_canvas_wrap',camera_thumbs_wrap).animate({opacity:0},0);
							canvasLoader();
							if(idNum==amountSlide-1){
								nextSlide(1);
							} else {
								nextSlide(idNum+2);
						   }
						   opts.onStartLoading.call(this);
						}
					});
					fakeHover.bind('swiperight',function(event){
						if(!elem.hasClass('camerasliding')){
							var idNum = parseFloat($('.cameraSlide.cameracurrent',target).index());
							clearInterval(u);
							imgFake();
							$('#'+pieID+', .camera_canvas_wrap',camera_thumbs_wrap).animate({opacity:0},0);
							canvasLoader();
							if(idNum!=0){
								nextSlide(idNum);
							} else {
								nextSlide(amountSlide);
						   }
						   opts.onStartLoading.call(this);
						}
					});
				}

				if($(pagination).length){
					$('.camera_pag li',wrap).click(function(){
						if(!elem.hasClass('camerasliding')){
							var idNum = parseFloat($(this).index());
							var curNum = parseFloat($('.cameraSlide.cameracurrent',target).index());
							if(idNum!=curNum) {
								clearInterval(u);
								imgFake();
								$('#'+pieID+', .camera_canvas_wrap',camera_thumbs_wrap).animate({opacity:0},0);
								canvasLoader();
								nextSlide(idNum+1);
								opts.onStartLoading.call(this);
							}
						}
					});
				}

				if($(thumbs).length) {

					$('.pix_thumb img',thumbs).click(function(){
						if(!elem.hasClass('camerasliding')){
							var idNum = parseFloat($(this).parents('li').index());
							var curNum = parseFloat($('.cameracurrent',target).index());
							if(idNum!=curNum) {
								clearInterval(u);
								imgFake();
								$('#'+pieID+', .camera_canvas_wrap',camera_thumbs_wrap).animate({opacity:0},0);
								$('.pix_thumb',thumbs).removeClass('cameracurrent');
								$(this).parents('li').addClass('cameracurrent');
								canvasLoader();
								nextSlide(idNum+1);
								thumbnailPos();
								opts.onStartLoading.call(this);
							}
						}
					});

					$('.camera_thumbs_cont .camera_prevThumbs',camera_thumbs_wrap).hover(function(){
						$(this).stop(true,false).animate({opacity:1},250);
					},function(){
						$(this).stop(true,false).animate({opacity:.7},250);
					});
					$('.camera_prevThumbs',camera_thumbs_wrap).click(function(){
						var sum = 0,
							wTh = $(thumbs).outerWidth(),
							offUl = $('ul', thumbs).offset().left,
							offDiv = $('> div', thumbs).offset().left,
							ulLeft = offDiv-offUl;
							$('.camera_visThumb',thumbs).each(function(){
								var tW = $(this).outerWidth();
								sum = sum+tW;
							});
							if(ulLeft-sum>0){
								$('ul', thumbs).animate({'margin-left':'-'+(ulLeft-sum)+'px'},500,thumbnailVisible);
							} else {
								$('ul', thumbs).animate({'margin-left':0},500,thumbnailVisible);
							}
					});

					$('.camera_thumbs_cont .camera_nextThumbs',camera_thumbs_wrap).hover(function(){
						$(this).stop(true,false).animate({opacity:1},250);
					},function(){
						$(this).stop(true,false).animate({opacity:.7},250);
					});
					$('.camera_nextThumbs',camera_thumbs_wrap).click(function(){
						var sum = 0,
							wTh = $(thumbs).outerWidth(),
							ulW = $('ul', thumbs).outerWidth(),
							offUl = $('ul', thumbs).offset().left,
							offDiv = $('> div', thumbs).offset().left,
							ulLeft = offDiv-offUl;
							$('.camera_visThumb',thumbs).each(function(){
								var tW = $(this).outerWidth();
								sum = sum+tW;
							});
							if(ulLeft+sum+sum<ulW){
								$('ul', thumbs).animate({'margin-left':'-'+(ulLeft+sum)+'px'},500,thumbnailVisible);
							} else {
								$('ul', thumbs).animate({'margin-left':'-'+(ulW-wTh)+'px'},500,thumbnailVisible);
							}
					});

				}
		
		
	
}

})(jQuery);

;(function($){$.fn.cameraStop = function() {
	var wrap = $(this),
		elem = $('.camera_src',wrap),
		pieID = 'pie_'+wrap.index();
	elem.addClass('stopped');
	if($('.camera_showcommands').length) {
		var camera_thumbs_wrap = $('.camera_thumbs_wrap',wrap);
	} else {
		var camera_thumbs_wrap = wrap;
	}
}
})(jQuery);

;(function($){$.fn.cameraPause = function() {
	var wrap = $(this);
	var elem = $('.camera_src',wrap);
	elem.addClass('paused');
}
})(jQuery);

;(function($){$.fn.cameraResume = function() {
	var wrap = $(this);
	var elem = $('.camera_src',wrap);
	if(typeof autoAdv === 'undefined' || autoAdv!==true){
		elem.removeClass('paused');
	}
}
})(jQuery);
;/*global define, module*/

/* Detect-zoom
 * -----------
 * Cross Browser Zoom and Pixel Ratio Detector
 * Version 1.0.4 | Apr 1 2013
 * dual-licensed under the WTFPL and MIT license
 * Maintained by https://github/tombigel
 * Original developer https://github.com/yonran
 */

//AMD and CommonJS initialization copied from https://github.com/zohararad/audio5js
(function (root, ns, factory) {
    'use strict';

    if (typeof (module) !== 'undefined' && module.exports) { // CommonJS
        module.exports = factory(ns, root);
    } else if (typeof (define) === 'function' && define.amd) { // AMD
        define('detect-zoom', function () {
            return factory(ns, root);
        });
    } else {
        root[ns] = factory(ns, root);
    }

}(window, 'detectZoom', function () {
    'use strict';

    /**
     * Use devicePixelRatio if supported by the browser
     * @return {Number}
     * @private
     */
    var devicePixelRatio = function () {
        return window.devicePixelRatio || 1;
    };

    /**
     * Use a binary search through media queries to find zoom level in Firefox
     * @param property
     * @param unit
     * @param a
     * @param b
     * @param maxIter
     * @param epsilon
     * @return {Number}
     */
    var mediaQueryBinarySearch = function (property, unit, a, b, maxIter, epsilon) {
        var binarySearch = function(a, b, maxIter) {
            var mid = (a + b) / 2;
            if (maxIter <= 0 || b - a < epsilon) {
                return mid;
            }
            var query = '(' + property + ':' + mid + unit + ')';
            if (matchMedia(query).matches) {
                return binarySearch(mid, b, maxIter - 1);
            } else {
                return binarySearch(a, mid, maxIter - 1);
            }
        };
        var matchMedia;
        var head, style, div;
        if (window.matchMedia) {
            matchMedia = window.matchMedia;
        } else {
            head = document.getElementsByTagName('head')[0];
            style = document.createElement('style');
            head.appendChild(style);

            div = document.createElement('div');
            div.className = 'mediaQueryBinarySearch';
            div.style.display = 'none';
            document.body.appendChild(div);

            matchMedia = function (query) {
                style.sheet.insertRule('@media ' + query + '{.mediaQueryBinarySearch ' + '{text-decoration: underline} }', 0);
                var matched = getComputedStyle(div, null).textDecoration === 'underline';
                style.sheet.deleteRule(0);
                return {matches: matched};
            };
        }
        var ratio = binarySearch(a, b, maxIter);
        if (div) {
            head.removeChild(style);
            document.body.removeChild(div);
        }
        return ratio;

    };

    /**
     * Generate detection function
     * @private
     */
    var detectFunction = (function () {
        // first the fallback
        var func = function () {
            return {
                zoom: 1,
                devicePxPerCssPx: 1
            };
        };

        // IE 8 and 9: no trick needed!
        if (!isNaN(screen.logicalXDPI) && !isNaN(screen.systemXDPI)) {
            func = function () {
                var zoom = Math.round((screen.deviceXDPI / screen.logicalXDPI) * 100) / 100;
                return {
                    zoom: zoom,
                    devicePxPerCssPx: zoom * devicePixelRatio()
                };
            };
        }

        // IE10+ / Touch
        // thanks https://github.com/stefanvanburen
        // TODO: Test this function!!!
        // Chrome returns the full height of the document... not just the viewport for the offsetHeight
        // and the height of the window (duh) for window.innerHeight.
        else if (window.navigator.msMaxTouchPoints) {
            func = function () {
                var zoom = Math.round((document.documentElement.offsetHeight / window.innerHeight) * 100) / 100;
                return {
                    zoom: zoom,
                    devicePxPerCssPx: zoom * devicePixelRatio()
                };
            };
        }


        // Mobile Webkit
        // the trick: window.innerWIdth is in CSS pixels, while
        // screen.width and screen.height are in system pixels.
        // And there are no scrollbars to mess up the measurement.
        else if ('orientation' in window) {
            func = function () {
                var deviceWidth = (Math.abs(window.orientation) === 90) ? screen.height : screen.width;
                var zoom = deviceWidth / window.innerWidth;
                return {
                    zoom: zoom,
                    devicePxPerCssPx: zoom * devicePixelRatio()
                };
            };
        }

        // Desktop Webkit
        // the trick: an element's clientHeight is in CSS pixels, while you can
        // set its line-height in system pixels using font-size and
        // -webkit-text-size-adjust:none.
        // device-pixel-ratio: http://www.webkit.org/blog/55/high-dpi-web-sites/
        // 
        // Previous trick (used before http://trac.webkit.org/changeset/100847):
        // documentElement.scrollWidth is in CSS pixels, while
        // document.width was in system pixels. Note that this is the
        // layout width of the document, which is slightly different from viewport
        // because document width does not include scrollbars and might be wider
        // due to big elements.
        // 
        else if ('-webkit-text-size-adjust' in document.body.style || 'text-size-adjust' in document.body.style) {
            func = function () {
                var important = function (str) {
                    return str.replace(/;/g, ' !important;');
                };

                var div = document.createElement('div');
                div.innerHTML = '1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9<br>0';
                div.setAttribute('style', important('font: 100px/1em sans-serif; -webkit-text-size-adjust: none; text-size-adjust: none; height: auto; width: 1em; padding: 0; overflow: visible;'));

                // The container exists so that the div will be laid out in its own flow
                // while not impacting the layout, viewport size, or display of the
                // webpage as a whole.
                // Add !important and relevant CSS rule resets
                // so that other rules cannot affect the results.
                var container = document.createElement('div');
                container.setAttribute('style', important('width:0; height:0; overflow:hidden; visibility:hidden; position: absolute;'));
                container.appendChild(div);

                document.body.appendChild(container);
                var zoom = 1000 / div.clientHeight;
                zoom = Math.round(zoom * 100) / 100;
                document.body.removeChild(container);

                return{
                    zoom: zoom,
                    devicePxPerCssPx: zoom * devicePixelRatio()
                };
            };
        }

        else {
            // FF 4.0+
            // This one's a bit more expensive, so we'll do it almost last...
            //
            // no real trick; device-pixel-ratio is the ratio of device dpi / css dpi.
            // (Note that this is a different interpretation than Webkit's device
            // pixel ratio, which is the ratio device dpi / system dpi).
            //
            // Also, for Mozilla, there is no difference between the zoom factor and the device ratio.
            //
            // The pixel ratio is present starting in version 18, so if it's there, use it...
            var ff = function () {
                var zoom = mediaQueryBinarySearch('min--moz-device-pixel-ratio', '', 0, 10, 20, 0.0001);
                zoom = Math.round(zoom * 100) / 100;
                return {
                    zoom: zoom,
                    devicePxPerCssPx: window.devicePixelRatio || zoom
                };
            };

            if (ff().zoom > 0.001) {
                func = ff;
            }

            // Chrome & Safari
            // This is actually last, as it's the most error prone... Known not to work well with
            // the web inspector open on the side and generally ends up a few pixels off anyway.
            else if(window.outerWidth && window.innerWidth){
                func = function() {
                    var zoom = Math.round((window.outerWidth / window.innerWidth)*100) / 100;
                    return {
                        zoom: zoom,
                        devicePxPerCssPx: zoom * devicePixelRatio()
                    };      
                };
            }

            // Opera
            // I'm not sure why this one needs to be different... but it's almost the same,
            // so we'll stick it down here, as well.
            //
            // works starting Opera 11.11
            // the trick: outerWidth is the viewport width including scrollbars in
            // system px, while innerWidth is the viewport width including scrollbars
            // in CSS px
            else if (window.top.outerWidth && window.top.innerWidth) {
                func = function () {
                    var zoom = window.top.outerWidth / window.top.innerWidth;
                    zoom = Math.round(zoom * 100) / 100;
                    return {
                        zoom: zoom,
                        devicePxPerCssPx: zoom * devicePixelRatio()
                    };
                };
            }
        }

        return func;
    }());


    return ({

        /**
         * Ratios.zoom shorthand
         * @return {Number} Zoom level
         */
        zoom: function () {
            return detectFunction().zoom;
        },

        /**
         * Ratios.devicePxPerCssPx shorthand
         * @return {Number} devicePxPerCssPx level
         */
        device: function () {
            return detectFunction().devicePxPerCssPx;
        }
    });
}));
