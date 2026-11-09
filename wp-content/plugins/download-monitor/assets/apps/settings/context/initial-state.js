export const getInitialTab = () => {
	const url = new URL( window.location );

	return url.searchParams.get( 'tab' ) || 'general';
};

export const initialState = {
	activeTab: getInitialTab(),
	options: {},
};
