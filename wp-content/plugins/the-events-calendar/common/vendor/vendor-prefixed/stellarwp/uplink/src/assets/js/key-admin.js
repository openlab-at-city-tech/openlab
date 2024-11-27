( function( $, obj ) {
	obj.init = function() {

		$('a.uplink-authorize').on( 'click', function (e) {
			if ( $( this ).attr('disabled') === 'disabled' ) {
				e.preventDefault();
			}
		} );

		$( '.stellarwp-uplink-license-key-field' ).each( function() {
			var $el = $( this );
			var slug = $el.data( 'plugin-slug' );
			var $field = $el.find( 'input[type="text"]' );
			var $oauth = $el.find( `a[data-plugin-slug="${slug}"]`);

			if ( '' === $field.val().trim() ) {
				$el.find( '.license-test-results' ).hide();
				obj.disableAuthorizeButton( $oauth );
			}

			obj.validateKey( $el );
		} );

		$( document ).on( 'change', '.stellarwp-uplink-license-key-field', function() {
			const $el = $( this );
			obj.validateKey( $el );
		} );
	};

	obj.disableAuthorizeButton = function( $button ) {
		$button.attr( 'aria-disabled', 'true' );
		$button.attr( 'disabled', 'disabled' );
	}

	obj.enableAuthorizeButton = function( $button ) {
		$button.removeAttr( 'aria-disabled' );
		$button.removeAttr( 'disabled' );
	}

	obj.validateKey = function( $el ) {
		const field          = $el.find( 'input[type="text"]' )
		const action         = $el.data( 'action' );
		const slug           = $el.data( 'plugin-slug' );
		const $oauth         = $el.find( `a[data-plugin-slug="${slug}"]`);
		let $validityMessage = $el.find( '.key-validity' );

		if ( '' === field.val().trim() ) {
			obj.disableAuthorizeButton( $oauth );
			return;
		}

		$( $el ).find( '.license-test-results' ).show();
		$( $el ).find( '.tooltip' ).hide();
		$( $el ).find( '.ajax-loading-license' ).show();

		$validityMessage.hide();

		// Strip whitespace from key
		let licenseKey = field.val().trim();
		field.val( licenseKey );

		const nonceField = $($el).find('.wp-nonce-fluent') || $($el).find('.wp-nonce');

		const data = {
			action: window[`stellarwp_config_${action}`]['action'],
			slug: slug,
			key: licenseKey,
			_wpnonce: nonceField.val()
		};

		$.post(ajaxurl, data, function (response) {
			$validityMessage.show();
			$validityMessage.html(response.message);

			switch (response.status) {
				case 1:
					$validityMessage.addClass('valid-key').removeClass('invalid-key');
					obj.enableAuthorizeButton( $oauth );
					if ( $oauth.hasClass( 'not-authorized' ) ) {
						$oauth.attr( 'href', response.auth_url );
					}
					break;
				case 2:
					$validityMessage.addClass('valid-key service-msg');
					obj.enableAuthorizeButton( $oauth );
					if ( $oauth.hasClass( 'not-authorized' ) ) {
						$oauth.attr( 'href', response.auth_url );
					}
					break;
				default:
					$validityMessage.addClass('invalid-key').removeClass('valid-key');
					obj.disableAuthorizeButton( $oauth );
					break;
			}
		}).fail(function(error) {
			$validityMessage.show();
			$validityMessage.html(error.message);
			obj.disableAuthorizeButton( $oauth );
		}).always(function() {
			$($el).find('.ajax-loading-license').hide();
		});
	};

	$( function() {
		obj.init();
	} );
} )( jQuery, {}	 );
