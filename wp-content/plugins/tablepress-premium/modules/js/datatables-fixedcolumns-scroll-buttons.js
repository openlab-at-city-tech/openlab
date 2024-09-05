/**
 * JavaScript code for the scroll buttons for horizontal scrolling of the DataTables FixedColumns module.
 *
 * @package TablePress
 * @subpackage DataTables FixedColumns
 * @author Tobias Bäthge
 * @since 2.4.0
 */

/* globals DataTable */

/* jshint strict: global */
'use strict';

DataTable.addScrollButtons = ( dtApi, btnLeftTitle, btnRightTitle ) => {
	const wrapper = dtApi.table().container().querySelector( ':scope>div>.dataTables_scroll' );
	const dtScrollBody = wrapper.children[1];
	const wrapperClassList = wrapper.parentNode.classList;

	if ( wrapperClassList.contains( 'tablepress-dt-scroll-buttons-wrapper' ) ) {
		if ( wrapperClassList.contains( 'tablepress-dt-scroll-buttons-wrapper-visible' ) && dtScrollBody.scrollWidth <= dtScrollBody.offsetWidth + 60 ) {
			wrapperClassList.remove( 'tablepress-dt-scroll-buttons-wrapper-visible' );
		} else if ( ! wrapperClassList.contains( 'tablepress-dt-scroll-buttons-wrapper-visible' ) && dtScrollBody.scrollWidth > dtScrollBody.offsetWidth ) {
			wrapperClassList.add( 'tablepress-dt-scroll-buttons-wrapper-visible' );
		}
		return;
	}

	/* Only add buttons when needed. */
	if ( dtScrollBody.scrollWidth === dtScrollBody.offsetWidth ) {
		return;
	}

	wrapperClassList.add( 'tablepress-dt-scroll-buttons-wrapper', 'tablepress-dt-scroll-buttons-wrapper-visible' );

	const btnLeft = document.createElement( 'button' );
	btnLeft.classList.add( 'tablepress-dt-scroll-button' );
	btnLeft.title = btnLeftTitle;
	btnLeft.textContent = '❮';
	btnLeft.addEventListener( 'click', () => dtScrollBody.scrollBy( 'rtl' === document.dir ? 200 : -200, 0 ) );

	const btnRight = document.createElement( 'button' );
	btnRight.classList.add( 'tablepress-dt-scroll-button' );
	btnRight.title = btnRightTitle;
	btnRight.textContent = '❯';
	btnRight.addEventListener( 'click', () => dtScrollBody.scrollBy( 'rtl' === document.dir ? -200 : 200, 0 ) );

	wrapper.before( btnLeft );
	wrapper.after( btnRight );

	/* Refresh the column widths. */
	dtApi.columns.adjust().draw();
};

/* Check if scroll buttons need to be added/shown/hidden on window resize and debounce the event handler. */
window.TPDT_debounce = function ( func, wait ) {
	let timeout;
	return function ( ...args ) {
		const context = this;
		clearTimeout( timeout );
		timeout = setTimeout( () => {
			func.apply( context, args );
		}, wait );
	};
};
