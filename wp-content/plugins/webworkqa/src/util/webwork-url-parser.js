const getCurrentHash = ( router ) => {
	return ( null === router.locationBeforeTransitions ) ? window.location.hash : router.locationBeforeTransitions.hash
}

const getCurrentView = ( router ) => {
	const hash = getCurrentHash( router )
	return getViewFromHash( hash )
}

const getViewFromHash = ( hash ) => {
	const rawParams = hash.split( ':' )
	let params = {}
	let paramParts
	for ( var i in rawParams ) {
		paramParts = rawParams[ i ].split( '=' )

		// All params must have values. Leading '#' will not.
		if ( 1 === paramParts.length ) {
			continue
		}

		params[ paramParts[0] ] = paramParts[1]
	}

	return params
}

const indexFilters = {
	course: 1,
	problemSet: 1
}

const buildHashFromFilter = ( slug, value, currentLocation ) => {
	let newState
	const currentView = getCurrentView( currentLocation )

	if ( value ) {
		let newStatelet = {}
		newStatelet[ slug ] = value
		newState = Object.assign( {}, currentView, newStatelet )
	} else {
		newState = Object.assign( {}, currentView )
		delete newState[ slug ]
	}

	if ( indexFilters.hasOwnProperty( slug ) ) {
		delete( newState.problemId )
		delete( newState.questionId )
	}

	return buildHashFromState( newState )
}

const buildHashFromState = ( state ) => {
	if ( 0 === state.length ) {
		return ''
	}

	let hash = ''
	for ( var i in state ) {
		hash += ':' + i + '=' + state[i]
	}

	if ( hash.length ) {
		hash = '#' + hash
	}

	return hash
}

export { buildHashFromFilter, getCurrentHash, getCurrentView, getViewFromHash }
