import { useRef, useState } from '@wordpress/element';
import { BaseControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import htmlHelp from './htmlHelp';
import styles from './ConsoleUriField.module.scss';

export default function ConsoleUriField( { field } ) {
	const inputRef = useRef();
	const [ copied, setCopied ] = useState( false );

	const copy = () => {
		if ( ! inputRef.current ) {
			return;
		}

		inputRef.current.focus();
		inputRef.current.select();
		document.execCommand( 'copy' );

		setCopied( true );
		setTimeout( () => setCopied( false ), 1500 );
	};

	return (
		<BaseControl label={ field.label } help={ htmlHelp( field.desc ) }>
			<div className={ styles.control }>
				<input ref={ inputRef } type="text" readOnly value={ field.default || '' } onFocus={ ( e ) => e.target.select() } />
				<button
					type="button"
					className={ `button button-primary dashicons ${ styles.copyBtn } ${ copied ? 'dashicons-yes-alt ' + styles.copied : 'dashicons-format-gallery' }` }
					onClick={ copy }
					title={ copied ? __( 'Copied', 'download-monitor' ) : __( 'Copy', 'download-monitor' ) }
				></button>
			</div>
		</BaseControl>
	);
}
