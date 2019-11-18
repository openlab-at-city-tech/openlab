( function( window, $, app ) {
	app.init = function() {
		app.cache();
		app.bindEvents();
	};

	app.cache = function() {
		app.entry = { id: 0 };
		app.settings = window.portfolioSettings;
		app.template = wp.template( 'add-to-portfolio' );
		app.modal = new A11yDialog(
			document.getElementById( 'add-to-protfolio-dialog' ),
			document.getElementById( 'content' )
		);
	};

	app.bindEvents = function() {
		$( '.portfolio-actions' ).on( 'click', '.add', app.onClick );

		app.modal.on( 'hide', app.unbindEvents );
	};

	app.unbindEvents = function() {
		// Remove submit handlers when hiding modal.
		$( app.modal.container )
			.find( '.dialog__footer' )
			.off( 'click', '.btn-primary', app.submitEntry );

		$( app.modal.container ).attr( 'aria-hidden', true );
	};

	app.onClick = function( event ) {
		var data = $( this ).data( 'entry' );

		event.preventDefault();

		// @todo Maybe cache in sessionStorage.
		if ( app.entry.id === data.id ) {
			app.renderModal( data, app.entry );
		} else {
			app.fetchEntry( data.id, data.type ).done( function( response ) {
				// Cache entry response and set custom data.
				app.entry           = response;
				app.entry.site_id   = data.site_id;
				app.entry.rest_base = data.type;

				app.renderModal( data, app.entry );
			} );
		}
	};

	app.renderModal = function( data ) {
		if ( 'comments' !== data.type ) {
			data.title = app.entry.title.rendered;
		}

		// Toggle aria-hidden
		$( app.modal.container ).attr( 'aria-hidden', false );

		// Render template.
		$( app.modal.container )
			.find( '.dialog__body' )
			.html( app.template( data ) );

		// Attach submit action.
		$( app.modal.container )
			.find( '.dialog__footer' )
			.on( 'click', '.btn-primary', app.submitEntry );

		// Display modal.
		app.modal.show();
	};

	app.submitEntry = function( event ) {
		event.preventDefault();

		// Get data from modal.
		var title      = $( app.modal.container ).find( '#title' ).val();
		var type       = $( app.modal.container ).find( 'input[name="type"]:checked' ).val();
		var citation   = $( app.modal.container ).find( '#citation' ).html();
		var annotation = $( app.modal.container ).find( '#annotation' ).val();

		var data = {
			author: app.entry.author,
			title: title,
			content: ( app.entry.type === 'comment' ) ? app.entry.content.rendered : app.entry.content.raw,
			status: 'draft',
			type: type,
			meta: {
				'portfolio_citation': citation,
				'portfolio_annotation': annotation,
			},
		};

		app.saveEntry( data ).done( function( res ) {
			// Update current entry meta.
			app.saveMeta( app.entry.id, app.entry.rest_base, res.id );

			app.updateButton( app.entry, res.id );

			app.modal.hide();
		} );
	};

	app.fetchEntry = function( id, type ) {
		// Create endpoint based on content type.
		var endpoint = app.settings.root + 'wp/v2/' + type + '/' + id;

		if ( 'comments' !== type ) {
			var endpoint = endpoint + '?context=edit';
		}

		return $.ajax( {
			url: endpoint,
			method: 'GET',
			beforeSend: function( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', app.settings.nonce );
			},
		} );
	};

	app.saveEntry = function( data ) {
		var endpoint = app.settings.portfolioRoot + 'wp/v2/' + data.type;

		return $.ajax( {
			url: endpoint,
			method: 'POST',
			data: data,
			beforeSend: function( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', app.settings.nonce );
			},
		} );
	};

	app.saveMeta = function( id, restBase, portfolioId ) {
		var endpoint = app.settings.root + 'wp/v2/' + restBase + '/' + id;

		if ( 'comments' === restBase ) {
			var endpoint = app.settings.root + 'wp/v2/' + restBase + '/shared/' + id;
		}

		return $.ajax( {
			url: endpoint,
			method: 'POST',
			data: {
				meta: {
					'portfolio_post_id': portfolioId
				}
			},
			beforeSend: function( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', app.settings.nonce );
			},
		} );
	};

	app.updateButton = function( entry, id ) {
		var editUrl = app.settings.portfolioAdmin + 'post.php?action=edit&post=' + id;

		// Create new edit link element.
		var editLink = $( '<a />' )
			.attr( 'href', editUrl )
			.text( 'Added to my Portfolio' );

		$( '.portfolio-actions-' + entry.id )
			.find( 'button' )
			.replaceWith( editLink );
	};

	$( document ).ready( app.init );
}( window, jQuery, {} ) );
