window.GFDropboxFolder = null;

( function( $ ) {

	GFDropboxFolder = function( args ) {

		for ( var prop in args ) {
			if ( args.hasOwnProperty( prop ) ) {
				this[prop] = args[prop];
			}
		}

		var self = this;

		this.init = function() {

			// Define folder tree DOM element.
			this.folderTree = $( '.folder_tree' );

			// Define if this is the first load of the folder tree.
			this.firstLoad = true;

			// If the folder tree does not exist, exit.
			if ( this.folderTree.length == 0 ) {
				return;
			}

			// Initialize folder tree.
			this.initializeFolderTree();

			// Bind select folder event.
			this.onSelectFolder();

		}

		this.initializeFolderTree = function() {

			self.folderTree.jstree( {
			 	'core' : {
				 	'themes': { 'name' : 'proton' },
			 		'data':   {
			 			'url':      ajaxurl,
			 			'dataType': 'JSON',
			 			'data':     function( node ) {
							return {
								'action':     'gfdropbox_folder_contents',
								'first_load': self.firstLoad,
								'nonce':      gform_dropbox_formeditor_strings.nonce_folder,
								'path':       '#' === node.id ? self.initialPath : node.id
							};
						},
			 			'success':  function () {
				 			self.firstLoad = ( self.firstLoad == true ) ? false : self.firstLoad;
				 		}
			 		}
				}
			} );

		}

		this.onSelectFolder = function() {

			self.folderTree.on( 'select_node.jstree', function( e, jstree ) {

				var target = self.folderTree.data( 'target' );

				$( 'input[name="' + target + '"]' ).val( jstree.node.id );

			} );

		}

		this.init();

	}

} )( jQuery );
