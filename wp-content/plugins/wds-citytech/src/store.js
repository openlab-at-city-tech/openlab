import { createReduxStore, register } from '@wordpress/data'

const DEFAULT_STATE = {
	prePublishPanelStatus: {
		postSharingOptions: 'closed',
		prePublicationPrivacy: 'open',
	}
}

const actions = {
	setPrePublishPanelStatus( panelKey, panelStatus ) {
		return { type: 'SET_PRE_PUBLISH_PANEL_STATUS', panelKey: panelKey, panelStatus: panelStatus }
	}
}

const reducer = ( state = DEFAULT_STATE, action ) => {
	switch ( action.type ) {
		case 'SET_PRE_PUBLISH_PANEL_STATUS':
			return {
				...state,
				prePublishPanelStatus: {
					...state.prePublishPanelStatus,
					[ action.panelKey ]: action.panelStatus,
				},
			}
		default:
			return state
	}
}

const selectors = {
	isPanelOpen( state, panelKey ) {
		return state.openPanel === panelKey
	},
}

export const STORE_NAME = 'wds-citytech'

register(
	createReduxStore( STORE_NAME, {
		reducer,
		actions,
		selectors,
	} )
)
