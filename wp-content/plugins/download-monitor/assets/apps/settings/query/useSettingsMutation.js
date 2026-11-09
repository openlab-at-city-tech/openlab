import { useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';

export const useSettingsMutation = () => {
	const queryClient = useQueryClient();

	return useMutation( {
		mutationFn: async ( data ) =>
			apiFetch( { path: '/download-monitor/v1/settings', method: 'POST', data } ),
		onSuccess: async () => {
			await queryClient.invalidateQueries( { queryKey: [ 'dlm-settings-tabs' ] } );
		},
	} );
};
