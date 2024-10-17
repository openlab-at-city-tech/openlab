/**
 * JavaScript code for the Row Order module's controls in the TablePress table editor block.
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
import { TableOptionRadioControl, TableOptionSelectControl, TableOptionTextControl } from './common/components';

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
						title={ __( 'Row Order', 'tablepress' ) }
						initialOpen={ false }
						className={ 'wp-block-tablepress-table-inspector-panel' }
					>
						<TableOptionSelectControl
							label={ __( 'Row Order:', 'tablepress' ) }
							help={ __( 'Choose the desired row order.', 'tablepress' ) }
							options={ [
								{ value: '', label: __( '— Choose —', 'tablepress' ) },
								{ value: 'default', label: __( 'Default', 'tablepress' ) },
								{ value: 'reverse', label: __( 'Reverse', 'tablepress' ) },
								{ value: 'sort', label: __( 'Sort', 'tablepress' ) },
								{ value: 'manual', label: __( 'Custom', 'tablepress' ) },
							] }
							tableOption={ 'row_order' }
							{ ...tableOptionProps }
						/>
						{ ( 'sort' === shortcodeAttrs.named.row_order ) &&
							<>
								<TableOptionTextControl
									label={ __( 'Sort columns:', 'tablepress' ) }
									help={ __( 'Enter the columns which shall be sorted on, e.g. “2,5”.', 'tablepress' ) }
									tableOption={ 'row_order_sort_column' }
									{ ...tableOptionProps }
								/>
								{ ( shortcodeAttrs.named.row_order_sort_column?.includes( ',' ) ) &&
									<TableOptionTextControl
										label={ __( 'Sort order:', 'tablepress' ) }
										help={ __( 'Type the desired sort orders for the columns, e.g. “desc,asc”.', 'tablepress' ) }
										tableOption={ 'row_order_sort_direction' }
										{ ...tableOptionProps }
									/>
								}
								{ ( ! shortcodeAttrs.named.row_order_sort_column?.includes( ',' ) ) &&
									<TableOptionRadioControl
										label={ __( 'Sort order:', 'tablepress' ) }
										help={ __( 'Choose the desired sort order.', 'tablepress' ) }
										options={ [
											{ value: 'asc', label: __( 'Ascending', 'tablepress' ) },
											{ value: 'desc', label: __( 'Descending', 'tablepress' ) },
										] }
										tableOption={ 'row_order_sort_direction' }
										{ ...tableOptionProps }
									/>
								}
							</>
						}
						{ ( 'manual' === shortcodeAttrs.named.row_order ) &&
							<TableOptionTextControl
								label={ __( 'Custom row order:', 'tablepress' ) }
								help={ __( 'Enter a comma-separated list of rows, e.g. “1,3-5,7”.', 'tablepress' ) + ' ' + __( 'You can also use the keywords “all”, “reverse”, and “last”.', 'tablepress' ) }
								tableOption={ 'row_order_manual_order' }
								{ ...tableOptionProps }
							/>
						}
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	};
}, 'addTableBlockSidebarControls' );

add_filter(
	'editor.BlockEdit',
	'tp/row-order/add-table-block-sidebar-controls',
	addTableBlockSidebarControls
);
