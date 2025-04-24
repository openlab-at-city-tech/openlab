import React, { Component } from 'react';
import Problem404 from '../components/Problem404'
import ProblemStatsContainer from '../containers/ProblemStatsContainer'
import ProblemSummary from '../components/ProblemSummary'
import QuestionFormContainer from '../containers/QuestionFormContainer'
import QuestionSortDropdownContainer from '../containers/QuestionSortDropdownContainer'
import QuestionList from '../components/QuestionList'
import { __, sprintf } from '@wordpress/i18n';

export default class Problem extends Component {
	componentWillMount() {
		const { onComponentWillMount } = this.props
		const { problemId } = this.props
		onComponentWillMount( problemId )
	}

	componentDidMount() {
		// This is so amazing it makes me want to wrap up my programming career
		setTimeout( function() {
			if ( ! document.hasOwnProperty( 'webwork_initialized' ) || ! document.webwork_initialized ) {
				document.webwork_scaffold_init()
				document.webwork_initialized = true
			}
		}, 1000 );
	}

	componentWillUnmount() {
		document.webwork_initialized = false
	}

	render() {
		const {
			appIsLoading, initialLoadComplete,
			problems, problemId, questionsById, userCanAskQuestion
		} = this.props

		const problem = problems[problemId]

		const questionFormElement = userCanAskQuestion ? <QuestionFormContainer problemId={problemId} /> : ''

		let problemTitle = 'Another Math Problem'
		if ( problem && problem.hasOwnProperty( 'problemSet' ) ) {
			/* translators: Problem set name */
			problemTitle = sprintf( __( 'Problem: %s', 'webworkqa' ), problem.problemSet )
		}

		let element
		if ( problem || ! initialLoadComplete ) {
			element = (
				<div className="ww-problem">
					<h2 className="ww-header">{problemTitle}</h2>

					<div className="problem-topmatter">
						<ProblemStatsContainer />
						<ProblemSummary problemId={problemId} problem={problem} />
					</div>

					{questionFormElement}

					<div className="problem-questions">
						<QuestionSortDropdownContainer
						  itemType='problem'
						  problemId={problemId}
						/>
						<QuestionList
							isLoading={appIsLoading}
							questionsById={questionsById}
						/>
					</div>
				</div>
			)
		} else {
			element = <Problem404 />
		}

		return element
	}
}
