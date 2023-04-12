import fetch from 'isomorphic-fetch'
import { setScore } from './scores'

export function clickVote( itemId, voteType, itemType ) {
	return ( dispatch ) => {
		dispatch( sendVote( itemId, voteType, itemType ) )
		dispatch( setVote( itemId, voteType ) )
	}
}

function sendVote(itemId, voteType, itemType) {
	return ( dispatch ) => {
		const { rest_api_endpoint, rest_api_nonce } = window.WWData
		const endpoint = rest_api_endpoint + 'votes/'

		return fetch( endpoint, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': rest_api_nonce
			},
			body: JSON.stringify({
				item_id: itemId,
				item_type: itemType,
				value: voteType
			})
		} )
		.then( response => response.json() )
		.then( json => {
			dispatch( setScore( itemId, json ) )
		} );

	}
}

export const SET_VOTE = 'SET_VOTE'
export const setVote = (itemId, voteType) => {
	return {
		type: SET_VOTE,
		payload: {
			itemId,
			voteType
		}
	}
}

export const SET_VOTES_BULK = 'SET_VOTES_BULK'
export const setVotesBulk = (votes) => {
	return {
		type: SET_VOTES_BULK,
		payload: votes
	}
}
