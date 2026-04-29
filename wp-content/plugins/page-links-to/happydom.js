import { GlobalRegistrator } from '@happy-dom/global-registrator';

GlobalRegistrator.register({
	settings: {
		navigation: {
			disableMainFrameNavigation: true,
			disableChildFrameNavigation: true,
			disableChildPageNavigation: true,
			disableFallbackToSetURL: true,
		},
	},
});
