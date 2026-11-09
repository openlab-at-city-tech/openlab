export const actionTypes = {
	SET_ACTIVE_TAB: 'SET_ACTIVE_TAB',
	SET_OPTIONS: 'SET_OPTIONS',
};

export const reducer = ( state, action ) => {
	switch ( action.type ) {
		case actionTypes.SET_ACTIVE_TAB:
			return { ...state, activeTab: action.payload };

		case actionTypes.SET_OPTIONS:
			return { ...state, options: action.payload };

		default:
			return state;
	}
};
