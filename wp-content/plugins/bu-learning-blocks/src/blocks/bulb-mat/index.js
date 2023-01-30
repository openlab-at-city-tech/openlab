/**
 * Block dependencies
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;

import Question from '../../components/Question';
import MatchingAnswers from './MatchingAnswers';
import Controls from '../../components/Controls';

// Register the block.
export default registerBlockType( 'bulb/question-mat', {
	title: __( 'BULB - Matching', 'bu-learning-blocks' ),
	description: __( 'Add a Matching question.' ),
	icon: 'welcome-learn-more',
	category: 'bu-learning-blocks',
	keywords: [
		__( 'bu-learning-block', 'bu-learning-blocks' ),
		__( 'BULB', 'bu-learning-blocks' ),
		__( 'Matching Question', 'bu-learning-blocks' ),
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
				id: `bulb_question_${ clientId.replace( /-/g, '' ) }`,
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
					<MatchingAnswers
						answers={ answers }
						onChangeAnswers={ onSimpleAttributeChange( 'answers' ) }
						minAnswers={ 2 }
						maxAnswers={ 12 }
					/>
				</Question>
			</Fragment>
		);
	},
	save: () => null,
} );
