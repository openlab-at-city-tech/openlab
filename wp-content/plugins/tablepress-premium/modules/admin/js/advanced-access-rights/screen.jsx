/**
 * JavaScript code for the "Advanced Access Rights Screen" component.
 *
 * @package TablePress
 * @subpackage Advanced Access Rights Screen
 * @author Tobias Bäthge
 * @since 2.2.0
 */

/* globals tp */

/**
 * WordPress dependencies.
 */
import { useEffect, useRef, useState } from 'react';
import { __, _x } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import { $ } from '../../../../admin/js/common/functions';
import { save_changes as saveChanges } from '../common/save-changes';
import { register_save_changes_keyboard_shortcut as registerSaveChangesKeyboardShortcut } from '../../../../admin/js/common/keyboard-shortcut';

/**
 * Saves the Advanced Access Rights configuration to the server.
 *
 * @param {HTMLElement} domNode    DOM node into which to insert the spinner and notice.
 * @param {Object}      screenData Advanced Access Rights configuration.
 */
const saveAdvancedAccessRightsConfig = ( domNode, screenData ) => {
	// Prepare the data for the AJAX request.
	const requestData = {
		action: 'tablepress_advanced_access_rights',
		_ajax_nonce: $( '#_wpnonce' ).value,
		tablepress: JSON.stringify( screenData.map ),
	};

	saveChanges( domNode, requestData );
};

// Add the "New tables" and "New users" entries to allow defining default behavior.
tp.advanced_access_rights.tables[ '#new_tables' ] = __( 'New tables', 'tablepress' );
tp.advanced_access_rights.users[ '#new_users' ] = {};

const tableIds = Object.keys( tp.advanced_access_rights.tables );
const userIds = Object.keys( tp.advanced_access_rights.users );

// Ensure that the Access Rights map has an entry for every table and user.
tp.advanced_access_rights.map = Object.fromEntries( tableIds.map( ( tableId ) => {
	const tableAccessRights = Object.fromEntries( userIds.map( ( userId ) => {
		const userHasAccess = tp.advanced_access_rights.map[ tableId ]?.[ userId ] ? 1 : 0;
		return [ userId, userHasAccess ];
	} ) );
	return [ tableId, tableAccessRights ];
} ) );

/**
 * Returns the "Advanced Access Rights Screen" component's JSX markup.
 *
 * @return {Object} Advanced Access Rights Screen component.
 */
const Screen = () => {
	const submitButtonElement = useRef( null );
	const [ screenData, setScreenData ] = useState( {
		map: tp.advanced_access_rights.map,
		lastCheckboxIdx: null,
	} );

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

	// Register the "Save Changes" keyboard shortcut.
	useEffect( () => {
		registerSaveChangesKeyboardShortcut( submitButtonElement.current );
	}, [] );

	return (
		<>
			<p>
				<input
					type="button"
					className="button button-primary button-large button-save-changes"
					value={ __( 'Save Changes', 'tablepress' ) }
					onClick={ ( event ) => saveAdvancedAccessRightsConfig( event.target.parentNode, screenData ) }
				/>
			</p>
			<table id="tablepress-access-rights-map" className="tablepress-access-rights-map striped">
				<thead>
					<tr>
						<th></th>
						<th></th>
						{
							Object.entries( tp.advanced_access_rights.users ).map( ( [ userId, user ] ) => (
								<th
									key={ userId }
									className={ ( '#new_users' === userId ) ? 'new-users-column' : undefined }
								>
									{ ( '#new_users' === userId ) && __( 'New users', 'tablepress' ) }
									{ ( '#new_users' !== userId ) && (
										<>
											{ userId }:
											<br />
											<abbr title={ user.displayName }>{ user.userLogin }</abbr>
										</>
									) }
								</th>
							) )
						}
					</tr>
				</thead>
				<tbody>
					{
						Object.entries( tp.advanced_access_rights.tables ).map( ( [ tableId, tableName ] ) => (
							<tr
								key={ tableId }
								className={ ( '#new_tables' === tableId ) ? 'new-tables-row' : undefined }
							>
								<th
									className={ ( '#new_tables' !== tableId ) ? 'column-table-id' : undefined }
								>
									{ ( '#new_tables' !== tableId ) && `${ tableId }:` }
								</th>
								<th
									className={ ( '#new_tables' !== tableId ) ? 'column-table-name' : undefined}
								>
									{ '' !== tableName.trim() ? tableName : __( '(no name)', 'tablepress' ) }
								</th>
								{
									Object.keys( tp.advanced_access_rights.users ).map( ( userId ) => (
										<td
											key={ userId }
											className={ ( '#new_users' === userId ) ? 'new-users-column' : undefined }
										>
											<input
												type="checkbox"
												checked={ 1 === screenData.map[ tableId ][ userId ] }
												onChange={ ( event ) => {
													// Find indices of the current table and user IDs, as these are not in consecutive order.
													const currentCheckboxIdx = {
														tableId: tableIds.indexOf( tableId ),
														userId: userIds.indexOf( userId ),
													};

													// Retrieve the last pressed checkbox table and user ID indices from screen data state.
													let lastCheckboxIdx = screenData.lastCheckboxIdx ? { ...screenData.lastCheckboxIdx } : null;

													// If no checkbox had been pressed before, or if the Shift key was not held, only change the current checkbox.
													if ( null === lastCheckboxIdx || ! event.nativeEvent.shiftKey ) {
														lastCheckboxIdx = currentCheckboxIdx;
													}

													// Determine first and last table and user ID indices, as these determine the range of checkboxes.
													const firstIdx = {
														tableId: ( lastCheckboxIdx.tableId < currentCheckboxIdx.tableId ) ? lastCheckboxIdx.tableId : currentCheckboxIdx.tableId,
														userId: ( lastCheckboxIdx.userId < currentCheckboxIdx.userId ) ? lastCheckboxIdx.userId : currentCheckboxIdx.userId,
													};
													const lastIdx = {
														tableId: ( currentCheckboxIdx.tableId > lastCheckboxIdx.tableId ) ? currentCheckboxIdx.tableId : lastCheckboxIdx.tableId,
														userId: ( currentCheckboxIdx.userId > lastCheckboxIdx.userId ) ? currentCheckboxIdx.userId : lastCheckboxIdx.userId,
													};

													// Loop over the range and grant/revoke access for all in that range, to also toggle their checkbox.
													const map = { ...screenData.map };
													for ( let tableIdIdx = firstIdx.tableId; tableIdIdx <= lastIdx.tableId; tableIdIdx++ ) {
														for ( let userIdIdx = firstIdx.userId; userIdIdx <= lastIdx.userId; userIdIdx++ ) {
															const checkboxTableId = tableIds[ tableIdIdx ];
															const checkboxUserId = userIds[ userIdIdx ];
															map[ checkboxTableId ][ checkboxUserId ] = event.target.checked ? 1 : 0;
														}
													}
													updateScreenData( { map } );

													// After processing the clicks, the current checkbox is the last clicked checkbox.
													updateScreenData( { lastCheckboxIdx: currentCheckboxIdx } );
												} }
											/>
										</td>
									) )
								}
							</tr>
						) )
					}
				</tbody>
			</table>
			<p>
				<input
					ref={ submitButtonElement }
					type="button"
					id="tablepress-advanced-access-rights-save-changes"
					className="button button-primary button-large button-save-changes"
					value={ __( 'Save Changes', 'tablepress' ) }
					data-shortcut={ _x( '%1$sS', 'keyboard shortcut for Save Changes', 'tablepress' ) }
					onClick={ ( event ) => saveAdvancedAccessRightsConfig( event.target.parentNode, screenData ) }
				/>
			</p>
		</>
	);
};

export default Screen;
