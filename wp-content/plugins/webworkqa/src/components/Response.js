import React, { Component } from 'react';
import Scroll from 'react-scroll'
import ReactTooltip from 'react-tooltip'
import { __, sprintf } from '@wordpress/i18n';

import EditSaveButtonContainer from '../containers/EditSaveButtonContainer'
import PreviewableFieldContainer from '../containers/PreviewableFieldContainer'
import ScoreDialogContainer from '../containers/ScoreDialogContainer'
import FormattedProblem from './FormattedProblem'

var moment = require( 'moment' )

export default class Response extends Component {
	render() {
		const {
			attachments, isEditing, isMyQuestion,
			questionId, questionIsAnonymous, response, responseId,
			userCanEdit, userCanPostResponse,
			onDeleteClick, onEditClick
		} = this.props

		if ( ! response ) {
			return null
		}

		const {
			authorAvatar, authorName, authorUserType, obfuscateAuthor,
			content, isAnswer
		} = response

		const userIsAdmin = window.WWData.user_is_admin

		const timestamp = moment( response.postDate ).format( window.WWData.momentFormat )

		const editLinkOnclick = function( e ) {
			e.preventDefault()
			onEditClick()
		}

		const deleteLinkOnclick = function( e ) {
			e.preventDefault()
			onDeleteClick()
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

		let respondLinkElement
		if ( userCanPostResponse ) {
			respondLinkElement = (
				<div className="respond-link">
					<a
					  href="#"
					  onClick={ e => {
						  e.preventDefault()
						  this.onGoToResponseFormClick( response.questionId )
					  } }
					>
						Reply
					</a>
				</div>
			)
		}

		const contentId = 'response-' + responseId

		let contentElements = []
		if ( isEditing ) {
			contentElements.push(
				<div key="content" className="editable-field">
					<PreviewableFieldContainer
						fieldId={'response-' + responseId}
						fieldName='content'
						key='content'
						label=''
					/>
				</div>
			)

			contentElements.push(
				<EditSaveButtonContainer
					fieldId={responseId}
					fieldType='response'
					key="button"
				/>
			)

			contentElements.push(
				<div className='edit-button-links' key='links'>
					<a href='#' onClick={editLinkOnclick}>{ __( 'Cancel', 'webworkqa' ) }</a>
					<span key="editing-sep" className="ww-subtitle-sep">|</span>
					<a href='#' onClick={deleteLinkOnclick}>{ __( 'Delete', 'webworkqa' ) }</a>
				</div>
			)
		} else {
			contentElements.push(
				<FormattedProblem
					attachments={attachments}
				  itemId={contentId}
				  content={content}
				  key='content'
				/>
			)
		}

		let liClassName = 'ww-response'
		if ( isEditing ) {
			liClassName += ' is-editing'
		}

		/* translators: Name of author to whom avatar belongs */
		const avatarAltText = authorName ? sprintf( __( 'Avatar of %s', 'webworkqa' ), authorName ) : __( 'Avatar' )

		const authorUserTypeEl = authorUserType ? <div className="response-user-type">{authorUserType}</div> : <span></span>

		let authorNameText
		if ( obfuscateAuthor ) {
			const anonTitle = __( 'Question Author', 'webworkqa' )
			if ( authorName ) {
				authorNameText = (
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
				authorNameText = anonTitle
			}

		} else {
			authorNameText = authorName
		}

		return (
			<li className={liClassName}>
				<div className="ww-author-avatar">
					<img src={authorAvatar} alt={avatarAltText} />
					{authorUserTypeEl}
				</div>

				<div className="ww-response-content">
					<div className="ww-author-name">{authorNameText}</div>
					<div className="ww-subtitle ww-response-subtitle">
						<span className="ww-subtitle-section">
							{/* translators: Post timestamp */}
							{ sprintf( __( 'Posted %s', 'webworkqa' ), timestamp ) }
						</span>
						{editLinkElements}
					</div>

					{contentElements}
				</div>

				<div className="item-metadata">
					{respondLinkElement}
					<ScoreDialogContainer
					  itemId={responseId}
					  itemType='response'
					/>
				</div>
			</li>
		);
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
