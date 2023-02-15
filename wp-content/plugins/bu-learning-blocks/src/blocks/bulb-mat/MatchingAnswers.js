import MatchingAnswer from './MatchingAnswer';

export default ( {
	answers = [],
	onChangeAnswers,
	minAnswers = 1,
	maxAnswers = 12,
	feedbackPerAnswer = true,
} ) => {
	const onChangeAnswerValue = ( newAnswerValue, index ) => {
		const newAnswers = [ ...answers ];
		newAnswers[ index ] = { ...answers[ index ], answer: newAnswerValue };
		onChangeAnswers( newAnswers );
	};

	const onChangeFeedback = ( newFeedback, index ) => {
		const newAnswers = [ ...answers ];
		newAnswers[ index ] = { ...answers[ index ], feedback: newFeedback };
		onChangeAnswers( newAnswers );
	};

	const onChangeCorrect = ( newCorrect, index ) => {
		const oldCorrect = answers[ index ].correct;
		const newAnswers = answers.map( ( answer ) => {
			// Correct propery must be unique, so swap for the old correct on a conflicting answer.
			if ( answer.correct === newCorrect ) {
				answer.correct = oldCorrect;
			}
			return answer;
		} );

		newAnswers[ index ] = { ...answers[ index ], correct: newCorrect };
		onChangeAnswers( newAnswers );
	};

	const onAddAnswer = () => {
		if ( answers.length < maxAnswers ) {
			// Add next letter as the default correct on new answers.
			const newAnswers = [ ...answers, {
				answer: '',
				feedback: '',
				correct: String.fromCharCode( 65 + ( answers.length ) ),
			} ];
			onChangeAnswers( newAnswers );
		}
	};

	const onRemoveAnswer = index => {
		if ( answers.length > minAnswers ) {
			const newAnswers = answers.filter( ( answer, i ) => index !== i );
			onChangeAnswers( newAnswers );
		}
	};

	const renderAnswers = () => {
		const answerList = answers.map( ( answer, index ) => (
			<MatchingAnswer
				key={ index }
				index={ index }
				{ ...answer }
				answerCount={ answers.length }
				feedbackPerAnswer={ feedbackPerAnswer }
				onChangeAnswerValue={ onChangeAnswerValue }
				onChangeFeedback={ onChangeFeedback }
				onChangeCorrect={ onChangeCorrect }
				onRemoveAnswer={ answers.length > minAnswers ? onRemoveAnswer : null }
			/>
		) );
		return answerList;
	};

	const renderAddAnswer = () => {
		if ( answers.length < maxAnswers ) {
			return <button onClick={ onAddAnswer }>Add Pair</button>;
		}
	};

	return (
		<div>
			<h5 className="bulb-editor-section-label">Pairs</h5>
			{ renderAnswers() }
			{ renderAddAnswer() }
		</div>
	);
};
