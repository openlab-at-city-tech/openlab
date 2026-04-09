(() => {
	setTimeout(() => {
		// Add missing form label.
		const pageNumberInput = document.querySelector( '.fbdl-layout-control-limit input' );
		if ( pageNumberInput ) {
			const fieldId = 'fbdl-page-number';
			pageNumberInput.setAttribute( 'id', fieldId );

			const label = document.createElement( 'label' );
			label.textContent = 'Page Number:';
			label.classList.add( 'screen-reader-text' );
			label.setAttribute( 'for', fieldId );
			pageNumberInput.parentNode.insertBefore( label, pageNumberInput );
		}
	}, 1000);

	setTimeout(() => {
		// Force improved styling for pagination text.
		const paginationText = document.querySelectorAll( '.fbdl-pagination-info p' );
		if ( paginationText.length ) {
			paginationText.forEach( ( element ) => {
				// add color: #767676 !important
				element.style.setProperty( 'color', '#767676', 'important' );
			} );
		}

		// Add dummy href attributes to pagination links to allow them to receive focus.
		const paginationLinks = document.querySelectorAll( '.fbdl-pagination-page-number a' );
		if ( paginationLinks.length ) {
			paginationLinks.forEach( ( link ) => {
				if ( ! link.hasAttribute( 'href' ) ) {
					link.setAttribute( 'href', '#' );
				}
			} );
		}

	}, 5000);
})();
