import { useRef, useState } from '@wordpress/element';

export default function CopyField( {
	label,
	value,
	placeholder = '',
	disabled = false,
	copyLabel = 'Copy',
	copiedLabel = 'Copied',
} ) {
	const inputRef = useRef();
	const [ copied, setCopied ] = useState( false );

	function copy() {
		if ( ! inputRef.current || ! value ) {
			return;
		}

		inputRef.current.focus();
		inputRef.current.select();
		document.execCommand( 'copy' );

		setCopied( true );
		setTimeout( () => setCopied( false ), 1500 );
	}

	return (
		<div>
			<p>{ label }</p>
			<div className="dlm-info-row__control">
				<input
					ref={ inputRef }
					type="text"
					readOnly
					value={ value || '' }
					placeholder={ placeholder }
					onFocus={ ( e ) => e.target.select() }
				/>
				<button
					type="button"
					className={
						'button button-primary dlm-info-row__copy-btn dashicons ' +
						( copied ? 'dashicons-yes-alt is-copied' : 'dashicons-format-gallery' )
					}
					disabled={ disabled || ! value }
					onClick={ copy }
					title={ copied ? copiedLabel : copyLabel }
				></button>
			</div>
		</div>
	);
}
