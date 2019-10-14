/**
 * WordPress dependencies
 */
import { compose, ifCondition } from '@wordpress/compose';
import { withSelect, withDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import Modal from './modal';

const EditModal = ( props ) => {
	return (
		<Modal { ...props } />
	);
};

export default compose( [
	withSelect( ( select ) => {
		const { isOpen, item, ...rest } = select( 'openlab/modal' ).get();

		return { isOpen, item, ...rest };
	} ),
	withDispatch( ( dispatch ) => {
		return {
			onClose() {
				dispatch( 'openlab/modal' ).hide();
			},
			updateItem( item ) {
				dispatch( 'openlab/attributions' ).update( item );
			},
		};
	} ),
	ifCondition( ( { isOpen } ) => true === isOpen ),
] )( EditModal );
