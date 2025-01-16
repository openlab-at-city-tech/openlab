export function unregister_block_for_non_page(wp, block_name) {

	const { unregisterBlockType, getBlockType } = wp.blocks;
	const { select, subscribe } = wp.data;

	wp.domReady(() => {

		let is_unregistered = false;

		const unsubscribe = subscribe(() => {

			// Do nothing if already unregistered
			if (is_unregistered) {
				return;
			}

			// Try to get current post type
			let post_type = null;
			if (select('core/editor') && typeof select('core/editor').getCurrentPostType === 'function') {
				post_type = select('core/editor').getCurrentPostType();
			}

			// If post type is not available yet, then do not continue
			if (!post_type) {
				return;
			}

			// If post type is 'page', then unsubscribe and do nothing
			if (post_type === 'page') {
				is_unregistered = true;
				unsubscribe();
				return;
			}

			// Unregister the current block for non-page post type
			if (getBlockType('echo-knowledge-base/' + block_name)) {
				try {
					unregisterBlockType('echo-knowledge-base/' + block_name);
				} catch (error) {}
			}

			// Unsubscribe to prevent further execution
			is_unregistered = true;
			unsubscribe();
		});
	});
}