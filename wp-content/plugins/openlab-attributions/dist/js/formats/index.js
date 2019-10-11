/**
 * WordPress dependencies
 */
import { RichTextToolbarButton } from '@wordpress/block-editor';
import { Fragment, useState } from '@wordpress/element';
import {
	create,
	insert,
	isCollapsed,
	registerFormatType,
} from '@wordpress/rich-text';

/**
 * Internal dependencies
 */
import { formatIcon } from './icon';
import formatAttribution from '../utils/format-attribution';
import Modal from '../components/modal';

const licenses = Object.values( window.attrLicenses );
const name = 'ol/attributions';
const title = 'Attribution';

const addShortcode = ( data, props ) => {
	const { value, onChange } = props;

	const attribution = formatAttribution( data, licenses );
	const shortcode = `[ref]${ attribution }[/ref]`;

	// Create RichText value from shortcode.
	const valueToInsert = create( { html: shortcode } );
	const startIndex = isCollapsed( value ) ? value.start : value.end;
	const newValue = insert( value, valueToInsert, startIndex );

	onChange( newValue );
};

const Edit = ( props ) => {
	const [ isOpen, setOpen ] = useState( false );

	const { isActive } = props;

	return (
		<Fragment>
			<RichTextToolbarButton
				icon={ formatIcon }
				title={ title }
				onClick={ () => setOpen( true ) }
				isActive={ isActive }
			/>
			<Modal
				isOpen={ isOpen }
				title={ 'Add Attribution' }
				onClose={ () => setOpen( false ) }
				onSubmit={ ( data ) => {
					setOpen( false );
					addShortcode( data, props );
				} }
			/>
		</Fragment>
	);
};

// Register fake format.
registerFormatType( name, {
	title,
	tagName: 'sup',
	className: 'attr-anchor',
	edit: Edit,
} );
