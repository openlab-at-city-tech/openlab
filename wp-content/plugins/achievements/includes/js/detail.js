jQuery(document).ready(function($) {

	var button = $("#content li.load-more");
	if ( button.attr('class') == 'load-more' ) {
		button.removeClass('load-more');
		button.addClass('achievements-load-more');
	}

	$(".achievements-load-more").live('click', function(event){
		if ( null == $.cookie('bp-activity-oldestpage') )
			$.cookie('bp-activity-oldestpage', 1, {path: '/'} );

		var button = $(this);
		var oldest_page = ( $.cookie('bp-activity-oldestpage') * 1 ) + 1;
		button.addClass('loading');

		$.post( ajaxurl, {
			action: 'dpa_activity_get_older_updates',
			'cookie': encodeURIComponent(document.cookie),
			'page': oldest_page
		},
		function(response)
		{
			button.remove();
			$.cookie( 'bp-activity-oldestpage', oldest_page, {path: '/'} );
			$("#content ul.activity-list").append(response.contents);
		},
		'json' );
	});

});