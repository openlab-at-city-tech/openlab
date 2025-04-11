import React, { Component } from 'react'
import Scroll, { Element } from 'react-scroll'
import Waypoint from 'react-waypoint'
import ReactTooltip from 'react-tooltip'
import { __, _n, sprintf } from '@wordpress/i18n';

import ScoreDialogContainer from '../containers/ScoreDialogContainer'
import SubscriptionDialogContainer from '../containers/SubscriptionDialogContainer'
import ResponseList from './ResponseList'
import ResponseFormContainer from '../containers/ResponseFormContainer'
import PreviewableFieldContainer from '../containers/PreviewableFieldContainer'
import FormattedProblem from './FormattedProblem'
import EditSaveButtonContainer from '../containers/EditSaveButtonContainer'

var moment = require( 'moment' )

export default class Question extends Component {
	componentDidMount() {
		const {
			isCurrentQuestion, isSingleProblem,
			itemId, initialLoadComplete,
			userCanPostResponse
		} = this.props

		if ( ! isSingleProblem ) {
			return
		}

		if ( userCanPostResponse ) {
			setTimeout( function() {
				if ( initialLoadComplete ) {
					const responseScrollElementName = 'question-' + itemId
					Scroll.scroller.scrollTo( responseScrollElementName, {
						duration: 1000,
						offset: -80, // for toolbar
						smooth: true
					} )
				}
			}, 500 );
		}

		if ( isCurrentQuestion ) {
			// Delay to allow LaTeX to render
			setTimeout( function() {
				Scroll.scroller.scrollTo( 'question-' + itemId, {
					duration: 1500,
					smooth: true
				} )
			}, 500 )
		}
	}

	render() {
		const {
			attachments, feedback, isCurrentQuestion,
			isCollapsed, isEditing, isPending, isProblemSummaryCollapsed, isSingleProblem,
			itemId, question, questionLink,
			responseIds, responses, userCanEdit, userCanPostResponse,
			onAccordionClick, onDeleteClick, onEditClick, onEditSaveClick,
			onProblemSummaryClick, onRespondClick, onWaypointEnter,
			responseIsPending, userCanSubscribe
		} = this.props

		const {
			tried, content, questionId, authorAvatar, authorName,
			problemText, isAnonymous
		} = question

		const isMyQuestion = question.isMyQuestion > 0

		let hasProblemText = false
		if ( problemText && problemText.length > 0 ) {
			hasProblemText = true
		}

		const responseScrollElementName = 'response-form-' + itemId
		const Element = Scroll.Element

		const anchorName = 'question-' + itemId

		let aeClass = 'fa accordion-toggle'
		if ( isCollapsed ) {
			aeClass += ' fa-angle-down'
		} else {
			aeClass += ' fa-angle-up'
		}

		const accordionElement = (
			<i
			  aria-hidden="true"
			  className={aeClass}
			></i>
		)

		let questionTitleText
		if ( isAnonymous ) {
			const anonTitle = __( 'Question from a Student', 'webworkqa' )
			if ( authorName ) {
				questionTitleText = (
					<span className="anonymous-tooltip">
						<span
							data-tip={authorName}
							data-type="info"
							data-class="login-tooltip"
							>{anonTitle}</span>
						<ReactTooltip />
					</span>
				)
			} else {
				questionTitleText = anonTitle
			}
		} else {
			/* translators: Name of question poster */
			questionTitleText = sprintf( __( 'A Question from %s', 'webworkqa' ), authorName )
		}

		let questionTitleElement
		if ( isSingleProblem ) {
			questionTitleElement = (
				<div className="ww-author-name">{questionTitleText}</div>
			)

		} else {
			questionTitleElement = (
				<a
					className="ww-question-link"
					href={questionLink}
				>
					<div className="ww-author-name">{questionTitleText}</div>
				</a>
			)
		}

		const timestamp = moment( question.postDate ).format( 'MMMM D, YYYY' )

		let feedbackElements = []
		let thisFeedback
		if ( feedback ) {
			for ( var feedbackType in feedback ) {
				let chunkKey = 0
				thisFeedback = feedback[feedbackType].split( "\n" ).map( function(item) {
					chunkKey++
					return (
						<span key={'chunk-' + chunkKey}>
							{item}
							<br/>
						</span>
					)
				} )

				feedbackElements.push(
					<div
						key={feedbackType}
						className={'item-feedback item-feedback-' + feedbackType}
					>
						{thisFeedback}
					</div>
				)
			}
		}

		const responseCount = question.responseCount

		/* translators: Response count */
		const responseCountText = sprintf( _n( '%s Reply', '%s Replies', responseCount, 'webworkqa' ), responseCount )

		let responseCountElements = []
		if ( ! isSingleProblem || isCollapsed ) {
			responseCountElements.push( <span key="response-sep" className="ww-subtitle-sep">|</span> )
			responseCountElements.push(
				<span key="response-value" className="ww-subtitle-section">
					{responseCountText}
				</span>
			)
		}

		const editLinkOnclick = function( e ) {
			e.preventDefault()
			onEditClick()
		}

		const deleteLinkOnclick = function( e ) {
			e.preventDefault()
			if ( window.confirm( __( 'Are you sure you want to delete this question and all its responses?', 'webworkqa' ) ) ) {
				onDeleteClick()
			}
		}

		let editLinkElements = []
		if ( userCanEdit ) {
			editLinkElements.push(
				<span key="editing-sep" className="ww-subtitle-sep">|</span>
			)

			if ( isEditing ) {
				editLinkElements.push(
					<a
						href="#"
						onClick={editLinkOnclick}
						key="edit-link-editing"
						className="ww-edit-link ww-edit-link-editing"
					>
						{ __( 'Editing', 'webworkqa' ) }
						<i className="fa fa-pencil" aria-hidden="true"></i>
					</a>
				)
			} else {
				editLinkElements.push(
					<a
						href="#"
						onClick={editLinkOnclick}
						key="edit-link-edit"
						className="ww-edit-link ww-edit-link-edit"
					>
						{ __( 'Edit', 'webworkqa' ) }
						<i className="fa fa-pencil" aria-hidden="true"></i>
					</a>
				)
			}
		}

		const questionSubtitleElement = (
			<div className="ww-subtitle ww-question-subtitle">
				<span className="ww-subtitle-section">
					<a href={questionLink}>
						{/* translators: Post timestamp */}
						{ sprintf( __( 'Posted %s', 'webworkqa' ), timestamp ) }
					</a>
				</span>
				{responseCountElements}
				{editLinkElements}
			</div>
		)

		let questionCourseElement
		if ( ! isSingleProblem ) {
			let courseData = []
			if ( question.problemSet ) {
				courseData.push( question.problemSet );
			}

			if ( question.course ) {
				courseData.push( question.course );
			}

			const courseDataString = courseData.join( ' | ' )

			questionCourseElement = (
				<div className="question-course-data hide-when-closed">
					{courseDataString}
				</div>
			)
		}

		const questionScoreElement = (
			<ScoreDialogContainer
			  itemId={itemId}
			  itemType='question'
			/>
		)

		let respondLinkElement
		if ( isSingleProblem && userCanPostResponse ) {
			respondLinkElement = (
				<div className="respond-link hide-when-closed">
					<a
					  href="#"
					  onClick={ e => {
						  e.preventDefault()
						  this.onGoToResponseFormClick( itemId )
						  onRespondClick()
					  } }
					>
						{ __( 'Reply', 'webworkqa' ) }
					</a>
				</div>
			)
		} else {
			let respondLinkHref = questionLink
			if ( ! userCanPostResponse ) {
				respondLinkHref = window.WWData.loginURL + '?redirect_to=' + respondLinkHref
			}

			respondLinkElement = (
				<div className="respond-link hide-when-closed">
					<a
					  href={respondLinkHref}
					>
						{ __( 'Respond to this question', 'webworkqa' ) }
					</a>
				</div>
			)
		}


		const questionMetadataElement = (
			<div className="item-metadata">
				{questionCourseElement}
				{respondLinkElement}
				{questionScoreElement}
			</div>
		)

		let problemSummaryClass = 'ww-question-content-section ww-question-problem-summary hide-when-closed'
		if ( isProblemSummaryCollapsed ) {
			problemSummaryClass += ' problem-summary-collapsed'
		}

		const contentId = 'content-' + itemId
		const formattedContent =
			<FormattedProblem
			  attachments={attachments}
			  itemId={contentId}
			  content={content}
			/>

		const triedId = 'tried-' + itemId
		const formattedTried = (
			<FormattedProblem
			  attachments={attachments}
			  itemId={triedId}
			  content={tried}
			/>
		)

		let accordionToggleClass = 'fa accordion-toggle'
		if ( isProblemSummaryCollapsed ) {
			accordionToggleClass += ' fa-plus-circle'
		} else {
			accordionToggleClass += ' fa-minus-circle'
		}

		let contentElementsChildren = []
		if ( isEditing ) {
			contentElementsChildren.push(
				<div key="content-editable-children-1" className="editable-field">
					<PreviewableFieldContainer
					  fieldId={'question-' + itemId}
					  fieldName='content'
					  id="ww-question-content"
					  label={ __( 'My question:', 'webworkqa' ) }
					/>
				</div>
			)

			contentElementsChildren.push(
				<div key="content-editable-children-2" className="editable-field">
					<PreviewableFieldContainer
					  fieldId={'question-' + itemId}
					  fieldName='tried'
					  id="ww-question-content"
					  label={ __( 'What I\'ve tried:', 'webworkqa' ) }
					/>
				</div>
			)

			contentElementsChildren.push(
				<EditSaveButtonContainer
					fieldId={itemId}
					fieldType='question'
					key="content-elements-children-3"
				/>
			)

			contentElementsChildren.push(
				<div className='edit-button-links' key='links'>
					<a href='#' onClick={editLinkOnclick}>{ __( 'Cancel', 'webworkqa' ) }</a>
					<span key="editing-sep" className="ww-subtitle-sep">|</span>
					<a href='#' className="delete-link hover-notice-parent" onClick={deleteLinkOnclick}>
						{ __( 'Delete', 'webworkqa' ) }
						<div aria-hidden="true" className="hover-notice delete-notice">
							{ __( 'Delete question and all responses.', 'webworkqa' ) }
						</div>
					</a>
				</div>
			)
		} else {
			let triedElements
			if ( isSingleProblem ) {
				triedElements = (
					<span key="content-elements-children-3">
						<div className="ww-question-content-section-header">{ __( 'What I\'ve tried:', 'webworkqa' ) }</div>
						<div className="ww-question-content-section ww-question-content-text">
							{formattedTried}
						</div>
					</span>
				)
			}

			contentElementsChildren.push( <div key="content-elements-children-1" className="ww-question-content-section-header">{ __( 'My question:', 'webworkqa' ) }</div> )
			contentElementsChildren.push( <div key="content-elements-children-2" className="ww-question-content-section ww-question-content-text">{formattedContent}</div> )
			contentElementsChildren.push( triedElements )
		}

		const contentElements = (
			<div key="contentElements" className="hide-when-closed">
				{contentElementsChildren}
			</div>
		)

		const problemElement = (
			<div
			  className={problemSummaryClass}
			  key="problemElement"
			  onClick={onProblemSummaryClick}
			>
				<div
				  className="ww-my-problem"
				>
					<span
					  className="ww-my-problem-text"
					>
						{isProblemSummaryCollapsed ? __( 'Show WeBWorK Problem', 'webworkqa' ) : __( 'Hide WeBWorK Problem', 'webworkqa' )}
					</span>
				</div>

				<div
				  className="ww-my-problem-content"
				>
					<FormattedProblem
					  itemId={questionId}
					  content={problemText}
					/>
				</div>
			</div>
		)

		let orderedElements = [ problemElement, contentElements ]

		const questionSummaryElement = (
			<div className="ww-question-content-wrapper">
				<div className="ww-question-content">
					{questionTitleElement}
					{questionSubtitleElement}

					{orderedElements}
				</div>

				{questionMetadataElement}
			</div>
		)

		let responsesElement
		if ( isSingleProblem ) {
			responsesElement = (
				<div className={isCollapsed ? 'accordion-content accordion-closed' : 'accordion-content accordion-open'}>
					<ResponseList
					  isMyQuestion={isMyQuestion}
						isPending={responseIsPending}
					  questionId={itemId}
					  responseIds={responseIds}
					  responses={responses}
					/>
				</div>
			)
		}

		let scrollWaypoint
		if ( ! isSingleProblem ) {
			scrollWaypoint = (
				<Waypoint
					onEnter={onWaypointEnter}
				/>
			)
		}

		let subscriptionElement
		if ( userCanSubscribe && isSingleProblem && ! isCollapsed ) {
			subscriptionElement = (
				<SubscriptionDialogContainer
					itemId={itemId}
				/>
			)
		}

		/* translators: Name of author to whom avatar belongs */
		const avatarAltText = isAnonymous ? __( 'Avatar', 'webworkqa' ) : sprintf( __( 'Avatar of %s', 'webworkqa' ), authorName )

		const showAnonymousClass = isAnonymous && authorName

		return (
			<li
			  className={this.getClassName( isCollapsed, isMyQuestion, isCurrentQuestion, responseCount, showAnonymousClass )}
			>

				{scrollWaypoint}

				<Element name={anchorName}>
					<a name={anchorName}></a>
				</Element>

				<div className="ww-question">
					<div className="ww-question-header">
						<a
						  href="#"
						  onClick={ e => {
							  e.preventDefault()
							  onAccordionClick( itemId )
						  } }
						>
							{accordionElement}
							<span className="ww-question-header-text">
								{responseCountText}
							</span>
						</a>

					</div>

					{feedbackElements}

					<div className="ww-question-wrapper">
						<div className="ww-author-avatar">
							<img src={authorAvatar} alt={avatarAltText} />
						</div>

						<div>
							{questionSummaryElement}
						</div>
					</div>
				</div>

				{responsesElement}

				{subscriptionElement}
			</li>
		);
	}

	/**
	 * Get a class name for the <li> element.
	 */
	getClassName( isCollapsed, isMyQuestion, isCurrentQuestion, responseCount, isAnonymous ) {
		let classes = []

		if ( isCollapsed ) {
			classes.push( 'question-closed' )
		} else {
			classes.push( 'question-open' )
		}

		if ( isMyQuestion ) {
			classes.push( 'my-question' )
		}

		if ( responseCount > 0 ) {
			classes.push( 'has-responses' )
		}

		if ( isCurrentQuestion ) {
			classes.push( 'current-question' )
		}

		if ( isAnonymous ) {
			classes.push( 'anonymous-question' )
		}

		return classes.join( ' ' )
	}

	/**
	 * Scrolling callback for clicking the "Respond" link.
	 *
	 * Not currently aware of state, but maybe it should be - ie to expand the Response form
	 * or flash the form after scroll. At that point, callback should be moved to the
	 * container with associated action/reducer.
	 */
	onGoToResponseFormClick( itemId ) {
		Scroll.scroller.scrollTo( 'response-form-' + itemId, {
			duration: 1000,
			offset: -80, // for toolbar
			smooth: true
		} )
	}
}
