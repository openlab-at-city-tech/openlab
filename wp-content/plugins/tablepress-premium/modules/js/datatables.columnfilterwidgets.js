/*
 * File:        ColumnFilterWidgets.js
 * Version:     1.0.5
 * Description: Controls for filtering based on unique column values in DataTables
 * Author:      Dylan Kuhn (www.cyberhobo.net)
 * Language:    Javascript
 * License:     GPL v2 or BSD 3 point style
 * Contact:     cyberhobo@cyberhobo.net
 *
 * Copyright 2011 Dylan Kuhn (except fnGetColumnData by Benedikt Forchhammer), all rights reserved.
 *
 * This source file is free software, under either the GPL v2 license or a
 * BSD style license, available at:
 *   http://datatables.net/license_gpl2
 *   http://datatables.net/license_bsd
 *
 * Quickly hacked to use new DataTables 1.10 API for extracting unique keys from columns by abs@absd.org
 *
 * With modifications regarding empty cells, special characters like &, HTML, and sorting by Tobias Bäthge.
 */

/* globals jQuery */
/* eslint-disable no-var */

( function( $ ) {
	/*
	 * Function: fnGetColumnData
	 * Purpose:  Return an array of table values from a particular column.
	 * Returns:  array string: 1d data array
	 */
	$.fn.dataTableExt.oApi.fnGetColumnData = function( oSettings, iColumn ) {
		return oSettings.oInstance.api().column( iColumn, { search: 'applied' } ).data().sort().unique();
	};

	/**
	 * Add backslashes to regular expression symbols in a string.
	 *
	 * Allows a regular expression to be constructed to search for
	 * variable text.
	 *
	 * @param {string} sText The text to escape.
	 * @return {string} The escaped string.
	 */
	var fnRegExpEscape = function( sText ) {
		return sText.replace( /[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&' );
	};

	/**
	 * Menu-based filter widgets based on distinct column values for a table.
	 *
	 * @class ColumnFilterWidgets
	 * @constructor
	 * @param {Object} oDataTableSettings Settings for the target table.
	 */
	var ColumnFilterWidgets = function( oDataTableSettings ) {
		var me = this;
		let columns = Object.keys( oDataTableSettings.aoColumns );
		me.$WidgetContainer = $( '<div class="column-filter-widgets"></div>' );
		me.$MenuContainer = me.$WidgetContainer;
		me.$TermContainer = null;
		me.aoWidgets = [];
		me.sSeparator = '';
		if ( 'oColumnFilterWidgets' in oDataTableSettings.oInit ) {
			if ( 'columns' in oDataTableSettings.oInit.oColumnFilterWidgets ) {
				columns = oDataTableSettings.oInit.oColumnFilterWidgets.columns;
			}
			if ( 'aiExclude' in oDataTableSettings.oInit.oColumnFilterWidgets ) {
				columns = columns.filter( ( column ) => {
					return ! oDataTableSettings.oInit.oColumnFilterWidgets.aiExclude.includes( parseInt( column, 10 ) );
				} );
			}
			if ( 'bGroupTerms' in oDataTableSettings.oInit.oColumnFilterWidgets && oDataTableSettings.oInit.oColumnFilterWidgets.bGroupTerms ) {
				me.$MenuContainer = $( '<div class="column-filter-widget-menus"></div>' );
				me.$TermContainer = $( '<div class="column-filter-widget-selected-terms"></div>' ).hide();
			}
		}

		// Add a widget for each visible and filtered column
		columns.forEach( function( columnIdx ) {
			const $WidgetElem = $( '<div class="column-filter-widget"></div>' );
			me.aoWidgets.push( new ColumnFilterWidget( $WidgetElem, oDataTableSettings, columnIdx, me ) );
			me.$MenuContainer.append( $WidgetElem );
		} );
		if ( me.$TermContainer ) {
			me.$WidgetContainer.append( me.$MenuContainer );
			me.$WidgetContainer.append( me.$TermContainer );
		}
		oDataTableSettings.aoDrawCallback.push( {
			name: 'ColumnFilterWidgets',
			fn() {
				$.each( me.aoWidgets, function( i, oWidget ) {
					oWidget.fnDraw();
				} );
			}
		} );

		return me;
	};

	/**
	 * Get the container node of the column filter widgets.
	 *
	 * @method
	 * @return {Node} The container node.
	 */
	ColumnFilterWidgets.prototype.getContainer = function() {
		return this.$WidgetContainer.get( 0 );
	};

	/**
	 * A filter widget based on data in a table column.
	 *
	 * @class ColumnFilterWidget
	 * @constructor
	 * @param {Object} $Container         The jQuery object that should contain the widget.
	 * @param {Object} oDataTableSettings The target table's settings.
	 * @param {number} i                  The numeric index of the target table column.
	 * @param {Object} widgets            The ColumnFilterWidgets instance the widget is a member of.
	 */
	var ColumnFilterWidget = function( $Container, oDataTableSettings, i, widgets ) {
		var widget = this;
		widget.iColumn = i;
		widget.oColumn = oDataTableSettings.aoColumns[i];
		widget.$Container = $Container;
		widget.oDataTable = oDataTableSettings.oInstance;
		widget.asFilters = [];
		widget.sSeparator = '';
		widget.bSort = true;
		widget.iMaxSelections = -1;
		if ( 'oColumnFilterWidgets' in oDataTableSettings.oInit ) {
			if ( 'sSeparator' in oDataTableSettings.oInit.oColumnFilterWidgets ) {
				widget.sSeparator = oDataTableSettings.oInit.oColumnFilterWidgets.sSeparator;
			}
			if ( 'iMaxSelections' in oDataTableSettings.oInit.oColumnFilterWidgets ) {
				widget.iMaxSelections = oDataTableSettings.oInit.oColumnFilterWidgets.iMaxSelections;
			}
			if ( 'aoColumnDefs' in oDataTableSettings.oInit.oColumnFilterWidgets ) {
				$.each( oDataTableSettings.oInit.oColumnFilterWidgets.aoColumnDefs, function( iIndex, oColumnDef ) {
					var sTargetList = '|' + oColumnDef.aiTargets.join( '|' ) + '|';
					if ( sTargetList.indexOf( '|' + i + '|' ) >= 0 ) {
						$.each( oColumnDef, function( sDef, oDef ) {
							widget[sDef] = oDef;
						} );
					}
				} );
			}
		}
		widget.$Select = $( '<select></select>' ).addClass( 'widget-' + widget.iColumn ).on( 'change', function() {
			var sSelected = widget.$Select.val(), sText, $TermLink, $SelectedOption;
			if ( '' === sSelected ) {
				// The blank option is a default, not a filter, and is re-selected after filtering
				return;
			}
			sText = $( '<div>' + sSelected + '</div>' ).text();
			$TermLink = $( '<a class="filter-term" href="#"></a>' )
				.addClass( 'filter-term-' + sText.toLowerCase().replace( /\W/g, '' ) )
				.text( sText )
				.on( 'click', function() {
					// Remove from current filters array
					widget.asFilters = $.grep( widget.asFilters, function( sFilter ) {
						return sFilter !== sSelected;
					} );
					$TermLink.remove();
					if ( widgets.$TermContainer && 0 === widgets.$TermContainer.find( '.filter-term' ).length ) {
						widgets.$TermContainer.hide();
					}
					// Add it back to the select
					widget.$Select.append( $( '<option></option>' ).val( sSelected ).text( sText ) );
					if ( widget.iMaxSelections > 0 && widget.iMaxSelections > widget.asFilters.length ) {
						widget.$Select.prop( 'disabled', false );
					}
					if ( widget.bSort ) {
						widget.fnSortOptions();
					}
					widget.fnFilter();
					return false;
				} );
			widget.asFilters.push( sSelected );
			if ( widgets.$TermContainer ) {
				widgets.$TermContainer.show();
				widgets.$TermContainer.prepend( $TermLink );
			} else {
				widget.$Select.after( $TermLink );
			}
			$SelectedOption = widget.$Select.children( 'option:selected' );
			widget.$Select.val( '' );
			$SelectedOption.remove();
			if ( widget.iMaxSelections > 0 && widget.iMaxSelections <= widget.asFilters.length ) {
				widget.$Select.prop( 'disabled', true );
			}
			widget.fnFilter();
		} );
		widget.$Container.append( widget.$Select );
		widget.fnDraw();
	};

	/**
	 * Perform filtering on the target column.
	 *
	 * @method fnFilter
	 */
	ColumnFilterWidget.prototype.fnFilter = function() {
		var widget = this;
		if ( widget.asFilters.length > 0 ) {
			var asEscapedFilters = [];
			// Filters must have RegExp symbols escaped
			$.each( widget.asFilters, function( i, sFilter ) {
				asEscapedFilters.push( fnRegExpEscape( sFilter ) );
			} );
			// This regular expression filters by either whole column values or an item in a comma list
			var sFilterStart = widget.sSeparator ? '(^|' + widget.sSeparator + ')(' : '^(';
			var sFilterEnd = widget.sSeparator ? ')(' + widget.sSeparator + '|$)' : ')$';
			widget.oDataTable.fnFilter( sFilterStart + asEscapedFilters.join('|') + sFilterEnd, widget.iColumn, true, false );
		} else {
			// Clear any filters for this column
			widget.oDataTable.fnFilter( '', widget.iColumn );
		}
	};

	/**
	 * Sort the widget menu options, using a custom function if one was supplied.
	 *
	 * @method fnSortOptions
	 */
	ColumnFilterWidget.prototype.fnSortOptions = function() {
		var widget = this,
			$options = widget.$Select.find( 'option' ).slice( 1 );

		// Default sort function.
		let fnSort = ( a, b ) => {
			return $( a ).text().localeCompare( $( b ).text(), undefined, {
				numeric: true,
				sensitivity: 'base'
			} );
		};

		// If defined, use a custom sort function instead.
		if ( widget.hasOwnProperty( 'fnSort' ) ) {
			fnSort = ( a, b ) => {
				return widget.fnSort( $( a ).text(), $( b ).text() );
			};
		}

		$options.sort( fnSort );
		widget.$Select.append( $options );
	};

	/**
	 * On each table draw, update filter menu items as needed. This allows any process to
	 * update the table's column visibility and menus will still be accurate.
	 *
	 * @method fnDraw
	 */
	ColumnFilterWidget.prototype.fnDraw = function() {
		var widget = this;
		if ( widget.asFilters.length === 0 ) {
			var oDistinctOptions = {};
			var aDistinctOptions = [];
			// Find distinct column values
			var aData = widget.oDataTable.fnGetColumnData( widget.iColumn );
			$.each( aData, function( i, sValue ) {
				var asValues = widget.sSeparator ? sValue.split( new RegExp( widget.sSeparator ) ) : [ sValue ];
				$.each( asValues, function( j, sOption ) {
					if ( '' !== sOption && ! oDistinctOptions.hasOwnProperty( sOption ) ) {
						oDistinctOptions[sOption] = true;
						aDistinctOptions.push( sOption );
					}
				} );
			} );
			// Build the menu
			var title = $( '<div>' + widget.oColumn.sTitle + '</div>' ).text();
			widget.$Select.empty().append( $( '<option selected disabled></option>' ).val( '' ).text( title ) );
			$.each( aDistinctOptions, function( i, sOption ) {
				var sText = $( '<div>' + sOption + '</div>' ).text().replace( /\n/g, ' ' );
				widget.$Select.append( $( '<option></option>' ).val( sText ).text( sText ) );
			} );
			if ( aDistinctOptions.length > 0 ) {
				if ( widget.bSort ) {
					widget.fnSortOptions();
				}
				// Enable the menu
				widget.$Select.prop( 'disabled', false );
			} else {
				// No option is not a useful menu, disable it
				widget.$Select.prop( 'disabled', true );
			}
		}
	};

	/*
	 * Register a new feature with DataTables.
	 */
	$.fn.dataTableExt.aoFeatures.push( {
		fnInit( oDTSettings ) {
			var oWidgets = new ColumnFilterWidgets( oDTSettings );
			return oWidgets.getContainer();
		},
		cFeature: 'W',
		sFeature: 'ColumnFilterWidgets',
	} );

}( jQuery ) );
