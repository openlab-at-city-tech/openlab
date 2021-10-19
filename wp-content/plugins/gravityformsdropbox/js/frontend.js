window.GFDropbox = null;

( function( $ ) {

	GFDropbox = function ( args ) {

		for ( var prop in args ) {
			if ( args.hasOwnProperty( prop ) ) {
				this[prop] = args[prop];
			}
		}

		var GFDropboxObj = this;

		this.fieldUniqueIdentifier = this.formId + '_' + this.inputId;

		this.init = function() {

			this.createDropboxButton();

			this.refreshPreview();

			this.setupPreviewBind();

		}

		this.createDropboxButton = function() {
			var button = Dropbox.createChooseButton( {
				extensions:  this.extensions,
				linkType:    this.linkType,
				multiselect: this.multiselect,
				success:     this.uploadHandler
			} );

			document.querySelector( '#field_' + this.fieldUniqueIdentifier + ' .ginput_container' ).prepend( button );
			// Add an ID to the label to add it to the aria-describedby IDs
			// as this attribute is applied to a link and not an input where it is described by the label by default.
			$( 'label[for=input_'+ this.fieldUniqueIdentifier +']').attr('id', 'input_' + this.fieldUniqueIdentifier + '_label' );
			// Move aria-describedby value from hidden input to dropbox link.
			var describedBy = 'input_' + this.fieldUniqueIdentifier + '_label ';
			describedBy +=  jQuery( '#input_' + this.fieldUniqueIdentifier ).attr( 'aria-describedby' );
			jQuery( '#input_' + this.fieldUniqueIdentifier ).removeAttr( 'aria-describedby' );
			jQuery( '#field_' + this.fieldUniqueIdentifier + ' .dropbox-dropin-btn' ).attr( 'aria-describedby', describedBy );

		}

		this.getFieldValue = function() {

			var value = document.getElementById( 'input_' + this.fieldUniqueIdentifier ).value || [];

			try {
				return value ? JSON.parse( value ) : [];
			} catch ( e ) {
				return [];
			}

		}

		this.getFileName = function( path ) {

			return path.split( '/' ).pop().split( '?dl=' ).shift();

		}

		this.refreshPreview = function() {

			var preview = document.getElementById( 'gform_preview_' + this.fieldUniqueIdentifier ),
				files = GFDropboxObj.getFieldValue();

			// Remove existing preview.
			preview.innerHTML = '';

			// Loop through selected files, add to preview.
			for ( i = 0; i < files.length; i++ ) {

				var file_name = decodeURIComponent( this.getFileName( files[ i ] ) );

				html = '<div aria-live="assertive" aria-atomic="true" class="ginput_preview">';
				html += '<input aria-label="Delete file' + file_name + '" tabindex="0" type="button" class="gform_delete"  id="gfrom_dropbox_filename_' + i + '" data-file="' + file_name + '" >';
				html += '<label for="gfrom_dropbox_filename_' + i + '" tabindex="0">' + file_name + '</label>';
				html += '</div>';

				preview.innerHTML += html;

			}

			// Prevent deleting the file when clicking on the filename label.
			jQuery( document ).on( 'click', '.gdropbox_preview .gform_delete + label', function ( e ) {
				e.preventDefault();
				e.stopImmediatePropagation();
			});
		}

		this.removeFile = function( file_name ) {

			var field_value = this.getFieldValue();

			for ( i = 0; i < field_value.length; i++ ) {

				var name = this.getFileName( field_value[i] );

				if ( decodeURIComponent( name ) === file_name ) {
					field_value.splice( i, 1 );
				}

			}

			this.setFieldValue( field_value );

			this.refreshPreview();

		}

		this.setFieldValue = function( value ) {

			value = value.length ? JSON.stringify( value ) : '';
			document.getElementById( 'input_' + this.fieldUniqueIdentifier ).value = value;

		}

		this.setupPreviewBind = function() {

			var preview = $( '#gform_preview_' + this.fieldUniqueIdentifier );

			$( preview ).on( 'click', '.ginput_preview .gform_delete', function() {
				GFDropboxObj.removeFile( $( this ).attr( 'data-file' ) );
			} );

		}

		this.uploadHandler = function( files ) {

			var field_value = GFDropboxObj.getFieldValue() && GFDropboxObj.multiselect ? GFDropboxObj.getFieldValue() : [];

			for ( i = 0; i < files.length; i++ ) {
				field_value.push( files[i].link );
			}

			GFDropboxObj.setFieldValue( field_value );
			GFDropboxObj.refreshPreview();

		}

		this.init();

	}

} )( jQuery );
