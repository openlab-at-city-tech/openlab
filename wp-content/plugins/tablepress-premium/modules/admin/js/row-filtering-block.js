/**
 * JavaScript code for the Row Filtering module's controls in the TablePress table editor block.
 *
 * @package TablePress
 * @subpackage Blocks
 * @author Tobias Bäthge
 * @since 2.0.0
 */

/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { addFilter as add_filter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { Fragment }  from 'react';
import shortcode from '@wordpress/shortcode';

/**
 * Get the block name from the block.json.
 */
import block from '../../../blocks/table/block.json';

/**
 * Internal dependencies.
 */
import { TableOptionCheckboxControl, TableOptionTextControl } from './common/components';

/**
 * Add custom controls to the sidebar.
 */
const addTableBlockSidebarControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		// Return early if we are not dealing with the TablePress table block.
		if ( block.name !== props.name ) {
			return (
				<BlockEdit { ...props } />
			);
		}

		const { attributes, setAttributes } = props;

		// Don't show the extra sidebar panel if no existing table has been chosen.
		if ( ! attributes.id || ! tp.tables.hasOwnProperty( attributes.id ) ) {
			return (
				<BlockEdit { ...props } />
			);
		}

		let shortcodeAttrs = shortcode.attrs( attributes.parameters );
		shortcodeAttrs = { named: { ...shortcodeAttrs.named }, numeric: [ ...shortcodeAttrs.numeric ] }; // Use object destructuring to get a clone of the object.

		const tableOptionProps = {
			shortcodeAttrs,
			setAttributes
		};

		return (
			<Fragment>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody
						title={ __( 'Row Filtering', 'tablepress' ) }
						initialOpen={ false }
						className={ 'wp-block-tablepress-table-inspector-panel' }
					>
						<TableOptionTextControl
							label={ __( 'Row Filter term:', 'tablepress' ) }
							help={ __( 'The table will show only rows that contain this filter term. You can combine multiple filter terms with an AND or OR operator, e.g. “term1&&term2” or “term1||term2”.', 'tablepress' ) }
							tableOption={ 'filter' }
							{ ...tableOptionProps }
						/>
						<TableOptionTextControl
							label={ __( 'Row Filter URL parameter:', 'tablepress' ) }
							help={ __( 'Instead, or in addition, of using a pre-defined filter term, a URL parameter like “table_filter” can be used.', 'tablepress' ) }
							tableOption={ 'filter_url_parameter' }
							{ ...tableOptionProps }
						/>
						{ ( shortcodeAttrs.named.filter || shortcodeAttrs.named.filter_url_parameter ) &&
							<>
								<TableOptionCheckboxControl
									label={ __( 'Full cell matching', 'tablepress' ) }
									help={ __( 'If this is turned on, the full cell content has to match the filter term.', 'tablepress' ) }
									tableOption={ 'filter_full_cell_match' }
									{ ...tableOptionProps }
								/>
								<TableOptionCheckboxControl
									label={ __( 'Case-sensitive matching', 'tablepress' ) }
									help={ __( 'If this is turned on, the case sensitivity of the filter term has to match the content in the cell.', 'tablepress' ) }
									tableOption={ 'filter_case_sensitive' }
									{ ...tableOptionProps }
								/>
								<TableOptionTextControl
									label={ __( 'Filter columns:', 'tablepress' ) }
									help={ __( 'Enter a comma-separated list of the columns which should be searched for the filter terms, e.g. “1,3-5,7”.', 'tablepress' ) }
									tableOption={ 'filter_columns' }
									{ ...tableOptionProps }
								/>
								<TableOptionCheckboxControl
									label={ __( 'Invert filtering result', 'tablepress' ) }
									help={ __( 'If this is turned on, rows with the filter term will be hidden and rows without the filter term will be shown.', 'tablepress' ) }
									tableOption={ 'filter_inverse' }
									{ ...tableOptionProps }
								/>
							</>
						}
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	};
}, 'addTableBlockSidebarControls' );

add_filter(
	'editor.BlockEdit',
	'tp/row-filtering/add-table-block-sidebar-controls',
	addTableBlockSidebarControls
);
