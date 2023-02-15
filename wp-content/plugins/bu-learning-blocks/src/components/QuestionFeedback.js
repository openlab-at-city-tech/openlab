import EnhancedRichText from './EnhancedRichText';

const { __ } = wp.i18n;

export default props => {
	const onChangeFeedback = changedFeedback => {
		const newFeedback = { ...props.feedback, ...changedFeedback };
		props.onChangeFeedback( newFeedback );
	};

	if ( props.singleFeedback ) {
		return (
			<div>
				<h5>{ __( 'Feedback:', 'bu-learning-blocks' ) }</h5>
				<EnhancedRichText
					className="question-feedback"
					placeholder={ __( 'Enter Feedback', 'bu-learning-blocks' ) }
					value={ props.feedback.correct }
					onChange={ newValue =>
						onChangeFeedback( {
							correct: newValue,
							incorrect: newValue,
						} )
					}
					{ ...props }
				/>
			</div>
		);
	}
	return (
		<div>
			<h4 className="bulb-editor-section-label">{ __( 'Feedback', 'bu-learning-blocks' ) }</h4>
			<h5 className="bulb-editor-field-label"> { __( 'Correct Feedback', 'bu-learning-blocks' ) }</h5>
			<EnhancedRichText
				className="question-feedback"
				placeholder={ __( 'Enter Correct Feedback', 'bu-learning-blocks' ) }
				value={ props.feedback.correct }
				onChange={ newValue => onChangeFeedback( { correct: newValue } ) }
				{ ...props }
			/>
			<h5 className="bulb-editor-field-label">{ __( 'Incorrect Feedback', 'bu-learning-blocks' ) }</h5>
			<EnhancedRichText
				className="question-feedback"
				placeholder={ __( 'Enter Incorrect Feedback', 'bu-learning-blocks' ) }
				value={ props.feedback.incorrect }
				onChange={ newValue => onChangeFeedback( { incorrect: newValue } ) }
				{ ...props }
			/>
		</div>
	);
};
