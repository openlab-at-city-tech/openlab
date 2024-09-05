/**
 * JavaScript code for the "Automatic Table Export Screen" component.
 *
 * @package TablePress
 * @subpackage Automatic Table Export Screen
 * @author Tobias Bäthge
 * @since 2.2.0
 */

/* globals tp */

/**
 * WordPress dependencies.
 */
import { Fragment, useRef, useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import { $ } from '../../../../admin/js/common/functions';
import { save_changes as saveChanges } from '../common/save-changes';

/**
 * Saves the Automatic Table Export configuration to the server.
 *
 * @param {HTMLElement} domNode    DOM node into which to insert the spinner and notice.
 * @param {Object}      screenData Automatic Table Export configuration.
 * @param {Object}      pathField  React reference to the "Server Path" input field.
 */
const saveAutomaticTableExportConfig = ( domNode, screenData, pathField ) => {
	// Don't submit the form if no path is given, while the export is active.
	if ( screenData.active && '' === screenData.path.trim() ) {
		window.alert( __( 'You must set a server path for the automatic table export.', 'tablepress' ) );
		pathField.current.focus();
		return;
	}

	// Don't submit the form if no export format was selected, while the export is active.
	if ( screenData.active && 0 === screenData.selectedFormats.length ) {
		window.alert( __( 'You must select at least one export format for the automatic table export.', 'tablepress' ) );
		return;
	}

	// Prepare the data for the AJAX request.
	const requestData = {
		action: 'tablepress_export',
		_ajax_nonce: $( '#_wpnonce' ).value,
		tablepress: JSON.stringify( screenData ),
	};

	saveChanges( domNode, requestData );
};

// The <option> entries for the dropdown do not depend on the state, so they can be created once.
const csvDelimitersSelectOptions = Object.entries( tp.automatic_table_export.csvDelimiters ).map( ( [ csvDelimiter, csvDelimiterName ] ) =>
	<option key={ csvDelimiter } value={ csvDelimiter }>{ csvDelimiterName }</option>
);

/**
 * Returns the "Automatic Table Export Screen" component's JSX markup.
 *
 * @return {Object} Automatic Table Export Screen component.
 */
const Screen = () => {
	const [ screenData, setScreenData ] = useState( {
		active: tp.automatic_table_export.active,
		path: tp.automatic_table_export.path,
		selectedFormats: tp.automatic_table_export.selectedFormats,
		csvDelimiter: tp.automatic_table_export.csvDelimiter,
	} );

	// Add a React reference to the "Server Path" input field, to be able to focus the DOM element.
	const pathField = useRef( null );

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

	return (
		<>
			<p>
				{ __( 'To automatically export a table to a file on your server after it has been edited, configure the desired path and export formats below.', 'tablepress' ) }
			</p>
			<table className="tablepress-postbox-table fixed">
			<tbody>
				<tr className="bottom-border">
					<th className="column-1" scope="row"></th>
					<td className="column-2">
						<label htmlFor="auto-export-active">
							<input
								type="checkbox"
								id="auto-export-active"
								checked={ screenData.active }
								onChange={ () => updateScreenData( { active: ! screenData.active } ) }
							/> { __( 'Activate Automatic Table Export', 'tablepress' ) }
						</label>
					</td>
				</tr>
				<tr className="top-border">
					<th className="column-1" scope="row">
						<label htmlFor="auto-export-path">
							{ __ ( 'Server Path', 'tablepress' ) }:
						</label>
					</th>
					<td className="column-2">
						<input
							ref={ pathField }
							type="text"
							id="auto-export-path"
							className="large-text code"
							disabled={ ! screenData.active }
							value={ screenData.path }
							onChange={ ( event ) => updateScreenData( { path: event.target.value } ) }
						/>
					</td>
				</tr>
				<tr className="top-border">
					<th className="column-1 top-align" scope="row">
						{ __ ( 'Export Format', 'tablepress' ) }:
					</th>
					<td className="column-2">
						{
							Object.entries( tp.automatic_table_export.exportFormats ).map( ( [ exportFormat, exportFormatName ] ) => (
								<Fragment
									key={ exportFormat }
								>
									<label
										htmlFor={ `auto-export-format-${ exportFormat }` }
									>
										<input
											type="checkbox"
											id={ `auto-export-format-${ exportFormat }` }
											value={ exportFormat }
											disabled={ ! screenData.active }
											checked={ screenData.selectedFormats.includes( exportFormat ) }
											onChange={ ( event ) => {
												let selectedFormats = [ ...screenData.selectedFormats ];
												if ( event.target.checked ) {
													selectedFormats.push( event.target.value );
												} else {
													selectedFormats = selectedFormats.filter( ( format ) => ( format !== event.target.value ) );
												}
												updateScreenData( { selectedFormats } );
											} }
										/> { exportFormatName }
									</label><br />
								</Fragment>
							) )
						}
					</td>
				</tr>
				<tr className="top-border bottom-border">
					<th className="column-1" scope="row">
						<label htmlFor="auto-export-csv-delimiter">
							{ __ ( 'CSV Delimiter', 'tablepress' ) }:
						</label>
					</th>
					<td className="column-2">
						<select
							id="auto-export-csv-delimiter"
							disabled={ ! screenData.active || ! screenData.selectedFormats.includes( 'csv' ) }
							value={ screenData.csvDelimiter }
							onChange={ ( event ) => updateScreenData( { csvDelimiter: event.target.value } ) }
						>
							{ csvDelimitersSelectOptions }
						</select>
						{ ! screenData.selectedFormats.includes( 'csv' ) &&
							<>
								{ ' ' }
								<span className="description">
									{ __( '(Only needed for CSV export.)', 'tablepress' ) }
								</span>
							</>
						}
					</td>
				</tr>
				<tr className="top-border">
					<td className="column-1"></td>
					<td className="column-2">
						<p
							style={ {
								margin: '0',
							} }
						>
							<input
								type="button"
								className="button button-secondary button-large button-save-changes"
								value={ __( 'Save Automatic Export configuration', 'tablepress' ) }
								onClick={ ( event ) => saveAutomaticTableExportConfig( event.target.parentNode, screenData, pathField ) }
							/>
						</p>
					</td>
				</tr>
				</tbody>
			</table>
		</>
	);
};

export default Screen;
