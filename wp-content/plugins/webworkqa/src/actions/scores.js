export const SET_SCORE = 'SET_SCORE'
export const setScore = (itemId, score) => {
	return {
		type: SET_SCORE,
		payload: {
			itemId,
			score
		}
	}
}

export const SET_SCORES_BULK = 'SET_SCORES_BULK'
export const setScoresBulk = (scores) => {
	return {
		type: SET_SCORES_BULK,
		payload: scores
	}
}
