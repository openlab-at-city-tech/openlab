import { Card, CardBody } from '@wordpress/components';
import useStateContext from './context/useStateContext';
import { setOptions } from './context/actions';
import FieldRenderer from './FieldRenderer';
import LockedForm from './LockedForm';
import GatewayOverviewField from './fields/GatewayOverviewField';
import htmlHelp from './fields/htmlHelp';
import styles from './SettingsForm.module.scss';

const DISPLAY_ONLY = [ 'title', 'desc' ];
const NARROW_TYPES = [ 'text', 'password', 'select', 'lazy_select' ];

const isChecked = ( value ) => '1' === value || true === value;

function renderDisplayOnly( field, index ) {
	if ( 'title' === field.type ) {
		return field.title ? <h3 key={ index }>{ field.title }</h3> : null;
	}

	return field.text ? <p key={ index }>{ htmlHelp( field.text ) }</p> : null;
}

function getWidthClass( field ) {
	if ( 'half' === field.width ) {
		return styles.halfField;
	}

	if ( NARROW_TYPES.includes( field.type ) ) {
		return styles.narrowField;
	}

	return '';
}

function isChildFieldVisible( fieldsByName, getValue, field, parentCheckboxValue ) {
	if ( ! field.child ) {
		return true;
	}

	if ( true === field.child ) {
		return isChecked( parentCheckboxValue );
	}

	const controllingField = fieldsByName[ field.child.field ];
	const controllingValue = controllingField ? getValue( controllingField ) : undefined;
	const expected = field.child.value;

	return Array.isArray( expected ) ? expected.includes( controllingValue ) : expected === controllingValue;
}

function renderFieldList( fields, getValue, onFieldChange ) {
	const rendered = [];
	const seenRows = new Set();
	let parentCheckboxValue = null;

	const fieldsByName = {};
	fields.forEach( ( f ) => {
		if ( f.name ) {
			fieldsByName[ f.name ] = f;
		}
	} );

	fields.forEach( ( field, index ) => {
		if ( ! isChildFieldVisible( fieldsByName, getValue, field, parentCheckboxValue ) ) {
			return;
		}

		if ( 'checkbox' === field.type && ! field.child ) {
			parentCheckboxValue = getValue( field );
		}

		if ( DISPLAY_ONLY.includes( field.type ) ) {
			rendered.push( renderDisplayOnly( field, index ) );

			return;
		}

		if ( field.row ) {
			if ( seenRows.has( field.row ) ) {
				return;
			}
			seenRows.add( field.row );

			const rowFields = fields.filter( ( f ) => f.row === field.row );

			rendered.push(
				<div key={ `row-${ field.row }` } className={ styles.fieldRow }>
					{ rowFields.map( ( rowField, rowIndex ) => (
						<div key={ rowField.name || rowIndex } className={ styles.fieldWrapper }>
							<FieldRenderer
								field={ rowField }
								value={ getValue( rowField ) }
								onChange={ ( value ) => onFieldChange( rowField, value ) }
							/>
						</div>
					) ) }
				</div>
			);

			return;
		}

		rendered.push(
			<div key={ field.name || index } className={ `${ styles.fieldWrapper } ${ getWidthClass( field ) }`.trim() }>
				<FieldRenderer
					field={ field }
					value={ getValue( field ) }
					onChange={ ( value ) => onFieldChange( field, value ) }
				/>
			</div>
		);
	} );

	return rendered;
}

export default function SettingsForm( { section } ) {
	const { state, dispatch } = useStateContext();

	const getValue = ( field ) =>
		Object.prototype.hasOwnProperty.call( state.options, field.name )
			? state.options[ field.name ]
			: field.default;

	const handleChange = ( field, value ) => {
		dispatch( setOptions( { ...state.options, [ field.name ]: value } ) );
	};

	const getGatewayValue = ( gateway ) => {
		const name = 'dlm_gateway_' + gateway.id + '_enabled';

		return Object.prototype.hasOwnProperty.call( state.options, name )
			? '1' === state.options[ name ]
			: gateway.enabled;
	};

	const handleGatewayChange = ( gateway, checked ) => {
		dispatch( setOptions( { ...state.options, [ 'dlm_gateway_' + gateway.id + '_enabled' ]: checked ? '1' : '0' } ) );
	};

	if ( ! section.fields || 0 === section.fields.length ) {
		return null;
	}

	const fieldsByName = {};
	section.fields.forEach( ( field ) => {
		if ( field.name ) {
			fieldsByName[ field.name ] = field;
		}
	} );

	const isFieldVisible = ( field, parentCheckboxValue ) => {
		if ( ! field.child ) {
			return true;
		}

		if ( true === field.child ) {
			return isChecked( parentCheckboxValue );
		}

		const controllingField = fieldsByName[ field.child.field ];
		const controllingValue = controllingField ? getValue( controllingField ) : undefined;
		const expected = field.child.value;

		return Array.isArray( expected ) ? expected.includes( controllingValue ) : expected === controllingValue;
	};

	const renderField = ( field, index, disabled ) => {
		if ( DISPLAY_ONLY.includes( field.type ) ) {
			return renderDisplayOnly( field, index );
		}

		if ( 'gateway_overview' === field.type ) {
			return (
				<GatewayOverviewField
					key={ index }
					getGatewayValue={ getGatewayValue }
					onGatewayChange={ handleGatewayChange }
					disabled={ section.locked }
				/>
			);
		}

		if ( 'group' === field.type ) {
			return (
				<Card key={ field.name || index } className={ styles.group }>
					<CardBody>
						<h4>{ field.label }</h4>
						{ renderFieldList( field.options || [], getValue, handleChange ) }
					</CardBody>
				</Card>
			);
		}

		const wrapperClass = `${ styles.fieldWrapper } ${ getWidthClass( field ) }`.trim();

		return (
			<div key={ field.name || index } className={ wrapperClass }>
				<FieldRenderer
					field={ field }
					value={ getValue( field ) }
					onChange={ ( value ) => handleChange( field, value ) }
					disabled={ disabled }
				/>
			</div>
		);
	};

	const renderItems = ( items, disabled ) => {
		const rendered = [];
		const seenRows = new Set();

		items.forEach( ( { field, index } ) => {
			const isRowable = field.row && ! DISPLAY_ONLY.includes( field.type ) && 'gateway_overview' !== field.type && 'group' !== field.type;

			if ( ! isRowable ) {
				rendered.push( renderField( field, index, disabled ) );

				return;
			}

			if ( seenRows.has( field.row ) ) {
				return;
			}
			seenRows.add( field.row );

			const rowItems = items.filter( ( it ) => it.field.row === field.row );

			rendered.push(
				<div key={ `row-${ field.row }` } className={ styles.fieldRow }>
					{ rowItems.map( ( { field: rowField, index: rowIndex } ) => (
						<div key={ rowField.name || rowIndex } className={ styles.fieldWrapper }>
							<FieldRenderer
								field={ rowField }
								value={ getValue( rowField ) }
								onChange={ ( value ) => handleChange( rowField, value ) }
								disabled={ disabled }
							/>
						</div>
					) ) }
				</div>
			);
		} );

		return rendered;
	};

	// Group consecutive plain fields locked for the exact same reason so the
	// "upgrade to plan X" message only shows once per group instead of once per field.
	let parentCheckboxValue = null;
	const groups = [];

	section.fields.forEach( ( field, index ) => {
		if ( ! isFieldVisible( field, parentCheckboxValue ) ) {
			return;
		}

		if ( 'checkbox' === field.type && ! field.child ) {
			parentCheckboxValue = getValue( field );
		}

		const isPlainField = ! DISPLAY_ONLY.includes( field.type ) && 'gateway_overview' !== field.type && 'group' !== field.type;
		const fieldLocked = isPlainField && ( section.locked || !! field.locked );
		const fieldBadge = field.badge || section.badge;
		const fieldReason = field.reason || section.reason;
		const fieldExtensionName = field.extensionName || section.extensionName;
		const lockKey = fieldLocked ? `${ fieldBadge || '' }|${ fieldReason || '' }` : null;

		const last = groups[ groups.length - 1 ];

		if ( last && last.lockKey === lockKey ) {
			last.items.push( { field, index } );

			return;
		}

		groups.push( { lockKey, badge: fieldBadge, reason: fieldReason, extensionName: fieldExtensionName, items: [ { field, index } ] } );
	} );

	return (
		<div className={ styles.form }>
			{ section.locked && <LockedForm badge={ section.badge } reason={ section.reason } extensionName={ section.extensionName } /> }

			{ groups.map( ( group, groupIndex ) => {
				if ( ! group.lockKey ) {
					return renderItems( group.items, false );
				}

				return (
					<div key={ `locked-group-${ groupIndex }` } className={ styles.lockedGroup }>
						{ renderItems( group.items, true ) }
						{ ! section.locked && <LockedForm badge={ group.badge } reason={ group.reason } extensionName={ group.extensionName } /> }
					</div>
				);
			} ) }
		</div>
	);
}
