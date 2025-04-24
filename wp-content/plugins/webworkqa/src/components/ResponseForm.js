import React, { Component } from 'react'
import PreviewableFieldContainer from '../containers/PreviewableFieldContainer'
import { __ } from '@wordpress/i18n';

export default class ResponseForm extends Component {
	render() {
		const {
			incompleteText, isCollapsed, isIncomplete, isPending, questionId, responseText,
			onAccordionClick, onIncompleteToggleChange, onResponseFormSubmit,
			showIncompleteToggle
		} = this.props

		const textareaName = 'response-text-' + questionId
		const submitName = 'response-submit-' + questionId

		let formClassName = 'response-form hide-when-closed'
		if ( isPending ) {
			formClassName += ' form-pending'
		}

		let accordionToggleClass = 'fa accordion-toggle'
		if ( isCollapsed ) {
			accordionToggleClass += ' fa-arrow-circle-o-down'
		} else {
			accordionToggleClass += ' fa-arrow-circle-up'
		}

		let divClassName = 'ww-question-response-form'
		if ( isCollapsed ) {
			divClassName += ' form-collapsed'
		}

		// Non-breaking space.
		const pfcLabel = __( 'Response text', 'webworkqa' )

		let incompleteToggle = ''
		if ( showIncompleteToggle ) {
			const incompleteToggleId = 'incomplete-toggle-checkbox-' + questionId
			incompleteToggle = (
				<div className="incomplete-toggle">
					<input
						checked={isIncomplete}
						id={incompleteToggleId}
						onChange={ (e) => onIncompleteToggleChange( ! isIncomplete ) }
						type="checkbox"
						value="1"
					/>

					<label
						htmlFor={incompleteToggleId}
					>{ __( 'Incomplete question? The student will be notified and the default reply posted.' ) }</label>
				</div>
			)
		}

		const textToSubmit = isIncomplete ? incompleteText : responseText

		return (
			<div className={divClassName}>
				<h3 className="ww-header">
					{ __( 'Respond to this question', 'webworkqa' ) }
				</h3>

				<div className="response-block">
					<form
					  className={formClassName}
					  onSubmit={ ( e ) => onResponseFormSubmit( e, textToSubmit ) }
					>
						<PreviewableFieldContainer
						  fieldId={'response-' + questionId}
						  fieldName='content'
						  id={textareaName}
						  label={pfcLabel}
						/>

						{incompleteToggle}

						<input
						  className="button"
						  disabled={isPending}
						  id={submitName}
						  name={submitName}
						  type="submit"
						  value={isPending ? __( 'Submitting...', 'webworkqa' ) : __( 'Submit', 'webworkqa' ) }
						/>
					</form>
				</div>
			</div>
		)
	}
}
