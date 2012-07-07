/**
 * This is the JavaScript related to group creation. It's loaded only during the group creation
 * process.
 *
 * Added by Boone 7/7/12. Don't remove me during remediation.
 */

function showHide(id) {
  var style = document.getElementById(id).style
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

	$('.noo_radio').click(function(el){
		var whichid = $(el.target).prop('id').split('_').pop();
		new_old_switch(whichid);
	});

	// setup
	new_old_switch( 'new' );

	/* AJAX validation for external RSS feeds */
	var esu = $('#external-site-url');

	$(esu).on( 'focus', function() { disable_gc_form() } );

	$(esu).on( 'blur', function(e) {
		var euf = e.target;
		var eu = $(euf).val();

		if ( 0 == eu.length ) {
			enable_gc_form();
			return;
		}

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
			}
		);
	} );
},(jQuery));