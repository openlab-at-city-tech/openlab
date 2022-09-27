/**
 * External dependencies
 */
const nanoid = require( 'nanoid' );

/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { dispatch, useSelect } from '@wordpress/data';
import { BlockControls } from '@wordpress/block-editor';
import { isCollapsed, insertObject } from '@wordpress/rich-text';
import { Toolbar, IconButton } from '@wordpress/components'
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import icon from './icon';
import Modal from '../components/modal';

const addMarker = ( value, data ) => {
	const id = nanoid( 8 );
	const item = { ...data, id };
	const format = {
		type: 'ol/attributions',
		attributes: {
			href: `#ref-${ id }`,
			id: `anchor-${ id }`,
		},
	};

	// Add attribution.
	dispatch( 'openlab/attributions' ).add( item );

	const startIndex = isCollapsed( value ) ? value.start : value.end;
	
	// Add empty space at the end of the sentence, so it's possible to continue writing after it.
	value.text += ' ';

	const newValue = insertObject( value, format, startIndex );

	return newValue;
};

export default function Edit( { isActive, value, onChange } ) {
	const [ isOpen, setIsOpen ] = useState( false );

	const { item } = useSelect(
		( select ) => ( {
			item: select( 'openlab/modal' ).get(),
		} ),
		[]
	);

	return (
		<>
			<BlockControls>
				<Toolbar>
					<IconButton
						icon={ icon }
						label="Add Attribution"
						className="components-toolbar_control"
						onClick={ () => setIsOpen( true ) }
					/>
				</Toolbar>
			</BlockControls>
			{ isOpen && (
				<Modal
					isOpen={ isOpen }
					modalType="add"
					title={ __( "Add Attribution", 'openlab-attributions' ) }
					item={ item }
					onClose={ () => setIsOpen( false ) }
					addItem={ ( data ) => onChange( addMarker( value, data ) ) }
				/>
			) }
		</>
	);
}
