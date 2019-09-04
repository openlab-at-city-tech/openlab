/**
 * WordPress dependencies
 */
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import { Fragment } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

const licenses = Object.values( window.attrLicenses );

const imageAttributes = {
	imageCitations: {
		type: 'string',
		source: 'meta',
		meta: 'citations',
	},
};

addFilter( 'blocks.registerBlockType', 'ol-attributions/images', function( settings, name ) {
	if ( 'core/image' !== name ) {
		return settings;
	}

	settings.attributes = Object.assign( settings.attributes, imageAttributes );
	return settings;
} );

/**
 * Filter the InspectorControls for a image block type.
 */
const registerFields = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		if ( 'core/image' !== props.name || ! props.attributes.id ) {
			return (
				<BlockEdit { ...props } />
			);
		}

		const { attributes, setAttributes } = props;
		const { id, imageCitations } = attributes;

		const citations = imageCitations ? JSON.parse( imageCitations ) : {};

		let citation = {
			author_name: '',
			author_uri: '',
			license: '',
		};

		if ( typeof citations[ id ] !== 'undefined' ) {
			citation = citations[ id ];
		}

		const handleChange = ( event ) => {
			const target = event.target;
			const value = target.value;
			const name = target.name;

			citation[ name ] = value;

			setAttributes( {
				imageCitations: JSON.stringify( { ...citations, [ id ]: citation } ),
			} );
		};

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title="Image Attributions">
						<div className="components-base-control">
							<div className="components-base-control__field">
								<label className="components-base-control__label" htmlFor="author_name">Author</label>
								<input
									type="text"
									name="author_name"
									className="components-text-control__input"
									defaultValue={ citation.author_name }
									onChange={ handleChange }
								/>
							</div>
							<div className="components-base-control__field">
								<label className="components-base-control__label" htmlFor="author_uri">Author URI</label>
								<input
									type="text"
									name="author_uri"
									className="components-text-control__input"
									defaultValue={ citation.author_uri }
									onChange={ handleChange }
								/>
							</div>
							<div className="components-base-control__field">
								<label className="components-base-control__label" htmlFor="license">License</label>
								<select
									id="license"
									name="license"
									className="components-select-control__input"
									defaultValue={ citation.license }
									onBlur={ handleChange }
								>
									<option key="0" value="">Choose...</option>
									{ licenses.map( ( option, index ) =>
										<option
											key={ `${ option.label }-${ index }` }
											value={ option.label }
										>
											{ option.label }
										</option>
									) }
								</select>
							</div>
						</div>
					</PanelBody>
				</InspectorControls>
				<BlockEdit { ...props } />
			</Fragment>
		);
	};
}, 'registerFields' );

addFilter( 'editor.BlockEdit', 'ol-attributions/images', registerFields );
