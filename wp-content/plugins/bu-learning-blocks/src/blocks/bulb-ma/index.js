/**
 * Block dependencies
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;

import Question from '../../components/Question';
import Answers from '../../components/Answers';
import Controls from '../../components/Controls';

// Register the block
export default registerBlockType( 'bulb/question-ma', {
	title: __( 'BULB - Multiple Answer', 'bu-learning-blocks' ),
	description: __( 'Add a Multiple Answer question to your learning module.' ),
	icon: 'welcome-learn-more',
	category: 'bu-learning-blocks',
	keywords: [
		__( 'bu-learning-block', 'bu-learning-blocks' ),
		__( 'BULB', 'bu-learning-blocks' ),
		__( 'Multiple Answer Question', 'bu-learning-blocks' ),
	],

	edit: props => {
		const {
			attributes: {
				id,
				type,
				header,
				body,
				answers,
				feedback,
				fontSize,
				textAlignment,
				textColorControl,
				backgroundColorControl,
			},
			setAttributes,
			clientId,
		} = props;

		if ( ! id ) {
			setAttributes( {
				id: 'bulb_question_' + clientId.replace( /-/g, '' ),
			} );
		}

		const onSimpleAttributeChange = attribute => value => {
			setAttributes( {
				[ attribute ]: value,
			} );
		};

		return (
			<Fragment>
				<Controls { ...props } />
				<Question
					{ ...{
						classes: [ `bulb-question-${ type }` ],
						header,
						onChangeHeader: onSimpleAttributeChange( 'header' ),
						body,
						onChangeBody: onSimpleAttributeChange( 'body' ),
						feedback,
						onChangeFeedback: onSimpleAttributeChange( 'feedback' ),
						textAlignment,
						textColorControl,
						backgroundColorControl,
						fontSize,
					} }
				>
					<Answers
						answers={ answers }
						onChangeAnswers={ onSimpleAttributeChange( 'answers' ) }
						multipleCorrectAllowed={ true }
						minAnswers={ 2 }
						maxAnswers={ 6 }
					/>
				</Question>
			</Fragment>
		);
	},
	save: () => null,
} );
