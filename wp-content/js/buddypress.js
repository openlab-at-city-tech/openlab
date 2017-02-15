jQuery(document).ready(function(){jQuery("a.confirm").click(function(){return!!confirm(BP_Confirm.are_you_sure)})});;function member_widget_click_handler(){jQuery(".widget div#members-list-options a").on("click",function(){var a=this;return jQuery(a).addClass("loading"),jQuery(".widget div#members-list-options a").removeClass("selected"),jQuery(this).addClass("selected"),jQuery.post(ajaxurl,{action:"widget_members",cookie:encodeURIComponent(document.cookie),_wpnonce:jQuery("input#_wpnonce-members").val(),"max-members":jQuery("input#members_widget_max").val(),filter:jQuery(this).attr("id")},function(b){jQuery(a).removeClass("loading"),member_widget_response(b)}),!1})}function member_widget_response(a){a=a.substr(0,a.length-1),a=a.split("[[SPLIT]]"),"-1"!==a[0]?jQuery(".widget ul#members-list").fadeOut(200,function(){jQuery(".widget ul#members-list").html(a[1]),jQuery(".widget ul#members-list").fadeIn(200)}):jQuery(".widget ul#members-list").fadeOut(200,function(){var b="<p>"+a[1]+"</p>";jQuery(".widget ul#members-list").html(b),jQuery(".widget ul#members-list").fadeIn(200)})}jQuery(document).ready(function(){member_widget_click_handler(),"undefined"!=typeof wp&&wp.customize&&wp.customize.selectiveRefresh&&wp.customize.selectiveRefresh.bind("partial-content-rendered",function(){member_widget_click_handler()})});;function bp_get_querystring(a){var b=location.search.split(a+"=")[1];return b?decodeURIComponent(b.split("&")[0]):null};!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):jQuery)}(function(a){function b(a){return h.raw?a:encodeURIComponent(a)}function c(a){return h.raw?a:decodeURIComponent(a)}function d(a){return b(h.json?JSON.stringify(a):String(a))}function e(a){0===a.indexOf('"')&&(a=a.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return a=decodeURIComponent(a.replace(g," ")),h.json?JSON.parse(a):a}catch(a){}}function f(b,c){var d=h.raw?b:e(b);return a.isFunction(c)?c(d):d}var g=/\+/g,h=a.cookie=function(e,g,i){if(void 0!==g&&!a.isFunction(g)){if(i=a.extend({},h.defaults,i),"number"==typeof i.expires){var j=i.expires,k=i.expires=new Date;k.setTime(+k+864e5*j)}return document.cookie=[b(e),"=",d(g),i.expires?"; expires="+i.expires.toUTCString():"",i.path?"; path="+i.path:"",i.domain?"; domain="+i.domain:"",i.secure?"; secure":""].join("")}for(var l=e?void 0:{},m=document.cookie?document.cookie.split("; "):[],n=0,o=m.length;n<o;n++){var p=m[n].split("="),q=c(p.shift()),r=p.join("=");if(e&&e===q){l=f(r,g);break}e||void 0===(r=f(r))||(l[q]=r)}return l};h.defaults={},a.removeCookie=function(b,c){return void 0!==a.cookie(b)&&(a.cookie(b,"",a.extend({},c,{expires:-1})),!a.cookie(b))}});;!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):jQuery)}(function(a){function b(b){return a.isFunction(b)||"object"==typeof b?b:{top:b,left:b}}var c=a.scrollTo=function(b,c,d){return a(window).scrollTo(b,c,d)};return c.defaults={axis:"xy",duration:parseFloat(a.fn.jquery)>=1.3?0:1,limit:!0},c.window=function(){return a(window)._scrollable()},a.fn._scrollable=function(){return this.map(function(){var b=this,c=!b.nodeName||a.inArray(b.nodeName.toLowerCase(),["iframe","#document","html","body"])!==-1;if(!c)return b;var d=(b.contentWindow||b).document||b.ownerDocument||b;return/webkit/i.test(navigator.userAgent)||"BackCompat"===d.compatMode?d.body:d.documentElement})},a.fn.scrollTo=function(d,e,f){return"object"==typeof e&&(f=e,e=0),"function"==typeof f&&(f={onAfter:f}),"max"===d&&(d=9e9),f=a.extend({},c.defaults,f),e=e||f.duration,f.queue=f.queue&&f.axis.length>1,f.queue&&(e/=2),f.offset=b(f.offset),f.over=b(f.over),this._scrollable().each(function(){function g(a){j.animate(l,e,f.easing,a&&function(){a.call(this,k,f)})}if(null!==d){var h,i=this,j=a(i),k=d,l={},m=j.is("html,body");switch(typeof k){case"number":case"string":if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(k)){k=b(k);break}if(k=m?a(k):a(k,this),!k.length)return;case"object":(k.is||k.style)&&(h=(k=a(k)).offset())}var n=a.isFunction(f.offset)&&f.offset(i,k)||f.offset;a.each(f.axis.split(""),function(a,b){var d="x"===b?"Left":"Top",e=d.toLowerCase(),o="scroll"+d,p=i[o],q=c.max(i,b);if(h)l[o]=h[e]+(m?0:p-j.offset()[e]),f.margin&&(l[o]-=parseInt(k.css("margin"+d))||0,l[o]-=parseInt(k.css("border"+d+"Width"))||0),l[o]+=n[e]||0,f.over[e]&&(l[o]+=k["x"===b?"width":"height"]()*f.over[e]);else{var r=k[e];l[o]=r.slice&&"%"===r.slice(-1)?parseFloat(r)/100*q:r}f.limit&&/^\d+$/.test(l[o])&&(l[o]=l[o]<=0?0:Math.min(l[o],q)),!a&&f.queue&&(p!==l[o]&&g(f.onAfterFirst),delete l[o])}),g(f.onAfter)}}).end()},c.max=function(b,c){var d="x"===c?"Width":"Height",e="scroll"+d;if(!a(b).is("html,body"))return b[e]-a(b)[d.toLowerCase()]();var f="client"+d,g=b.ownerDocument.documentElement,h=b.ownerDocument.body;return Math.max(g[e],h[e])-Math.min(g[f],h[f])},c});;jQuery(document).ready( function($) {

	//Hide the sort form submit, we're gonna submit on change
	$('#bp-group-documents-sort-form input[type=submit]').hide();
	$('#bp-group-documents-sort-form select[name=order]').change(function(){
		$('form#bp-group-documents-sort-form').submit();
	});

	//Hide the category form submit, we're gonna submit on change
	$('#bp-group-documents-category-form input[type=submit]').hide();
	$('#bp-group-documents-category-form select[name=category]').change(function(){
		$('form#bp-group-documents-category-form').submit();
	});

	//Hide the upload form by default, expand as needed
	$('#bp-group-documents-upload-new').hide();
	$('#bp-group-documents-upload-button').show();
	$('#bp-group-documents-upload-button').click(function(){
		$('#bp-group-documents-upload-button').slideUp();
		$('#bp-group-documents-upload-new').slideDown();
		return false;
	});

	//prefill the new category field
	$('input.bp-group-documents-new-category').val('New Category...').css('color','#999').focus(function(){
		$(this).val('').css('color','inherit');
	});
		
	//check for presence of a file before submitting form
	$('form#bp-group-documents-form').submit(function(){
		
		//check for pre-filled values, and remove before sumitting
		if( $('input.bp-group-documents-new-category').val() == 'New Category...' ) {
			$('input.bp-group-documents-new-category').val('');
		}
		if( $('input[name=bp_group_documents_operation]').val() == 'add' ) {
			if($('input.bp-group-documents-file').val()) {
				return true;
			}
			alert('You must select a file to upload!');
			return false;
		}
	});	

	//validate group admin form before submitting
	$('form#group-settings-form').submit(function() {
		
		//check for pre-filled values, and remove before sumitting
		if( $('input.bp-group-documents-new-category').val() == 'New Category...' ) {
			$('input.bp-group-documents-new-category').val('');
		}
	});

	//Make the user confirm when deleting a document
	$('a#bp-group-documents-delete').click(function(){
		return confirm('Are you sure you wish to permanently delete this document?');
	});

	//Track when a user clicks a document via Ajax
	$('a.group-documents-title').add($('a.group-documents-icon')).click(function(){
		dash_position = $(this).attr('id').lastIndexOf('-');
		document_num = $(this).attr('id').substring(dash_position+1);

		$.post( ajaxurl ,{
			action:'bp_group_documents_increment_downloads',
			document_id:document_num
		});

	});

	//Make user confirm when deleting a category
	$('a.group-documents-category-delete').click(function(){
		return confirm('Are you sure you wish to permanently delete this category?');
	});

	//add new single categories in the group admin screen via ajax
	$('#group-documents-group-admin-categories input[value=Add]').click(function(){
		$.post(ajaxurl, {
			action:'group_documents_add_category',
			category:$('input[name=bp_group_documents_new_category]').val()
		}, function(response){
			$('#group-documents-group-admin-categories input[value=Add]').parent().before(response);
		});
		return false;
	});

	//delete single categories in the group admin screen via ajax
	$('#group-documents-group-admin-categories a.group-documents-category-delete').click(function(){
		cat_id_string = $(this).parent('li').attr('id');
		pos = cat_id_string.indexOf('-');
		cat_id = cat_id_string.substring(pos+1);
		$.post(ajaxurl, {
			action:'group_documents_delete_category',
			category_id:cat_id
		}, function(response){
			if( '1' == response ) {
				$('li#' + cat_id_string).slideUp();
			}
		});
		return false;
	});

});
;jQuery(document).ready( function() {
	var j = jQuery;

	// topic follow/mute
	j( document ).on("click", '.ass-topic-subscribe > a', function() {
		it = j(this);
		var theid = j(this).attr('id');
		var stheid = theid.split('-');

		//j('.pagination .ajax-loader').toggle();

		var data = {
			action: 'ass_ajax',
			a: stheid[0],
			topic_id: stheid[1],
			group_id: stheid[2]
			//,_ajax_nonce: stheid[2]
		};

		// TODO: add ajax code to give status feedback that will fade out

		j.post( ajaxurl, data, function( response ) {
			if ( response == 'follow' ) {
				var m = bp_ass.mute;
				theid = theid.replace( 'follow', 'mute' );
			} else if ( response == 'mute' ) {
				var m = bp_ass.follow;
				theid = theid.replace( 'mute', 'follow' );
			} else {
				var m = bp_ass.error;
			}

			j(it).html(m);
			j(it).attr('id', theid);
			j(it).attr('title', '');

			//j('.pagination .ajax-loader').toggle();

		});
	});


	// group subscription options
	j( document ).on("click", '.group-sub', function() {
		it = j(this);
		var theid = j(this).attr('id');
		var stheid = theid.split('-');
		group_id = stheid[1];
		current = j( '#gsubstat-'+group_id ).html();
		j('#gsubajaxload-'+group_id).toggle();

		var data = {
			action: 'ass_group_ajax',
			a: stheid[0],
			group_id: stheid[1]
			//,_ajax_nonce: stheid[2]
		};

		j.post( ajaxurl, data, function( response ) {
			status = j(it).html();
			if ( !current || current == 'No Email' ) {
				j( '#gsublink-'+group_id ).html('change');
				//status = status + ' / ';
			}
			j( '#gsubstat-'+group_id ).html( status ); //add .animate({opacity: 1.0}, 2000) to slow things down for testing
			j( '#gsubstat-'+group_id ).addClass( 'gemail_icon' );
			j( '#gsubopt-'+group_id ).slideToggle('fast');
			j( '#gsubajaxload-'+group_id ).toggle();
		});

	});

	j( document ).on("click", '.group-subscription-options-link', function() {
		stheid = j(this).attr('id').split('-');
		group_id = stheid[1];
		j( '#gsubopt-'+group_id ).slideToggle('fast');
	});

	j( document ).on("click", '.group-subscription-close', function() {
		stheid = j(this).attr('id').split('-');
		group_id = stheid[1];
		j( '#gsubopt-'+group_id ).slideToggle('fast');
	});

	//j( document ).on("click", '.ass-settings-advanced-link', function() {
	//	j( '.ass-settings-advanced' ).slideToggle('fast');
	//});

	// Toggle welcome email fields on group email options page
	j( document ).on("change", '#ass-welcome-email-enabled', function() {
		if ( j(this).prop('checked') ) {
			j('.ass-welcome-email-field').show();
		} else {
			j('.ass-welcome-email-field').hide();
		}
	});

});
;!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(c){return a.returnExportsGlobal=b(c)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(a){"use strict";var b,c,d,e,f,g,h,i,j,k,l;k="caret",b=function(){function b(a){this.$inputor=a,this.domInputor=this.$inputor[0]}return b.prototype.setPos=function(a){return this.domInputor},b.prototype.getIEPosition=function(){return this.getPosition()},b.prototype.getPosition=function(){var a,b;return b=this.getOffset(),a=this.$inputor.offset(),b.left-=a.left,b.top-=a.top,b},b.prototype.getOldIEPos=function(){var a,b;return b=h.selection.createRange(),a=h.body.createTextRange(),a.moveToElementText(this.domInputor),a.setEndPoint("EndToEnd",b),a.text.length},b.prototype.getPos=function(){var a,b,c;return(c=this.range())?(a=c.cloneRange(),a.selectNodeContents(this.domInputor),a.setEnd(c.endContainer,c.endOffset),b=a.toString().length,a.detach(),b):h.selection?this.getOldIEPos():void 0},b.prototype.getOldIEOffset=function(){var a,b;return a=h.selection.createRange().duplicate(),a.moveStart("character",-1),b=a.getBoundingClientRect(),{height:b.bottom-b.top,left:b.left,top:b.top}},b.prototype.getOffset=function(b){var c,d,e,f,g;return j.getSelection&&(e=this.range())?(e.endOffset-1>0&&e.endContainer===!this.domInputor&&(c=e.cloneRange(),c.setStart(e.endContainer,e.endOffset-1),c.setEnd(e.endContainer,e.endOffset),f=c.getBoundingClientRect(),d={height:f.height,left:f.left+f.width,top:f.top},c.detach()),d&&0!==(null!=d?d.height:void 0)||(c=e.cloneRange(),g=a(h.createTextNode("|")),c.insertNode(g[0]),c.selectNode(g[0]),f=c.getBoundingClientRect(),d={height:f.height,left:f.left,top:f.top},g.remove(),c.detach())):h.selection&&(d=this.getOldIEOffset()),d&&(d.top+=a(j).scrollTop(),d.left+=a(j).scrollLeft()),d},b.prototype.range=function(){var a;if(j.getSelection)return a=j.getSelection(),a.rangeCount>0?a.getRangeAt(0):null},b}(),c=function(){function b(a){this.$inputor=a,this.domInputor=this.$inputor[0]}return b.prototype.getIEPos=function(){var a,b,c,d,e,f,g;return b=this.domInputor,f=h.selection.createRange(),e=0,f&&f.parentElement()===b&&(d=b.value.replace(/\r\n/g,"\n"),c=d.length,g=b.createTextRange(),g.moveToBookmark(f.getBookmark()),a=b.createTextRange(),a.collapse(!1),e=g.compareEndPoints("StartToEnd",a)>-1?c:-g.moveStart("character",-c)),e},b.prototype.getPos=function(){return h.selection?this.getIEPos():this.domInputor.selectionStart},b.prototype.setPos=function(a){var b,c;return b=this.domInputor,h.selection?(c=b.createTextRange(),c.move("character",a),c.select()):b.setSelectionRange&&b.setSelectionRange(a,a),b},b.prototype.getIEOffset=function(a){var b,c,d,e;return c=this.domInputor.createTextRange(),a||(a=this.getPos()),c.move("character",a),d=c.boundingLeft,e=c.boundingTop,b=c.boundingHeight,{left:d,top:e,height:b}},b.prototype.getOffset=function(b){var c,d,e;return c=this.$inputor,h.selection?(d=this.getIEOffset(b),d.top+=a(j).scrollTop()+c.scrollTop(),d.left+=a(j).scrollLeft()+c.scrollLeft(),d):(d=c.offset(),e=this.getPosition(b),d={left:d.left+e.left-c.scrollLeft(),top:d.top+e.top-c.scrollTop(),height:e.height})},b.prototype.getPosition=function(a){var b,c,e,f,g,h,i;return b=this.$inputor,f=function(a){return a=a.replace(/<|>|`|"|&/g,"?").replace(/\r\n|\r|\n/g,"<br/>"),/firefox/i.test(navigator.userAgent)&&(a=a.replace(/\s/g,"&nbsp;")),a},void 0===a&&(a=this.getPos()),i=b.val().slice(0,a),e=b.val().slice(a),g="<span style='position: relative; display: inline;'>"+f(i)+"</span>",g+="<span id='caret' style='position: relative; display: inline;'>|</span>",g+="<span style='position: relative; display: inline;'>"+f(e)+"</span>",h=new d(b),c=h.create(g).rect()},b.prototype.getIEPosition=function(a){var b,c,d,e,f;return d=this.getIEOffset(a),c=this.$inputor.offset(),e=d.left-c.left,f=d.top-c.top,b=d.height,{left:e,top:f,height:b}},b}(),d=function(){function b(a){this.$inputor=a}return b.prototype.css_attr=["borderBottomWidth","borderLeftWidth","borderRightWidth","borderTopStyle","borderRightStyle","borderBottomStyle","borderLeftStyle","borderTopWidth","boxSizing","fontFamily","fontSize","fontWeight","height","letterSpacing","lineHeight","marginBottom","marginLeft","marginRight","marginTop","outlineWidth","overflow","overflowX","overflowY","paddingBottom","paddingLeft","paddingRight","paddingTop","textAlign","textOverflow","textTransform","whiteSpace","wordBreak","wordWrap"],b.prototype.mirrorCss=function(){var b,c=this;return b={position:"absolute",left:-9999,top:0,zIndex:-2e4},"TEXTAREA"===this.$inputor.prop("tagName")&&this.css_attr.push("width"),a.each(this.css_attr,function(a,d){return b[d]=c.$inputor.css(d)}),b},b.prototype.create=function(b){return this.$mirror=a("<div></div>"),this.$mirror.css(this.mirrorCss()),this.$mirror.html(b),this.$inputor.after(this.$mirror),this},b.prototype.rect=function(){var a,b,c;return a=this.$mirror.find("#caret"),b=a.position(),c={left:b.left,top:b.top,height:a.height()},this.$mirror.remove(),c},b}(),e={contentEditable:function(a){return!(!a[0].contentEditable||"true"!==a[0].contentEditable)}},g={pos:function(a){return a||0===a?this.setPos(a):this.getPos()},position:function(a){return h.selection?this.getIEPosition(a):this.getPosition(a)},offset:function(a){var b;return b=this.getOffset(a)}},h=null,j=null,i=null,l=function(a){var b;return(b=null!=a?a.iframe:void 0)?(i=b,j=b.contentWindow,h=b.contentDocument||j.document):(i=void 0,j=window,h=document)},f=function(a){var b;h=a[0].ownerDocument,j=h.defaultView||h.parentWindow;try{return i=j.frameElement}catch(a){b=a}},a.fn.caret=function(d,f,h){var i;return g[d]?(a.isPlainObject(f)?(l(f),f=void 0):l(h),i=e.contentEditable(this)?new b(this):new c(this),g[d].apply(i,[f])):a.error("Method "+d+" does not exist on jQuery.caret")},a.fn.caret.EditableCaret=b,a.fn.caret.InputCaret=c,a.fn.caret.Utils=e,a.fn.caret.apis=g});;!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(c){return a.returnExportsGlobal=b(c)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(a){var b,c,d,e,f,g,h,i=[].slice;c=function(){function b(b){this.current_flag=null,this.controllers={},this.alias_maps={},this.$inputor=a(b),this.setIframe(),this.listen()}return b.prototype.createContainer=function(b){if(0===(this.$el=a("#atwho-container",b)).length)return a(b.body).append(this.$el=a("<div id='atwho-container'></div>"))},b.prototype.setIframe=function(a,b){var c;return null==b&&(b=!1),a?(this.window=a.contentWindow,this.document=a.contentDocument||this.window.document,this.iframe=a):(this.document=document,this.window=window,this.iframe=null),(this.iframeStandalone=b)?(null!=(c=this.$el)&&c.remove(),this.createContainer(this.document)):this.createContainer(document)},b.prototype.controller=function(a){var b,c,d,e;if(this.alias_maps[a])c=this.controllers[this.alias_maps[a]];else{e=this.controllers;for(d in e)if(b=e[d],d===a){c=b;break}}return c?c:this.controllers[this.current_flag]},b.prototype.set_context_for=function(a){return this.current_flag=a,this},b.prototype.reg=function(a,b){var c,e;return c=(e=this.controllers)[a]||(e[a]=new d(this,a)),b.alias&&(this.alias_maps[b.alias]=a),c.init(b),this},b.prototype.listen=function(){return this.$inputor.on("keyup.atwhoInner",function(a){return function(b){return a.on_keyup(b)}}(this)).on("keydown.atwhoInner",function(a){return function(b){return a.on_keydown(b)}}(this)).on("scroll.atwhoInner",function(a){return function(b){var c;return null!=(c=a.controller())?c.view.hide(b):void 0}}(this)).on("blur.atwhoInner",function(a){return function(b){var c;if(c=a.controller())return c.view.hide(b,c.get_opt("display_timeout"))}}(this)).on("click.atwhoInner",function(a){return function(b){return a.dispatch()}}(this))},b.prototype.shutdown=function(){var a,b,c;c=this.controllers;for(b in c)a=c[b],a.destroy(),delete this.controllers[b];return this.$inputor.off(".atwhoInner"),this.$el.remove()},b.prototype.dispatch=function(){return a.map(this.controllers,function(a){return function(b){var c;return(c=b.get_opt("delay"))?(clearTimeout(a.delayedCallback),a.delayedCallback=setTimeout(function(){if(b.look_up())return a.set_context_for(b.at)},c)):b.look_up()?a.set_context_for(b.at):void 0}}(this))},b.prototype.on_keyup=function(b){var c;switch(b.keyCode){case f.ESC:b.preventDefault(),null!=(c=this.controller())&&c.view.hide();break;case f.DOWN:case f.UP:case f.CTRL:a.noop();break;case f.P:case f.N:b.ctrlKey||this.dispatch();break;default:this.dispatch()}},b.prototype.on_keydown=function(b){var c,d;if(c=null!=(d=this.controller())?d.view:void 0,c&&c.visible())switch(b.keyCode){case f.ESC:b.preventDefault(),c.hide(b);break;case f.UP:b.preventDefault(),c.prev();break;case f.DOWN:b.preventDefault(),c.next();break;case f.P:if(!b.ctrlKey)return;b.preventDefault(),c.prev();break;case f.N:if(!b.ctrlKey)return;b.preventDefault(),c.next();break;case f.TAB:case f.ENTER:if(!c.visible())return;b.preventDefault(),c.choose(b);break;default:a.noop()}},b}(),d=function(){function b(b,c){this.app=b,this.at=c,this.$inputor=this.app.$inputor,this.id=this.$inputor[0].id||this.uid(),this.setting=null,this.query=null,this.pos=0,this.cur_rect=null,this.range=null,0===(this.$el=a("#atwho-ground-"+this.id,this.app.$el)).length&&this.app.$el.append(this.$el=a("<div id='atwho-ground-"+this.id+"'></div>")),this.model=new g(this),this.view=new h(this)}return b.prototype.uid=function(){return(Math.random().toString(16)+"000000000").substr(2,8)+(new Date).getTime()},b.prototype.init=function(b){return this.setting=a.extend({},this.setting||a.fn.atwho.default,b),this.view.init(),this.model.reload(this.setting.data)},b.prototype.destroy=function(){return this.trigger("beforeDestroy"),this.model.destroy(),this.view.destroy(),this.$el.remove()},b.prototype.call_default=function(){var b,c,d;d=arguments[0],b=2<=arguments.length?i.call(arguments,1):[];try{return e[d].apply(this,b)}catch(b){return c=b,a.error(""+c+" Or maybe At.js doesn't have function "+d)}},b.prototype.trigger=function(a,b){var c,d;return null==b&&(b=[]),b.push(this),c=this.get_opt("alias"),d=c?""+a+"-"+c+".atwho":""+a+".atwho",this.$inputor.trigger(d,b)},b.prototype.callbacks=function(a){return this.get_opt("callbacks")[a]||e[a]},b.prototype.get_opt=function(a,b){var c;try{return this.setting[a]}catch(a){return c=a,null}},b.prototype.content=function(){var a;if(this.$inputor.is("textarea, input"))return this.$inputor.val();if(a=this.mark_range())return(a.startContainer.textContent||"").slice(0,a.startOffset)},b.prototype.catch_query=function(){var a,b,c,d,e,f;return b=this.content(),a=this.$inputor.caret("pos",{iframe:this.app.iframe}),f=b.slice(0,a),d=this.callbacks("matcher").call(this,this.at,f,this.get_opt("start_with_space")),"string"==typeof d&&d.length<=this.get_opt("max_len",20)?(e=a-d.length,c=e+d.length,this.pos=e,d={text:d,head_pos:e,end_pos:c},this.trigger("matched",[this.at,d.text])):(d=null,this.view.hide()),this.query=d},b.prototype.rect=function(){var b,c,d;if(b=this.$inputor.caret("offset",this.pos-1,{iframe:this.app.iframe}))return this.app.iframe&&!this.app.iframeStandalone&&(c=a(this.app.iframe).offset(),b.left+=c.left,b.top+=c.top),this.$inputor.is("[contentEditable]")&&(b=this.cur_rect||(this.cur_rect=b)),d=this.app.document.selection?0:2,{left:b.left,top:b.top,bottom:b.top+b.height+d}},b.prototype.reset_rect=function(){if(this.$inputor.is("[contentEditable]"))return this.cur_rect=null},b.prototype.mark_range=function(){var a;if(this.$inputor.is("[contentEditable]"))return this.app.window.getSelection&&(a=this.app.window.getSelection()).rangeCount>0?this.range=a.getRangeAt(0):this.app.document.selection?this.ie8_range=this.app.document.selection.createRange():void 0},b.prototype.insert_content_for=function(b){var c,d,e;return d=b.data("value"),e=this.get_opt("insert_tpl"),this.$inputor.is("textarea, input")||!e?d:(c=a.extend({},b.data("item-data"),{"atwho-data-value":d,"atwho-at":this.at}),this.callbacks("tpl_eval").call(this,e,c))},b.prototype.insert=function(b,c){var d,e,f,g,h,i,j,k,l,m,n,o;if(d=this.$inputor,l=this.callbacks("inserting_wrapper").call(this,d,b,this.get_opt("suffix")),d.is("textarea, input"))i=d.val(),j=i.slice(0,Math.max(this.query.head_pos-this.at.length,0)),k=""+j+l+i.slice(this.query.end_pos||0),d.val(k),d.caret("pos",j.length+l.length,{iframe:this.app.iframe});else if(g=this.range){for(f=g.startOffset-(this.query.end_pos-this.query.head_pos)-this.at.length,g.setStart(g.endContainer,Math.max(f,0)),g.setEnd(g.endContainer,g.endOffset),g.deleteContents(),o=a(l,this.app.document),m=0,n=o.length;m<n;m++)e=o[m],g.insertNode(e),g.setEndAfter(e),g.collapse(!1);h=this.app.window.getSelection(),h.removeAllRanges(),h.addRange(g)}else(g=this.ie8_range)&&(g.moveStart("character",this.query.end_pos-this.query.head_pos-this.at.length),g.pasteHTML(l),g.collapse(!1),g.select());return d.is(":focus")||d.focus(),d.change()},b.prototype.render_view=function(a){var b;return b=this.get_opt("search_key"),a=this.callbacks("sorter").call(this,this.query.text,a.slice(0,1001),b),this.view.render(a.slice(0,this.get_opt("limit")))},b.prototype.look_up=function(){var b,c;if(b=this.catch_query())return c=function(a){return a&&a.length>0?this.render_view(a):this.view.hide()},this.model.query(b.text,a.proxy(c,this)),b},b}(),g=function(){function b(a){this.context=a,this.at=this.context.at,this.storage=this.context.$inputor}return b.prototype.destroy=function(){return this.storage.data(this.at,null)},b.prototype.saved=function(){return this.fetch()>0},b.prototype.query=function(a,b){var c,d,e;return c=this.fetch(),d=this.context.get_opt("search_key"),c=this.context.callbacks("filter").call(this.context,a,c,d)||[],e=this.context.callbacks("remote_filter"),c.length>0||!e&&0===c.length?b(c):e.call(this.context,a,b)},b.prototype.fetch=function(){return this.storage.data(this.at)||[]},b.prototype.save=function(a){return this.storage.data(this.at,this.context.callbacks("before_save").call(this.context,a||[]))},b.prototype.load=function(a){if(!this.saved()&&a)return this._load(a)},b.prototype.reload=function(a){return this._load(a)},b.prototype._load=function(b){return"string"==typeof b?a.ajax(b,{dataType:"json"}).done(function(a){return function(b){return a.save(b)}}(this)):this.save(b)},b}(),h=function(){function b(b){this.context=b,this.$el=a("<div class='atwho-view'><ul class='atwho-view-ul'></ul></div>"),this.timeout_id=null,this.context.$el.append(this.$el),this.bind_event()}return b.prototype.init=function(){var a;return a=this.context.get_opt("alias")||this.context.at.charCodeAt(0),this.$el.attr({id:"at-view-"+a})},b.prototype.destroy=function(){return this.$el.remove()},b.prototype.bind_event=function(){var b;return b=this.$el.find("ul"),b.on("mouseenter.atwho-view","li",function(c){return b.find(".cur").removeClass("cur"),a(c.currentTarget).addClass("cur")}).on("click.atwho-view","li",function(c){return function(d){return b.find(".cur").removeClass("cur"),a(d.currentTarget).addClass("cur"),c.choose(d),d.preventDefault()}}(this))},b.prototype.visible=function(){return this.$el.is(":visible")},b.prototype.choose=function(a){var b,c;if((b=this.$el.find(".cur")).length&&(c=this.context.insert_content_for(b),this.context.insert(this.context.callbacks("before_insert").call(this.context,c,b),b),this.context.trigger("inserted",[b,a]),this.hide(a)),this.context.get_opt("hide_without_suffix"))return this.stop_showing=!0},b.prototype.reposition=function(b){var c,d,e,f;return f=this.context.app.iframeStandalone?this.context.app.window:window,b.bottom+this.$el.height()-a(f).scrollTop()>a(f).height()&&(b.bottom=b.top-this.$el.height()),b.left>(d=a(f).width()-this.$el.width()-5)&&(b.left=d),c={left:b.left,top:b.bottom},null!=(e=this.context.callbacks("before_reposition"))&&e.call(this.context,c),this.$el.offset(c),this.context.trigger("reposition",[c])},b.prototype.next=function(){var a,b;return a=this.$el.find(".cur").removeClass("cur"),b=a.next(),b.length||(b=this.$el.find("li:first")),b.addClass("cur"),this.$el.animate({scrollTop:Math.max(0,a.innerHeight()*(b.index()+2)-this.$el.height())},150)},b.prototype.prev=function(){var a,b;return a=this.$el.find(".cur").removeClass("cur"),b=a.prev(),b.length||(b=this.$el.find("li:last")),b.addClass("cur"),this.$el.animate({scrollTop:Math.max(0,a.innerHeight()*(b.index()+2)-this.$el.height())},150)},b.prototype.show=function(){var a;return this.stop_showing?void(this.stop_showing=!1):(this.context.mark_range(),this.visible()||(this.$el.show(),this.$el.scrollTop(0),this.context.trigger("shown")),(a=this.context.rect())?this.reposition(a):void 0)},b.prototype.hide=function(a,b){var c;if(this.visible())return isNaN(b)?(this.context.reset_rect(),this.$el.hide(),this.context.trigger("hidden",[a])):(c=function(a){return function(){return a.hide()}}(this),clearTimeout(this.timeout_id),this.timeout_id=setTimeout(c,b))},b.prototype.render=function(b){var c,d,e,f,g,h,i;if(!(a.isArray(b)&&b.length>0))return void this.hide();for(this.$el.find("ul").empty(),d=this.$el.find("ul"),g=this.context.get_opt("tpl"),h=0,i=b.length;h<i;h++)e=b[h],e=a.extend({},e,{"atwho-at":this.context.at}),f=this.context.callbacks("tpl_eval").call(this.context,g,e),c=a(this.context.callbacks("highlighter").call(this.context,f,this.context.query.text)),c.data("item-data",e),d.append(c);return this.show(),this.context.get_opt("highlight_first")?d.find("li:first").addClass("cur"):void 0},b}(),f={DOWN:40,UP:38,ESC:27,TAB:9,ENTER:13,CTRL:17,P:80,N:78},e={before_save:function(b){var c,d,e,f;if(!a.isArray(b))return b;for(f=[],d=0,e=b.length;d<e;d++)c=b[d],a.isPlainObject(c)?f.push(c):f.push({name:c});return f},matcher:function(a,b,c){var d,e,f,g;return a=a.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&"),c&&(a="(?:^|\\s)"+a),f=decodeURI("%C3%80"),g=decodeURI("%C3%BF"),e=new RegExp(""+a+"([A-Za-z"+f+"-"+g+"0-9_+-]*)$|"+a+"([^\\x00-\\xff]*)$","gi"),d=e.exec(b),d?d[2]||d[1]:null},filter:function(a,b,c){var d,e,f,g;for(g=[],e=0,f=b.length;e<f;e++)d=b[e],~new String(d[c]).toLowerCase().indexOf(a.toLowerCase())&&g.push(d);return g},remote_filter:null,sorter:function(a,b,c){var d,e,f,g;if(!a)return b;for(g=[],e=0,f=b.length;e<f;e++)d=b[e],d.atwho_order=new String(d[c]).toLowerCase().indexOf(a.toLowerCase()),d.atwho_order>-1&&g.push(d);return g.sort(function(a,b){return a.atwho_order-b.atwho_order})},tpl_eval:function(a,b){var c;try{return a.replace(/\$\{([^\}]*)\}/g,function(a,c,d){return b[c]})}catch(a){return c=a,""}},highlighter:function(a,b){var c;return b?(c=new RegExp(">\\s*(\\w*?)("+b.replace("+","\\+")+")(\\w*)\\s*<","ig"),a.replace(c,function(a,b,c,d){return"> "+b+"<strong>"+c+"</strong>"+d+" <"})):a},before_insert:function(a,b){return a},inserting_wrapper:function(a,b,c){var d;return c=""===c?c:c||" ",a.is("textarea, input")?""+b+c:"true"===a.attr("contentEditable")?(c=" "===c?"&nbsp;":c,/firefox/i.test(navigator.userAgent)?d="<span>"+b+c+"</span>":(c="<span contenteditable='false'>"+c+"</span>",d="<span contenteditable='false'>"+b+c+"</span>"),this.app.document.selection&&(d="<span contenteditable='true'>"+b+"</span>"),d+"<span></span>"):void 0}},b={load:function(a,b){var c;if(c=this.controller(a))return c.model.load(b)},setIframe:function(a,b){return this.setIframe(a,b),null},run:function(){return this.dispatch()},destroy:function(){return this.shutdown(),this.$inputor.data("atwho",null)}},a.fn.atwho=function(d){var e,f;return f=arguments,e=null,this.filter('textarea, input, [contenteditable=""], [contenteditable=true]').each(function(){var g,h;return(h=(g=a(this)).data("atwho"))||g.data("atwho",h=new c(this)),"object"!=typeof d&&d?b[d]&&h?e=b[d].apply(h,Array.prototype.slice.call(f,1)):a.error("Method "+d+" does not exist on jQuery.caret"):h.reg(d.at,d)}),e||this},a.fn.atwho.default={at:void 0,alias:void 0,data:null,tpl:"<li data-value='${atwho-at}${name}'>${name}</li>",insert_tpl:"<span id='${id}'>${atwho-data-value}</span>",callbacks:e,search_key:"name",suffix:void 0,hide_without_suffix:!1,start_with_space:!0,highlight_first:!0,limit:5,max_len:20,display_timeout:300,delay:null}});;window.bp=window.bp||{},function(a,b,c){var d,e=[];a.mentions=a.mentions||{},a.mentions.users=window.bp.mentions.users||[],"object"==typeof window.BP_Suggestions&&(a.mentions.users=window.BP_Suggestions.friends||a.mentions.users),b.fn.bp_mentions=function(a){b.isArray(a)&&(a={data:a});var c={delay:200,hide_without_suffix:!0,insert_tpl:"</>${atwho-data-value}</>",limit:10,start_with_space:!1,suffix:"",callbacks:{filter:function(a,b,c){var d,e,f,g=[],h=new RegExp("^"+a+"| "+a,"ig");for(e=0,f=b.length;e<f;e++)d=b[e],d[c].toLowerCase().match(h)&&g.push(d);return g},highlighter:function(a,b){if(!b)return a;var c=new RegExp(">(\\s*|[\\w\\s]*)("+this.at.replace("+","\\+")+"?"+b.replace("+","\\+")+")([\\w ]*)\\s*<","ig");return a.replace(c,function(a,b,c,d){return">"+b+"<strong>"+c+"</strong>"+d+"<"})},before_reposition:function(a){var c,d,e,f,g=b("#atwho-ground-"+this.id+" .atwho-view"),h=b("body"),i=this.$inputor.data("atwho");"undefined"!==i&&"undefined"!==i.iframe&&null!==i.iframe?(c=this.$inputor.caret("offset",{iframe:i.iframe}),e=b(i.iframe).offset(),"undefined"!==e&&(c.left+=e.left,c.top+=e.top)):c=this.$inputor.caret("offset"),c.left>h.width()/2?(g.addClass("right"),f=c.left-a.left-this.view.$el.width()):(g.removeClass("right"),f=c.left-a.left+1),h.width()<=400&&b(document).scrollTop(c.top-6),d=parseInt(this.$inputor.css("line-height").substr(0,this.$inputor.css("line-height").length-2),10),(!d||d<5)&&(d=19),a.top=c.top+d,a.left+=f},inserting_wrapper:function(a,b,c){return""+b+c}}},f={callbacks:{remote_filter:function(a,c){var f=b(this),g={};return d=e[a],"object"==typeof d?void c(d):(f.xhr&&f.xhr.abort(),g={action:"bp_get_suggestions",term:a,type:"members"},b.isNumeric(this.$inputor.data("suggestions-group-id"))&&(g["group-id"]=parseInt(this.$inputor.data("suggestions-group-id"),10)),void(f.xhr=b.getJSON(ajaxurl,g).done(function(d){if(d.success){var f=b.map(d.data,function(a){return a.search=a.search||a.ID+" "+a.name,a});e[a]=f,c(f)}})))}},data:b.map(a.data,function(a){return a.search=a.search||a.ID+" "+a.name,a}),at:"@",search_key:"search",tpl:'<li data-value="@${ID}"><img src="${image}" /><span class="username">@${ID}</span><small>${name}</small></li>'},g=b.extend(!0,{},c,f,a);return b.fn.atwho.call(this,g)},b(document).ready(function(){b(".bp-suggestions, #comments form textarea, .wp-editor-area").bp_mentions(a.mentions.users)}),a.mentions.tinyMCEinit=function(){"undefined"!=typeof window.tinyMCE&&null!==window.tinyMCE.activeEditor&&"undefined"!=typeof window.tinyMCE.activeEditor&&b(window.tinyMCE.activeEditor.contentDocument.activeElement).atwho("setIframe",b(".wp-editor-wrap iframe")[0]).bp_mentions(a.mentions.users)}}(bp,jQuery);