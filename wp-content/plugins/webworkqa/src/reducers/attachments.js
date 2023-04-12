import { ADD_ATTACHMENT, RECEIVE_ATTACHMENTS } from '../actions/app'

export function attachments( state = {}, action ) {
	switch ( action.type ) {
		case ADD_ATTACHMENT :
			const { attData } = action.payload

			let urlThumb = attData.attributes.sizes.full.url
			if ( attData.attributes.width > 800 ) {
				if ( attData.attributes.sizes.hasOwnProperty( 'large' ) && attData.attributes.sizes.large.width <= 800 ) {
					urlThumb = attData.attributes.sizes.large.url
				} else if ( attData.attributes.sizes.hasOwnProperty( 'medium' ) ) {
					urlThumb = attData.attributes.sizes.medium.url
				}
			}

			const attDataForState = {
				id: attData.id,
				caption: attData.attributes.caption,
				filename: attData.attributes.filename,
				urlFull: attData.attributes.sizes.full.url,
				urlThumb,
				title: attData.attributes.title,
			}

			let newState = Object.assign( {}, state )
			newState[ attData.id ] = attDataForState

			return newState

		case RECEIVE_ATTACHMENTS :
			return Object.assign( {}, state, action.payload )

		default :
			return state
	}
}

