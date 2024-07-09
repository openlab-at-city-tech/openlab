( function( $ ) {
	$( function() {
		function pdfembSetFormActionToTab( id ) {
			let form = $( '#pdfemb_form' );

			form.attr(
				'action',
				form.attr( 'action' ).replace( /(#.+)?$/, '#' + id ),
			);
		}

		function decodeHtml(html) {
			var txt = document.createElement("textarea");
			txt.innerHTML = html;
			return txt.value;
		}

		$( '#pdfemb-tabs a' )
			.on( 'click', function() {

				// Tab nav item.
				$( '#pdfemb-tabs' ).find( 'a' ).removeClass( 'nav-tab-active' );

				// Tab content.
				$( '.pdfembtab' ).removeClass( 'active' );

				let id = $( this ).attr( 'id' ).replace( '-tab', '' );
				$( '#' + id + '-section' ).addClass( 'active' );
				$( this ).addClass( 'nav-tab-active' );

				if ( 'about' === id ) {
					$( '.submit' ).hide();
				} else {
					$( '.submit' ).show();
				}

				// Set submit URL to this tab
				pdfembSetFormActionToTab( id );

				// Update upgrade URL dynamically based on URL hash.
				let url = getPdfembUpdateURL( id );

				$( '#pdfemb-settings-bottom-cta a.pdfemb-upgrade-url' ).attr( 'href', decodeHtml( url ) );
			} );

		// Process the "free plugins" link in the footer.
		$('#pdfemb-footer .free-plugins')
			.on( 'click', function( e ) {
				e.preventDefault();

				// As we prevented the default behavior above - the hash hasn't changed.
				// So let's do that manually.
				window.location.hash = '#about';

				$( '#pdfemb-tabs a#about-tab' ).trigger( 'click' );
			} );

		// Move the default "settings update" notice under tabs.
		$( '#wpbody-content > .notice' )
			.prependTo( '#pdfemb-tabswrapper' )
			.css( 'display', 'block' );

		// Did page load with a tab active?
		var active_tab = window.location.hash.replace( '#', '' );
		if ( active_tab !== '' ) {
			var activeSection = $( '#' + active_tab + '-section' );
			var activeTab = $( '#' + active_tab + '-tab' );

			if ( 'about' === active_tab ) {
				$( '.submit' ).hide();
			} else {
				$( '.submit' ).show();
			}
			if ( activeSection && activeTab ) {
				$( '#pdfemb-tabs' ).find( 'a' ).removeClass( 'nav-tab-active' );
				$( '.pdfembtab' ).removeClass( 'active' );

				activeSection.addClass( 'active' );
				activeTab.addClass( 'nav-tab-active' );
				pdfembSetFormActionToTab( active_tab );

				let url = getPdfembUpdateURL( active_tab );

				$( '#pdfemb-settings-bottom-cta a.pdfemb-upgrade-url' ).attr( 'href', decodeHtml( url ) );
			}
		}

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
