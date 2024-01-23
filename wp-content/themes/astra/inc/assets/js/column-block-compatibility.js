const updatingBlock = ['core/group'];

wp.hooks.addFilter(
	'blocks.registerBlockType',
	'astra/meta/groupLayoutSettings',
	(settings, name) => {
		if (!updatingBlock.includes(name)) {
			return settings;
		}

		const newSettings = {
			...settings,
			supports: {
				...(settings.supports || {}),
				layout: {
					...(settings.supports.layout || {}),
					allowEditing: true,
					allowSwitching: false,
					allowInheriting: true,
				},
				__experimentalLayout: {
					...(settings.supports.__experimentalLayout || {}),
					allowEditing: true,
					allowSwitching: false,
					allowInheriting: true,
				},
			},
		};
		return newSettings;
	},
	20
);

/**
 * Set "Inherit default layout" option enable by default for Group block.
 *
 * Also set "Full Width" layout by default on drag-drop for following blocks.
 */
wp.blocks.registerBlockVariation(
	'core/group',
	{
		isDefault: true,
		attributes: {
			layout: {
				inherit: true,
			},
			align: 'full'
		},
	}
);
wp.blocks.registerBlockVariation(
	'core/cover',
	{
		isDefault: true,
		attributes: {
			align: 'full'
		},
	}
);
