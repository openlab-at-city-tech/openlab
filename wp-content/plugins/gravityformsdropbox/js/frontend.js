window.GFDropbox = null;

( function( $ ) {
	
	GFDropbox = function ( args ) {
		
		for ( var prop in args ) {
			if ( args.hasOwnProperty( prop ) ) {
				this[prop] = args[prop];
			}
		}
		
		var GFDropboxObj = this;

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
			
			document.querySelector( '#field_' + this.formId + '_' + this.inputId + ' .ginput_container' ).appendChild( button );
			
		}

		this.getFieldValue = function() {

			var value = document.getElementById( 'input_' + this.formId + '_' + this.inputId ).value || [];

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
			
			var preview = document.getElementById( 'gform_preview_' + this.formId + '_' + this.inputId ),
				files   = GFDropboxObj.getFieldValue();
				
			// Remove existing preview.
			preview.innerHTML = '';
			
			// Loop through selected files, add to preview.
			for ( i = 0; i < files.length; i++ ) {
				
				var file_name = this.getFileName( files[i] );
				
				html  = '<div class="ginput_preview">';
				html += '<img class="gform_delete" src="' + this.deleteImage + '" alt="' + this.deleteText + '" title="' + this.deleteText + '" data-file="' + file_name + '" /> ';
				html += '<strong>' + file_name + '</strong>';
				html += '</div>';
				
				preview.innerHTML += html;
				
			}
			
		}
		
		this.removeFile = function( file_name ) {
			
			var field_value = this.getFieldValue();
			
			for ( i = 0; i < field_value.length; i++ ) {
				
				var name = this.getFileName( field_value[i] );
				
				if ( name === file_name ) {
					field_value.splice( i, 1 );
				}
				
			}
			
			this.setFieldValue( field_value );
			
			this.refreshPreview();
			
		}
		
		this.setFieldValue = function( value ) {

			document.getElementById( 'input_' + this.formId + '_' + this.inputId ).value = JSON.stringify( value );

		}
		
		this.setupPreviewBind = function() {
			
			var preview = $( '#gform_preview_' + this.formId + '_' + this.inputId );

			$( preview ).on( 'click', '.ginput_preview img', function() {
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