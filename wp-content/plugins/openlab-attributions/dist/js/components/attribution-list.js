/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { RawHTML } from '@wordpress/element';
import { Button, ToolbarGroup } from '@wordpress/components';

/**
 * Internal dependencies
 */
import ServerData from './server-data';
import { formatAttribution } from '../utils/format';

export default function AttributionList() {
	const { items } = useSelect(
		( select ) => ( {
			items: select( 'openlab/attributions' ).get(),
		} ),
		[]
	);

	const { open } = useDispatch( 'openlab/modal' );
	const { remove } = useDispatch( 'openlab/attributions' );

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
									: formatAttribution( { ...item } ) }
							</RawHTML>
							<ToolbarGroup>
								<Button
									className="components-toolbar__control"
									icon="edit"
									label="Edit"
									onClick={ () => open( { item, modalType: 'update' } ) }
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
}
