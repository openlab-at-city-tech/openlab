jQuery(document).ready(function(){jQuery("a.confirm").click(function(){return!!confirm(BP_Confirm.are_you_sure)})});;function member_widget_click_handler(){jQuery(".widget div#members-list-options a").on("click",function(){var a=this;return jQuery(a).addClass("loading"),jQuery(".widget div#members-list-options a").removeClass("selected"),jQuery(this).addClass("selected"),jQuery.post(ajaxurl,{action:"widget_members",cookie:encodeURIComponent(document.cookie),_wpnonce:jQuery("input#_wpnonce-members").val(),"max-members":jQuery("input#members_widget_max").val(),filter:jQuery(this).attr("id")},function(b){jQuery(a).removeClass("loading"),member_widget_response(b)}),!1})}function member_widget_response(a){a=a.substr(0,a.length-1),a=a.split("[[SPLIT]]"),"-1"!==a[0]?jQuery(".widget ul#members-list").fadeOut(200,function(){jQuery(".widget ul#members-list").html(a[1]),jQuery(".widget ul#members-list").fadeIn(200)}):jQuery(".widget ul#members-list").fadeOut(200,function(){var b="<p>"+a[1]+"</p>";jQuery(".widget ul#members-list").html(b),jQuery(".widget ul#members-list").fadeIn(200)})}jQuery(document).ready(function(){member_widget_click_handler(),"undefined"!=typeof wp&&wp.customize&&wp.customize.selectiveRefresh&&wp.customize.selectiveRefresh.bind("partial-content-rendered",function(){member_widget_click_handler()})});;function bp_get_querystring(a){var b=location.search.split(a+"=")[1];return b?decodeURIComponent(b.split("&")[0]):null};jQuery(document).ready( function($) {

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
;window.bp=window.bp||{},function(a,b,c){var d,e=[];a.mentions=a.mentions||{},a.mentions.users=window.bp.mentions.users||[],"object"==typeof window.BP_Suggestions&&(a.mentions.users=window.BP_Suggestions.friends||a.mentions.users),b.fn.bp_mentions=function(a){b.isArray(a)&&(a={data:a});var c={delay:200,hide_without_suffix:!0,insert_tpl:"</>${atwho-data-value}</>",limit:10,start_with_space:!1,suffix:"",callbacks:{filter:function(a,b,c){var d,e,f,g=[],h=new RegExp("^"+a+"| "+a,"ig");for(e=0,f=b.length;e<f;e++)d=b[e],d[c].toLowerCase().match(h)&&g.push(d);return g},highlighter:function(a,b){if(!b)return a;var c=new RegExp(">(\\s*|[\\w\\s]*)("+this.at.replace("+","\\+")+"?"+b.replace("+","\\+")+")([\\w ]*)\\s*<","ig");return a.replace(c,function(a,b,c,d){return">"+b+"<strong>"+c+"</strong>"+d+"<"})},before_reposition:function(a){var c,d,e,f,g=b("#atwho-ground-"+this.id+" .atwho-view"),h=b("body"),i=this.$inputor.data("atwho");"undefined"!==i&&"undefined"!==i.iframe&&null!==i.iframe?(c=this.$inputor.caret("offset",{iframe:i.iframe}),e=b(i.iframe).offset(),"undefined"!==e&&(c.left+=e.left,c.top+=e.top)):c=this.$inputor.caret("offset"),c.left>h.width()/2?(g.addClass("right"),f=c.left-a.left-this.view.$el.width()):(g.removeClass("right"),f=c.left-a.left+1),h.width()<=400&&b(document).scrollTop(c.top-6),d=parseInt(this.$inputor.css("line-height").substr(0,this.$inputor.css("line-height").length-2),10),(!d||d<5)&&(d=19),a.top=c.top+d,a.left+=f},inserting_wrapper:function(a,b,c){return""+b+c}}},f={callbacks:{remote_filter:function(a,c){var f=b(this),g={};return d=e[a],"object"==typeof d?void c(d):(f.xhr&&f.xhr.abort(),g={action:"bp_get_suggestions",term:a,type:"members"},b.isNumeric(this.$inputor.data("suggestions-group-id"))&&(g["group-id"]=parseInt(this.$inputor.data("suggestions-group-id"),10)),void(f.xhr=b.getJSON(ajaxurl,g).done(function(d){if(d.success){var f=b.map(d.data,function(a){return a.search=a.search||a.ID+" "+a.name,a});e[a]=f,c(f)}})))}},data:b.map(a.data,function(a){return a.search=a.search||a.ID+" "+a.name,a}),at:"@",search_key:"search",tpl:'<li data-value="@${ID}"><img src="${image}" /><span class="username">@${ID}</span><small>${name}</small></li>'},g=b.extend(!0,{},c,f,a);return b.fn.atwho.call(this,g)},b(document).ready(function(){b(".bp-suggestions, #comments form textarea, .wp-editor-area").bp_mentions(a.mentions.users)}),a.mentions.tinyMCEinit=function(){"undefined"!=typeof window.tinyMCE&&null!==window.tinyMCE.activeEditor&&"undefined"!=typeof window.tinyMCE.activeEditor&&b(window.tinyMCE.activeEditor.contentDocument.activeElement).atwho("setIframe",b(".wp-editor-wrap iframe")[0]).bp_mentions(a.mentions.users)}}(bp,jQuery);