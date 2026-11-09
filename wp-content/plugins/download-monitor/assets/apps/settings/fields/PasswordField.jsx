import { useState } from '@wordpress/element';
import { BaseControl } from '@wordpress/components';
import htmlHelp from './htmlHelp';
import styles from './PasswordField.module.scss';

export default function PasswordField( { field, value, onChange, disabled } ) {
	const [ visible, setVisible ] = useState( false );

	return (
		<BaseControl label={ field.label } help={ htmlHelp( field.desc ) } id={ `dlm-pw-${ field.name }` }>
			<div className={ styles.inputWrap }>
				<input
					id={ `dlm-pw-${ field.name }` }
					type={ visible ? 'text' : 'password' }
					value={ value || '' }
					disabled={ disabled }
					onChange={ ( e ) => onChange( e.target.value ) }
				/>
				<button
					type="button"
					className={ `wp-hide-pw ${ styles.eyeToggle }` }
					onClick={ () => setVisible( ( v ) => ! v ) }
				>
					<span className={ `dashicons ${ visible ? 'dashicons-hidden' : 'dashicons-visibility' }` }></span>
				</button>
			</div>
		</BaseControl>
	);
}
