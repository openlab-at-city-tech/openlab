(function($){
	var $addNewButton, $emptyRow, $groupCreatorEditList;
	var nextIndex = 0;

	$(document).ready(function(){
		$addNewMember = $( '#group-creator-add-new-member' );
		$addNewNonMember = $( '#group-creator-add-new-non-member' );
		$emptyRow = $( '#group-creator-empty-row' );
		$groupCreatorEditList = $( '#group-creator-edit-list' );

		$addNewMember.on(
			'click',
			function(e) {
				e.preventDefault();
				addNewRow( 'member' );
			}
		);

		$addNewNonMember.on(
			'click',
			function(e) {
				e.preventDefault();
				addNewRow( 'non-member' );
			}
		);

		$groupCreatorEditList.find( '.group-creator-form-entry' ).each( function( k, v ) {
			initEntry( $( v ) );
			nextIndex++;
		} );
	});

	function getNextIndex() {
		nextIndex++;
		return nextIndex;
	}

	function initEntry( $entryEl ) {
		// Set the visibility of fields based on 'Type'.
		resetEntryFieldVisibility( $entryEl );

		var $creatorType = $entryEl.find( '.creator-type' );

		// Set the change event for the 'Type' dropdown.
		// @todo can probably remove this
		$creatorType.on(
			'change',
			function( e ) {
				resetEntryFieldVisibility( $entryEl );
			}
		);

		$entryEl.find( '.member-login-autocomplete' ).autocomplete( {
				source: ajaxurl + '?action=openlab_group_creator_autocomplete',
				minLength: 2,
				select: function( event, ui ) {
					$entryEl.find( '.member-display-name' ).val( ui.item.label );
					$entryEl.find( '.member-url' ).val( ui.item.url );

					// Ack
					var targetEl = event.target;
					setTimeout(
						function() {
							$( targetEl ).blur();
							setUpPreviewMode( $entryEl );
						},
						100
					);
				}
			} );

		// If the field has value, then switch it to "preview" mode.
		setUpPreviewMode( $entryEl );

		// Not shown with JS enabled.
		$entryEl.find( '.creator-fields-type' ).hide();

		$entryEl.find( '.delete-entry' ).on(
			'click',
			function( e ) {
				e.preventDefault();
				$( e.target ).closest( 'ul.group-creator-edit-list li' ).remove();
			}
		);

		$entryEl.find( '.edit-entry' ).on(
			'click',
			function( e ) {
				e.preventDefault();
				$entryEl.removeClass( 'is-preview-mode' );
			}
		);

		$entryEl.find( '.non-member-name, .non-member-url' ).on(
			'blur',
			function( e ) {
				setTimeout(
					function() {
						if ( ! $entryEl.find( '.non-member-url, .non-member-name' ).is( ':focus' ) ) {
							setUpPreviewMode( $entryEl );
						}
					},
					100
				);
			}
		);
	}

	function resetEntryFieldVisibility( $entryEl ) {
		$entryEl.removeClass( 'creator-type-member creator-type-non-member creator-type-null' );

		switch ( $entryEl.find( '.creator-type :selected' ).val() ) {
			case 'member' :
				$entryEl.addClass( 'creator-type-member' );
				break;

			case 'non-member' :
				$entryEl.addClass( 'creator-type-non-member' );
				break;
		}
	}

	function addNewRow( type ) {
		var $newEntry = $emptyRow.children( '.group-creator-form-entry' ).clone();

		var rowIndex = getNextIndex();
		var rowId = 'creator' + rowIndex.toString();

		$newEntry
		  .find( '.creator-type .creator-type-' + type )
			.prop( 'selected', 'selected' );

		$newEntry
			.find( 'label' )
			.each( function( k, v ) {
				var $theLabel = $(v);
				var forProp = $theLabel.prop( 'for' );
				$theLabel.prop( 'for', forProp.replace( '_nullcreator', rowId ) );
			} );

		$newEntry
			.find( 'input,select' )
			.each( function( k, v ) {
				var $theEl = $(v);

				var idProp = $theEl.prop( 'id' );
				$theEl.prop( 'id', idProp.replace( '_nullcreator', rowId ) );

				var nameProp = $theEl.prop( 'name' );
				$theEl.prop( 'name', nameProp.replace( '_nullcreator', rowId ) );
			} );

		initEntry( $newEntry );

		var $newLi = $( '<li></li>' );
		$newLi.append( $newEntry );

		$groupCreatorEditList.append( $newLi );
	}

	function setUpPreviewMode( $entryEl ) {
		var creatorTypeValue = $entryEl.find( '.creator-type' ).find( ':selected' ).val();
		var isPreviewMode = false;
		switch ( creatorTypeValue ) {
			case 'member' :
				var $memberLogin = $entryEl.find( '.member-login' );
				isPreviewMode = $memberLogin.val().length > 0 && $entryEl.find( '.member-display-name' ).val().length > 0;
				break;

			case 'non-member' :
				isPreviewMode = $entryEl.find( '.non-member-name' ).val().length > 0;
				break;
		}

		if ( ! isPreviewMode ) {
			return;
		}

		var $previewEl = $( '<span></span>' );
		switch ( creatorTypeValue ) {
			case 'member' :
				var memberLogin = $memberLogin.val();
				var memberDisplayName = $entryEl.find( '.member-display-name' ).val();
				var memberUrl = $entryEl.find( '.member-url' ).val();

				$previewEl.append( '<span class="fa fa-user"></span>' );

				var $memberLinkEl;

				if ( memberUrl.length ) {
					$memberLinkEl = $( '<a></a>' );
					$memberLinkEl.text( memberDisplayName );
					$memberLinkEl.attr( 'href', memberUrl );
				} else {
					$memberLinkEl = '<span></span>';
					$memberLinkEl.text( memberDisplayName );
				}

				$previewEl.append( $memberLinkEl );

				break;

			case 'non-member' :
				var nonMemberName = $entryEl.find( '.non-member-name' ).val();

				$previewEl.append( '<span class="fa fa-globe"></span>' );

				var $nonMemberLinkEl = $( '<span></span>' );
				$nonMemberLinkEl.text( nonMemberName );
				$previewEl.append( $nonMemberLinkEl );
				break;
		}

		// Remove existing previews before appending a new one.
		$entryEl.find( '.creator-preview' ).remove();

		var $preview = $( '<div class="creator-preview"></div>' );
		$preview.append( $previewEl );
		$entryEl.append( $preview );

		$entryEl.addClass( 'is-preview-mode' );
	}
}(jQuery));
