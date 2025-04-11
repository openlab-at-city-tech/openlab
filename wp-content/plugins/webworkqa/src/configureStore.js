import { createStore, applyMiddleware, compose } from 'redux'
import thunkMiddleware from 'redux-thunk'
import rootReducer from './reducers'
import createBrowserHistory from 'history/lib/createBrowserHistory'
import { routerMiddleware, syncHistoryWithStore } from 'react-router-redux'
import { fetchQuestionIndexList } from './actions/questions'
import { fetchProblem } from './actions/problems'
import { getCurrentHash, getViewFromHash } from './util/webwork-url-parser'

export default function configureStore( initialState ) {
	const history = createBrowserHistory()

	const middleware = applyMiddleware(
		routerMiddleware( history ),
		thunkMiddleware
	)

	const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose
	const store = createStore(
		rootReducer,
		composeEnhancers(
			middleware
		)
	)

	syncHistoryWithStore( history, store )

	let prevHash = getCurrentHash( store.getState().routing )

	store.subscribe(() => {
		const currentHash = getCurrentHash( store.getState().routing )

		const hashIsChanged = currentHash !== prevHash

		// Set prevHash to avoid recursion during dispatch.
		prevHash = currentHash

		if ( hashIsChanged ) {
			const newView = getViewFromHash( currentHash )
			if ( ! newView.hasOwnProperty( 'problemId' ) ) {
				store.dispatch( fetchQuestionIndexList( false ) )
			} else {
				store.dispatch( fetchProblem( newView.problemId ) )
			}
		}

	})

	return store
};
