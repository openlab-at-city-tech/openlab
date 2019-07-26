if (window.OpenLab === undefined) {
	var OpenLab = {};
}

var resizeTimer;

OpenLab.utility = (function ($) {

	return {
		newMembers: {},
		newMembersHTML: {},
		protect: 0,
		mapCheck: {},
		uiCheck: {},
		selectDisplay: {},
		customSelectHTML: '',
		init: function () {

			OpenLab.utility.adjustLoginBox();
			OpenLab.utility.sliderFocusHandler();
			OpenLab.utility.eventValidation();
			OpenLab.utility.refreshActivity();
			OpenLab.utility.initMemberRoleDefinitions();

			//EO Calendar JS filtering
			if (typeof wp !== 'undefined' && typeof wp.hooks !== 'undefined') {
				wp.hooks.addFilter( 'eventorganiser.fullcalendar_options', OpenLab.utility.calendarFiltering );
			}

			//BP EO Editor tweaks
			//doing this client-side for now
			OpenLab.utility.BPEOTweaks();

			// Accessibility mods for Settings > Email Notifications
			OpenLab.utility.EmailSettingsA11y();
		},
		detectZoom: function () {

			var zoom   = detectZoom.zoom();
			var device = detectZoom.device();

		},
		adjustLoginBox: function () {
			if ($( '#user-info' )) {

				var userInfo = $( '#user-info' );
				var helpInfo = $( '#login-help' )
				var avatar   = userInfo.find( '.avatar' );
				if (userInfo.height() > avatar.height()) {
					userInfo.addClass( 'multi-line' );
					helpInfo.addClass( 'multi-line' );
				} else {
					userInfo.removeClass( 'multi-line' );
					helpInfo.removeClass( 'multi-line' );
				}

			}
		},
		sliderFocusHandler: function () {

			if ($( '.camera_wrap_sr' ).length) {

				$( '.camera_wrap_sr .camera_content a' ).each(
					function () {

						var thisLink = $( this );
						thisLink.on(
							'focus',
							function () {

								thisLink.closest( '.camera_content' ).addClass( 'focus' );

							}
						);
						thisLink.on(
							'blur',
							function () {

								thisLink.closest( '.camera_content' ).removeClass( 'focus' );

							}
						);

					}
				);

			}

		},
		eventValidation: function () {

			var eventPublish       = $( '.action-events #publish' );
			var groupMetaBox       = $( '#bp_event_organiser_metabox .inside' );
			var eventDetailMetaBox = $( '#eventorganiser_detail .inside' );

			if (eventPublish.length) {

				eventPublish.on(
					'click',
					function (e) {

						//can't submit an event without a group selection
						var groupSelection = $( '#bp_event_organiser_metabox .select2-selection__rendered .select2-selection__choice' );

						if ( ! groupSelection.length) {
							e.preventDefault();

							var message = '<div class="bp-template-notice error">Events must be associated with at least one group.</div>';
							groupMetaBox.prepend( message );
						} else {
							groupMetaBox.find( '.bp-template-notice' ).remove();
						}

						//can't submit an event if the end time is *before* the start time (or vice versa)
						var rawStartTime = eventDetailMetaBox.find( '#eo-start-time' ).val();
						var rawStartDate = eventDetailMetaBox.find( '#eo-start-date' ).val();
						var rawEndTime   = eventDetailMetaBox.find( '#eo-end-time' ).val();
						var rawEndDate   = eventDetailMetaBox.find( '#eo-end-date' ).val();

						var startTime = OpenLab.utility.buildTime( rawStartDate, rawStartTime );
						var endTime   = OpenLab.utility.buildTime( rawEndDate, rawEndTime );

						if (startTime > endTime) {
							e.preventDefault();
							var message = '<div class="bp-template-notice error">Start Time must be earlier than the End Time.</div>';

							//clean up first before adding new error message
							eventDetailMetaBox.find( '.bp-template-notice' ).remove();
							eventDetailMetaBox.prepend( message );
						} else {
							eventDetailMetaBox.find( '.bp-template-notice' ).remove();
						}

					}
				);

			}

		},
		venueMapControl: function () {

			var latCheck = $( '#eo_venue_Lat' );
			var venueMap = $( '#venuemap' );

			//if there is no venue present, time to quit
			if (typeof eovenue === 'undefined' && ! venueMap.length) {
				return;
			}

			//on initial load, hide map if we have no LatLng values
			if (latCheck.val() === 'NaN' || parseInt( latCheck.val() ) === 0) {
				venueMap.css( 'display', 'none' );
			}

			OpenLab.utility.protect++;

			//going to use an interval to pick up on the map obj
			if (typeof eovenue.maps !== 'undefined' && Object.keys( eovenue.maps ).length > 0) {

				//saftey first
				clearTimeout( OpenLab.utility.menuCheck );

				eovenue.maps.venuemap.map.addListener(
					'center_changed',
					function () {
						if (latCheck.val() === 'NaN' || parseInt( latCheck.val() ) === 0) {
							venueMap.css( 'display', 'none' );
						} else {
							venueMap.css( 'display', 'block' );
						}

					}
				);

			} else {

				if (OpenLab.utility.protect < 2000) {
					OpenLab.utility.mapCheck = setTimeout( OpenLab.utility.venueMapControl(), 50 );
				}

			}

		},
		venueDropdownControl: function () {

			var dropdownSelector = $( '#venue_select' );

			OpenLab.utility.protect++;

			if (dropdownSelector.length) {

				var comboBoxSelector = $( '#venue_select.ui-combobox-input' );

				if (comboBoxSelector.length) {

					//safety first
					clearTimeout( OpenLab.utility.uiCheck );

					comboBoxSelector.on(
						"autocompletesearch",
						function (event, ui) {

							event.preventDefault();

						}
					);

				} else {

					if (OpenLab.utility.protect < 2000) {
						OpenLab.utility.uiCheck = setTimeout( OpenLab.utility.venueDropdownControl(), 50 );
					}

				}
			}

		},
		convertTimeToNum: function (time) {
			var hoursMinutes = time.split( /[.:]/ );
			var hours        = parseInt( hoursMinutes[0], 10 );

			var partOfDay = 0;

			if (hoursMinutes[1].indexOf( 'pm' ) !== -1) {
				partOfDay = 12;
			}

			var minutes = hoursMinutes[1] ? parseInt( hoursMinutes[1], 10 ) : 0;

			return partOfDay + hours + minutes / 60;
		},
		buildTime: function (date, time) {
			var d         = new Date();
			var dateParts = date.split( '-' );
			d.setFullYear( dateParts[2] );
			d.setMonth( dateParts[0] );
			d.setDate( dateParts[1] );

			var timeParts = time.split( /[.:]/ );
			var hour      = parseInt( timeParts[0] );
			var min       = parseInt( timeParts[1].substr( 0, 2 ) );
			var amOrPm    = timeParts[1].substr( 2 );

			if ('pm' === amOrPm && hour < 12) {
				hour = hour + 12;
			} else if ('am' === amOrPm && hour === 12) {
				//clock strikes midnight
				hour = 0;
			}

			d.setHours( hour );
			d.setMinutes( min );

			return d;
		},
		calendarFiltering: function (args, calendar) {

			if (calendar.defaultview === 'agendaWeek') {
				args.scrollTime = '08:00:00';
				args.viewRender = function (view, element) {
					OpenLab.utility.calendarScrollBarPadding( view, element );
					OpenLab.utility.calendarButtonCustomization( view, element );
				}
			} else {
				args.viewRender = function (view, element) {
					OpenLab.utility.calendarScrollBarPadding( view, element );
					OpenLab.utility.calendarButtonCustomization( view, element );
				}
			}

			return args;

		},
		calendarScrollBarPadding: function (view, element) {

			if (view.name === 'agendaWeek') {

				var width = OpenLab.utility.getScrollBarWidth();

				console.log( 'width', width );

				$( '.eo-fullcalendar .fc-row.fc-widget-header' ).wrap( "<div class='fc-header-wrapper'></div>" );

				$( '.eo-fullcalendar .fc-day-grid, .eo-fullcalendar .fc-header-wrapper' ).css(
					{
						'border-right': width + 'px #f3f3f3 solid'
					}
				);

			}

		},
		calendarButtonCustomization: function (view, element) {

			//add sr-only text for accessibility
			var buttons = $( '.eo-fullcalendar .fc-button-group' );

			//get viewtype
			var viewLabel = 'Month';

			if (view.name === 'agendaWeek') {
				viewLabel = 'Week';

				//if this is a week view, also fill in empty table header
				$( '.fc-agendaWeek-view .fc-axis.fc-widget-header' ).text( 'Time' );

			}

			buttons.find( '.fc-button' ).each(
				function () {

					var thisButton = $( this );

					var direction = 'Previous';

					if (thisButton.hasClass( 'fc-next-button' )) {
						direction = 'Next';
					}

					var label = '<span class="sr-only">' + direction + ' ' + viewLabel + '</span>';

					thisButton.find( '.fc-icon' ).html( label );

				}
			);

		},
		getScrollBarWidth: function () {

			var scrollDiv       = document.createElement( "div" );
			scrollDiv.className = "scrollbar-measure";
			document.body.appendChild( scrollDiv );

			var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;

			document.body.removeChild( scrollDiv );

			return scrollbarWidth;

		},
		BPEOTweaks: function () {

			var bpeo_metabox = $( '#bp_event_organiser_metabox' );

			if (bpeo_metabox.length) {

				var desc = ' <span class="bold">The event will appear in the OpenLab sitewide calendar unless one or more of the groups selected is private.</span>';

				bpeo_metabox.find( '.inside .bp_event_organiser_desc' ).append( desc );
				bpeo_metabox.find( '.hndle span' ).text( 'Display' );

			}

		},
		setUpNewMembersBox: function (resize) {

			if (resize) {
				//OpenLab.utility.newMembers.html(OpenLab.utility.newMembersHTML);
				OpenLab.utility.newMembers.trigger( 'refreshCarousel', '[all]' )
			} else {
				OpenLab.utility.newMembers     = $( '#home-new-member-wrap' );
				OpenLab.utility.newMembersHTML = $( '#home-new-member-wrap' ).html();

				//this is for the new OpenLab members slider on the homepage
				OpenLab.utility.newMembers.jCarouselLite(
					{
						circular: true,
						btnNext: ".next",
						btnPrev: ".prev",
						vertical: false,
						visible: 2,
						auto: true,
						speed: 200,
						autoWidth: true,
					}
				);
			}

			$( '#home-new-member-wrap' ).css( 'visibility', 'visible' ).hide().fadeIn(
				700,
				function () {
					console.log( 'openLab', OpenLab );
					OpenLab.truncation.truncateOnTheFly( false, true );

				}
			);
		},
		refreshActivity: function () {

			var refreshActivity = $( '#refreshActivity' );

			if ( ! refreshActivity.length) {
				return;
			}

			var activityContainer = $( '#whatsHappening' );

			//safety first
			refreshActivity.off( 'click' );

			refreshActivity.on(
				'click',
				function (e) {

					e.preventDefault();
					refreshActivity.addClass( 'fa-spin' );

					$.ajax(
						{
							type: 'GET',
							url: ajaxurl,
							data:
							{
								action: 'openlab_ajax_return_latest_activity',
								nonce: localVars.nonce
							},
							success: function (data, textStatus, XMLHttpRequest)
						{
								refreshActivity.removeClass( 'fa-spin' );
								if (data === 'exit') {
									//for right now, do nothing
								} else {
									activityContainer.html( data );
								}
							},
							error: function (MLHttpRequest, textStatus, errorThrown) {
								refreshActivity.removeClass( 'fa-spin' );
								console.log( errorThrown );
							}
						}
					);

				}
			);

		},
		customSelects: function (resize) {
			//custom select arrows
			if (resize) {
				$( '.custom-select-parent' ).html( OpenLab.utility.customSelectHTML );
				$( '.custom-select select' ).select2(
					{
						minimumResultsForSearch: Infinity,
						width: "100%",
						escapeMarkup: function (text) {
							return text;
						}
					}
				);
			} else {
				OpenLab.utility.customSelectHTML = $( '.custom-select-parent' ).html();
				$( '.custom-select select' ).select2(
					{
						minimumResultsForSearch: Infinity,
						width: "100%",
						escapeMarkup: function (text) {
							return text;
						}
					}
				);
			}
		},
		sliderTagManagerTracking: function () {

			//record slider nav clicks
			$( '.camera_pag li' ).on(
				'click',
				function () {

					console.log( 'click nav' );

					dataLayer.push(
						{
							'event': 'openlab.click',
							'category': 'slider.nav',
							'label': $( '.cameraContents .cameraContent.cameracurrent h2' ).text()
						}
					);

				}
			);

		},
		EmailSettingsA11y: function() {
			$( '.notification-settings' ).find( 'th.icon' ).each(
				function() {
					$( this ).html( '<span class="bp-screen-reader-text">Icon column</span>' );
				}
			);
		},
		initMemberRoleDefinitions: function() {
			$( '.member-role-definition-label' ).on(
				'click',
				function( e ) {
					$clicked = $( e.target );
					$def     = $clicked.closest( '.member-role-definition' );

					$clicked.find( 'i' ).toggleClass( 'fa-caret-square-o-right' ).toggleClass( 'fa-caret-square-o-down' );
					$clicked.closest( '.member-role-definition' ).toggleClass( 'show-definition-text' );
				}
			);
		}
	}
})( jQuery, OpenLab );

(function ($) {
	var related_links_count,
			$add_new_related_link,
			$relatedLinks,
			$cloned_related_link_fields;

	$( document ).ready(
		function () {

			OpenLab.utility.init();

			// Workshop fields on Contact Us
			function toggle_workshop_meeting_items() {
				if ( ! ! contact_us_topic) {
					if ('Request a Workshop / Meeting' == contact_us_topic.value) {
						$workshop_meeting_items.slideDown( 'fast' );
					} else {
						$workshop_meeting_items.slideUp( 'fast' );
					}
				}
			}

			function toggle_other_details() {
				if ('Other (please specify)' == $reason_for_request.val()) {
					$other_details.slideDown( 'fast' );
				} else {
					$other_details.slideUp( 'fast' );
				}
			}

			// + button on Related Links List Settings
			$add_new_related_link = $( '#add-new-related-link' );
			$add_new_related_link.on(
				'click',
				function () {
					create_new_related_link_field();
				}
			);

			$relatedLinks = $('.related-links-edit-items');
			$relatedLinks.on(
				'click',
				'.related-link-remove',
				function(e) {
					// If this is the only item, just clear the boxes. Otherwise, remove row.
					var $thisLink = $(e.target).closest('.related-links-edit-items > li');
					if ( $thisLink.siblings( 'li' ).length === 0 ) {
						$thisLink.find( 'input' ).val( '' );
					} else {
						$thisLink.remove();
					}
				}
			);

			var contact_us_topic    = document.getElementById( 'contact-us-topic' );
			$workshop_meeting_items = jQuery( '#workshop-meeting-items' );
			jQuery( '#contact-us-topic' ).on(
				'change',
				function () {
					toggle_workshop_meeting_items();
				}
			);
			toggle_workshop_meeting_items();

			// Move the contact form output field to the bottom of the form.
			var contact_us_response_output = jQuery( '.wpcf7-response-output' );
			if (contact_us_response_output.length > 0) {
				contact_us_response_output.appendTo( contact_us_response_output.closest( 'form' ) );
			}

			$other_details      = jQuery( '#other-details' );
			$reason_for_request = jQuery( '#reason-for-request' );
			$reason_for_request.on(
				'change',
				function () {
					toggle_other_details();
				}
			);
			toggle_other_details();

			jQuery( '#wds-accordion-slider' ).easyAccordion(
				{
					autoStart: true,
					slideInterval: 6000,
					slideNum: false
				}
			);

			jQuery( "#header #menu-item-40 ul li ul li a" ).prepend( "+ " );

			// this add an onclick event to the "New Topic" button while preserving
			// the original event; this is so "New Topic" can have a "current" class
			$( '.show-hide-new' ).click(
				function () {
					var origOnClick = $( '.show-hide-new' ).onclick;
					return function (e) {
						if (origOnClick != null && ! origOnClick()) {
							return false;
						}
						return true;
					}
				}
			);

			window.new_topic_is_visible = $( '#new-topic-post' ).is( ":visible" );
			$( '.show-hide-new' ).click(
				function () {
					if (window.new_topic_is_visible) {
						$( '.single-forum #message' ).slideUp( 300 );
						window.new_topic_is_visible = false;
					} else {
						$( '.single-forum #message' ).slideDown( 300 );
						window.new_topic_is_visible = true;
					}
				}
			);

			//printing page
			if ($( '.print-page' ).length) {
				$( '.print-page' ).on(
					'click',
					function (e) {
						e.preventDefault();
						window.print();
					}
				);
			}

			function clear_form() {
				document.getElementById( 'group_seq_form' ).reset();
			}

			//member profile friend/cancel friend hover fx
			if ($( '.btn.is_friend.friendship-button' ).length) {
				var allButtons = $( '.btn.is_friend.friendship-button' );
				allButtons.each(
					function () {
						var thisButton     = $( this );
						var thisButtonHTML = $( this ).html();
						thisButton.hover(
							function () {
								thisButton.html( '<span class="pull-left"><i class="fa fa-user"></i> Cancel Friend</span><i class="fa fa-minus-circle pull-right"></i>' );
							},
							function () {
								thisButton.html( thisButtonHTML );
							}
						);
					}
				);
			}

			//member notificatoins page - injecting Bootstrap classes
			if ($( 'table.notification-settings' ).length) {
				$( 'table.notification-settings' ).each(
					function () {
						$( this ).addClass( 'table' );
					}
				);
			}

			//clear login form
			if ($( '#user-login' ).length) {
				$( '#sidebar-user-login, #sidebar-user-pass' ).on(
					'focus',
					function () {
						$( this ).attr( 'placeholder', '' );
					}
				);
			}

			// Move the 'public group' notification setting.
			var public_group_not = $( '#groups-notification-settings-joined-my-public-group' );
			if (public_group_not.length) {
				public_group_not.remove();
				$( '#groups-notification-settings-request' ).after( public_group_not );
			}

				$( '#bp-group-documents-folder-delete' ).click(
					function(e){
						if ( confirm( 'Are you sure you wish to permanently delete this folder? The files associated with this folder will not be deleted.' ) ) {
							return true;
						}
						return false;
					}
				);

		}
	);//end document.ready

	$( window ).on(
		'resize',
		function (e) {

			clearTimeout( resizeTimer );
			resizeTimer = setTimeout(
				function () {

					OpenLab.utility.adjustLoginBox();
					OpenLab.utility.customSelects( true );

					if ($( '#home-new-member-wrap' ).length) {
						OpenLab.utility.setUpNewMembersBox( true );
					}

				},
				250
			);

		}
	);

	$( window ).load(
		function () {

			$( 'html' ).removeClass( 'page-loading' );
			OpenLab.utility.detectZoom();
			OpenLab.utility.customSelects( false );
			OpenLab.utility.venueMapControl();
			OpenLab.utility.venueDropdownControl();

			//setting equal rows on homepage group list
			equal_row_height();

			//camera js slider on home
			if ($( '.camera_wrap' ).length) {
				$( '.camera_wrap' ).camera(
					{
						autoAdvance: true,
						loader: 'none',
						fx: 'simpleFade',
						playPause: false,
						height: '295px',
						navigation: false,
						navigationHover: false,
						onLoaded: function () {

							var cameraImages = $( '.camera_wrap .camera_target' );

							//have to do this because on first load, the first image is not
							//actually 'loaded' per se
							if ( ! cameraImages.hasClass( 'fully-loaded' )) {

								cameraImages.addClass( 'fully-loaded' );

								//initiate GTM tracking
								OpenLab.utility.sliderTagManagerTracking();

							}

						},
						onEndTransition: function () {

							//record slider link clicks
							$( '.cameraContents .cameraContent.cameracurrent .camera_content a' ).on(
								'click',
								function () {

									console.log( 'click link' );

									dataLayer.push(
										{
											'event': 'openlab.click',
											'category': 'slider.link',
											'label': $( this ).text()
										}
									);

								}
							);
						}
					}
				);
			}

			if ($( '#home-new-member-wrap' ).length) {
				OpenLab.utility.setUpNewMembersBox( false );
			}

		}
	);

	$( document ).ajaxComplete(
		function () {

			if ($( '.wpcf7' ).length && ! $( '.wpcf7-mail-sent-ok' ).length) {
				$( '.wpcf7-form-control-wrap' ).each(
					function () {
						var thisElem = $( this );
						if (thisElem.find( '.wpcf7-not-valid-tip' ).text()) {

							thisElem.remove( '.wpcf7-not-valid-tip' );

							var thisText    = 'Please enter your ' + thisElem.find( '.wpcf7-form-control' ).attr( 'name' );
							var newValidTip = '<div class="bp-template-notice error" style="display: none;"><p>' + thisText + '</p></div>';

							thisElem.prepend( newValidTip );
							thisElem.find( '.bp-template-notice.error' ).css( 'visiblity', 'visible' ).hide().fadeIn( 550 );

						}
					}
				);
			}
			if ($( '.wpcf7' ).length && $( '.wpcf7-mail-sent-ok' ).length) {
				$( '.wpcf7-form-control-wrap' ).each(
					function () {
						var thisElem = $( this );
						if (thisElem.find( '.bp-template-notice.error' )) {
							thisElem.remove( '.bp-template-notice.error' );
						}
					}
				);
			}

		}
	);

	function create_new_related_link_field() {
		$cloned_related_link_fields = $relatedLinks.find('li:first-child').clone();

		// Get count of existing link fields for the iterator
		related_links_count = $( '.related-links-edit-items li' ).length + 1;

		// Swap label:for and input:id attributes
		$cloned_related_link_fields.html(
			function (i, old_html) {
				return old_html.replace( /(related\-links\-)[0-9]+\-(name|url)/g, '$1' + related_links_count + '-$2' );
			}
		);

		// Swap name iterator
		$cloned_related_link_fields.html(
			function (i, old_html) {
				return old_html.replace( /(related\-links\[)[0-9]+(\])/g, '$1' + related_links_count + '$2' );
			}
		);

		// Add new fields to the DOM
		$( '.related-links-edit-items' ).append( $cloned_related_link_fields );

		// Remove values
		$( '#related-links-' + related_links_count + '-name' ).val( '' );
		$( '#related-links-' + related_links_count + '-url' ).val( '' );
	}

	/*this is for the homepage group list, so that cells in each row all have the same height
	 - there is a possiblity of doing this template-side, but requires extensive restructuring of the group list function*/
	function equal_row_height() {
		/*first we get the number of rows by finding the column with the greatest number of rows*/
		var $row_num = 0;
		$( '.activity-list' ).each(
			function () {
				var $row_check = $( this ).find( '.activity-item' ).length;

				if ($row_check > $row_num) {
					$row_num = $row_check;
				}
			}
		);

		//build a loop to iterate through each row
		var $i = 1;

		while ($i <= $row_num) {
			//check each cell in the row - find the one with the greatest height
			var $greatest_height = 0;

			$( '.row-' + $i ).each(
				function () {
					var $cell_height = $( this ).outerHeight();

					if ($cell_height > $greatest_height) {
						$greatest_height = $cell_height;
					}

				}
			);

			//now apply that height to the other cells in the row
			$( '.row-' + $i ).css( 'height', $greatest_height + 'px' );

			//iterate to next row
			$i++;
		}

		//there is an inline script that hides the lists from the user on load (just so the adjusment isn't jarring) - this will show the lists
		$( '.activity-list' ).css( 'visibility', 'visible' ).hide().fadeIn( 700 );

	}

})( jQuery );
