import { useEffect, useState } from '@wordpress/element';
import useStateContext from './context/useStateContext';
import DateRangeSelect from './components/DateRangeSelect';
import TabNavigation from './components/TabNavigation';
import OverviewTab from './components/OverviewTab';
import DetailedTab from './components/DetailedTab';
import { applyFilters } from '@wordpress/hooks';
import styles from './ReportsWrapper.module.scss';

export default function ReportsWrapper() {
	const { state, dispatch } = useStateContext();
	const [ mountedTabs, setMountedTabs ] = useState( () => new Set( [ state.activeTab ] ) );

	useEffect( () => {
		setMountedTabs( ( prev ) => {
			if ( prev.has( state.activeTab ) ) {
				return prev;
			}
			return new Set( prev ).add( state.activeTab );
		} );
	}, [ state.activeTab ] );

	const isKnownTab = 'overview' === state.activeTab || 'detailed' === state.activeTab;

	return (
		<div className={styles.dlmReportsWrapper} >
			<div className={styles.dlmReportsHeader}>
				<TabNavigation />
				{applyFilters('dlm.reports.after.nav', '', { dispatch, state })}
			</div>
			<div className={styles.dlmReportsRangeSelect}>
				<DateRangeSelect />
				{applyFilters('dlm.reports.after.rangeSelect', '', { dispatch, state })}
			</div>
			<div className={styles.dlmReportsBody}>
				{ mountedTabs.has( 'overview' ) && (
					<div style={ { display: 'overview' === state.activeTab ? undefined : 'none' } }>
						<OverviewTab />
					</div>
				) }
				{ mountedTabs.has( 'detailed' ) && (
					<div style={ { display: 'detailed' === state.activeTab ? undefined : 'none' } }>
						<DetailedTab />
					</div>
				) }
				{ ! isKnownTab && applyFilters( `dlm.reports.tab.${ state.activeTab }.body`, '', { dispatch, state } ) }
				{applyFilters('dlm.reports.after.tab.content', '', { dispatch, state })}
			</div>
		</div>
	);
}
