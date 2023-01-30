import EnhancedRichText from './EnhancedRichText';

const { __ } = wp.i18n;

export default props => (
	<div>
		<h5 className="bulb-editor-field-label" >{ __( 'Question Body', 'bu-learning-blocks' ) }</h5>
		<EnhancedRichText
			className="question-body"
			placeholder={ __( 'Enter Question Body', 'bu-learning-blocks' ) }
			{ ...props }
		/>
	</div>
);
