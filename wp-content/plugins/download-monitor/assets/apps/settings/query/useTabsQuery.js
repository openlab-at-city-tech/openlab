import { useQuery } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';

export const useTabsQuery = () => {
	return useQuery( {
		queryKey: [ 'dlm-settings-tabs' ],
		retry: 1,
		queryFn: async () => apiFetch( { path: '/download-monitor/v1/settings-tabs', method: 'GET' } ),
	} );
};
