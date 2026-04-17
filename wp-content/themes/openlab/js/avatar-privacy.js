(() => {
	document.addEventListener( 'DOMContentLoaded', function() {
		const panelAvatarPrivacy = document.getElementById( 'panel-avatar-privacy' )
		if ( ! panelAvatarPrivacy ) {
			return;
		}

		const avatarPrivacyRadios = document.querySelectorAll( '.avatar-visibility-radio' );
		if ( ! avatarPrivacyRadios ) {
			return;
		}

		Array.from( avatarPrivacyRadios ).forEach( ( radio ) => {
			radio.addEventListener( 'change', () => {
				panelAvatarPrivacy.classList.add( 'loading' );

				const nonce = document.getElementById( 'openlab-avatar-privacy-nonce' )?.value;
				const userId = document.getElementById( 'avatar-privacy-user-id' )?.value;

				// send to admin-ajax.php?action=openlab_avatar_privacy
				const formData = new FormData();
				formData.append( 'visibility', radio.value );
				formData.append( 'nonce', nonce );
				formData.append( 'user_id', userId );
				formData.append( 'action', 'openlab_avatar_privacy' );

				fetch( window.ajaxurl, {
					method: 'POST',
					body: formData,
				} )
				.then( response => response.json() )
				.then( data => {
					panelAvatarPrivacy.classList.remove( 'loading' );
				} )
			} )
		} )
	})
})()
