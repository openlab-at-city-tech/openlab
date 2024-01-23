/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';

const DEFAULT_ITEM = {
	id: 0,
	title: '',
	titleUrl: '',
	authorName: '',
	authorUrl: '',
	publisher: '',
	publisherUrl: '',
	project: '',
	projectUrl: '',
	license: '',
	datePublished: '',
	derivative: '',
};

const initalState = {
	isOpen: false,
	modalType: 'update',
	item: DEFAULT_ITEM,
	selectedBlockClientId: null,
	blockSelectionStart: null,
};

// Reducer
function reducer( state = initalState, action ) {
	switch ( action.type ) {
		case 'OPEN_MODAL':
			const { item, ...rest } = action;

			return {
				isOpen: true,
				item: { ...DEFAULT_ITEM, ...item },
				...rest,
			};

		case 'HIDE_MODAL':
			return initalState;

		case 'SET_BLOCK_SELECTION_START':
			return {
				...state,
				blockSelectionStart: action.clientId,
			};

		case 'SET_SELECTED_BLOCK_CLIENT_ID':
			return {
				...state,
				selectedBlockClientId: action.clientId,
			};
	}

	return state;
}

// Actions.
const actions = {
	open( args ) {
		return {
			type: 'OPEN_MODAL',
			isOpen: true,
			...args,
		};
	},
	hide() {
		return {
			type: 'HIDE_MODAL',
		};
	},
	setBlockSelectionStart( clientId ) {
		return {
			type: 'SET_BLOCK_SELECTION_START',
			clientId,
		};
	},
	setSelectedBlockClientId( clientId ) {
		return {
			type: 'SET_SELECTED_BLOCK_CLIENT_ID',
			clientId,
		};
	}
};

// Selectors
const selectors = {
	get( state ) {
		return state;
	},
	getSelectedBlockClientId( state ) {
		return state.selectedBlockClientId;
	},
	getBlockSelectionStart( state ) {
		return state.blockSelectionStart;
	},
};

registerStore( 'openlab/modal', {
	reducer,
	actions,
	selectors,
} );
