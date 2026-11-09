import { useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import { useCallback } from '@wordpress/element';

export const useExtensionMutation = () => {
	const queryClient = useQueryClient();

	const mutationFn = useCallback( ( vars ) => {
		return apiFetch( {
			path: `/dlm-pro/v1/extension`,
			method: 'POST',
			data: {
				extension: vars.extension,
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
