( function( $ ) {
	var added_icons = false,
		calendars = [],
		l10n;

	/**
	 * Bindings etc that happen on $(document).ready.
	 */
	init = function() {

		// test for our localisation object
		if ( 'undefined' !== typeof BpEventOrganiserSettings ) {
			l10n = BpEventOrganiserSettings;
		}

		// test if we have wp.hooks
		if ( 'undefined' !== typeof wp && 'undefined' !== typeof wp.hooks ) {

			// Filter interface only appears on My Events.
			if ( $( 'body' ).hasClass( 'bp-user' ) ) {
				// Add filter for event rendering.
				wp.hooks.addFilter(
					'eventorganiser.fullcalendar_render_event',
					function( retval, eo_event, eo_event_link, monthview ) {
						prerender_event( eo_event, eo_event_link.closest( '.eo-fullcalendar' ).attr( 'id' ) );
						return retval;
					}
				);

				// Register eventAfterRender callback.
				wp.hooks.addFilter(
					'eventorganiser.fullcalendar_options',
					function( args, calendar ) {
						args.eventAfterRender = function( eo_event, eo_event_link, calendar ) {
							afterrender_event( eo_event, eo_event_link, calendar );
						}

						return args;
					}
				);

				// Set up the calendar div.
				create_calendar_filter();
			}
		}
	};

	/**
	 * Create the calendar filter interface.
	 */
	create_calendar_filter = function() {
		$( '.eo-fullcalendar' ).append(
			'<div class="bpeo-calendar-filter bpeo-calendar-filter-empty">' + "\n" +
				'<h3>' + l10n.calendar_filter_title + '</h3>' + "\n" +

				'<div class="bpeo-calendar-filter-type bpeo-calendar-filter-type-author bpeo-calendar-filter-empty">' + "\n" +
					'<h4>' + l10n.calendar_author_filter_title + '</h4>' + "\n" +
					'<ul></ul>' + "\n" +
				'</div>' + "\n" +

				'<div class="bpeo-calendar-filter-type bpeo-calendar-filter-type-group bpeo-calendar-filter-empty">' + "\n" +
					'<h4>' + l10n.calendar_group_filter_title + '</h4>' + "\n" +
					'<ul></ul>' + "\n" +
				'</div>' + "\n" +
			'</div>'
		);

		// Bind filter click events.
		$( '.bpeo-calendar-filter' ).on( 'click', 'input', function( e ) {
			process_filter_click( $( e.target ) );
		} );
	}

	/**
	 * Process events as they are rendered.
	 *
	 * When an event is posted, we detect whether there's a corresponding filter for its author and connected
	 * groups. If not, we add them.
	 *
	 * @param {object} eo_event Event data object.
	 * @param {string} calendar_id ID of the parent calendar.
	 */
	prerender_event = function( eo_event, calendar_id ) {
		if ( 'undefined' === typeof calendars.calendar_id ) {
			calendars.calendar_id = { 'groups': {}, 'authors': {}, 'checked_groups': [], 'checked_authors': [] };
		}

		if ( 'undefined' !== typeof eo_event.groups ) {
			$.each( eo_event.groups, function( group_id, group_data ) {
				if ( 'undefined' === typeof calendars.calendar_id.groups[ group_id ] ) {
					add_group_filter( group_data, calendar_id );
				}
			} );
		}

		if ( 'undefined' === typeof calendars.calendar_id.authors[ eo_event.author.id ] ) {
			add_author_filter( eo_event.author, calendar_id );
		}

		return eo_event;
	};

	/**
	 * Add a group filter to a calendar.
	 */
	add_group_filter = function( group_data, calendar_id ) {
		var checkbox,
			group_id = parseInt( group_data.id );

		// Stash data about the group with the calendar.
		calendars.calendar_id.groups[ group_id ] = group_data;
		calendars.calendar_id.checked_groups.push( group_id );

		// Create the checkbox.
		checkbox  = '<input type="checkbox" id="bpeo-group-filter-' + group_id + '" value="1" name="bpeo-group-filter-' + calendar_id + '" data-group-id="' + group_id + '" checked="checked" />';
		checkbox += '<span class="bpeo-calendar-icon bpeo-calendar-icon-group" style="background-color:#' + group_data.color + '"></span>';
		checkbox += '<label for="bpeo-group-filter-' + group_id + '">' + group_data.name + '</label>';

		if ( 'undefined' === typeof calendars.calendar_id.filter_groups ) {
			calendars.calendar_id.filter_groups = $( '#' + calendar_id ).find( '.bpeo-calendar-filter-type-group ul' );
		}

		// Append to filter list.
		calendars.calendar_id.filter_groups.append( '<li>' + checkbox + '</li>' );

		// Make visible and sort.
		calendars.calendar_id.filter_groups.closest( '.bpeo-calendar-filter' ).removeClass( 'bpeo-calendar-filter-empty' );
		calendars.calendar_id.filter_groups.closest( '.bpeo-calendar-filter-type' ).removeClass( 'bpeo-calendar-filter-empty' );

		sort_by_label( calendars.calendar_id.filter_groups );
	};

	/**
	 * Add an author filter to a calendar.
	 */
	add_author_filter = function( author_data, calendar_id ) {
		var checkbox,
			author_id = parseInt( author_data.id );

		if ( 'undefined' === typeof calendars.calendar_id.filter_authors ) {
			calendars.calendar_id.filter_authors = $( '#' + calendar_id ).find( '.bpeo-calendar-filter-type-author ul' );
		}

		// Stash data about the author with the calendar.
		calendars.calendar_id.authors[ author_id ] = author_data;
		calendars.calendar_id.checked_authors.push( author_id );

		// Create the checkbox.
		checkbox  = '<input type="checkbox" id="bpeo-author-filter-' + author_id + '" value="1" name="bpeo-author-filter-' + calendar_id + '" data-author-id="' + author_id + '" />';
		checkbox += '<span class="bpeo-calendar-icon bpeo-calendar-icon-author" style="border-bottom-color:#' + author_data.color + '"></span>';
		checkbox += '<label for="bpeo-author-filter-' + author_id + '">' + author_data.name + '</label>';

		// Append to filter list.
		calendars.calendar_id.filter_authors.append( '<li>' + checkbox + '</li>' );

		// Make visible and sort.
		calendars.calendar_id.filter_authors.closest( '.bpeo-calendar-filter' ).removeClass( 'bpeo-calendar-filter-empty' );
		calendars.calendar_id.filter_authors.closest( '.bpeo-calendar-filter-type' ).removeClass( 'bpeo-calendar-filter-empty' );

		sort_by_label( calendars.calendar_id.filter_authors, l10n.loggedin_user_id );
	};

	/**
	 * Sort list of filters by label.
	 */
	sort_by_label = function( $list, author_first ) {
		var items = $list.children();

		if ( 'undefined' === typeof author_first ) {
			author_first = null;
		}

		items.sort( function( a, b ) {
			var aname = $( a ).find( 'label' ).text();
			var bname = $( b ).find( 'label' ).text();

			if ( aname < bname ) return -1;
			if ( aname > bname ) return 1;
			return 0;
		} );

		// Groan. Keep author at the top.
		if ( author_first ) {
			items.each( function( k, v ) {
				if ( author_first == $( v ).find( 'input' ).data( 'author-id' ) ) {
					// Remove the original.
					items.splice( k, 1 )

					// Put the author at the beginning of the array.
					items.splice( 0, 0, v );

					// Check the author box.
					var $author_cb = $( items[0] ).find( 'input' );
					$author_cb.prop( 'checked', true );
					process_filter_click( $author_cb );
					return false;
				}
			} );
		}

		$list.empty();
		$list.append( items );
	};

	/**
	 * Process a filter click.
	 */
	process_filter_click = function( $clicked ) {
		var calendar_id = $clicked.closest( '.eo-fullcalendar' ).attr( 'id' );
		var group_id = $clicked.data( 'group-id' );
		var author_id = $clicked.data( 'author-id' );
		var $current;

		if ( group_id ) {
			calendars.calendar_id.checked_groups = [];
			calendars.calendar_id.filter_groups.find( 'li input' ).each( function( k, v ) {
				$current = $( v );
				if ( $current.is( ':checked' ) ) {
					calendars.calendar_id.checked_groups.push( parseInt( $current.data( 'group-id' ) ) );
				}
			} );
		} else if ( author_id ) {
			calendars.calendar_id.checked_authors = [];
			calendars.calendar_id.filter_authors.find( 'li input' ).each( function( k, v ) {
				$current = $( v );
				if ( $current.is( ':checked' ) ) {
					calendars.calendar_id.checked_authors.push( parseInt( $current.data( 'author-id' ) ) );
				}
			} );
		}

		show_hide_events( calendar_id );
	};

	/**
	 * Show/hide events based on checked authors/groups.
	 */
	show_hide_events = function( calendar_id ) {
		var event_author,
			event_classes,
			event_groups = [],
			has_checked_author = false,
			has_checked_group = false;

		$( '#' + calendar_id ).find( '.eo-event' ).each( function( k, e ) {
			event_author = '';
			event_groups = [];
			event_classes = e.className.split( ' ' );
			$.each( event_classes, function( eck, event_class ) {
				// Cast to ints so $.inArray strict check passes later on.
				if ( 'eo-event-author-' === event_class.substr( 0, 16 ) ) {
					event_author = +event_class.substr( 16 );
				} else if( 'eo-event-bp-group-' === event_class.substr( 0, 18 ) ) {
					event_groups.push( +event_class.substr( 18 ) );
				}
			} );

			// If the item doesn't belong to any checked authors or groups, hide it.
			has_checked_author = false;
			has_checked_group = false;

			if ( '-1' != $.inArray( event_author, calendars.calendar_id.checked_authors ) ) {
				has_checked_author = true;
			} else if ( event_groups.length > 0 ) {
				// See if any of the event groups are checked.
				$.each( event_groups, function( egk, event_group ) {
					if ( '-1' != $.inArray( event_group, calendars.calendar_id.checked_groups ) ) {
						has_checked_group = true;
						return true; // break
					}
				} );
			}

			if ( has_checked_author || has_checked_group ) {
				$( e ).removeClass( 'bpeo-event-hidden' );
			} else {
				$( e ).addClass( 'bpeo-event-hidden' );
			}
		} );
	};

	/**
	 * Add author and group calendar icons to event.
	 */
	afterrender_event = function( eo_event, eo_event_link, calendar ) {
		var author_icon,
			group_icons = [],
			icon_div;

		author_icon = '<span class="bpeo-calendar-icon bpeo-calendar-icon-author" title="' + eo_event.author.name + '" style="border-bottom-color:#' + eo_event.author.color + '"></span>';

		if ( 'undefined' !== typeof eo_event.groups ) {
			$.each( eo_event.groups, function( group_id, group_data ) {
				group_icons.push( '<span class="bpeo-calendar-icon bpeo-calendar-icon-group" title="' + group_data.name + '" style="background-color:#' + group_data.color + '"></span>' );
			} );
		}

		icon_div = '<div class="bpeo-event-icons">' + author_icon + group_icons.join( '' ) + '</div>';

		eo_event_link.append( icon_div );

		reset_week_height( eo_event, eo_event_link, calendar );
	};

	/**
	 * Reset week height.
	 */
	reset_week_height = function( eo_event, eo_event_link, calendar ) {
		var event_cell = calendar.dateToCell( eo_event._start );
		var event_height = eo_event_link.height();
		var weeks = calendar.element.find( '.fc-week' );
		var $week = $( weeks[ event_cell.row ] );
		var $week_content_div = $week.find( '.fc-day-content > div' );

		var event_delta = event_height - $week_content_div.height();
		if ( event_delta <= 0 ) {
			return;
		}

		// Adjust the week height.
		$week_content_div.height( event_height );

		// Align the event with the top of the content cell.
		if ( $week_content_div.length ) {
			eo_event_link.css( 'top', $week_content_div.position().top );
		}
	};

	$( document ).ready( function() {
		init();
	});
}( jQuery ));
