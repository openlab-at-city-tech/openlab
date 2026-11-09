import { Button } from '@wordpress/components';
import { useCallback } from '@wordpress/element';
import useStateContext from './context/useStateContext';
import { setActiveTab } from './context/actions';
import { useTabsQuery } from './query/useTabsQuery';
import styles from './Navigation.module.scss';

const SKELETON_WIDTHS = [ 110, 120, 90, 70, 170, 60 ];

export default function Navigation() {
	const { state, dispatch } = useStateContext();
	const { data, isLoading } = useTabsQuery();

	const handleClick = useCallback(
		( slug ) => {
			const url = new URL( window.location );
			url.searchParams.set( 'tab', slug );
			window.history.replaceState( {}, '', url );
			dispatch( setActiveTab( slug ) );
		},
		[ dispatch ]
	);

	if ( isLoading || ! data ) {
		return (
			<div className={ styles.nav }>
				{ SKELETON_WIDTHS.map( ( width, index ) => (
					<div key={ index } className={ styles.navSkeletonBar } style={ { width } } />
				) ) }
			</div>
		);
	}

	return (
		<div className={ styles.nav }>
			{ data.map( ( tab ) => (
				<Button
					key={ tab.slug }
					onClick={ () => handleClick( tab.slug ) }
					className={ `${ styles.navButton } ${ state.activeTab === tab.slug ? styles.navButtonActive : '' }` }
				>
					{ tab.label }
				</Button>
			) ) }
		</div>
	);
}
