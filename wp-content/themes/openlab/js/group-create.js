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
		form_validated = false,
		new_group_type = $( '#new-group-type' ).val(),
		$body          = $( 'body' ),
		$gc_submit     = $( '#group-creation-create' ),
		$required_fields,
    $setuptoggle   = $( 'input[name="wds_website_check"]' );

		if ( $body.hasClass( 'group-admin' ) ) {
			form_type = 'admin';
			form      = document.getElementById( 'group-settings-form' );
		} else {
			form_type = 'create';
			form      = document.getElementById( 'create-group-form' );
		}

		$form = $( form );

		$required_fields = $form.find( 'input:required' );

		function maybeShowSiteFields() {
			if ( ! $setuptoggle.length ) {
				return;
			}

			var showSiteFields = $setuptoggle.is( ':checked' );

			if ( showSiteFields ) {
				$( '#site-options' ).show();
			} else {
				$( '#site-options' ).hide();
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
												$( element ).removeProp( 'disabled' ).removeClass( 'disabled' );
							}
						);
						$( thisid ).find( 'select' ).each(
							function(index,element){
								if ($( element ).attr( 'type' ) !== 'radio') {
									$( element ).removeClass( 'disabled-opt' );
									$( element ).removeProp( 'disabled' ).removeClass( 'disabled' );
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
			$gc_submit.removeAttr( 'disabled' );
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

						// Additional Description
						$( 'textarea[name="wds_course_html"]' ).val( r.additional_description );

						// Associated site
						if ( r.site_id ) {
							// Check 'Set up a site'
							if ( ! $setuptoggle.is( ':checked' ) ) {
								$setuptoggle.trigger( 'click' );
							}

							// Un-grey the website clone options
							$( '#wds-website-clone .radio' ).removeClass( 'disabled-opt' );
							$( '#wds-website-clone input[name="new_or_old"]' ).removeAttr( 'disabled' );

							// Auto-select the "Name your cloned site" option,
							// and trigger setup JS
							$( '#new_or_old_clone' ).attr( 'checked', true );
							$( '#new_or_old_clone' ).trigger( 'click' );

							// Site URL
							$( '#cloned-site-url' ).html( 'Your original address was: ' + r.site_url );
							$( '#blog-id-to-clone' ).val( r.site_id );
						} else {
							$( '#wds-website-clone .radio' ).addClass( 'disabled-opt' );
							$( '#wds-website-clone input[name="new_or_old"]' ).attr( 'disabled','disabled' );

							// Pre-select "Create a new site"
							$( '#new_or_old_new' ).attr( 'checked', true );
							$( '#new_or_old_new' ).trigger( 'click' );
						}

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

		if ( 'admin' !== form_type && OLGroupCreate.groupTypeCanBeCloned ) {
			var $create_or_clone, create_or_clone, group_id_to_clone, new_create_or_clone;

			$create_or_clone  = $( 'input[name="create-or-clone"]' );
			create_or_clone   = $create_or_clone.val();
			group_id_to_clone = $.urlParam( 'clone' );

			if ( 'create' === create_or_clone ) {
				// Show the Site Details section.
				$( '#panel-site-details' ).show();
			} else {
				$( '#panel-site-details' ).hide();
			}

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

					if ( 'create' === create_or_clone ) {
						// Show the Site Details section.
						$( '#panel-site-details' ).show();
					} else {
						$( '#panel-site-details' ).hide();
					}
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
		} );

		if ( $setuptoggle.is( ':checked' ) ) {
			showHideAll();
		};
		maybeShowSiteFields();

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

		$( '.domain-validate' ).on(
			'change',
			function() {
				form_validated = false;
			}
		);

		// Schools/Departments are required fields for Courses.
		$gc_submit.on(
			'mouseover focus',
			function() {
				if ( 'course' == new_group_type ) {
					var school_tech        = document.getElementById( 'school_tech' );
					var is_school_selected = $( '.school-inputs input:checked' ).length > 0;
					school_tech.setCustomValidity( is_school_selected ? '' : 'You must select a School.' );

					if ( is_school_selected ) {
						var is_department_selected = $( '.departments input:checked' ).length > 0;
						document.getElementsByClassName( 'wds-department' )[0].setCustomValidity( is_department_selected ? '' : 'You must select a Department.' );
					}
				}
			}
		);

		/**
		 * Form validation.
		 *
		 * - Site URL is validated by AJAX.
		 * - Name and Description use native validation.
		 */
		validate_form = function( event ) {
			event = ( event ? event : window.event );

			if ( form_validated ) {
				return true;
			}

			// If "Set up a site" is not checked, there's no validation to do
			if ( $setuptoggle.length && ! $setuptoggle.is( ':checked' ) ) {
				return true;
			}

			var new_or_old = $( 'input[name=new_or_old]:checked' ).val();
			var domain, $domain_field;

			// Different fields require different validation.
			switch ( new_or_old ) {
				case 'old' :

					// do something
					break;

				case 'clone' :
					$domain_field = $( '#clone-destination-path' );
					break;

				case 'new' :
					$domain_field = $( '#new-site-domain' );
					break;
			}

			if ( 'undefined' === typeof $domain_field ) {
				return true;
			}

			event.preventDefault();

			domain = $domain_field.val();

			var warn = $domain_field.siblings( '.ajax-warning' );
			if ( warn.length > 0 ) {
				warn.remove();
			}

			if ( 0 == domain.length ) {
				$domain_field.after( '<div class="ajax-warning bp-template-notice error">This field cannot be blank.</div>' );
				return false;
			}

			$.post(
				'/wp-admin/admin-ajax.php', // Forward-compatibility with ajaxurl in BP 1.6
				{
					action: 'openlab_validate_groupblog_url_handler',
					'path': domain
				},
				function( response ) {
					if ( 'exists' == response ) {
						$domain_field.after( '<div class="ajax-warning bp-template-notice error">Sorry, that URL is already taken.</div>' );
						return false;
					} else {
						// We're done validating.
						form_validated = true;
						$form.append( '<input name="save" value="1" type="hidden" />' );
						$form.submit();
						return true;
					}
				}
			);
		};

		// Form validation.
		if (form) {
			form.onsubmit = validate_form;
		}
	},
	(jQuery)
);
