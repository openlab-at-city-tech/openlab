/**
 * WordPress dependencies
 */
import { compose } from '@wordpress/compose';
import { withSelect, withDispatch } from '@wordpress/data';
import { RawHTML } from '@wordpress/element';
import { Button, ToolbarGroup } from '@wordpress/components';

/**
 * Internal dependencies
 */
import ServerData from './server-data';
import formatAttribution from '../utils/format-attribution';

const licenses = Object.values( window.attrLicenses );

const AttributionList = ( props ) => {
	const { items, edit, remove } = props;

	return (
		<div className="component-attributions-list">
			<ol>
				{ items.map( ( item, index ) => (
					<li key={ item.id }>
						{ /* Needed for alignment. */ }
						<div>
							<RawHTML>
								{ item.content
									? item.content
									: formatAttribution(
											{ ...item },
											licenses
									  ) }
							</RawHTML>
							<ToolbarGroup>
								<Button
									className="components-toolbar__control"
									icon="edit"
									label="Edit"
									onClick={ () => edit( item ) }
								/>
								<Button
									className="components-toolbar__control"
									icon="trash"
									label="Delete"
									onClick={ () => remove( item.id ) }
								/>
							</ToolbarGroup>
							<ServerData item={ item } index={ index } />
						</div>
					</li>
				) ) }
			</ol>
		</div>
	);
};

export default compose( [
	withSelect( ( select ) => {
		const { get } = select( 'openlab/attributions' );

		return {
			items: get(),
		};
	} ),
	withDispatch( ( dispatch ) => {
		return {
			edit( item ) {
				dispatch( 'openlab/modal' ).open( {
					item,
					modalType: 'update',
				} );
			},
			remove( id ) {
				dispatch( 'openlab/attributions' ).remove( id );
			},
		};
	} ),
] )( AttributionList );
