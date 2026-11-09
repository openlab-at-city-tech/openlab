import { useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import { useCallback } from '@wordpress/element';

export const useExtensionsMutation = () => {
	const queryClient = useQueryClient();

	const mutationFn = useCallback( ( vars ) => {
		return apiFetch( {
			path: `/dlm-pro/v1/extensions`,
			method: 'POST',
			data: {
				extensions: vars.extensions,
				status: vars.status,
			},
		} );
	}, [] );

	return useMutation( {
		mutationFn,
		onSuccess: () => {
			queryClient.invalidateQueries( {
				refetchType: 'all',
				queryKey: [ 'extensions' ],
			} );
		},
	} );
};
