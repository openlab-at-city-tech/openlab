/**
 * JavaScript code for the "Stack" mode of the Responsive Tables module.
 *
 * @package TablePress
 * @subpackage Responsive Tables
 * @author Tobias Bäthge
 * @since 2.0.0
 */

/* jshint strict: global */
'use strict';

const css_class = 'tablepress-responsive-stack-headers';
document.querySelectorAll( '.' + css_class ).forEach( ( $table ) => {
	if ( ! $table.tHead || ! $table.tBodies ) {
		$table.classList.remove( css_class );
		return;
	}

	// Extract the header cell texts, and fill colspanned cells with empty strings.
	const header_cells = [];
	for ( const $cell of $table.tHead.rows[0].cells ) {
		header_cells.push( $cell.textContent );
		for ( let colspan = 1; colspan < $cell.colSpan; colspan++ ) {
			header_cells.push( '' );
		}
	}

	// Add the header cell texts to the data-th attribute of each cell in the table body.
	for ( const $row of $table.tBodies[0].rows ) {
		let col_idx = 0;
		for ( const $cell of $row.cells ) {
			$cell.dataset.th = header_cells[ col_idx ];
			col_idx += $cell.colSpan; // For regular single cells, colSpan is 1. Colspanned cells are skipped.
		}
	}
} );
