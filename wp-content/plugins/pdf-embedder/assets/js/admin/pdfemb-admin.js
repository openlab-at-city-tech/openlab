( function( $ ) {
	$( function() {
		// Move the default "settings update" notice under tabs.
		$( '#wpbody-content > .notice' )
			.prependTo( '#pdfemb-section-wrapper' )
			.css( 'display', 'block' );

		$( '.trigger-getstarted' ).on( 'click', function( e ) {
			e.preventDefault();

			if ( ! $( '#pdfemb-getstarted' ).hasClass( 'hidden' ) ) {
				return;
			}

			$.post( ajaxurl, {
				 action: 'pdfemb_admin_settings_getstarted_open',
			 } );

			 $( '#pdfemb-getstarted' ).slideDown( 'fast', function() {
				 $( this ).removeClass( 'hidden' );
			 });
		} );

		/**
		 * Partner plugin installation.
		 */
		$( '.pdfemb-partners .pdfemb-partners-install' ).on(
			'click',
			function( e ) {
				e.preventDefault();
				var $this = $( this );
				if ( $this.hasClass( 'disabled' ) ) {
					return false;
				}

				var url = $this.data( 'url' );
				var basename = $this.data( 'basename' );
				var message = $( this )
					.parent()
					.parent()
					.find( '.pdfemb-partner-status' );

				var install_opts = {
					url: pdfemb_args.ajax,
					type: 'post',
					async: true,
					cache: false,
					dataType: 'json',
					beforeSend: function(){
						$this.addClass('disabled');
						$this.siblings('.spinner').toggleClass('active');
					},
					data: {
						action: 'pdfemb_partners_install',
						nonce: pdfemb_args.install_nonce,
						basename: basename,
						download_url: url,
					},
					success: function( response ) {
						$this.text( pdfemb_args.activate )
							.removeClass( 'pdfemb-partners-install' )
							.addClass( 'pdfemb-partners-activate' );

						$( message ).text( pdfemb_args.inactive );
						// Trick here to wrap a span around the last word of the status
						var heading = $( message ),
							word_array,
							last_word,
							first_part;

						word_array = heading.html().split( /\s+/ ); // split on spaces
						last_word = word_array.pop(); // pop the last word
						first_part = word_array.join( ' ' ); // rejoin the first words together

						heading.html(
							[
								first_part,
								' <span>',
								last_word,
								'</span>',
							].join( '' ),
						);
						// Proc
					},
					error: function( xhr, textStatus, e ) {
						console.log( e );
					},
					complete: function() {
						$this.removeClass('disabled');
						$this.siblings('.spinner').toggleClass('active');
					}
				};
				$.ajax( install_opts );
			},
		);

		/**
		 * Partner plugin activation.
		 */
		$( '.pdfemb-partners .pdfemb-partners-activate' ).on(
			'click',
			function( e ) {
				e.preventDefault();
				var $this = $( this );
				if ( $this.hasClass( 'disabled' ) ) {
					return false;
				}

				var url = $this.data( 'url' );
				var basename = $this.data( 'basename' );
				var message = $( this )
					.parent()
					.parent()
					.find( '.pdfemb-partner-status' );
				var activate_opts = {
					url: pdfemb_args.ajax,
					type: 'post',
					async: true,
					cache: false,
					dataType: 'json',
					beforeSend: function(){
						$this.addClass('disabled');
						$this.siblings('.spinner').toggleClass('active');
					},
					data: {
						action: 'pdfemb_partners_activate',
						nonce: pdfemb_args.activate_nonce,
						basename: basename,
						download_url: url,
					},
					success: function( response ) {
						$this.text( pdfemb_args.deactivate )
							.removeClass( 'pdfemb-partners-activate' )
							.addClass( 'pdfemb-partners-deactivate' );

						$( message ).text( pdfemb_args.active );
						// Trick here to wrap a span around the last word of the status.
						var heading = $( message ),
							word_array,
							last_word,
							first_part;

						word_array = heading.html().split( /\s+/ ); // split on spaces
						last_word = word_array.pop(); // pop the last word
						first_part = word_array.join( ' ' ); // rejoin the first words together

						heading.html(
							[
								first_part,
								' <span>',
								last_word,
								'</span>',
							].join( '' ),
						);
						location.reload( true );
					},
					error: function( xhr, textStatus, e ) {
						console.log( e );
					},
					complete: function() {
						$this.removeClass('disabled');
						$this.siblings('.spinner').toggleClass('active');
					}
				};
				$.ajax( activate_opts );
			},
		);

		/**
		 * Partner plugin deactivation.
		 */
		$( '.pdfemb-partners .pdfemb-partners-deactivate' ).on(
			'click',
			function( e ) {
				e.preventDefault();
				var $this = $( this );
				if ( $this.hasClass( 'disabled' ) ) {
					return false;
				}

				var url = $this.data( 'url' );
				var basename = $this.data( 'basename' );
				var message = $( this )
					.parent()
					.parent()
					.find( '.pdfemb-partner-status' );
				var deactivate_opts = {
					url: pdfemb_args.ajax,
					type: 'post',
					async: true,
					cache: false,
					dataType: 'json',
					beforeSend: function(){
						$this.addClass('disabled');
						$this.siblings('.spinner').toggleClass('active');
					},
					data: {
						action: 'pdfemb_partners_deactivate',
						nonce: pdfemb_args.deactivate_nonce,
						basename: basename,
						download_url: url,
					},
					success: function( response ) {
						$this.text( pdfemb_args.activate )
							.removeClass( 'pdfemb-partners-deactivate' )
							.addClass( 'pdfemb-partners-activate' );

						$( message ).text( pdfemb_args.inactive );
						// Trick here to wrap a span around the last word of the status.
						var heading = $( message ),
							word_array,
							last_word,
							first_part;

						word_array = heading.html().split( /\s+/ ); // split on spaces
						last_word = word_array.pop(); // pop the last word
						first_part = word_array.join( ' ' ); // rejoin the first words together

						heading.html(
							[
								first_part,
								' <span>',
								last_word,
								'</span>',
							].join( '' ),
						);
						location.reload( true );
					},
					error: function( xhr, textStatus, e ) {
						console.log( e );
					},
					complete: function() {
						$this.removeClass('disabled');
						$this.siblings('.spinner').toggleClass('active');
					}
				};
				$.ajax( deactivate_opts );
			},
		);
	} );
} )( jQuery );
