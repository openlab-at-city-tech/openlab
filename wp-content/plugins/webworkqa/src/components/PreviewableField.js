import React, { Component } from 'react';
import FormattedProblem from './FormattedProblem'
import UploaderContainer from '../containers/UploaderContainer'
import { __ } from '@wordpress/i18n';

export default class PreviewableField extends Component {
	componentDidUpdate() {
		if ( window.hasOwnProperty( 'MathJax' ) && window.MathJax.hasOwnProperty( 'Hub' ) ) {
			const cssId = this.props.id + '-preview'
			window.MathJax.Hub.Queue(["Update", window.MathJax.Hub, cssId]);
		}
	}

	render() {
		const {
			attachments, id, fieldId, fieldName, label, labelIsVisible,
			value, incompleteText, isIncomplete, isPending, isPreviewVisible,
			onPreviewToggleClick, onTextareaChange
		} = this.props

		let contentSectionClass = 'ww-question-form-section'
		if ( isPreviewVisible ) {
			contentSectionClass += ' preview-visible'
		}

		const previewId = id + '-preview'

		let display, openDelim, closeDelim, newVal
		const delimRegExp = /\\begin\{((?:display)?math)\}([^]*?)\\end\{\1\}/gm
		let previewContent = value.replace( delimRegExp, function( a, type, math ) {

			if ( 'displaymath' == type ) {
				display = 'block'
				openDelim = '{{{LATEX_DELIM_DISPLAY_OPEN}}}'
				closeDelim = '{{{LATEX_DELIM_DISPLAY_CLOSE}}}'
			} else {
				display = 'inline'
				openDelim = '{{{LATEX_DELIM_INLINE_OPEN}}}'
				closeDelim = '{{{LATEX_DELIM_INLINE_CLOSE}}}'
			}

			return openDelim + math + closeDelim
		} )

		const shortDelimRegExp = /\$latex([^\$]+)\$/gm
		previewContent = previewContent.replace( shortDelimRegExp, function( a, math ) {
			if ( math.match( /\n/ ) ) {
				openDelim = '{{{LATEX_DELIM_DISPLAY_OPEN}}}'
				closeDelim = '{{{LATEX_DELIM_DISPLAY_CLOSE}}}'
			} else {
				openDelim = '{{{LATEX_DELIM_INLINE_OPEN}}}'
				closeDelim = '{{{LATEX_DELIM_INLINE_CLOSE}}}'
			}

			return openDelim + math + closeDelim
		} )

		if ( isIncomplete ) {
			previewContent = incompleteText
		}

		let uploadElements = []
		if ( ! isPreviewVisible && ! isIncomplete ) {
			uploadElements.push(
				<UploaderContainer
					fieldName={fieldName}
					formId={fieldId}
					key='upload-button'
				/>
			)
		}

		const labelId = id + '-label'
		const pfcLabel = '\u00a0'

		let buttonContent
		if ( ! isIncomplete ) {
			if ( isPreviewVisible ) {
				buttonContent = (
					<span
						>{ __( 'Edit', 'webworkqa' ) } <i className="fa fa-pencil"></i></span>
				)
			} else {
				buttonContent = (
					<span
						>{ __( 'Preview', 'webworkqa' ) } <i className="fa fa-eye"></i></span>
				)
			}
		}

		const labelClass = labelIsVisible ? '' : 'screen-reader-text'

		return (
			<div className={contentSectionClass}>
				<label
					htmlFor={id}
					id={labelId}
				>{pfcLabel}<span className={labelClass}>{label}</span></label>

				<div className="edit-action-buttons">
					{uploadElements}

					<button
						className="preview-toggle"
						onClick={e => {
							onPreviewToggleClick()
						}}
						type="button"
					>
						{buttonContent}
					</button>
				</div>

				<textarea
					aria-labelledby={labelId}
				  id={id}
				  name={id}
				  value={value}
				  disabled={isPending}
				  onChange={ e => {
						onTextareaChange( e )
				  } }
				/>

				<div
				  className="ww-preview"
				  id={previewId}
				>
					<FormattedProblem
						attachments={attachments}
					  itemId={id}
					  isVisible={isPreviewVisible}
					  content={previewContent}
					/>
				</div>
			</div>
		)
	}
}
