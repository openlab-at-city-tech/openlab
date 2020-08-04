(function($) {
	var $action = $('.ol-private-comment');

	$action.on( 'click', 'button', function(event) {
		event.preventDefault();

		$button = $(this);
		$.post( ajaxurl, {
			id: $(this).data('comment-id'),
			is_private: $(this).data('is-private'),
			action: 'openlab_private_comments',
			_ajax_nonce: olPrivateComments.nonce,
		}, function( response ) {
			if ( response.success ) {
				$button.data( 'is-private', response.data.is_private );
				$button.html( response.data.label );
			}
		} );
	} );
})(jQuery);
