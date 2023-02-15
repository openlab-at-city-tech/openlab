export default ( {
	answer,
	onChangeAnswer,
} ) => (
	<div>
		<h5>Answer:</h5>
		<input type="text" size="50" value={ answer } onChange={ event => onChangeAnswer( event.target.value ) } />
	</div>
);
