/**
 * Block dependencies
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { InspectorControls } = wp.editor;
const { PanelBody, PanelRow, FormToggle } = wp.components;

import Question from '../../components/Question';
import Controls from '../../components/Controls';
import FillInTheBlankAnswer from './FillInTheBlankAnswer';

// Register the block
export default registerBlockType( 'bulb/question-fitb', {
	title: __( 'BULB - Fill in the Blank', 'bu-learning-blocks' ),
	description: __( 'Add a Fill in the Blank question to your learning module.' ),
	icon: 'welcome-learn-more',
	category: 'bu-learning-blocks',
	keywords: [
		__( 'bu-learning-block', 'bu-learning-blocks' ),
		__( 'BULB', 'bu-learning-blocks' ),
		__( 'Fill in the Blank Question', 'bu-learning-blocks' ),
	],

	edit: props => {
		const {
			attributes: {
				id,
				type,
				header,
				body,
				answer,
				feedback,
				caseSensitive,
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

		const toggleCaseSensitivity = event => setAttributes( { caseSensitive: !! event.target.checked } );

		return (
			<div className="bulb-question-fitb">
				<InspectorControls>
					<PanelBody>
						<PanelRow>
							<label
								htmlFor="bulb-fitb-case-sensitivity"
							>
								{ __( 'Case sensitivity', 'bu-learning-blocks' ) }
							</label>
							<FormToggle
								id="bulb-fitb-case-sensitivity"
								label={ __( 'Case sensitivity', 'bu-learning-blocks' ) }
								checked={ caseSensitive }
								onChange={ toggleCaseSensitivity }
							/>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
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
						<FillInTheBlankAnswer
							answer={ answer }
							onChangeAnswer={ onSimpleAttributeChange( 'answer' ) }
						/>
					</Question>
				</Fragment>
			</div>
		);
	},
	save: () => null,
} );
