/**
 * WordPress dependencies
 */
import { registerStore, dispatch, select } from '@wordpress/data';

// Pre-load initial state from server.
const initialState = window.attrMeta ? Object.values( window.attrMeta ) : [];

/**
 * Remove inline attribution anchor from block content.
 *
 * @param {string} id Attribution ID.
 */
function removeInlineAnchor( id ) {
	const anchorId = `anchor-${ id }`;
	const blocks = select( 'core/block-editor' ).getBlocks();

	blocks.forEach( ( block ) => {
		// Check if block has content with the anchor.
		if ( block.attributes && block.attributes.content ) {
			const content = block.attributes.content;

			// Check if this block contains our anchor.
			if ( content.includes( anchorId ) ) {
				// Create a temporary DOM element to parse and modify the HTML.
				const tempDiv = document.createElement( 'div' );
				tempDiv.innerHTML = content;

				// Find and remove the anchor span.
				const anchor = tempDiv.querySelector( `#${ anchorId }` );
				if ( anchor ) {
					anchor.remove();

					// Update the block with the modified content.
					dispatch( 'core/block-editor' ).updateBlockAttributes(
						block.clientId,
						{ content: tempDiv.innerHTML }
					);
				}
			}
		}
	} );
}

// Reducer
function reducer( state, action ) {
	switch ( action.type ) {
		case 'ADD_ATTRIBUTION':
			return state.concat( [ action.item ] );

		case 'UPDATE_ATTRIBUTION':
			return state.map( ( item ) =>
				item.id === action.item.id ? action.item : item
			);

		case 'REMOVE_ATTRIBUTION':
			return state.filter( ( item ) => item.id !== action.id );
	}

	return state;
}

// Actions.
const actions = {
	add( item ) {
		return {
			type: 'ADD_ATTRIBUTION',
			item,
		};
	},
	update( item ) {
		return {
			type: 'UPDATE_ATTRIBUTION',
			item,
		};
	},
	remove( id ) {
		// Remove the inline anchor from block content.
		removeInlineAnchor( id );

		return {
			type: 'REMOVE_ATTRIBUTION',
			id,
		};
	},
};

// Selectors
const selectors = {
	get( state ) {
		return state;
	},
};

registerStore( 'openlab/attributions', {
	reducer,
	actions,
	selectors,
	initialState,
} );
