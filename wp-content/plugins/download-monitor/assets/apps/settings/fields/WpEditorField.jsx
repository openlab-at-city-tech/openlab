import { useEffect, useRef } from '@wordpress/element';
import { BaseControl } from '@wordpress/components';
import htmlHelp from './htmlHelp';
import styles from './WpEditorField.module.scss';

let instanceCount = 0;

export default function WpEditorField( { field, value, onChange, disabled } ) {
	const idRef = useRef();
	if ( ! idRef.current ) {
		idRef.current = `dlm-wp-editor-${ field.name || 'field' }-${ instanceCount++ }`;
	}
	const id = idRef.current;

	const onChangeRef = useRef( onChange );
	onChangeRef.current = onChange;

	useEffect( () => {
		if ( disabled || ! window.wp || ! window.wp.editor ) {
			return;
		}

		const textarea = document.getElementById( id );

		if ( ! textarea ) {
			return;
		}

		const handleChange = () => onChangeRef.current( textarea.value );

		window.wp.editor.initialize( id, {
			tinymce: {
				wpautop: false,
				plugins: 'lists,link,paste,wordpress,wplink',
				toolbar1: 'bold,italic,bullist,numlist,blockquote,link,unlink,undo,redo',
				content_style: 'body { font-family: -apple-system, system-ui, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; font-size: 16px; }',
				setup( editor ) {
					editor.on( 'change keyup setcontent', () => {
						onChangeRef.current( editor.getContent() );
					} );
				},
			},
			quicktags: true,
			mediaButtons: false,
		} );

		textarea.addEventListener( 'input', handleChange );
		textarea.addEventListener( 'change', handleChange );

		return () => {
			textarea.removeEventListener( 'input', handleChange );
			textarea.removeEventListener( 'change', handleChange );

			if ( window.wp && window.wp.editor ) {
				window.wp.editor.remove( id );
			}
		};
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [ id, disabled ] );

	return (
		<BaseControl label={ field.label } help={ htmlHelp( field.desc ) } className={ styles.wrapper }>
			<textarea id={ id } defaultValue={ value || '' } disabled={ disabled } rows={ 10 } className={ styles.textarea } />
		</BaseControl>
	);
}
