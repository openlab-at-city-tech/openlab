import Answer from './Answer';

export default function Answers( {
	answers = [],
	onChangeAnswers,
	multipleCorrectAllowed = false,
	minAnswers = 1,
	maxAnswers = 1,
	defaultAnswer = {
		answer: '',
		feedback: '',
		correct: false,
	},
	feedbackPerAnswer = true,
} ) {
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
	const onChangeSingleCorrect = index => {
		// Make answers[index] the only correct answer in the answers array.
		const newAnswers = answers.map( ( answer, i ) => {
			const isCorrect = index === i;
			return {
				...answer,
				correct: isCorrect,
			};
		} );
		onChangeAnswers( newAnswers );
	};
	const onChangeMultipleCorrect = index => {
		// Toggle the 'correct' property for answers[index].
		const newAnswers = answers.map( ( answer, i ) => {
			if ( index === i ) {
				return {
					...answer,
					correct: ! answer.correct,
				};
			}
			return answer;
		} );

		// Make sure that at least one answer is selected as the correct answer before updating state.
		const numCorrectAnswers = newAnswers.reduce(
			( accumulator, answer ) => accumulator + answer.correct,
			0
		);
		if ( numCorrectAnswers > 0 ) {
			onChangeAnswers( newAnswers );
		}
	};

	const onAddAnswer = () => {
		if ( answers.length < maxAnswers ) {
			const newAnswers = [ ...answers, defaultAnswer ];
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
			<Answer
				key={ index }
				index={ index }
				{ ...answer }
				feedbackPerAnswer={ feedbackPerAnswer }
				onChangeAnswerValue={ onChangeAnswerValue }
				onChangeFeedback={ onChangeFeedback }
				onChangeCorrect={
					multipleCorrectAllowed ?
						onChangeMultipleCorrect :
						onChangeSingleCorrect
				}
				onRemoveAnswer={ answers.length > minAnswers ? onRemoveAnswer : null }
				multipleCorrectAllowed={ multipleCorrectAllowed }
			/>
		) );
		return answerList;
	};

	const renderAddAnswer = () => {
		if ( answers.length < maxAnswers ) {
			return <button onClick={ onAddAnswer }>Add Answer</button>;
		}
	};

	return (
		<div>
			<h5 className="bulb-editor-section-label">Answers</h5>
			{ renderAnswers() }
			{ renderAddAnswer() }
		</div>
	);
}
