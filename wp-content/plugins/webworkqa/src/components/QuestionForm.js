import React, { Component } from 'react';
import { __ } from '@wordpress/i18n';
import PreviewableFieldContainer from '../containers/PreviewableFieldContainer'
import AnonymousToggleContainer from '../containers/AnonymousToggleContainer'

export default class QuestionForm extends Component {
	render() {
		const {
			content, isCollapsed, isPending,
			problemId, problemText, problemHasQuestions, tried, triedIsEmpty,
			onAccordionClick, onTextareaChange, onQuestionFormSubmit
		} = this.props

		let formClassName = 'question-form'
		if ( isPending ) {
			formClassName += ' form-pending'
		}

		let divClassName = 'ww-ask-question-form'
		if ( isCollapsed ) {
			divClassName += ' form-collapsed'
		}

		let accordionToggleClass = 'fa accordion-toggle'
		if ( isCollapsed ) {
			accordionToggleClass += ' fa-arrow-circle-o-down'
		} else {
			accordionToggleClass += ' fa-arrow-circle-up'
		}

		let questionGloss = ''
		if ( problemHasQuestions ) {
			questionGloss = (
				<p className="ww-question-gloss">
					{ __( 'Please review the questions below to see if your question has already been answered.', 'webworkqa' ) }
				</p>
			)
		}

		const isPreviewContent = true
		let contentSectionClass = 'ww-question-form-section'
		if ( isPreviewContent ) {
			contentSectionClass += ' preview'
		}

		let triedIsEmptyNotice
		if ( triedIsEmpty ) {
			triedIsEmptyNotice = (
				<div
				  className="tried-is-empty"
				>{ __( 'You haven\'t described what you\'ve tried. Did you know you can upload an image of your work?', 'webworkqa' ) }</div>
			)
		}

		let buttonClassName = 'button'
		if ( triedIsEmpty ) {
			buttonClassName += ' button-error'
		}

		return (
			<div className={divClassName}>
				<form
				  className={formClassName}
				  onSubmit={ e => {
					  onQuestionFormSubmit( e, content, tried, problemText, triedIsEmpty )
				  } }
				>
					<a
					  className="ww-collapsible-section-link"
					  href="#"
					  onClick={ e => {
						  e.preventDefault()
						  onAccordionClick()
					  } }
					>
						<h3 className="ww-header ww-collapsible-section-header">{ __( 'Ask a Question', 'webworkqa' ) }</h3>

						<i
						  aria-hidden="true"
						  className={accordionToggleClass}
						></i>
					</a>

					<div className="ww-collapsible-block">
						{questionGloss}

						<input type="hidden" name="ww-problem-id" value={problemId} />

						<PreviewableFieldContainer
						  fieldId='question-form'
						  fieldName="content"
						  id="ww-question-form-content"
						  label={ __( 'What is your question?', 'webworkqa' ) }
							labelIsVisible={true}
						/>

						<PreviewableFieldContainer
						  fieldId='question-form'
						  fieldName="tried"
						  id="ww-question-form-tried"
						  label={ __( 'Describe what you have tried.', 'webworkqa' ) }
							labelIsVisible={true}
						/>

						{triedIsEmptyNotice}

						<div className="submit-container">
							<div className="submit-div">
								<input
									className={buttonClassName}
									disabled={isPending}
									type="submit"
									value={isPending ? __( 'Submitting...', 'webworkqa' ) : __( 'Submit', 'webworkqa' )}
								/>
							</div>

							<AnonymousToggleContainer />
						</div>
					</div>
				</form>
			</div>
		);
	}
}
