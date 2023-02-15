import FloatInput from '../../components/FloatInput';
const { Fragment } = wp.element;

export default ( {
	answer,
	answerRange,
	decimalPlaces,
	onChangeAnswer,
	onChangeAnswerRange,
	onChangeDecimalPlaces,
} ) => {
	const renderPossibleAnswers = () => {
		const answerFloat = parseFloat( answer );
		const answerRangeFloat = parseFloat( answerRange );

		const min = ( answerFloat - answerRangeFloat ).toFixed( decimalPlaces );
		const max = ( answerFloat + answerRangeFloat ).toFixed( decimalPlaces );

		const interval = 1 / Math.pow( 10, decimalPlaces );

		const nearestLess = answerFloat - interval;
		const nearestGreater = answerFloat + interval;

		const nearestAnswers = [ nearestLess, answerFloat, nearestGreater ].map( val => parseFloat( val ).toFixed( decimalPlaces ) );

		const nearestAnswersItems = nearestAnswers.map( ( possibleAnswer, index ) => (
			<li key={ index }>{ possibleAnswer }</li>
		) );

		return (
			<div>
				<h5>Examples of the nearest acceptable answers:</h5>
				{ nearestAnswersItems.length ? (
					<Fragment>
						<ul className="possible-answers-list">{ nearestAnswersItems }</ul>
						<div> Minimum: { min }, Maximum: { max }</div>
					</Fragment>
				) : (
					'No possible answers found'
				) }
			</div>
		);
	};

	return (
		<div>
			<h5>Answer:</h5>
			<FloatInput value={ answer } onChange={ onChangeAnswer } />
			<h5>Accepted Range:</h5>
			<FloatInput value={ answerRange } onChange={ onChangeAnswerRange } />
			<h5>Decimal Places:</h5>
			<input
				type="number"
				step="1"
				min="0"
				max="100"
				value={ decimalPlaces }
				onChange={ event => onChangeDecimalPlaces( event.target.value ) }
			/>
			{ ( answerRange !== '0' ) && renderPossibleAnswers() }
		</div>
	);
};
