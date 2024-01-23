(function($){
	let originalOnChange;
	let $removeAvatarLink;

	$(document).ready(function(){
		let $avatarCropPreview, $originalAvatarPreview;
		let $avatarCropPreviewParent, $originalAvatarPreviewParent;

		const originalAvatarSrc = $( '#avatar-preview' ).attr( 'src' );

		$removeAvatarLink = $( '#remove-avatar-link' );
		$removeAvatarLink.hide();

		$removeAvatarLink.on( 'click', function( event ) {
			event.preventDefault();
			hideCropPreviewPane();
			$removeAvatarLink.hide();
			bp.Avatar.nav.trigger( 'bp-avatar-view:changed', 'upload' );
		} );

		$( '.bp-avatar-nav' ).hide();
		$( '#signup_submit' ).on( 'click', function(){
			window.onbeforeunload = null;
		} );

		// Listen for changes to bp.Avatar.jcropapi, so that we can add a listener.

		// Store the original value of jcropapi
		let _originalJcropApi = bp.Avatar.jcropapi;
		let jcropIsLoaded = false;
		let previewPaneIsSwapped = false;

		// Define a getter and setter for jcropapi
		Object.defineProperty(bp.Avatar, 'jcropapi', {
			get() {
				// Return the stored original value
				return _originalJcropApi;
			},
			set(value) {
				// Set the new value to the stored original value
				_originalJcropApi = value;

				if ( jcropIsLoaded ) {
					return;
				}

				if ( ! _originalJcropApi.hasOwnProperty( 'getOptions' ) ) {
					return;
				}

				options = _originalJcropApi.getOptions();

				_originalJcropApi.setOptions({
					onChange: jcropOnChange,
				});

				jcropIsLoaded = true;
			}
		});

		const jcropOnChange = function( c ) {
			if ( typeof originalOnChange === 'function' ) {
				originalOnChange(c);
			}

			if ( ! previewPaneIsSwapped ) {
				$avatarCropPreview = $( '#avatar-crop-preview' );
				$originalAvatarPreview = $( '#avatar-preview' );

				// Swap the DOM position of the preview panes.
				$avatarCropPreviewParent = $avatarCropPreview.parent();
				$originalAvatarPreviewParent = $originalAvatarPreview.parent();

				showCropPreviewPane();
			}
		}

		const showCropPreviewPane = function() {
			$avatarCropPreviewParent.append( $originalAvatarPreview );
			$originalAvatarPreviewParent.append( $avatarCropPreview );

			$( '#avatar-crop-pane' ).hide();

			previewPaneIsSwapped = true;
		}

		const hideCropPreviewPane = function() {
			$avatarCropPreviewParent.append( $avatarCropPreview );
			$originalAvatarPreviewParent.append( $originalAvatarPreview );

			previewPaneIsSwapped = false;
		}

		$( document ).on( 'click', '#cancel-avatar-upload', function( event ) {
			event.preventDefault();

			if ( ! previewPaneIsSwapped ) {
				return;
			}

			hideCropPreviewPane();
			jcropIsLoaded = false;
			bp.Avatar.nav.trigger( 'bp-avatar-view:changed', 'upload' );
		} );
	});

	$( document ).ajaxSuccess(function( event, xhr, settings ) {
		// If settings.url doesn't end in wp-admin/admin-ajax.php, ignore it.
		if ( settings.url.indexOf( 'wp-admin/admin-ajax.php' ) == -1 ) {
			return;
		}

		// Get the 'action' param from the data property.
		var action = null;
		var dataEntries = settings.data.split('&');
		if ( dataEntries.length == 0 ) {
			return;
		}

		for ( var i = 0; i < dataEntries.length; i++ ) {
			var dataEntry = dataEntries[i].split('=');
			if ( dataEntry[0] == 'action' ) {
				action = dataEntry[1];
				break;
			}
		}

		if ( 'bp_avatar_set' !== action ) {
			return;
		}

		// Get the response data.
		var responseData = $.parseJSON( xhr.responseText );
		if ( ! responseData.success ) {
			return;
		}

		// Get the avatar URL.
		var avatarUrl = responseData.data.avatar;
		$( '#avatar-preview' ).attr( 'src', avatarUrl );

		showRemoveAvatarButton();
	} );

	const showRemoveAvatarButton = function() {
		$removeAvatarLink.show();
	}
}(jQuery));
