import { combineReducers } from 'redux'

import { appIsLoading } from './appIsLoading'
import { attachments } from './attachments'
import { collapsed } from './collapsed'
import { currentFilters } from './currentFilters'
import { editing } from './editing'
import { feedback } from './feedback'
import { filterOptions } from './filterOptions'
import { formData } from './formData'
import { initialLoadComplete } from './initialLoadComplete'
import { problems } from './problems'
import { questions } from './questions'
import { questionsById } from './questionsById'
import { responseFormPending } from './responseFormPending'
import { responseIdMap } from './responseIdMap'
import { responses } from './responses'
import { routing } from './routing'
import { scores } from './scores'
import { subscriptions } from './subscriptions'
import { triedIsEmpty } from './triedIsEmpty'
import { votes } from './votes'

const queryString = function() {
	var query_string = {};
	var query = window.location.search.substring(1);
	var vars = query.split('&');
	for (var i = 0; i < vars.length; i++) {
		var pair = vars[i].split('=');
		// If first entry with this name
		if (typeof query_string[pair[0]] === 'undefined') {
			query_string[pair[0]] = decodeURIComponent(pair[1]);

		// If second entry with this name
		} else if (typeof query_string[pair[0]] === 'string') {
			var arr = [ query_string[pair[0]], decodeURIComponent(pair[1]) ];
			query_string[pair[0]] = arr;

		// If third or later entry with this name
		} else {
			query_string[pair[0]].push(decodeURIComponent(pair[1]));
		}
	}
	return query_string;
};

const rootReducer = combineReducers({
	appIsLoading,
	attachments,
	collapsed,
	currentFilters,
	editing,
	feedback,
	filterOptions,
	formData,
	initialLoadComplete,
	problems,
	queryString,
	questions,
	questionsById,
	responseFormPending,
	responseIdMap,
	responses,
	routing,
	scores,
	subscriptions,
	triedIsEmpty,
	votes
})

export default rootReducer
