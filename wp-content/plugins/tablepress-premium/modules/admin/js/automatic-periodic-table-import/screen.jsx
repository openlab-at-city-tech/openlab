/**
 * JavaScript code for the "Automatic Periodic Table Import Screen" component.
 *
 * @package TablePress
 * @subpackage Automatic Periodic Table Import Screen
 * @author Tobias Bäthge
 * @since 2.2.0
 */

/* globals tp */

/**
 * WordPress dependencies.
 */
import { useState } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import {
	ToggleControl,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */
import IntervalControl from './components/interval-control';
import { $ } from '../../../../admin/js/common/functions';
import { save_changes as saveChanges } from '../common/save-changes';

/**
 * Saves the Automatic Periodic Table Import configuration to the server.
 *
 * @param {HTMLElement} domNode    DOM node into which to insert the spinner and notice.
 * @param {Object}      screenData Automatic Periodic Table Import configuration.
 */
const saveAutomaticPeriodicTableImportConfig = ( domNode, screenData ) => {
	const tables = Object.fromEntries( Object.entries( screenData.tables )
		// Only save the config for tables that have changes and not just the default settings.
		.filter( ( [ , table ] ) => ( table.active || 'https://' !== table.location || /* DAY_IN_SECONDS */ 86400 !== table.interval ) )
		// Don't save the "last_import" property.
		.map( ( [ tableId, table ] ) => {
			delete table.selected;
			delete table.last_import;
			return [ tableId, table ];
		} )
	);

	// Prepare the data for the AJAX request.
	const requestData = {
		action: 'tablepress_import',
		_ajax_nonce: $( '#_wpnonce' ).value,
		tablepress: JSON.stringify( tables ),
	};

	saveChanges( domNode, requestData );
};

// Ensure that all tables have a configuration.
tp.automatic_periodic_table_import.tables = Object.fromEntries( Object.keys( tp.import.tables ).map( ( tableId ) => {
	const tableImportConfig = {
		selected: false,
		active: false,
		location: 'https://',
		interval: /* DAY_IN_SECONDS */ 86400,
		last_import: '-',
		...tp.automatic_periodic_table_import.tables[ tableId ],
	};
	return [ tableId, tableImportConfig ];
} ) );

/**
 * Returns the "Automatic Periodic Table Import Screen" component's JSX markup.
 *
 * @return {Object} Automatic Periodic Table Import Screen component.
 */
const Screen = () => {
	const [ screenData, setScreenData ] = useState( () => ( {
		tables: tp.automatic_periodic_table_import.tables,
		shownTableIds: Object.keys( tp.automatic_periodic_table_import.tables ),
		lastCheckboxTableId: null,
		searchTerm: '',
		sort: {
			column: '', // '' means no sorting, otherwise the object key to sort by.
			direction: 1, // 1 for descending, -1 for ascending.
		},
		bulkAction: '',
		bulkActionInterval: /* DAY_IN_SECONDS */ 86400,
	} ) );

	/**
	 * Handles screen data state changes.
	 *
	 * @param {Object} updatedData Data in the screen data state that should be updated.
	 */
	const updateScreenData = ( updatedData ) => {
		const newScreenData = {
			...screenData,
			...updatedData,
		};
		setScreenData( newScreenData );
	};

	/**
	 * Filters and sorts the table list to generate the list of tables that is to be shown.
	 *
	 * @param {string} searchTerm Search term.
	 * @param {Object} sort       Sort configuration.
	 * @return {Array} List of table IDs that is to be shown.
	 */
	const getShownTableIdsFromFilterAndSort = ( searchTerm, sort ) => {
		let tables = Object.entries( screenData.tables );

		if ( '' !== searchTerm ) {
			searchTerm = searchTerm.toLowerCase();
			tables = tables.filter( ( [ tableId, tableData ] ) => (
				tableId.toLowerCase().includes( searchTerm ) ||
				tp.import.tables[ tableId ].toLowerCase().includes( searchTerm ) ||
				tableData.location.toLowerCase().includes( searchTerm )
			) );
		}

		if ( '' !== sort.column ) {
			tables.sort( ( [ tableIdA, tableDataA ], [ tableIdB, tableDataB ] ) => {
				let sortDataA;
				let sortDataB;
				if ( 'id' === sort.column ) {
					sortDataA = tableIdA;
					sortDataB = tableIdB;
				} else if ( 'name' === sort.column ) {
					sortDataA = tp.import.tables[ tableIdA ];
					sortDataB = tp.import.tables[ tableIdB ];
				} else {
					sortDataA = tableDataA[ sort.column ].toString();
					sortDataB = tableDataB[ sort.column ].toString();
				}
				const sortResult = sortDataA.localeCompare( sortDataB, undefined, {
					numeric: true,
					sensitivity: 'base'
				} );
				return sort.direction * sortResult;
			} );
		}

		return tables.map( ( [ tableId, ] ) => tableId );
	};

	// Create the "Save Automatic Import configuration" button once, so that it can be used in multiple places.
	const submitButton = (
		<p className="submit">
			<input
				type="button"
				className="button button-secondary button-large button-save-changes"
				value={ __( 'Save Automatic Import configuration', 'tablepress' ) }
				onClick={ ( event ) => saveAutomaticPeriodicTableImportConfig( event.target.parentNode, screenData ) }
			/>
		</p>
	);

	/**
	 * Returns the JSX markup for a sortable table head cell.
	 *
	 * @param {Object} props        Component properties.
	 * @param {string} props.column Column name to sort by.
	 * @param {string} props.text   Column text.
	 * @return {Object} JSX markup for a sortable table head cell.
	 */
	const HeadCell = ( { column, text } ) => (
		<th
			className={ column === screenData.sort.column ? `sorted ${ 1 === screenData.sort.direction ? 'asc' : 'desc' }` : 'sortable desc' }
		>
			{ /* eslint-disable-next-line jsx-a11y/anchor-is-valid */ }
			<a
				href=""
				role="button"
				onClick={ ( event ) => {
					event.preventDefault();
					const sort = {
						column,
						direction: event.target.closest( 'th' ).classList.contains( 'asc' ) ? -1 : 1,
					};
					const shownTableIds = getShownTableIdsFromFilterAndSort( screenData.searchTerm, sort );
					updateScreenData( { shownTableIds, sort } );
				} }
			>
				<span>{ text }</span>
				<span className="sorting-indicators">
					<span className="sorting-indicator asc" aria-hidden="true"></span>
					<span className="sorting-indicator desc" aria-hidden="true"></span>
				</span>
			</a>
		</th>
	);

	return (
		<>
			<p>
				{ __( 'To periodically import tables from files that were uploaded to a server, configure the desired interval and table source information below.', 'tablepress' ) }
			</p>
			<p className="description">
				{ __( 'Please note: In general, it is recommended to use a reasonably long interval, to reduce traffic on the import source server.', 'tablepress' ) + ' ' + __( 'In addition, it is recommended to set up a suitable server cron job that replaces the WP Cron system, for improved reliability.', 'tablepress' ) }
			</p>
			{ submitButton }
			<div className="tablenav top">
				<div className="alignleft actions bulkactions">
					<label htmlFor="bulk-action-selector-top" className="screen-reader-text">{ __( 'Select Bulk Action', 'tablepress' ) }</label>
					<select
						id="bulk-action-selector-top"
						value={ screenData.bulkAction }
						onChange={ ( event ) => {
							updateScreenData( { bulkAction: event.target.value } );
						} }
					>
						<option value="">{ __( 'Bulk Actions', 'tablepress' ) }</option>
						<option value="activate">{ __( 'Activate import', 'tablepress' ) }</option>
						<option value="deactivate">{ __( 'Deactivate import', 'tablepress' ) }</option>
						<option value="set-interval">{ __( 'Set import interval', 'tablepress' ) }</option>
					</select>
					{ 'set-interval' === screenData.bulkAction &&
						<>
							<IntervalControl
								value={ screenData.bulkActionInterval }
								onChange={ ( value ) => {
									updateScreenData( { bulkActionInterval: value } );
								} }
							/>
							{ ' ' }
						</>
					}
					<input
						type="button"
						className="button action"
						disabled={ '' === screenData.bulkAction || 0 === screenData.shownTableIds.length || ! screenData.shownTableIds.some( ( tableId ) => screenData.tables[ tableId ].selected ) }
						value={ __( 'Apply', 'tablepress' ) }
						onClick={ () => {
							const selectedTableIds = screenData.shownTableIds.filter( ( tableId ) => screenData.tables[ tableId ].selected );

							const tables = { ...screenData.tables };
							if ( 'activate' === screenData.bulkAction ) {
								selectedTableIds.forEach( ( tableId ) => {
									tables[ tableId ].active = true;
								} );
							} else if ( 'deactivate' === screenData.bulkAction ) {
								selectedTableIds.forEach( ( tableId ) => {
									tables[ tableId ].active = false;
								} );
							} else if ( 'set-interval' === screenData.bulkAction ) {
								selectedTableIds.forEach( ( tableId ) => {
									tables[ tableId ].interval = screenData.bulkActionInterval;
								} );
							}

							// Update table data and reset the bulk action dropdown.
							updateScreenData( {
								tables,
								bulkAction: '',
								bulkActionInterval: /* DAY_IN_SECONDS */ 86400,
							 } );
						} }
					/>
				</div>
				<p
					className="search-box"
					style={ {
						paddingBottom: '0.5em',
					} }
				>
					<label htmlFor="tables_search-search-input">{ __( 'Filter list:', 'tablepress' ) }</label>
					<input
						id="tables_search-search-input"
						type="search"
						value={ screenData.searchTerm }
						onChange={ ( event ) => {
							const searchTerm = event.target.value;
							const shownTableIds = getShownTableIdsFromFilterAndSort( searchTerm, screenData.sort );
							updateScreenData( {	shownTableIds, searchTerm } )
						} }
						style={ {
							marginLeft: '0.5em',
						} }
					/>
				</p>
				<br className="clear" />
			</div>
			<table id="tablepress-automatic-periodic-import-tables" className="widefat striped">
				<thead>
					<tr>
						<th className="column-checkbox">
							<input
								type="checkbox"
								id="auto-import-select-all-thead"
								checked={ 0 < screenData.shownTableIds.length && screenData.shownTableIds.every( ( tableId ) => screenData.tables[ tableId ].selected ) }
								disabled={ 0 === screenData.shownTableIds.length }
								onChange={ ( event ) => {
									const tables = { ...screenData.tables };
									screenData.shownTableIds.forEach( ( tableId ) => {
										tables[ tableId ].selected = event.target.checked;
									} );
									updateScreenData( { tables } );
								} }
							/>
							<label htmlFor="auto-import-select-all-thead">
								<span className="screen-reader-text">{ __( 'Select All' ) }</span>
							</label>
						</th>
						<HeadCell column="id" text={ __( 'ID', 'tablepress' ) } />
						<HeadCell column="name" text={ __( 'Table Name', 'tablepress' ) } />
						<HeadCell column="active" text={ __( 'Active', 'tablepress' ) } />
						<HeadCell column="location" text={ __( 'Import File Location', 'tablepress' ) } />
						<HeadCell column="interval" text={ __( 'Import Interval', 'tablepress' ) } />
						<HeadCell column="last_import" text={ __( 'Last Automatic Import', 'tablepress' ) } />
					</tr>
				</thead>
				<tbody>
					{
						0 === screenData.shownTableIds.length
						? (
							<tr>
								<td colSpan="7">
									{ __( 'No tables found.', 'tablepress' ) }
								</td>
							</tr>
						)
						: screenData.shownTableIds.map( ( tableId ) => {
							const tableConfig = screenData.tables[ tableId ];
							const tableName = '' === tp.import.tables[ tableId ].trim() ? __( '(no name)', 'tablepress' ) : tp.import.tables[ tableId ];
							return (
								<tr key={ tableId }>
									<th scope="row" className="column-checkbox">
										<input
											type="checkbox"
											checked={ tableConfig.selected }
											id={ `cb-select-${ tableId }` }
											onChange={ ( event ) => {
												// Find index of the current table ID, as these are not in consecutive order.
												const currentCheckboxIdx = screenData.shownTableIds.indexOf( tableId );

												// Retrieve the last pressed checkbox table ID from screen data state and find the checkbox position.
												let lastCheckboxIdx = screenData.shownTableIds.indexOf( screenData.lastCheckboxTableId );

												// If no checkbox had been pressed before, or if the Shift key was not held, only change the current checkbox.
												if ( null === screenData.lastCheckboxTableId || ! event.nativeEvent.shiftKey ) {
													lastCheckboxIdx = currentCheckboxIdx;
												}

												// Determine first and last table ID indices, as these determine the range of checkboxes.
												const firstIdx = ( lastCheckboxIdx < currentCheckboxIdx ) ? lastCheckboxIdx : currentCheckboxIdx;
												const lastIdx = ( currentCheckboxIdx > lastCheckboxIdx ) ? currentCheckboxIdx : lastCheckboxIdx;

												// Loop over the range and activate/deactivate all in that range, to also toggle their checkbox.
												const tables = { ...screenData.tables };
												for ( let tableIdIdx = firstIdx; tableIdIdx <= lastIdx; tableIdIdx++ ) {
													const checkboxTableId = screenData.shownTableIds[ tableIdIdx ];
													tables[ checkboxTableId ].selected = event.target.checked;
												}
												updateScreenData( {
													tables,
													lastCheckboxTableId: tableId, // After processing the clicks, the current checkbox is the last clicked checkbox.
												} );
											} }
										/>
										<label htmlFor={ `cb-select-${ tableId }` }>
											<span className="screen-reader-text">
												{ sprintf( __( 'Select table “%s”', 'tablepress' ), tableName ) }
											</span>
										</label>
									</th>
									<td className="column-table-id">{ tableId }</td>
									<td className="column-table-name">{ tableName }</td>
									<td className="column-import-active">
										<ToggleControl
											checked={ tableConfig.active }
											onChange={ ( checked ) => {
												const tables = { ...screenData.tables };
												tables[ tableId ].active = checked;
												updateScreenData( { tables } );
											} }
										/>
									</td>
									<td className="column-import-location">
										<input
											type="text"
											className="large-text code"
											disabled={ ! tableConfig.active }
											value={ tableConfig.location }
											onChange={ ( event ) => {
												const tables = { ...screenData.tables };
												tables[ tableId ].location = event.target.value;
												updateScreenData( { tables } );
											} }
										/>
									</td>
									<td className="column-import-interval">
										<IntervalControl
											disabled={ ! tableConfig.active }
											value={ tableConfig.interval }
											onChange={ ( value ) => {
												const tables = { ...screenData.tables };
												tables[ tableId ].interval = value;
												updateScreenData( { tables } );
											} }
										/>
									</td>
									<td
										className="column-import-last-import"
										dangerouslySetInnerHTML={ {
											__html: tableConfig.last_import
										} }
									/>
								</tr>
							);
						} )
					}
				</tbody>
				<tfoot>
					<tr>
						<th className="column-checkbox">
							<input
								type="checkbox"
								id="auto-import-select-all-tfoot"
								checked={ 0 < screenData.shownTableIds.length && screenData.shownTableIds.every( ( tableId ) => screenData.tables[ tableId ].selected ) }
								disabled={ 0 === screenData.shownTableIds.length }
								onChange={ ( event ) => {
									const tables = { ...screenData.tables };
									screenData.shownTableIds.forEach( ( tableId ) => {
										tables[ tableId ].selected = event.target.checked;
									} );
									updateScreenData( { tables } );
								} }
							/>
							<label htmlFor="auto-import-select-all-tfoot">
								<span className="screen-reader-text">{ __( 'Select All' ) }</span>
							</label>
						</th>
						<th>{ __( 'ID', 'tablepress' ) }</th>
						<th>{ __( 'Table Name', 'tablepress' ) }</th>
						<th>{ __( 'Active', 'tablepress' ) }</th>
						<th>{ __( 'Import File Location', 'tablepress' ) }</th>
						<th>{ __( 'Import Interval', 'tablepress' ) }</th>
						<th>{ __( 'Last Automatic Import', 'tablepress' ) }</th>
					</tr>
				</tfoot>
			</table>
			{ submitButton }
		</>
	);
};

export default Screen;
