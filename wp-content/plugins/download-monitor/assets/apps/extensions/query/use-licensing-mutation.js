import { useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import { useCallback } from '@wordpress/element';

export const useLicensingMutation = () => {
	const queryClient = useQueryClient();

	const mutationFn = useCallback( ( vars ) => {
		return apiFetch( {
			path: `/download-monitor/v1/license`,
			method: 'POST',
			data: {
				license_key: vars.licenseKey,
				action: vars.action,
			},
		} );
	}, [] );

	return useMutation( {
		mutationFn,
		onSuccess: () => {
			queryClient.invalidateQueries( { refetchType: 'all', queryKey: [ 'license' ] } );
			queryClient.invalidateQueries( { refetchType: 'all', queryKey: [ 'extensions' ] } );
		},
	} );
};
