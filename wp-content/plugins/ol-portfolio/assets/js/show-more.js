jQuery(document).ready( function( $ ) {
	var showChar = 250; // How many characters are shown by default
	var ellipsesText = "...";
	var moreText = "Show more";
	var lessText = "Show less";


	$('.show-more').each( function() {
		var content = $(this).html();

		if ( content.length > showChar ) {

			var c = content.substr( 0, showChar );
			var h = content.substr( showChar, content.length - showChar );

			var html = c + '<span class="more-ellipses">' + ellipsesText + '</span><span class="full-content"><span>' + h + '</span> <a href="" class="toggle-content">' + moreText + '</a></span>';

			$(this).html (html );
		}
	} );

	$('.toggle-content').click( function() {
		if ($(this).hasClass( 'show-less' )) {
			$(this).removeClass( 'show-less' );
			$(this).html( moreText );
		} else {
			$(this).addClass( 'show-less' );
			$(this).html( lessText );
		}

		$(this).parent().prev().toggle();
		$(this).prev().toggle();
		return false;
	} );
} );
