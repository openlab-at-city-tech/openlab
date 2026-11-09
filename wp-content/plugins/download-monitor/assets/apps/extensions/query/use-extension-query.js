import { useQuery } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';

export const useExtensionQuery = () => {
	return useQuery( {
		queryKey: [ 'extensions' ],
		queryFn: async () => {
			return await apiFetch( {
				path: `/download-monitor/v1/extensions`,
				method: 'GET',
			} );
		},
	} );
};
