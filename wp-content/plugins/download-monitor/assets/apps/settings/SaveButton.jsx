import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import useStateContext from './context/useStateContext';
import { setOptions } from './context/actions';
import { useSettingsMutation } from './query/useSettingsMutation';
import styles from './SaveButton.module.scss';

export default function SaveButton() {
	const { state, dispatch } = useStateContext();
	const mutation = useSettingsMutation();
	const [ showNotice, setShowNotice ] = useState( false );

	const isEmpty = 0 === Object.keys( state.options || {} ).length;

	const handleClick = () => {
		mutation.mutate( state.options, {
			onSuccess: () => {
				dispatch( setOptions( {} ) );
				setShowNotice( true );
			},
		} );
	};

	useEffect( () => {
		if ( ! showNotice ) {
			return;
		}

		const timer = setTimeout( () => setShowNotice( false ), 3000 );

		return () => clearTimeout( timer );
	}, [ showNotice ] );

	return (
		<div className={ styles.wrap }>
			<Button onClick={ handleClick } disabled={ mutation.isPending || isEmpty } variant="primary">
				{ mutation.isPending ? __( 'Saving…', 'download-monitor' ) : __( 'Save', 'download-monitor' ) }
			</Button>
			{ showNotice && <span className={ styles.notice }>{ __( 'Settings saved.', 'download-monitor' ) }</span> }
		</div>
	);
}
