var AjaxResult = {};

var grouping_digressit_commentbox_parser;
	
jQuery.fn.highlight = function (str, className)
{
    return this.each(function ()
    {
        this.innerHTML = this.innerHTML.replace(
            new RegExp(str, "g"),
            "<span class=\"" + className + "\">" + str + "</span>"
        );
    });
};
	


var userAgent = navigator.userAgent.toLowerCase();

// Figure out what browser is being used
jQuery.browser = {
	version: (userAgent.match( /.+(?:rv|it|ra|ie|me)[\/: ]([\d.]+)/ ) || [])[1],
	chrome: /chrome/.test( userAgent ),
	safari: /webkit/.test( userAgent ) && !/chrome/.test( userAgent ),
	opera: /opera/.test( userAgent ),
	msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
	mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )
};

var msie=jQuery.browser.msie;
var msie6=jQuery.browser.msie && jQuery.browser.version=="6.0";
var msie7=jQuery.browser.msie && jQuery.browser.version=="7.0";
var msie8=jQuery.browser.msie && jQuery.browser.version=="8.0";
var safari=jQuery.browser.safari;
var chrome=jQuery.browser.chrome;
var mozilla=jQuery.browser.mozilla;
var iOS = navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod';


var zi=10000;
var on_load_selected_paragraph;
var window_has_focus = true;
var selected_comment_color = '#3d9ddd';
var unselected_comment_color = '#DFE4E4';
var browser_width = jQuery(window).width();
var browser_height = jQuery(window).height();
var request_time = 0;
var request_time_delay = 500; // ms - adjust as you like


jQuery(document).ready(function() {

	
	if(jQuery('.tabs').length){
		jQuery('.tabs').generate_tabs();
		
	}


	//jQuery('#dynamic-sidebar').effect("bounce", { direction: 'right', times:1 }, 1500);







	
	jQuery('.submit, .lightbox-submit').click(function(e){
		if(jQuery(e.target).hasClass('ajax')){
			//return false;
		}
		else{
			jQuery(e.target).addClass('disabled');
		}
	});
	
	
	jQuery('.input').keypress(function(e){
		//alert(e.target);
	})

	


	
	jQuery("#mainpage .navigation a").hover(function (e) {
		jQuery('#mainpage .preview').hide();
		var index = jQuery('#mainpage .navigation a').index(this) + 1;
		var item = jQuery('#mainpage .preview').get(index);			
		jQuery(item).show();
	});
	
    jQuery("#mainpage").not('.navigation a').hover(function (e) {
		jQuery('#mainpage .preview').hide();
		jQuery('#mainpage .default').show();
	});


	jQuery('.ajax').live('click', function(e) {		
		if(jQuery(this).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
			jQuery(this).css('color', '#DFE4E4');
			return;
		}
		
		var form = this;
		form = jQuery(this).parentsUntil('form').parent();		
		var form_id = jQuery(form).attr('id');

		//alert(function_name);

		var function_name = form_id;
		var form_class = jQuery(form).attr('class');
		var fields = {};

		
		jQuery('input[type=button]').attr('disabled', true);
		jQuery('input[type=submit]').attr('disabled', true);
		jQuery('.lightbox-submit').addClass('disabled');
		jQuery('.submit').addClass('disabled');
		
		jQuery('form #' + form_id + ' .loading,' + '#' + form_id + ' .loading-bars, ' + '#' + form_id + ' . loading-bar , #' + form_id + ' .loading-throbber').css('display', 'inline');
		
		jQuery.post( siteurl + "/ajax/" + form_id +'/',	jQuery("#"+form_id).serialize(),
			function( data ) {	
				
				function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

				var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';


				if(eval(dynamic_call)){
					eval('AjaxResult.' + function_name + '(data);');
				}
				else{
					
				}

				jQuery('input[type=button]').attr('disabled', false);
				jQuery('input[type=submit]').attr('disabled', false);
				jQuery('.lightbox-submit').removeClass('disabled');
				jQuery('.submit').removeClass('disabled');
				
				jQuery('.loading, .loading-bars, .loading-bar, .loading-throbber').hide();
				
			}, 'json' );
	});
	
	jQuery('.ajax-auto-submit input').live('keyup', function(e) {
		if(e.keyCode == 13 && jQuery(this).val().length > 0){
			//alert('sf');
			//return;
		}
		else{
			return;
		}

		if(request_time) {
			clearTimeout(request_time)
		}
		
		request_time = setTimeout(function(obj){
			if(jQuery(obj).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
				jQuery(obj).css('color', '#DFE4E4');
				return true;
			}
		
			var form = obj;
			form = jQuery(form).parentsUntil('form').parent();		

	
			var form_id = jQuery(form).attr('id');
			var form_class = jQuery(form).attr('class');		
			var function_name = form_id;
			var function_parameters = jQuery("#"+form_id).serialize();

			jQuery.post( siteurl + "/ajax/" + function_name +'/',	function_parameters,
				function( data ) {					
					function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

					var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
					if(eval(dynamic_call)){
						eval('AjaxResult.' + function_name + '(data);');
					}
					else{

					}
				}, 'json' );
		}, request_time_delay, this);
	});
	


	
	jQuery('.ajax-live').live('keyup', function(e) {

		if(request_time) {
			clearTimeout(request_time)
		}
		
		request_time = setTimeout(function(obj){
			if(jQuery(obj).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
				jQuery(obj).css('color', '#DFE4E4');
				return true;
			}
			
			if(!jQuery(obj).attr('class').toString().length){
				return
			}
			var ajax_simple_classes = jQuery(obj).attr('class').split(' ');
			for(var i = 0; i < ajax_simple_classes.length; i++){

				if(ajax_simple_classes[i] == 'ajax-live'){
					function_name = ajax_simple_classes[i+1];
					break;				
				}
			}

			var form_id = jQuery(obj).attr('id');
			jQuery('#' + form_id + ' .loading,' + '#' + form_id + ' .loading-bars, ' + '#' + form_id + ' . loading-bar , #' + form_id + ' .loading-throbber').css('display', 'inline');

			var function_parameters = {'value' :jQuery(obj).attr('value')};
			jQuery.post( siteurl + "/ajax/" + function_name +'/',	function_parameters,
				function( data ) {					
					function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

					var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
					if(eval(dynamic_call)){
						eval('AjaxResult.' + function_name + '(data);');
					}
					else{

					}
					
					jQuery('.loading, .loading-bars, .loading-bar, .loading-throbber').css('display', 'none');
					
				}, 'json' );
				
		}, request_time_delay, this);
	});
	
	jQuery('.ajax-simple').live('click', function (e) {
		if(jQuery(this).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
			jQuery(this).css('color', '#DFE4E4');
			return true;
		}
		
		var ajax_simple_classes = jQuery(this).attr('class').split(' ');

		for(var i = 0; i < ajax_simple_classes.length; i++){
			if(ajax_simple_classes[i] == 'ajax-simple'){
				function_name = ajax_simple_classes[i+1];
				break;				
			}
		}

		//alert(function_name);
		var function_parameters = parseGetVariables( jQuery(this).attr('value'));
		jQuery(this).css('cursor', 'wait');
		jQuery.post( siteurl + "/ajax/" + function_name +'/',	function_parameters,
			function( data ) {					
				function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

				var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
				if(eval(dynamic_call)){
					eval('AjaxResult.' + function_name + '(data);');
				}
				else{
					
				}
				
				jQuery(this).css('cursor', 'auto');
				
				
			}, 'json' );
	});
	
	
	//same as above, just a click event. hack fix for tabs not calling ajax.
	jQuery('.ajax-simple-click').click(function (e) {
		if(jQuery(this).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
			jQuery(this).css('color', '#DFE4E4');
			return true;
		}
		
		var ajax_simple_classes = jQuery(this).attr('class').split(' ');

		for(var i = 0; i < ajax_simple_classes.length; i++){
			if(ajax_simple_classes[i] == 'ajax-simple-click'){
				function_name = ajax_simple_classes[i+1];
				break;				
			}
		}

		//alert(function_name);
		var function_parameters = parseGetVariables( jQuery(this).attr('value'));
		jQuery(this).css('cursor', 'wait');
		jQuery.post( siteurl + "/ajax/" + function_name +'/',	function_parameters,
			function( data ) {					
				function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

				var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
				if(eval(dynamic_call)){
					eval('AjaxResult.' + function_name + '(data);');
				}
				else{
					
				}
				
				jQuery(this).css('cursor', 'auto');
				
				
			}, 'json' );
	});
		
	jQuery('.button-green').hover(function(){
		
		jQuery(this).css('cursor', 'pointer');
		
		
	});
	
	
	jQuery('.ajax-auto-update').live('change', function(e) {
		if(jQuery(this).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
			jQuery(this).css('color', '#DFE4E4');
			return true;
		}
		
		var ajax_simple_classes = jQuery(this).attr('class').split(' ');

		for(var i = 0; i < ajax_simple_classes.length; i++){
			if(ajax_simple_classes[i] == 'ajax-auto-update'){
				function_name = ajax_simple_classes[i+1];
				break;				
			}
		}

		var var_value;
		
		if(jQuery(this).attr('type') == 'checkbox'){
			if(jQuery(this).attr("checked")){
				var_value = jQuery(this).attr('value');				
			}
			else{
				var_value = false;
			}
		}
		else{
			 var_value = jQuery(this).attr('value');
		}
		
		var name_values = jQuery(this).attr('name') + '=' + var_value;
		//alert(name_values);
		var function_parameters = parseGetVariables(name_values);
		
		jQuery('.' + function_name + '  + .loading-throbber' + ', .' + function_name + '  + .loading-bars').css('display','inline');
		
		jQuery.post( siteurl + "/ajax/" + function_name +'/',	function_parameters,
			function( data ) {					
				function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

				jQuery('.loading, .loading-bars, .loading-bar, .loading-throbber').hide();
				var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
				if(eval(dynamic_call)){
					eval('AjaxResult.' + function_name + '(data);');
				}
				else{
					
				}
				
				
				
			}, 'json' );
	});
	
		
	function ajax_callback(function_name, data) {
		window[function_name](data);
	}
	
	function call_method (func){
	    this[func].apply(this, Array.prototype.slice.call(arguments, 1));
	}
	

	if(is_single){
		jQuery('#commentbox').position_main_elements();
	}
	
	
    jQuery(window).scroll(function () { 
	
		//this should not fire every single time! do proper checks to help performance
		if(is_single){
			jQuery('#commentbox').position_main_elements();
		}
		
    });


	jQuery(window).resize(function(){
		if(is_single){
			jQuery('#commentbox').position_main_elements();
		}
	});


	function parseGetVariables(variables) {
		var var_list = {};
		var vars = variables.split("&");
		
		for (var i=0;i<vars.length;i++) {
			var pair = vars[i].split("=");
			var_list[pair[0]] = pair[1];
		}

		return var_list;
	}
	
	//http://www.idealog.us/2006/06/javascript_to_p.html
	function getQueryVariable(variable) {
		var query = window.location.search.substring(1);
		var vars = query.split("&");
		for (var i=0;i<vars.length;i++) {
			var pair = vars[i].split("=");
			if (pair[0] == variable) {
				return pair[1];
			}
		}
		return false;
	}
	
    jQuery(".commentarea").hover(
      function () {
		var pos = jQuery('.commentarea').index(this);
		//jQuery('.paragraph_feed').eq(pos).css('visibility', 'visible');
      }, 
      function () {
		var pos = jQuery('.commentarea').index(this);
		//jQuery('.paragraph_feed').eq(pos).css('visibility', 'hidden');
      }
    );


    jQuery(".paragraph_feed").click(function () {
		var paragraph = jQuery('.paragraph_feed').index(this);
		window.location.href = '/feed/paragraphcomments/'+post_name+','+paragraph;
      }
    );

	jQuery(".embed-link").click(function (e) {
		var id = jQuery(this).attr('id').substr(11);
		var format = jQuery(this).attr('id').substr(6, 4);

		if(format == 'obje'){
			id = jQuery(this).attr('id').substr(13);
			var data = '<object style="width: 100%;" onload="this.style.height = (this.contentDocument.body.offsetHeight + 40) + \'px\'; this.style.width = (this.contentDocument.body.offsetWidth + 40) + \'px\'" class="digressit-paragraph-embed" data="' + wp_path + '?p='+ post_ID +'&digressit-embed='+ id +'"></object><a href="' + window.location.href + '#'+id+'">@</a>';
			jQuery("#textarea-embed-" + id).text(data);			
		}
		else{		
			jQuery.get(wp_path + '?p=' + post_ID +'&format=' + format + '&digressit-embed=' + id, function(data){
				jQuery("#textarea-embed-" + id).text(data);
			});
		}
	});	
	
	


	if (document.location.hash.length) {
		var lightbox = '#lightbox-' + document.location.hash.substr(1);
		jQuery('body').openlightbox(lightbox);
	}	


	if (jQuery('.lightbox-auto-load').length) {
		var lightbox = '#'+jQuery('.lightbox-auto-load:first').attr('id');
		jQuery("body").closelightbox();

		jQuery('body').openlightbox(lightbox);
	}	
	

	/* we don't want error messages being linked */
	if (document.location.hash != '#lightbox-no-ie6-support' && jQuery('#lightbox-no-ie6-support').length ) {
		jQuery('body').openlightbox('#lightbox-no-ie6-support');

	}	
	
	
	
	jQuery("#search_context").change(function (e) {		
		jQuery("#searchform").attr('action', jQuery("#search_context option:selected").val());
	});
	
	
	jQuery('.close').click(function(){
		jQuery(this).parent().hide();		
		jQuery('#block-access').hide();
	});
	
	jQuery(".insert-link").click(function (e) {
		var name = jQuery("#link_name").val();
		var link = jQuery("#link_url").val();
		jQuery("#comment").val(jQuery("#comment").val() + '<a href="'+link+'">'+name+'</a>');
		jQuery("body").closelightbox();
		
	});
	
	
	
	
	
    jQuery(".lightbox").click(function (e) {

		if(jQuery(e.target).hasClass('button-disabled') || jQuery(e.target).hasClass('disabled')){
			return false;
		}

		var target = e.target;		
		var lightbox_name = jQuery(target).attr('class').split(' ');
		
		var lightbox, i;
		for(i = 0; i < lightbox_name.length; i++){
			if(lightbox_name[i] == 'lightbox'){
				lightbox = '#' + lightbox_name[i+1];
				break;				
			}
		}
		
		jQuery('body').openlightbox(lightbox);
		
	});
	
	

	
	jQuery(".lightbox-content input[type=text]").keyup(function(event) {


		if (event.keyCode == '13') {
			//alert(event.keyCode);
			//alert(jQuery(this).attr('class'));
			if(jQuery(this).hasClass('ajax')){
				//alert('ajax');
		  		/* UNDO COMMENT: */
				//jQuery(this).add('.lightbox-submit').click();
			}
			else{
				//alert('submit');
				
				jQuery(event.target).parentsUntil('form').parent().submit();		

		  		/* UNDO COMMENT: */
		  		//jQuery(this).parent().submit();
			}
		}
	});


    jQuery(".lightbox-close, .lightbox-submit-close").click(function (e) {
		jQuery('body').closelightbox();
	});
	
	/*
    jQuery(".lightbox-submit-close").click(function (e) {

		jQuery('body').closelightbox();
	});
	*/
	
	
    jQuery(".lightbox-submit").click(function (e) {

		//alert(jQuery(this).attr('class'));
		if(jQuery(this).hasClass('ajax')){
	  		//alert(jQuery(this).parent());
		}
		else{
	  		jQuery(this).parent().submit();				
		}

	});



    jQuery(".lightbox").hover(function (e) {
		jQuery(this).css('cursor', 'pointer');
	},	
	function (e) {
		jQuery(this).css('cursor', 'auto');
	});	

    jQuery(".lightbox-images").click(function (e) {
		jQuery('#lightbox-images').css('left', '10%');
		jQuery('#lightbox-images').css('top','5%');
		
		jQuery('#lightbox-images .ribbon-title').html(jQuery(this).attr('title'));

		var imagesrc = jQuery(this).attr('src').replace(siteurl, '');
		
		jQuery('#lightbox-images .large-lightbox-image').empty();
						
		jQuery.post( baseurl + "/ajax/lightbox-image/",		
			{ blog_id: blog_ID, imagesrc: imagesrc},
			function( data ) {				
				//console.log(data);
				jQuery('#lightbox-images .large-lightbox-image').html('<img src="' +data.message + '">');			
				
			}, 'json' );	
	});
	
	
	
	var current_slot = 0;


    jQuery(".lightbox-previous").click(function (e) {
		
		if(current_slot > 0){
			current_slot--;
			jQuery('.lightbox-slot').hide();		
	      	jQuery(jQuery('.lightbox-slot').get(current_slot)).show("slide", { direction: "left" }, 200);
		}

		if(current_slot == 0){	
			jQuery('.lightbox-previous').hide();
			jQuery('.lightbox-next').show();
			jQuery('.lightbox-submit').hide();
		}
		else{
			jQuery('.lightbox-previous').show();
			jQuery('.lightbox-next').show();			
			jQuery('.lightbox-submit').hide();
		}

	});

    jQuery(".lightbox-next").click(function (e) {
		
		if(current_slot < jQuery('.lightbox-slot').length -1){		
			current_slot++;
			jQuery('.lightbox-slot').hide();
	      	jQuery(jQuery('.lightbox-slot').get(current_slot)).show("slide", { direction: "right" }, 200);
		}
		
		if(current_slot == jQuery('.lightbox-slot').length -1){	
			jQuery('.lightbox-next').hide();
			jQuery('.lightbox-previous').show();
			jQuery('.lightbox-submit').show();
		}
		else{
			jQuery('.lightbox-previous').show();
			jQuery('.lightbox-next').show();			
		}		
	});
	
	
	jQuery(".lightbubble").click(function (e) {


		if(jQuery(e.target).hasClass('button-disabled') || jQuery(e.target).hasClass('disabled')){
			return false;
		}


		var target = e.target;

		//var top = jQuery(target).offset().top;
		//var left = jQuery(target).offset().left;
	
		var lightbubble_name = jQuery(target).attr('class').split(' ');
	
		var lightbubble, i;
		for(i = 0; i < lightbubble_name.length; i++){
		
			if(lightbubble_name[i] == 'lightbubble'){
				lightbubble = '#' + lightbubble_name[i+1];
				break;				
			}
		}
		//alert(lightbubble);

		jQuery(lightbubble).appendTo(jQuery(this));
		jQuery(lightbubble).show();
		//jQuery('body').openlightbubble(lightbubble);
	
	});		
	

    jQuery(".required").change(function (e) {

		var form =jQuery(this).parentsUntil('form').parent();		

		
		var form_id = jQuery(form).attr('id');

		jQuery('#' + form_id + ' .lightbox-submit').removeClass('button-disabled');			
		
		jQuery('#' + form_id + ' .required').each(function(e){
			
			//alert(jQuery(this).attr('type') + jQuery(this).val());
			//input[type='checkbox']
			//alert(jQuery(this).attr('type') + " " + jQuery('#'+jQuery(this).attr('id') + ':checked').val());

			if( (
					(
						jQuery(this).attr('type') == 'text' && jQuery(this).val().length == 0
					) 
					|| 
					(
						( jQuery(this).attr('type') == 'radio' || jQuery(this).attr('type') == 'checkbox')  
						&& 
						( jQuery("input[name='"+jQuery(this).attr('name')+"']").is(':checked') == false )
					)
				)
			  )
			{
				jQuery('#' + form_id + ' .lightbox-submit').addClass('button-disabled');			
			}
			
		})
		

	});


	AjaxResult.add_comment = function(data) {
		var result_id = parseInt(data.message.comment_ID);



		if(data.status == 0){
			jQuery('body').displayerrorslightbox(data);
			return;
		}
		var selected_paragraph_number = parseInt(jQuery('#selected_paragraph_number').val());

		var comment_parent  = data.message.comment_parent;

		var comment_id = 'comment-' +  blog_ID + '-' + result_id;
		var parent_id = 'comment-' +  blog_ID + '-' + data.message.comment_parent;
		var depth = 'depth-1';
		if(data.message.comment_parent > 0){
			depth = 'depth-2';
		}

		var new_comment = data.message.comment_response;
		
		/*
		 '<div id="'+comment_id+'" class="new-comment comment byuser bypostauthor '+depth+' paragraph-'+selected_paragraph_number+'">' +
				'<div class="comment-body" id="div-'+comment_id+'">' +
					'<div class="comment-header">' +
					'<div class="comment-author vcard">' +
					'<a href="'+siteurl+'/profile/'+data.message.comment_author+'">'+data.message.comment_author+'</a>'+
					'</div>'+
					'<div class="comment-meta">'+
					'<span title="'+blog_ID+'" class="comment-blog-id"></span>'+
					'<span title="'+result_id+'" class="comment-id"></span>'+
					'<span title="0" class="comment-parent"></span>'+
					'<span title="'+selected_paragraph_number+'" class="comment-paragraph-number"></span>'+
					'<span class="comment-date">'+data.message.comment_date+'</span>'+
					'<span class="comment-icon comment-icon-quarantine"></span><span class="comment-icon comment-icon-flag"></span>'+
					'</div>'+
					'</div>'+
					'<div class="comment-text"><p>'+ jQuery('#comment').val() + '</p>' +
					'</div>' +
					'<div title="'+result_id+'" class="comment-reply comment-hover small-button" ></div>'+
					'<div class="comment-respond"></div>' +
				'</div>' +
			'</div>';
		*/

		jQuery('#no-comments').hide();


		/* responding to parent */
		if(comment_parent > 0){
			//we are grouping comments
			if(jQuery('#paragraph-block-' + selected_paragraph_number).length){
				jQuery('#respond').appendTo('#paragraph-block-' + selected_paragraph_number + ' .toplevel-respond');			
				jQuery('#paragraph-block-' + selected_paragraph_number).append(new_comment);
				//jQuery('#commentbox').scrollTo('#'+comment_id , 200);
				jQuery('.comment-reply').html('reply');
				jQuery('#'+comment_id).fadeIn("#"+comment_id);
				jQuery('#commentbox').scrollTo('#'+comment_id , 500, {easing:'easeOutBack'});
				
			}
			else{
				//alert('nogrouping');
				if( jQuery('#' + parent_id).next().hasClass('children') ){
					jQuery('#' + parent_id + ' + .children').prepend(new_comment);
					
					jQuery('#'+comment_id).fadeIn("#"+comment_id);
				}
				else{
					jQuery('#' + parent_id).after('<ul class="children">' + new_comment + '</ul>');					
					jQuery('#'+comment_id).fadeIn("#"+comment_id);
				}

			}
		}
		/* new thread */
		else{
			//we are grouping comments
			if(jQuery('#paragraph-block-' + selected_paragraph_number).length){
				jQuery(new_comment).appendTo('#'+ 'paragraph-block-' + selected_paragraph_number);						
				jQuery('#'+comment_id).fadeIn("#"+comment_id);
				jQuery('#commentbox').scrollTo('#'+comment_id , 500, {easing:'easeOutBack'});
			}
			else{
				//alert('nogrouping');
				jQuery('.commentlist').prepend(new_comment);			
				jQuery('#'+comment_id).fadeIn("#"+comment_id);
			}

		}


		

		//var current_count = parseInt(jQuery(jQuery('#content .commentcount').get((selected_paragraph_number ))).html());


		jQuery(jQuery('#content .commentcount').get((selected_paragraph_number -1 ))).html(data.message.paragraph_comment_count);
		jQuery(jQuery('#content .commentcount').get((selected_paragraph_number -1))).fadeIn('slow');


		jQuery(jQuery('#commentbox .commentcount').get((selected_paragraph_number))).html(data.message.paragraph_comment_count);
		jQuery(jQuery('#commentbox .commentcount').get((selected_paragraph_number))).fadeIn('slow');

		jQuery(jQuery('#digress-it-list-posts .sidebar-current .commentcount').get(0)).html(data.message.comment_count);
		jQuery(jQuery('#digress-it-list-posts .sidebar-current .commentcount').get(0)).fadeIn('slow');




		jQuery('#comment').val('');		
		jQuery('#comment_parent').val(0);
		return;
	}




	function handlePaginationClick(new_page_index, pagination_container) {
		// This selects 20 elements from a content array
		for(var i=new_page_index;i<7;i++) {
			jQuery('#commentbox').append(content[i]);
		}
		return false;
	}

	// First Parameter: number of items
	// Second Parameter: options object
/*
	jQuery("#commentbox").pagination(122, {
		items_per_page:20, 
		callback:handlePaginationClick
	});
*/


	jQuery('.comment').hover(function (e) {

		if(jQuery('body').hasClass('single')){
			return;
		}
		//alert('sdf');

		var index = jQuery('.comment').index(this);		
		if(jQuery('.comment-goto').length){
			var item = jQuery('.comment-goto').get(index);
			if(item){
				jQuery(item).show();			
			}
		}

	},	function (e) {
		if(jQuery('body').hasClass('single')){
			return;
		}

		var index = jQuery('.comment').index(this);
		if(jQuery('.comment-goto').length){
			var item = jQuery('.comment-goto').get(index);
			if(item){
				jQuery(item).hide();			
			}
		}
	});



	jQuery('.comment').click(function(e){

		var target = e.target;

		var comment =  jQuery(this).attr('id');


		var comment_id = jQuery('#' +comment + ' .comment-id').attr('title');


		jQuery.cookie('selected_comment', comment_id, { path: '/' , expires: 1} );				
		jQuery.cookie('selected_comment_id', comment_id, { path: '/' , expires: 1} );				



		jQuery('#comments-toolbar-sort').addClass('button-disabled');

		jQuery('.comment').removeClass('selected');
		jQuery('.moderate-comment').removeClass('disabled-button');

/*
		TEMPORARY DISABLE
		jQuery(this).addClass('selected');
		jQuery('#commentbox').scrollTo( jQuery(this), 1000);								
*/




		if(jQuery('body').hasClass('single')){
			var selected_blog_id = jQuery.cookie('selected_blog_id');				
			//document.location.hash = '#comment-' + selected_blog_id + '-' +comment_id;
		}
		/*

		if(jQuery(target).hasClass('comment-reply')){
			return;
		}
		*/
		//alert(jQuery(target).parents().hasClass('comment-respond') );
		if(!jQuery(target).parents().hasClass('comment-respond') && !jQuery(target).hasClass('comment-reply') && !jQuery('body').hasClass('page-template-moderator-php')){
			//alert(jQuery(target).hasClass('comment-reply'));
			//commentbox_open_state();			
		}



		if(jQuery('body').hasClass('single') && jQuery('.comment-reply').length){
			//jQuery('.comment-reply').hide();
			var index = jQuery('.comment').index(this);				
			var item = jQuery('.comment-reply').get(index);

			if(jQuery('#' + comment).hasClass('depth-3')){
				return;
			}
			else if(item){
				jQuery(item).show();			
			}
		}
	});	

	jQuery('.comment').hover(function(e){

		var selected_comment = parseInt(jQuery('#comment_parent').val());
		var current = parseInt(jQuery('.comment').index(this));	
		var item = jQuery('.comment-reply').get(current);


	}, function(e){

		var selected_comment = parseInt(jQuery('#comment_parent').val());
		var current = parseInt(jQuery('.comment').index(this));	
		var item = jQuery('.comment-reply').get(current);



	});



	/*
	jQuery('.comments-toolbar-icon').css('color', '#4A4848');

	jQuery('.comments-toolbar-icon').hover(function(e){

		jQuery(this).css('color', '#BBBBBB');
	});
	*/


	jQuery("#comment").focus(function (e) {


		jQuery("#submit-comment").show();

		//jQuery("#cancel-response").show();

		jQuery(".comment").removeClass('selected');
		//jQuery("#comment_parent").val('0');



	});

	//alert('sd');

	jQuery("#comment").focus(function (e) {
		if( jQuery(this).val() == 'Click here add a new comment...'){
			jQuery(this).val('');
		}
	});

	jQuery("#user_email").focus(function (e) {
		if( jQuery(this).val() == 'Email'){
			jQuery(this).val('');
		}
	});

	jQuery("#display_name").focus(function (e) {
		if( jQuery(this).val() == 'Your Name'){
			jQuery(this).val('');
		}
	});


	jQuery("#comment").keypress(function (e) {


		if(jQuery("#comment").val().length > 10){

			//jQuery('#submit-comment').removeClass('disabled');

		}
		else{

			//jQuery('#submit-comment').addClass('disabled');
		}


	});



	jQuery("#comments-toolbar #comment").click(function (e) {
		//jQuery('.comment-reply').hide();
	});


	jQuery("#comment").click(function (e) {
		//jQuery('form').submit();
	});




	var userAgent = navigator.userAgent.toLowerCase();

	// Figure out what browser is being used
	jQuery.browser = {
		version: (userAgent.match( /.+(?:rv|it|ra|ie|me)[\/: ]([\d.]+)/ ) || [])[1],
		chrome: /chrome/.test( userAgent ),
		safari: /webkit/.test( userAgent ) && !/chrome/.test( userAgent ),
		opera: /opera/.test( userAgent ),
		msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
		mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )
	};


	var msie7=jQuery.browser.msie && jQuery.browser.version=="7.0";
	var msie6=jQuery.browser.msie && jQuery.browser.version=="6.0";






	
	function isNumber(n) {
	  return !isNaN(parseFloat(n)) && isFinite(n);
	}

	grouping_digressit_commentbox_parser = function(data){

		jQuery('.textblock').each(function(i){
			var paragraphnumber = (i == 0) ? '&nbsp;'  : i;
			var commentlabel = (i == 0) ? ' general comment'  : ' comment';
			var commentcount = jQuery('.paragraph-' + (i)).length;
			
			commentlabel = (commentcount == 1) ? commentlabel  : commentlabel + 's';
			
			jQuery("#commentwindow").append('<div class="paragraph-block" id="paragraph-block-'+(i)+'"><div class="paragraph-block-button"><span class="paragraph-label">'+(paragraphnumber)+'</span>&nbsp; <span class="commentcount">'+commentcount+'</span> '+commentlabel+'</div><div class="toplevel-respond"></div></div>');
			
			jQuery('.paragraph-' + (i)).appendTo('#paragraph-block-'+(i));				
			
		});
		
		if(jQuery('.textblock').length > 0){

			var i = jQuery('.textblock').length;
			var paragraphnumber = (i == 0) ? '&nbsp;'  : i;
			var commentlabel = (i == 0) ? ' general comment'  : ' comment';
			var commentcount = jQuery('.paragraph-' + (i)).length;
			
			commentlabel = (commentcount == 1) ? commentlabel  : commentlabel + 's';
			
			jQuery("#commentwindow").append('<div class="paragraph-block" id="paragraph-block-'+(i)+'"><div class="paragraph-block-button"><span class="paragraph-label">'+(paragraphnumber)+'</span>&nbsp; <span class="commentcount">'+commentcount+'</span> '+commentlabel+'</div><div class="toplevel-respond"></div></div>');
			
			jQuery('.paragraph-' + (i)).appendTo('#paragraph-block-'+(i));				

		}
	}


	if(typeof(commentbox_function) !== 'undefined'){
		var dynamic_call = 'typeof(' + commentbox_function + ') != "undefined"';
		if(eval(dynamic_call)){
			eval(commentbox_function + '();');
		}
	}


	var comment_linked;

	jQuery('.comment').click(function(e){
		var index = jQuery('.comment').index(this);

		//comment-id
		var selected_blog_id = jQuery(jQuery('.comment .comment-blog-id').get(index)).attr('value');
		var selected_comment_id = jQuery(jQuery('.comment .comment-id').get(index)).attr('value');

		jQuery.cookie('selected_comment_id', null, { path: request_uri, expires: 1} );
		jQuery.cookie('selected_blog_id', null, { path: request_uri + '/', expires: 1} );

		jQuery.cookie('selected_comment_id', selected_comment_id, { path: '/', expires: 1} );
		jQuery.cookie('selected_blog_id', selected_blog_id, { path: '/', expires: 1} );
	});

	function expand_comment_area(item, paragraphnumber){
		jQuery('.textblock').removeClass('selected-textblock');
		jQuery('.commenticonbox').removeClass('selected-paragraph');
		jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');
		jQuery('#textblock-' + paragraphnumber + ' .commenticonbox').addClass('selected-paragraph');
		jQuery('.comment').removeClass('selected');	
		jQuery('#selected_paragraph_number').val(paragraphnumber);
		jQuery("#no-comments").hide();
		
		var no_comments = true;

		jQuery(".comment").hide();
		jQuery("#respond").show();
		jQuery('textblock-' +paragraphnumber).addClass('selected-textblock');	

		var selectedparagraph  = ".paragraph-" + paragraphnumber;
		
		if(jQuery(selectedparagraph).length){
			jQuery(selectedparagraph).show();
		}
		else{
			if(jQuery('.comment').length){
				jQuery("#no-comments").show();
			}			
		}
	}

	
	/****************************************************
	*	only if we are grouping the paragraphs 
	****************************************************/

	if(jQuery('.paragraph-block').length){

		//* this only happens when we are using the standard theme */
		if (isNumber(document.location.hash.substr(1))) {
			var paragraphnumber = document.location.hash.substr(1);
			if(paragraphnumber > jQuery('.textblock').length){
				return;
			}
			jQuery('.paragraph-'+(paragraphnumber)).show();			
			jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber));
		}

		jQuery("#cancel-response").click(function (e) {
			//jQuery('#comment_parent').val(0);
			jQuery('#comment').val('Click here add a new comment...');
		});


		jQuery("#menu ul li").click(function (e) {
			jQuery('#comment_parent').val(0);
			jQuery('#comment').val('Click here add a new comment...');

			jQuery('#submit-comment').hide();
			jQuery('#cancel-response').hide();

			jQuery('.textblock').removeClass('selected-textblock');
			jQuery('.comment'  ).hide();
			jQuery('#respond').hide();						
			jQuery('#selected_paragraph_number').val(0);
		});
		//jQuery('<li class="page_item"><input class="live-post-search" type="text" value="Search"></li>').appendTo('.menu ul');
		
		
		jQuery('.textblock').click(function(e){

			if(open_if_linked_in_paragraph(e)){
				return;
			}
			var paragraphnumber = parseInt(jQuery('.textblock').index(this)) +1 ;
	
	
			//alert(jQuery('#selected_paragraph_number').val());
			if(parseInt(jQuery('#selected_paragraph_number').val()) == paragraphnumber){

				/* PARAGRAPH BLOCKS - UNSELECTED */
				if(jQuery('.paragraph-block').length){
					jQuery('.textblock').removeClass('selected-textblock');
					jQuery('.comment').hide();
					jQuery('#respond').hide();
					jQuery("#no-comments").hide();				
					jQuery('#selected_paragraph_number').val(0);
				}
				/* ALL COMMENTS - UNSELECTED */
				else{
					if(jQuery('.comment' ).length){
						jQuery("#no-comments").hide();				
						jQuery(".comment").show();				
					}
					else{
						jQuery("#no-comments").show();
					}
					jQuery('.textblock').removeClass('selected-textblock');
					jQuery('#selected_paragraph_number').val(0);
					jQuery('#respond').appendTo(jQuery('#toplevel-commentbox'));						
				}	
			}
	
			else{
		
				/* PARAGRAPH BLOCKS - SELECTED */
				if(jQuery('.paragraph-block').length){
					jQuery('#respond').hide();			

					jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber) + ' .toplevel-respond');
					jQuery('#respond').show();			
					jQuery('.comment' ).hide();
					jQuery('.paragraph-' + paragraphnumber ).show();
					jQuery("#no-comments").hide();				

					jQuery('.textblock').removeClass('selected-textblock');
					var commentboxtop = jQuery('#commentbox').position().top;

					if(paragraphnumber > 0){
						jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');

						var top = parseInt(jQuery('#textblock-' + paragraphnumber).offset().top);
						var scrollto = (top > 200)  ? (top - 100) : 0;

						if(iOS){
							jQuery('#commentbox').position_main_elements();						
						}
				
						jQuery(window).scrollTo(scrollto , 100);


						jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 500, {easing:'easeOutBack'});
					}
					jQuery('#selected_paragraph_number').val(paragraphnumber);
		
					document.location.hash = '#' + paragraphnumber;

				}
				else{
					/* ALL COMMENTS - SELECTED */

					jQuery('#respond').hide();			
					var paragraphnumber = parseInt(jQuery('.textblock').index(this)) +1 ;

					jQuery('#respond').appendTo('#toplevel-commentbox');
					jQuery('#respond').show();			
					jQuery('.comment' ).hide();
					jQuery('.paragraph-' + paragraphnumber ).show();


					jQuery('#submit-comment').removeClass('disabled');

					jQuery('.textblock').removeClass('selected-textblock');
					jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');

					jQuery('#selected_paragraph_number').val(paragraphnumber);


					if(jQuery('.paragraph-' + paragraphnumber ).length){
						jQuery("#no-comments").hide();				
					}
					else{
						jQuery("#no-comments").show();
					}

					var top = jQuery('#textblock-' + paragraphnumber).offset().top;
					var scrollto = (top > 200)  ? (top - 35) : 0;

					jQuery(window).scrollTo(scrollto , 100);
					document.location.hash = '#' + paragraphnumber;
				}
			}

		});
	}


		

	jQuery('.paragraph-block-button').toggle(function(e){
		jQuery('.comment').hide();

		var paragraphnumber = parseInt(jQuery('.paragraph-block-button').index(this));
		jQuery('#selected_paragraph_number').val(paragraphnumber);
		jQuery('.paragraph-' + paragraphnumber).show();
		jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber) + ' .toplevel-respond');
		jQuery('#respond').show();			

		jQuery('.textblock').removeClass('selected-textblock');

		if(paragraphnumber > 0){

			var top = parseInt(jQuery('#textblock-' + paragraphnumber).offset().top);
			jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');
		
			var scrollto = (top > 200)  ? (top - 100) : 0;
			jQuery(window).scrollTo(scrollto , 200);
			jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 500, {easing:'easeOutBack'});
		}
		
	}, function(e){
		jQuery('.comment').hide();
		jQuery('#respond').hide();
		jQuery('.textblock').removeClass('selected-textblock');
		jQuery('#selected_paragraph_number').val(0);
	});



	function open_if_linked_in_paragraph(e){
		
		if(jQuery(e.target).attr('target') && jQuery(e.target).attr('href')){
			window.open(jQuery(e.target).attr('href').toString());				
			return 1;
		}
		else if(jQuery(e.target).attr('href')){
			window.location = jQuery(e.target).attr('href').toString();
			return 1;
		}
		else if(jQuery(e.target).parent().attr('href')){

			if(jQuery(e.target).parent().attr('target') && jQuery(e.target).parent().attr('href')){
				window.open(jQuery(e.target).parent().attr('href').toString());								
			}
			else{
				window.location = jQuery(e.target).parent().attr('href').toString();				
			}

			return 1;
		}
		
		return 0;
	}

	if ( document.location.hash.substr(1, 7) == 'comment') {
		var commentname = document.location.hash.substr(1);

		var comment_info = commentname.split('-');

		if(comment_info.length == 2){
			commentname = 'comment-' + blog_ID + '-'+ comment_info.pop(); 
		}
		
		var paragraphnumber = jQuery(jQuery('#'+commentname + ' .comment-paragraph-number').get(0)).attr('value');
		
		jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber) + ' .toplevel-respond');
		jQuery('#respond').show();
		jQuery('.comment').hide();
		jQuery('.paragraph-' + paragraphnumber).show();
		
		jQuery('#selected_paragraph_number').attr('value', paragraphnumber );
		
		
		if(jQuery('.paragraph-' + paragraphnumber).length == 0){
			jQuery('#no-comments').show();			
		}
		else{
			jQuery('#no-comments').hide();
		}
		
				
		jQuery('#commentbox').scrollTo('#'+commentname , 500);
		
		if(paragraphnumber > 0){
			//alert('sdf2');		
				
			var item = jQuery('.commenticonbox').get((paragraphnumber));
			var top = parseInt(jQuery('#textblock-' + paragraphnumber).offset().top);
			jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');

			var scrollto = (top > 200)  ? (top - 100) : 0;
			jQuery(window).scrollTo(scrollto , 500);
		}

	}
	else if ( document.location.hash.substr(1, 13) == 'search-result') {
		jQuery(window).scrollTo( jQuery('.search-result:first'), 1000);
	}
	else if (isNumber(document.location.hash.substr(1))) {
		var paragraphnumber = document.location.hash.substr(1);
		var scrollto;
		if(paragraphnumber > jQuery('.textblock').length){
			return;
		}
		
		if(paragraphnumber > 0){
		
			var item = jQuery('.commenticonbox').get((paragraphnumber));
			var top = parseInt(jQuery('#textblock-' + paragraphnumber).offset().top);

			jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber) + ' .toplevel-respond');
			jQuery('#respond').show();
			jQuery('.comment').hide();
			jQuery('.paragraph-' + paragraphnumber).show();
			jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');
			//jQuery('#selected_paragraph_number').attr('value', paragraphnumber );
			jQuery('#selected_paragraph_number').val(paragraphnumber);
			

			/*
			if(jQuery('.paragraph-' + paragraphnumber).length == 0){
				jQuery('#no-comments').show();			
			}
			else{
			}
			*/
			jQuery('#no-comments').hide();
			
		
			scrollto = (top > 200)  ? (top - 100) : 0;
		
			if(jQuery('#paragraph-block-' + paragraphnumber).length){
				jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 500);
			}
			
			if(iOS){
				jQuery(window).scrollTo(scrollto , 0);
				jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 1000, {easing:'easeOutBack'});				
				jQuery('#commentbox').position_main_elements();						
			}
			else{
				jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 1000, {easing:'easeOutBack'});				
				jQuery(window).scrollTo(scrollto  , 500);
				
			}
			
		}

		
	}
	else{
		if( parseInt(jQuery('.comment').length) == 0){
			jQuery("#no-comments").show();			
		}
		else{
			jQuery("#no-comments").hide();	
		}	
	}



	AjaxResult.live_content_search = function(data) {
		jQuery('#live-content-search-result').empty();
		jQuery('#live-content-search-result').html(data.message);
		jQuery('#live-content-search-result').fadeIn();
	}
	
	
	jQuery('#live-content-search').focus(function(){
		if(jQuery('#live-content-search').val() == 'Search Content'){
			jQuery('#live-content-search').val('');
		}
	});
	
	
	AjaxResult.live_comment_search = function(data) {
		jQuery('#live-comment-search-result').empty();
		jQuery('#live-comment-search-result').html(data.message);
		jQuery('#live-comment-search-result').fadeIn();
	}
	
	jQuery('#live-comment-search').focus(function(){
		if(jQuery('#live-comment-search').val() == 'Search Comments'){
			jQuery('#live-comment-search').val('');
		}
	});


	

	jQuery('body').click(function (e) {

		//alert(jQuery(e.target).attr('class'));
		if(!jQuery(e.target).hasClass('ajax-live')){
				jQuery('#live-content-search-result').hide();
				jQuery('#live-comment-search-result').hide();
		}
		
	});

	//jQuery.cookie('text_signature', null, { path: '/' , expires: 1} );				
	if(jQuery("#dynamic-sidebar").hasClass('sidebar-widget-auto-hide')){

		if(jQuery("#dynamic-sidebar").hasClass('sidebar-widget-position-right')){

			jQuery("#dynamic-sidebar").hover(function (e) {
				var t = setTimeout(function() {
					jQuery('#dynamic-sidebar').animate({ 
						right: "0px"
					}, 100 ); }, 200);
				jQuery(this).data('timeout', t);
			},function () {
			    clearTimeout(jQuery(this).data('timeout'));
				jQuery('#dynamic-sidebar').animate({ 
					right: "-260px"
				}, 100 );
			});
			
		}
		else{

			jQuery("#dynamic-sidebar").hover(function (e) {
				var t = setTimeout(function() {
					jQuery('#dynamic-sidebar').animate({ 
						left: "0px"
					}, 200 ); }, 300);
				jQuery(this).data('timeout', t);
			},function () {
			    clearTimeout(jQuery(this).data('timeout'));
				jQuery('#dynamic-sidebar').animate({ 
					left: "-260px"
				}, 100 );
			});
			
		}
	
	}


	jQuery('.comment-reply').click(function (e) {

		var top = 0;
		var comment_id = jQuery(this).attr('value');
		var current_comment_id = '#comment-'+ blog_ID +'-'+comment_id;		
		var paragraphnumber = jQuery(current_comment_id + ' .comment-paragraph-number').attr('value');
		var comment_id = jQuery(current_comment_id + ' .comment-id').attr('value');
		var blog_id = jQuery(current_comment_id + ' .comment-blog-id').attr('value');

		var selected_paragraphnumber = jQuery('#selected_paragraph_number').attr('value');

		if(jQuery('#comment_parent').val() == 0){


			jQuery('#selected_paragraph_number').attr('value', paragraphnumber);
			jQuery('#comment_parent').val(comment_id);

			//alert(jQuery('#comment_parent').val());
			jQuery.cookie('text_signature', paragraphnumber, { path: '/' , expires: 1} );				
			jQuery.cookie('selected_comment_id', comment_id, { path: '/' , expires: 1} );				

			var item = jQuery('.commenticonbox').get(parseInt(jQuery('.commenticonbox').index(this)));

			jQuery('.textblock').removeClass('selected-textblock');
			jQuery('.commenticonbox').removeClass('selected-paragraph');

			if(paragraphnumber > 0){
				jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');
				jQuery('#textblock-' + paragraphnumber + ' .commenticonbox').addClass('selected-paragraph');

				var textblockname = "#textblock-" + paragraphnumber;
				var textblock = jQuery(textblockname);

				var left = textblock.position().left;
				top = textblock.position().top;

			}			
			var commentbox = jQuery("#commentbox");

			var scrollto = (top - 100);
			jQuery('#respond').appendTo(current_comment_id + ' .comment-respond');		

			jQuery(window).scrollTo(scrollto, 200);
		
			jQuery('#commentbox').scrollTo( current_comment_id + ' .comment-reply', 500, {easing:'easeOutBack'});

			document.location.hash = '#' + paragraphnumber;


			jQuery('.comment .comment-reply').html('reply');
			jQuery(current_comment_id + ' .comment-reply').html('cancel response');

			jQuery(this).addClass('cancel-response');
		}
		else{


			jQuery('#comment_parent').val(0);
			jQuery('.comment-reply').html('reply');
			if(jQuery('.paragraph-block').length){
				jQuery('#respond').appendTo('#paragraph-block-'+(selected_paragraphnumber) + ' .toplevel-respond');			
			}
			else{	
				jQuery('#respond').appendTo('#toplevel-commentbox');
			}
			jQuery(this).removeClass('cancel-response');


		}
	
	});

	
});



jQuery.fn.extend({
	generate_tabs: function(type, options) {
		//Default Action


		jQuery('ul.tabs').each(function(item){
			var tab_id = jQuery(this).attr('id');			
			//alert(tab_id);
			jQuery("#" +tab_id +" + .tab-container .tab-content").hide();					//Hide all content
			jQuery("#" +tab_id +"  li:first").addClass("active").show();	//Activate first tab
			jQuery(".tab-container  .tab-content:first").show();				//Show first tab content
		});

		//On Click Event
		jQuery("ul.tabs li").click(function() {

			var tab_id = jQuery(this).parent().attr('id');		


			jQuery("#"+tab_id+ " li").removeClass("active"); //Remove any "active" class
			jQuery(this).addClass("active"); //Add "active" class to selected tab

			jQuery("#"+tab_id+ " + .tab-container .tab-content").hide(); //Hide all tab content
			jQuery("." +tab_id+" .tab-content").hide(); //Hide all tab content

			
			var activeTab = jQuery(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
			jQuery(activeTab).show();
			//alert(activeTab);
			return false;
		});
	},
	

	position_main_elements: function() {



		var default_top = parseInt(jQuery('#content').position().top);
		var scroll_top =  parseInt(jQuery(window).scrollTop());
		var top =  default_top  + parseInt(jQuery(window).scrollTop());
		var min_browser_height = (browser_height > 300) ? browser_height : 300; 
		var new_commentbox_height = ((browser_height - default_top - 50) < 370) ? 370 : (browser_height - default_top - 50);
		var commentbox_top = parseInt(jQuery(jQuery(".entry").get(0)).offset().top);
		
	//	alert(commentbox_top);

		jQuery('#commentbox').css('top',  commentbox_top + 'px' );
		jQuery('#commentbox').css('height', new_commentbox_height + 'px');



		var left = parseInt(jQuery('#content').offset().left) + parseInt( jQuery(jQuery('.entry').get(0)).width() ) + parseInt( jQuery(jQuery('.post').get(0)).css('padding-right') );

		if(safari || chrome){
			//left = left + 210;
		}


		var sidebar_fix_point = parseInt(jQuery("#header").outerHeight())  + parseInt(jQuery("#header").css('margin-top'));
		var commentbox_fix_point = commentbox_top;
		var comment_header = parseInt(jQuery("#commentbox-header").outerHeight());
		var header_outerheight = parseInt(jQuery("#header").outerHeight());
		
		
		/*
		var debug_data = 'top: ' + top + '<br>'+
						'commentbox_top:' + commentbox_top + '<br>'+
						'header_outerheight: ' + header_outerheight + '<br>' +
						'header_outerheight: ' + header_outerheight + '<br>' +
						'scroll_top' +  scroll_top;
						
						
		jQuery("#debug-message").html(debug_data);
		*/
		//iOS
		if(iOS){

			var ipad_scroll_top_position;
			
			ipad_scroll_top_position = (top < 360) ? 260: (top - 100) ;

			//alert(top);
			jQuery("#commentbox-header").css('position',  'absolute');			
			jQuery("#commentbox-header").css('top', ipad_scroll_top_position);			

			jQuery("#commentbox").css('position',  'absolute');			
			jQuery("#commentbox").css('top', ipad_scroll_top_position);
			
			jQuery("#dynamic-sidebar").css('position',  'absolute');			
			jQuery("#dynamic-sidebar").css('top',  ipad_scroll_top_position);			
			
			
		}
		else{


			//sidebar
			if(scroll_top > sidebar_fix_point){
				jQuery("#dynamic-sidebar").css('position',  'fixed');			
				jQuery("#dynamic-sidebar").css('top',  '0px');			
			}
			else{
				jQuery("#dynamic-sidebar").css('position',  'absolute');
				jQuery("#dynamic-sidebar").css('top',  header_outerheight + 'px');			
			}


			//commentbox
			if(scroll_top > (commentbox_fix_point - header_outerheight) ){

				jQuery("#commentbox-header").css('position',  'fixed');			
				jQuery("#commentbox-header").css('top',  header_outerheight+ 'px');

				jQuery("#commentbox").css('position',  'fixed');			
				jQuery("#commentbox").css('top',  (header_outerheight  + comment_header) + 'px');
			}
			else{
				jQuery("#commentbox-header").css('position',  'absolute');
				jQuery("#commentbox-header").css('top',  commentbox_top + 'px');			

				jQuery("#commentbox").css('position',  'absolute');
				jQuery("#commentbox").css('top',  (commentbox_top +  comment_header) + 'px');			
			}

			
		}
		


		
		
		jQuery('#commentbox,#commentbox-header').css('left', left + 'px');
		jQuery('#commentbox,#commentbox-header').css('display', 'block');
		

	}	
	
});


jQuery.fn.openlightbox = function (lightbox){
	if(jQuery(lightbox).length){
		jQuery('.lightbox-content').hide();
		var browser_width = jQuery(window).width();
		var browser_height = jQuery(window).height();
		var body_width = jQuery('#wrapper').width();
		var body_height = jQuery('#wrapper').height();


		jQuery('.lightbox-submit').removeClass('disabled');

		jQuery('.lightbox-transparency').css('width', body_width  + 'px');
		jQuery('.lightbox-transparency').css('height', ( body_height + 70 )+ 'px');
		jQuery('.lightbox-transparency').fadeTo(0, 0.20);				

		var left = (parseInt(browser_width) -  parseInt((jQuery(lightbox).width())))/2.5;
		var top = (parseInt(browser_height) -  parseInt((jQuery(lightbox).height())))/3;
	
		if(top < 45){
			top = 45;			
		}
		if(left < 100){
			left = 100;
		}
		
		jQuery(lightbox).css('left', left);			
		jQuery(lightbox).css('top', top);			
		
		jQuery('input[type=button]').attr('disabled', false);
		jQuery('input[type=submit]').attr('disabled', false);
		jQuery('input[type=text]').attr('readonly', '');
		jQuery('select').attr('disabled', false);
		jQuery('textarea').attr('readonly', '');
		

		//alert(jQuery(lightbox + ' .lightbox-slot').length);
		if(jQuery(lightbox + ' .lightbox-slot').length > 1){
			jQuery(lightbox + ' .lightbox-slot').hide();
			jQuery(lightbox + ' .lightbox-previous').hide();
			jQuery(lightbox + ' .lightbox-submit').hide();

			//jQuery(jQuery(lightbox + ' .lightbox-slot').get(0)).css('position','relative');
			//jQuery(lightbox + ' .lightbox-slot').hide();
			jQuery(jQuery(lightbox + ' .lightbox-slot').get(0)).show();
			
		}
		
		jQuery(lightbox).fadeIn();
		
		if(jQuery(lightbox + ' .lightbox-delay-close').length){
			var t = setTimeout(function() {
				jQuery("body").closelightbox();
 			}, 3000);
			jQuery(this).data('timeout', t);			
		}		
	}
	else{
		//console.log(lightbox + ' not found ');		
	}

}




jQuery.fn.closelightbox = function (){
	jQuery('.lightbox-content').hide();
	jQuery('.lightbox-transparency').css('width', 0);
	jQuery('.lightbox-transparency').css('height', 0);
	document.location.hash.length = '';
}

jQuery.fn.displayerrorslightbox = function (data){
	if(data.status == 0){
		var lightbox = '#lightbox-generic-response';
		jQuery(lightbox + ' > p').html(data.message);
		jQuery('body').openlightbox(lightbox);	
	}
}







function commentbox_closed_state(){
	//jQuery('#respond').appendTo('#comments-toolbar');
	jQuery('#comment_parent').val(0);
	jQuery('#comment').val('Discuss Here...');
	jQuery('#commentbox').css('overflow-y', 'scroll');
	//jQuery('#submit-comment').css('display', 'none');

	jQuery('#comments-toolbar').show();
}

function commentbox_reply_state(){
	
	//alert(selected_comment_id);
	var selected_comment_id = jQuery.cookie('selected_comment_id');				
	var selected_blog_id = jQuery.cookie('selected_blog_id');				
	
	//alert(selected_comment_id);
	var reply_box = '#comment-'+ selected_blog_id + '-' +selected_comment_id + ' .comment-respond';
	
	//alert(reply_box);
	//jQuery(reply_box).hide();
	jQuery('#respond').appendTo(reply_box);		
	jQuery('#respond').fadeIn();
	jQuery('.reply_box').show();
	jQuery('#submit-comment').show();	
}


function commentbox_open_state(){
	//jQuery('#respond').appendTo('#comments-toolbar');
	jQuery('#comment_parent').val(0);
	jQuery('#comment').val('Discuss Here...');
	jQuery('#commentbox').css('overflow-y', 'scroll');
	jQuery('#comment').removeClass('comment-expanded');
	jQuery('#comment').addClass('comment-collapsed');
}

function commentbox_expanded_state(){
	//jQuery('#respond').appendTo('#comments-toolbar');
	jQuery('#comment_parent').val(0);
	jQuery('#comment').val('');
	jQuery('#commentbox').css('overflow-y', 'scroll');
}

