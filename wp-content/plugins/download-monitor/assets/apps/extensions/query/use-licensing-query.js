import { useQuery } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';

export const useLicensingQuery = () => {
	return useQuery( {
		queryKey: [ 'license' ],
		queryFn: async () => {
			return await apiFetch( {
				path: '/download-monitor/v1/license',
				method: 'POST',
				data: {
					action: 'check',
				},
			} );
		},
	} );
};
