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
};

// Selectors
const selectors = {
	get( state ) {
		return state;
	},
};

registerStore( 'openlab/modal', {
	reducer,
	actions,
	selectors,
} );
