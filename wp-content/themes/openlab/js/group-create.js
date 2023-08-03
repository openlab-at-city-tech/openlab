/**
 * This is the JavaScript related to group creation. It's loaded only during the group creation
 * process.
 *
 * Added by Boone 7/7/12. Don't remove me during remediation.
 */

function showHide(id) {
	var elem = document.getElementById( id );
	if ( ! elem ) {
		  return;
	}

	var style = elem.style
	if (style.display == "none") {
		style.display = "";
	} else {
		style.display = "none";
	}
}

jQuery( document ).ready(
	function($){
		var form,
		form_type,
		new_group_type = $( '#new-group-type' ).val(),
		$body          = $( 'body' ),
    $setuptoggle   = $( 'input[name="wds_website_check"]' );
		$setuptoggle_mirror = $( 'input#set-up-site-toggle' );

		if ( $body.hasClass( 'group-admin' ) ) {
			form_type = 'admin';
			form      = document.getElementById( 'group-settings-form' );
		} else {
			form_type = 'create';
			form      = document.getElementById( 'create-group-form' );
		}

		$form = $( form );

		var $gc_submit = $form.find( 'input[type="submit"]' );

		/*
		 * Ensure proper focus/scroll when form does not validate.
		 *
		 * Parsley.js doesn't do this correctly out of the box with Schools/Departments.
		 */
		$form.parsley().on( 'form:validate', function( formInstance ) {
			if ( ! formInstance.isValid() ) {
				for (var i = 0; i < formInstance.fields.length; i++) {
					var field = formInstance.fields[i];
					if (true !== field.validationResult && field.validationResult.length > 0 && 'undefined' === typeof field.options.noFocus) {
						field.element.closest( '.panel' ).scrollIntoView()
					}
				}

				return false;
			}
		} )

		/*
		 * Ensure proper focus/scroll when form does not validate.
		 *
		 * Parsley.js doesn't do this correctly out of the box with Schools/Departments.
		 */
		$gc_submit.on( 'click', function() {
			if ( ! form.checkValidity() ) {
				var firstInvalidField = form.querySelector( ':invalid' );
				if ( firstInvalidField ) {
					firstInvalidField.scrollIntoView( {
						block: 'center',
						inline: 'center'
					} )
				}
				return false;
			}
		} )

		$( '#new-site-domain, #clone-destination-path' ).on( 'keyup', function() {
			console.log(this.dataset.parsleyErrorMessage)
			if ( this.value.length > 0 ) {
				this.dataset.parsleyErrorMessage = 'Sorry, that URL is already taken.';
			} else {
				this.dataset.parsleyErrorMessage = 'You must provide a URL.';
			}
		} );


		/**
		 * Rather than modifying the template ported from CBOX-OL, we change with JavaScript.
		 */
		var $templatePanelPicker = $( '.panel-template-picker' );
		if ( $templatePanelPicker.length > 0 ) {
			$templatePanelPicker.find( '.panel-heading' ).html( 'Site Template' );

			const newEl = '<p>Site Templates provide a basic structure and setup to help get you started building your Site. Please select the template that works best for your ' + OLGroupCreate.groupTypeLabel + '. <a href="https://openlab.citytech.cuny.edu/blog/help/site-templates" class="external-link">Learn more</a> in OpenLab Help.</p>';
			$templatePanelPicker.find( '.panel-body' ).prepend( newEl );
		}

		function maybeShowSiteFields() {
			if ( ! $setuptoggle.length && 'portfolio' !== new_group_type ) {
				return;
			}

			var showSiteFields = $setuptoggle.is( ':checked' );

			if ( showSiteFields || 'portfolio' === new_group_type ) {
				$( '#site-options' ).show();
			} else {
				$( '#site-options' ).hide();
			}
		}

		function maybeShowSiteTemplates() {
			var siteIsRequiredForGroupTypeEl = document.getElementById( 'site-is-required-for-group-type' )
			var siteIsRequiredForGroupType = siteIsRequiredForGroupTypeEl && '1' === siteIsRequiredForGroupTypeEl.value

			if ( siteIsRequiredForGroupType || $setuptoggle.is( ':checked' ) ) {
				$('.panel-template-picker').removeClass( 'hidden' );
			} else {
				$('.panel-template-picker').addClass( 'hidden' );
			}
		}

		function new_old_switch( noo ) {
			var radioid = '#new_or_old_' + noo;
			$( radioid ).prop( 'checked','checked' );

			$( '.noo_radio' ).each(
				function(i,v) {
					var thisval = $( v ).val();
					var thisid  = '#noo_' + thisval + '_options';

					if ( noo == thisval) {
						$( thisid ).find( 'input' ).each(
							function(index,element){
												$( element ).removeClass( 'disabled-opt' );
												$( element ).prop( 'disabled', false ).removeClass( 'disabled' );
							}
						);
						$( thisid ).find( 'select' ).each(
							function(index,element){
								if ($( element ).attr( 'type' ) !== 'radio') {
									$( element ).removeClass( 'disabled-opt' );
									$( element ).prop( 'disabled', false ).removeClass( 'disabled' );
								}
							}
						);

									//for external site note
						if ($( this ).attr( 'id' ) === 'new_or_old_external') {
							$( '#check-note' ).removeClass( 'disabled-opt' );
							$( '#wds-website-external #find-feeds' ).removeClass( 'disabled' );
						}

					} else {
						$( thisid ).find( 'input' ).each(
							function(index,element){
								if ($( element ).attr( 'type' ) !== 'radio') {
									$( element ).addClass( 'disabled-opt' );
									$( element ).prop( 'disabled','disabled' ).addClass( 'disabled' );
								}
							}
						);
						$( thisid ).find( 'select' ).each(
							function(index,element){
								if ($( element ).attr( 'type' ) !== 'radio') {
									$( element ).addClass( 'disabled-opt' );
									$( element ).prop( 'disabled','disabled' ).addClass( 'disabled' );
								}
							}
						);

									//for external site note
						if ($( this ).attr( 'id' ) === 'new_or_old_external') {
							$( '#check-note' ).addClass( 'disabled-opt' );
							$( '#wds-website-external #find-feeds' ).addClass( 'disabled' );
						}
					}
				}
			);

			var efr = $( '#external-feed-results' );
			if ( 'external' == noo ) {
				$( efr ).show();
			} else {
				$( efr ).hide();
			}
		}

		function disable_gc_form() {
			$gc_submit.attr( 'disabled', 'disabled' );
			$gc_submit.fadeTo( 500, 0.2 );
		}

		function enable_gc_form() {
			$gc_submit.prop( 'disabled', false );
			$gc_submit.fadeTo( 500, 1.0 );
		}

		function mark_loading( obj ) {
			$( obj ).before( '<span class="loading" id="group-create-ajax-loader"></span>' );
		}

		function unmark_loading( obj ) {
			var loader = $( obj ).siblings( '.loading' );
			$( loader ).remove();
		}

		function showHideAll() {
			showHide( 'wds-website-tooltips' );
			showHide( 'check-note-wrapper' );
		}

		function do_external_site_query(e) {
			var euf = $( '#external-site-url' );
			//var euf = e.target;
			var eu = $( euf ).val();

			if ( 0 == eu.length ) {
				enable_gc_form();
				return;
			}

			disable_gc_form();
			mark_loading( $( e.target ) );

			$.post(
				'/wp-admin/admin-ajax.php', // Forward-compatibility with ajaxurl in BP 1.6
				{
					action: 'openlab_detect_feeds',
					'site_url': eu
				},
				function(response) {
					var robj = $.parseJSON( response );

					var efr = $( '#external-feed-results' );

					if ( 0 != efr.length ) {
						$( efr ).empty(); // Clean it out
					} else {
						$( '#wds-website-external' ).after( '<div id="external-feed-results"></div>' );
						efr = $( '#external-feed-results' );
					}

					if ( "posts" in robj ) {
						$( efr ).append( '<p class="feed-url-tip">We found the following feed URLs for your external site, which we\'ll use to pull posts and comments into your activity stream.</p>' );
					} else {
						$( efr ).append( '<p class="feed-url-tip">We couldn\'t find any feed URLs for your external site, which we use to pull posts and comments into your activity stream. If your site has feeds, you may enter the URLs below.</p>' );
					}

					var posts    = "posts" in robj ? robj.posts : '';
					var comments = "comments" in robj ? robj.comments : '';
					var type     = "type" in robj ? robj.type : '';

					$( efr ).append( '<p class="feed-url posts-feed-url"><label for="external-posts-url">Posts:</label> <input name="external-posts-url" id="external-posts-url" value="' + posts + '" /></p>' );

					$( efr ).append( '<p class="feed-url comments-feed-url"><label for="external-comments-url">Comments:</label> <input name="external-comments-url" id="external-comments-url" value="' + comments + '" /></p>' );

					$( efr ).append( '<input name="external-site-type" id="external-site-type" type="hidden" value="' + type + '" />' );

					enable_gc_form();
					unmark_loading( $( e.target ) );
				}
			);
		}

		function toggle_clone_options( on_or_off ) {
			var $group_to_clone, group_id_to_clone;

			$group_to_clone = $( '#group-to-clone' );

			if ( 'on' == on_or_off ) {
				// Check "Clone a course" near the top
				$( '#create-or-clone-clone' ).attr( 'checked', true );

				maybeShowSiteTemplates();

				// Allow a course to be selected from the source dropdown,
				// and un-grey the associated labels/text
				$group_to_clone.removeClass( 'disabled-opt' );
				$group_to_clone.attr( 'disabled', false );
				$( '#ol-clone-description' ).removeClass( 'disabled-opt' );

				// Set up the site clone information
				group_id_to_clone = $group_to_clone.val();
				if ( ! group_id_to_clone ) {
					group_id_to_clone = $.urlParam( 'clone' );
				}

				// Ensure that the "Set up a site" section is visible
				if ( ! $setuptoggle.is( ':checked' ) ) {
					$setuptoggle.trigger( 'click' );
				}

				// Hide 'Create a new site' and 'Use an existing site'
				$( '#wds-website' ).hide();
				$( '#wds-website-existing' ).hide();
			} else {
				// Show the Site Details section.
				$( '#panel-site-details' ).show();

				// Check "Create a course" near the top
				$( '#create-or-clone-create' ).attr( 'checked', true );

				// Grey out options related to selecting a course to clone
				$group_to_clone.addClass( 'disabled-opt' );
				$group_to_clone.attr( 'disabled', true );
				$( '#ol-clone-description' ).addClass( 'disabled-opt' );

				// Show 'Create a new site' and 'Use an existing site'
				$( '#wds-website' ).show();
				$( '#wds-website-existing' ).show();

				group_id_to_clone = 0;
			}

			fetch_clone_source_details( group_id_to_clone );
		}

		function fetch_clone_source_details( group_id ) {
			$.ajax(
				{
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'openlab_group_clone_fetch_details',
						group_id: group_id
					},
					success: function( response ) {
						var r = JSON.parse( response );

						// Shared cloning UI
						var $sharedCloningSettings = $('#shared-cloning-authorship-settings');
						var $sharedCloningToggle   = $('#change-cloned-content-attribution');
						if ( r.group_id > 0 && r.is_shared_clone ) {
							$sharedCloningSettings.removeClass( 'hidden' );
							$sharedCloningToggle.prop('checked', true);
						} else {
							$sharedCloningSettings.addClass( 'hidden' );
							$sharedCloningToggle.prop('checked', false);
						}

						// Description
						$( '#group-desc' ).val( r.description );

						// Schools, Offices, Departments.
						if ( r.hasOwnProperty( 'schools' ) ) {
							$( 'input[name="schools[]"]' ).each(
								function(k,v){
									if ( -1 !== r.schools.indexOf( v.value ) ) {
										$( v ).prop( 'checked', true );
									}
								}
							);
						}

						if ( r.hasOwnProperty( 'offices' ) ) {
							$( 'input[name="offices[]"]' ).each(
								function(k,v){
									if ( -1 !== r.offices.indexOf( v.value ) ) {
										$( v ).prop( 'checked', true );
									}
								}
							);
						}

						// Schools and Departments
						if ( r.hasOwnProperty( 'departments' ) ) {
							$( 'input[name="departments[]"]' ).each(
								function(k,v){
									if ( -1 !== r.departments.indexOf( v.value ) ) {
										$( v ).prop( 'checked', true );
									}
								}
							);
						}

						if ( window.hasOwnProperty( 'openlab' ) ) {
							window.openlab.academicUnits.validateAcademicTypeSelector();
						}

						// Categories
						$( '.bp-group-categories-list-container input' ).each(
							function(k,v){
								var catId = parseInt( v.value );
								if ( -1 !== r.categories.indexOf( catId ) ) {
									$( v ).prop( 'checked', true );
								}
							}
						);

						// Course Code
						$( 'input[name="wds_course_code"]' ).val( r.course_code );

						// Section Code
						$( 'input[name="wds_section_code"]' ).val( r.section_code );

						// Associated site
						if ( r.site_id ) {
							// Show the Site Details section.
							$( '#panel-site-details' ).show();

							// Check 'Set up a site'
							if ( ! $setuptoggle.is( ':checked' ) ) {
								$setuptoggle.trigger( 'click' );
							}

							// Un-grey the website clone options
							$( '#wds-website-clone .radio' ).removeClass( 'disabled-opt' );
							$( '#wds-website-clone input[name="new_or_old"]' ).prop( 'disabled', true );

							// Auto-select the "Name your cloned site" option,
							// and trigger setup JS
							$( '#new_or_old_clone' ).attr( 'checked', true );
							$( '#new_or_old_clone' ).trigger( 'click' );

							// Site URL
							$( '#cloned-site-url' ).html( 'Your original address was: ' + r.site_url );
							$( '#blog-id-to-clone' ).val( r.site_id );
						} else {
							// Hide the Site Details section if cloning a group without a site.
							if ( group_id ) {
								$( '#panel-site-details' ).hide();
								$setuptoggle.prop( 'checked', false );
							}

							$( '#wds-website-clone .radio' ).addClass( 'disabled-opt' );
							$( '#wds-website-clone input[name="new_or_old"]' ).attr( 'disabled','disabled' );

							// Pre-select "Create a new site"
							$( '#new_or_old_new' ).attr( 'checked', true );
							$( '#new_or_old_new' ).trigger( 'click' );
						}

						maybeShowSiteTemplates();
					}
				}
			);
		}

		$( '.noo_radio' ).click(
			function(el){
				var whichid = $( el.target ).prop( 'id' ).split( '_' ).pop();
				new_old_switch( whichid );
			}
		);

		$.urlParam = function(name){
			var results = new RegExp( '[\\?&]' + name + '=([^&#]*)' ).exec( window.location.href );
			return results === null ? 0 : results[1];
		}

		// setup
		new_old_switch( 'new' );

		/* Clone setup */
		var group_type = $.urlParam( 'type' );

		var $create_or_clone = $( 'input[name="create-or-clone"]' );

		var create_or_clone = 'create';
		if ( $create_or_clone.length > 0 ) {
			create_or_clone = $create_or_clone.val();
		}

		if ( 'admin' !== form_type && OLGroupCreate.groupTypeCanBeCloned ) {
			var group_id_to_clone, new_create_or_clone;

			group_id_to_clone = $.urlParam( 'clone' );

			if ( group_id_to_clone ) {
				// Clone ID passed to URL
				toggle_clone_options( 'on' );
			} else {
				// No clone ID passed to URL
				toggle_clone_options( 'create' == create_or_clone ? 'off' : 'on' );
			}

			$create_or_clone.on(
				'change',
				function() {
					new_create_or_clone = 'create' == $( this ).val() ? 'off' : 'on';
					toggle_clone_options( new_create_or_clone );
				}
			);
		}

		// Switching between groups to clone
		$( '#group-to-clone' ).on(
			'change',
			function() {
				fetch_clone_source_details( this.value );
			}
		);

		/* AJAX validation for external RSS feeds */
		$( '#find-feeds' ).on(
			'click',
			function(e) {
				e.preventDefault();
				do_external_site_query( e );
			}
		);

		/* "Set up a site" toggle */
		$setuptoggle.on( 'click', function(){
			showHideAll();
			maybeShowSiteFields();
			$setuptoggle_mirror.trigger( 'click' )
		} );

		if ( $setuptoggle.is( ':checked' ) ) {
			showHideAll();
		};
		maybeShowSiteFields();

		setTimeout( maybeShowSiteTemplates, 1500 )

		if ( 'course' === group_type && ! $setuptoggle.is( ':checked' ) ) {
			$setuptoggle.trigger( 'click' );
		}

		// Set up Invite Anyone autocomplete
		if ( typeof ia_on_autocomplete_select !== 'undefined' ) {
			$( '#send-to-input' ).autocomplete(
				{
					serviceUrl: ajaxurl,
					width: 300,
					delimiter: /(,|;)\s*/,
					onSelect: ia_on_autocomplete_select,
					deferRequestBy: 300,
					params: { action: 'invite_anyone_autocomplete_ajax_handler' },
					noCache: true
				}
			);
		}
	},
	(jQuery)
);
