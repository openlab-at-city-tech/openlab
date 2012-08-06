/**
 * This is the JavaScript related to group creation. It's loaded only during the group creation
 * process.
 *
 * Added by Boone 7/7/12. Don't remove me during remediation.
 */

function showHide(id) {
  var elem = document.getElementById(id);
  if ( !elem ){
          return;
  }

  var style = elem.style
   if (style.display == "none")
	style.display = "";
   else
	style.display = "none";
}

jQuery(document).ready(function($){
	function new_old_switch( noo ) {
		var radioid = '#new_or_old_' + noo;
		$(radioid).prop('checked','checked');

		$('input.noo_radio').each(function(i,v) {
			var thisval = $(v).val();
			var thisid = '#noo_' + thisval + '_options';

			if ( noo == thisval ) {
				$(thisid).removeClass('disabled-opt');
				$(thisid).find('input').each(function(index,element){
					$(element).removeProp('disabled').removeClass('disabled');
				});
				$(thisid).find('select').each(function(index,element){
					$(element).removeProp('disabled').removeClass('disabled');
				});
			} else {
				$(thisid).addClass('disabled-opt');
				$(thisid).find('input').each(function(index,element){
					$(element).prop('disabled','disabled').addClass('disabled');
				});
				$(thisid).find('select').each(function(index,element){
					$(element).prop('disabled','disabled').addClass('disabled');
				});
			}
		});

		var efr = $('#external-feed-results');
		if ( 'external' == noo ) {
			$(efr).show();
		} else {
			$(efr).hide();
		}
	}

	function disable_gc_form() {
		var gc_submit = $('#group-creation-create');

		$(gc_submit).attr('disabled', 'disabled');
		$(gc_submit).fadeTo( 500, 0.2 );
	}

	function enable_gc_form() {
		var gc_submit = $('#group-creation-create');

		$(gc_submit).removeAttr('disabled');
		$(gc_submit).fadeTo( 500, 1.0 );
	}

	function mark_loading( obj ) {
		$(obj).before('<span class="loading" id="group-create-ajax-loader"></span>');
	}

	function unmark_loading( obj ) {
		var loader = $(obj).siblings('.loading');
		$(loader).remove();
	}

	function showHideAll() {
		showHide('wds-website');
		showHide('wds-website-existing');
		showHide('wds-website-external');
		showHide('wds-website-tooltips');
	}

	function do_external_site_query(e) {
		var euf = $('#external-site-url');
		//var euf = e.target;
		var eu = $(euf).val();

		if ( 0 == eu.length ) {
			enable_gc_form();
			return;
		}

		disable_gc_form();
		mark_loading( $(e.target) );

		$.post( '/wp-admin/admin-ajax.php', // Forward-compatibility with ajaxurl in BP 1.6
			{
				action: 'openlab_detect_feeds',
				'site_url': eu
			},
			function(response) {
				var robj = $.parseJSON(response);

				var efr = $('#external-feed-results');

				if ( 0 != efr.length ) {
					$(efr).empty(); // Clean it out
				} else {
					$('#wds-website-external').after( '<div id="external-feed-results"></div>' );
					efr = $('#external-feed-results');
				}

				if ( "posts" in robj ) {
					$(efr).append( '<p class="feed-url-tip">We found the following feed URLs for your external site, which we\'ll use to pull posts and comments into your activity stream.</p>' );
				} else {
					$(efr).append( '<p class="feed-url-tip">We couldn\'t find any feed URLs for your external site, which we use to pull posts and comments into your activity stream. If your site has feeds, you may enter the URLs below.</p>' );
				}

				var posts = "posts" in robj ? robj.posts : '';
				var comments = "comments" in robj ? robj.comments : '';
				var type = "type" in robj ? robj.type : '';

				$(efr).append( '<p class="feed-url posts-feed-url"><label for="external-posts-url">Posts:</label> <input name="external-posts-url" id="external-posts-url" value="' + posts + '" /></p>' );

				$(efr).append( '<p class="feed-url comments-feed-url"><label for="external-comments-url">Comments:</label> <input name="external-comments-url" id="external-comments-url" value="' + comments + '" /></p>' );

				$(efr).append( '<input name="external-site-type" id="external-site-type" type="hidden" value="' + type + '" />' );

				enable_gc_form();
				unmark_loading( $(e.target) );
			}
		);
	}

	$('.noo_radio').click(function(el){
		var whichid = $(el.target).prop('id').split('_').pop();
		new_old_switch(whichid);
	});

	// setup
	new_old_switch( 'new' );

	/* AJAX validation for external RSS feeds */
	$('#find-feeds').on( 'click', function(e) {
		e.preventDefault();
		do_external_site_query(e);
	} );

	/* "Set up a site" toggle */
	var setuptoggle = $('input[name="wds_website_check"]');
	$(setuptoggle).on( 'click', function(){ showHideAll(); } );
	if ( $(setuptoggle).is(':checked') ) {
		showHideAll();
	};

	/* AJAX validation for blog URLs */
	$('form input[type="submit"]').click(function(e){
                /* Don't hijack the wrong clicks */
                if ( $(e.target).attr('name') != 'save' ) {
                        return true;
                }

                /* Don't validate if a different radio button is selected */
                if ( 'new' != $('input[name=new_or_old]:checked').val() ) {
                        return true;
                }

		e.preventDefault();
		var domain = $('input[name="blog[domain]"]');

		var warn = $(domain).siblings('.ajax-warning');
		if ( warn.length > 0 ) {
			$(warn).remove();
		}

		var path = $(domain).val();
		$.post( '/wp-admin/admin-ajax.php', // Forward-compatibility with ajaxurl in BP 1.6
			{
				action: 'openlab_validate_groupblog_url_handler',
				'path': path
			},
			function(response) {
				if ( 'exists' == response ) {
					$(domain).after('<span class="ajax-warning">Sorry, that URL is already taken.</span>');
					return false;
				} else {
					var theform = $('form');
					$(theform).append('<input type="hidden" name="save" value="1" />');
					$('form').submit();
					return true;
				}
			}
		);
	});
},(jQuery));
