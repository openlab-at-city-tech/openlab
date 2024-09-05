/**
 * @summary     AlphabetSearch
 * @description Show an set of alphabet buttons alongside a table providing search input options
 * @version     2.0.0
 * @author      SpryMedia Ltd (www.sprymedia.co.uk)
 * @contact     www.sprymedia.co.uk/contact
 * @copyright   Copyright 2014 SpryMedia Ltd.
 *
 * License      MIT - http://datatables.net/license/mit
 *
 * For more detailed information please see:
 *     https://datatables.net/blog/2014-09-22
 *
 * With modifications and optimizations regarding the used alphabet by Tobias Bäthge.
 */

/* globals jQuery */

( function( $ ){

	// Search function.
	$.fn.dataTable.Api.register( 'alphabetSearch()', function ( searchTerm ) {
		this.iterator( 'table', function ( context ) {
			context.alphabetSearch = searchTerm;
		} );

		return this;
	} );

	// Recalculate the alphabet display for updated data.
	$.fn.dataTable.Api.register( 'alphabetSearch.recalc()', function () {
		this.iterator( 'table', function ( context ) {
			draw(
				new $.fn.dataTable.Api( context ),
				$( 'div.alphabet', this.table().container() )
			);
		} );

		return this;
	} );

	// Search plug-in.
	$.fn.dataTable.ext.search.push( function ( context, searchData ) {
		// Ensure that there is a search applied to this table before running it.
		if ( ! context.alphabetSearch ) {
			return true;
		}

		let columnId = 0;
		let caseSensitive = false;

		if ( context.oInit.alphabet !== undefined ) {
			columnId = ( context.oInit.alphabet.column !== undefined ) ? context.oInit.alphabet.column : 0;
			caseSensitive = ( context.oInit.alphabet.caseSensitive !== undefined ) ? context.oInit.alphabet.caseSensitive : false;
		}

		if ( caseSensitive ) {
			if ( searchData[ columnId ].charAt( 0 ) === context.alphabetSearch ) {
				return true;
			}
		} else {
			// eslint-disable-next-line no-lonely-if
			if ( searchData[ columnId ].charAt( 0 ).toUpperCase() === context.alphabetSearch ) {
				return true;
			}
		}

		return false;
	} );

	// Private support methods.
	function bin ( data, options ) {
		let letter;
		const bins = {};

		for ( let i = 0, ien = data.length ; i < ien ; i++ ) {
			if ( options.caseSensitive ) {
				letter = data[ i ]
					.toString()
					.replace( /<.*?>/g, '' )
					.charAt( 0 );
			} else {
				letter = data[ i ]
					.toString()
					.replace( /<.*?>/g, '' )
					.charAt( 0 ).toUpperCase();
			}
			if ( bins[ letter ] ) {
				bins[ letter ]++;
			} else {
				bins[ letter ] = 1;
			}
		}

		return bins;
	}

	function draw ( table, alphabet, options ) {
		alphabet.empty();
		alphabet.append( options.language.search );

		const columnData = table.column( options.column ).data();
		const bins = bin( columnData, options );

		const print_characters = function ( characters ) {
			for ( let i = 0; i < characters.length; i++ ) {
				const letter = characters[ i ];

				$( '<span></span>' )
					.data( 'letter', letter )
					.data( 'match-count', bins[ letter ] || 0 )
					.addClass( ! bins[ letter ] ? 'empty' : '' )
					.html( letter )
					.appendTo( alphabet );
			}
		};

		$( '<span class="clear active"></span>' )
			.data( 'letter', '' )
			.data( 'match-count', columnData.length )
			.html( options.language.none )
			.appendTo( alphabet );

		if ( options.numbers ) {
			print_characters( '0123456789' );
		}

		if ( options.letters ) {
			if ( 'greek' === options.alphabet ) {
				print_characters( 'ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩ' );
				if ( options.caseSensitive ) {
					print_characters( 'αβγδεζηθικλμνξοπρστυφχψω' );
				}
			} else {
				print_characters( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
				if ( options.caseSensitive ) {
					print_characters( 'abcdefghijklmnopqrstuvwxyz' );
				}
			}
		}

		$( '<div class="alphabetInfo"></div>' ).appendTo( alphabet );
	}

	$.fn.dataTable.AlphabetSearch = function ( context ) {
		const table = new $.fn.dataTable.Api( context );
		const alphabet = $('<div class="alphabet"></div>');
		const options = $.extend( {
			column: 0,
			caseSensitive: false,
			numbers: false,
			letters: true,
			alphabet: 'latin',
			language: {
				search: context.oLanguage.alphabetsearch?.search || 'Search: ',
				none: context.oLanguage.alphabetsearch?.none || 'None',
			},
		}, table.init().alphabet );

		draw( table, alphabet, options );

		// Trigger a search.
		alphabet.on( 'click', 'span', function () {
			alphabet.find( '.active' ).removeClass( 'active' );
			this.classList.add( 'active' );

			table
				.alphabetSearch( $( this ).data( 'letter' ) )
				.draw();
		} );

		// Mouse events to show helper information.
		alphabet
			.on( 'mouseenter', 'span', function () {
				const $span = $( this );
				alphabet
					.find( 'div.alphabetInfo' )
					.css( {
						opacity: 1,
						left: $span.position().left,
						top: $span.position().top + $span.height() + 6,
					} )
					.html( $( this ).data( 'match-count' ) );
			} )
			.on( 'mouseleave', 'span', function () {
				alphabet
					.find( 'div.alphabetInfo' )
					.css( 'opacity', 0 );
			} );

		// API method to get the alphabet container node.
		this.node = function () {
			return alphabet;
		};
	};

	$.fn.DataTable.AlphabetSearch = $.fn.dataTable.AlphabetSearch;

	// Register a search plug-in.
	$.fn.dataTable.ext.feature.push( {
		fnInit( settings ) {
			const search = new $.fn.dataTable.AlphabetSearch( settings );
			return search.node();
		},
		cFeature: 'A',
	} );

}( jQuery ) );
