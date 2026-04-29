jQuery( document ).ready( function( $ ) {

	const $page = $( '#epkb-kb-quizzes-page-container' );
	if ( ! $page.length ) {
		return;
	}

	const $form = $( '#epkb-quiz-editor-form' );
	const $questions = $( '#epkb-quiz-questions' );
	const $questionsEmpty = $( '#epkb-quiz-questions-empty' );
	const $notice = $( '#epkb-quiz-editor-notice' );
	const $warning = $( '#epkb-quiz-editor-warning' );
	const $quizzesList = $( '#epkb-quizzes-list' );
	const $statusBadge = $( '#epkb-quiz-status-badge' );
	const $editorTitle = $( '#epkb-quiz-editor-title' );
	const $sourceSelect = $( '#epkb-quiz-source-article' );
	const $interestModal = $( '#epkb-quiz-interest-modal' );
	const $interestMessage = $( '#epkb-quiz-interest-message' );
	const $viewLink = $( '#epkb-quiz-view-link' );
	const $editorTabTrigger = $page.find( '.epkb-admin__top-panel__item[data-target="quizzes-editor"]' );
	const $editorAddBtn = $form.find( '.epkb-quiz-create-trigger' );

	let isDirty = false;
	let isHydrating = false;
	let lastSourceArticleId = '0';

	function getCurrentSourceArticleId() {
		return String( $sourceSelect.val() || '0' );
	}

	function openEditorTab() {
		if ( ! $editorTabTrigger.length ) {
			return;
		}

		if ( $editorTabTrigger.hasClass( 'epkb-admin__top-panel__item--active' ) ) {
			return;
		}

		$editorTabTrigger.trigger( 'click' );
	}

	function sendAjax( action, data, onSuccess, onError ) {
		$.ajax( {
			url: epkbQuizAdmin.ajaxUrl,
			method: 'POST',
			dataType: 'json',
			data: $.extend( {
				action: action,
				_wpnonce_epkb_ajax_action: epkbQuizAdmin.nonce
			}, data || {} )
		} ).done( function( response ) {
			if ( ! response || response.success === false || response.error ) {
				const errorData = response && response.data ? response.data : ( response || {} );
				if ( onError ) {
					onError( errorData );
					return;
				}

				handleAjaxError( errorData );
				return;
			}

			onSuccess( response.data || {} );
		} ).fail( function( jqXHR ) {
			const response = jqXHR.responseJSON;
			if ( onError ) {
				onError( response && response.data ? response.data : {} );
				return;
			}

			handleAjaxError( response && response.data ? response.data : {} );
		} );
	}

	function handleAjaxError( errorData ) {
		if ( errorData && errorData.code === 'quiz_exists' && errorData.quiz ) {
			loadQuizData( errorData.quiz );
			updateQuizRow( errorData.quiz.quiz_id );
		}

		showNotice( errorData && errorData.message ? errorData.message : epkbQuizAdmin.strings.genericError, true );
	}

	function showNotice( message, isError ) {
		$notice.removeClass( 'epkb-quizzes-admin__notice--error epkb-quizzes-admin__notice--success' );
		$notice.addClass( isError ? 'epkb-quizzes-admin__notice--error' : 'epkb-quizzes-admin__notice--success' );
		$notice.html( message ).prop( 'hidden', false );
	}

	function hideNotice() {
		$notice.prop( 'hidden', true ).html( '' );
	}

	function escapeDialogText( value ) {
		return $( '<div></div>' ).text( value || '' ).html();
	}

	function closeSaveSuccessDialog() {
		$( '#epkb-quiz-save-success-dialog, .epkb-dialog-box-form-black-background--quiz-save-success' ).remove();
	}

	function showSaveSuccessDialog( message, onClose ) {
		let dialogHtml = '';
		const closeDialog = function() {
			closeSaveSuccessDialog();
			if ( typeof onClose === 'function' ) {
				onClose();
			}
		};

		closeSaveSuccessDialog();

		dialogHtml += '<div id="epkb-quiz-save-success-dialog" class="epkb-dialog-box-form epkb-dialog-box-form--active">';
		dialogHtml += '<div class="epkb-dbf__header"><h4>' + escapeDialogText( epkbQuizAdmin.strings.success ) + '</h4></div>';
		dialogHtml += '<div class="epkb-dbf__body"><p>' + escapeDialogText( message ) + '</p></div>';
		dialogHtml += '<div class="epkb-dbf__footer">';
		dialogHtml += '<div class="epkb-dbf__footer__accept epkb-dbf__footer__accept--success">';
		dialogHtml += '<span class="epkb-accept-button epkb-dbf__footer__accept__btn">' + escapeDialogText( epkbQuizAdmin.strings.close ) + '</span>';
		dialogHtml += '</div>';
		dialogHtml += '</div>';
		dialogHtml += '<div class="epkb-dbf__close epkbfa epkbfa-times"></div>';
		dialogHtml += '</div>';
		dialogHtml += '<div class="epkb-dialog-box-form-black-background epkb-dialog-box-form-black-background--quiz-save-success"></div>';

		$( 'body' ).append( dialogHtml );
		$( '#epkb-quiz-save-success-dialog .epkb-dbf__footer__accept__btn, #epkb-quiz-save-success-dialog .epkb-dbf__close, .epkb-dialog-box-form-black-background--quiz-save-success' ).on( 'click', function() {
			closeDialog();
		} );
	}

	function showWarning( message ) {
		if ( ! message ) {
			$warning.prop( 'hidden', true ).html( '' );
			return;
		}

		$warning.html( message ).prop( 'hidden', false );
	}

	function getEditorContent() {
		if ( typeof tinymce !== 'undefined' ) {
			const editor = tinymce.get( 'epkb_quiz_intro' );
			if ( editor && ! editor.isHidden() ) {
				return editor.getContent();
			}
		}

		return $( '#epkb_quiz_intro' ).val() || '';
	}

	function setEditorContent( content ) {
		const editorContent = content || '';
		$( '#epkb_quiz_intro' ).val( editorContent );

		if ( typeof tinymce !== 'undefined' ) {
			const editor = tinymce.get( 'epkb_quiz_intro' );
			if ( editor ) {
				editor.setContent( editorContent );
			}
		}
	}

	function setDirty( dirty ) {
		isDirty = dirty;
	}

	function bindIntroEditorDirtyTracking( editor ) {
		if ( ! editor || editor.id !== 'epkb_quiz_intro' || editor.epkbQuizDirtyTrackingBound ) {
			return;
		}

		editor.epkbQuizDirtyTrackingBound = true;
		$.each( [ 'input', 'change', 'keyup', 'undo', 'redo', 'ExecCommand' ], function( _, eventName ) {
			editor.on( eventName, function() {
				if ( ! isHydrating ) {
					setDirty( true );
				}
			} );
		} );
	}

	function initializeIntroEditorDirtyTracking() {
		if ( typeof tinymce === 'undefined' ) {
			return;
		}

		bindIntroEditorDirtyTracking( tinymce.get( 'epkb_quiz_intro' ) );
		if ( typeof tinymce.on === 'function' ) {
			tinymce.on( 'AddEditor', function( event ) {
				bindIntroEditorDirtyTracking( event.editor );
			} );
		}
	}

	function confirmDiscardChanges() {
		if ( ! isDirty ) {
			return true;
		}

		return window.confirm( epkbQuizAdmin.strings.discardChanges );
	}

	function updateStatusBadge( status ) {
		const isPublished = status === 'publish';
		$statusBadge
			.text( isPublished ? epkbQuizAdmin.strings.published : epkbQuizAdmin.strings.draft )
			.toggleClass( 'epkb-quizzes-admin__status--publish', isPublished )
			.toggleClass( 'epkb-quizzes-admin__status--draft', ! isPublished );
	}

	function updateViewLink( quiz ) {
		if ( ! $viewLink.length ) {
			return;
		}

		if ( quiz && quiz.status === 'publish' && quiz.source_article_url ) {
			$viewLink.attr( 'href', quiz.source_article_url + '#epkb-article-quiz-' + quiz.quiz_id ).prop( 'hidden', false );
		} else {
			$viewLink.prop( 'hidden', true );
		}
	}

	function ensureSourceOption( articleId, label ) {
		if ( ! articleId ) {
			return;
		}

		if ( $sourceSelect.find( 'option[value="' + articleId + '"]' ).length ) {
			return;
		}

		$sourceSelect.append(
			$( '<option></option>' )
				.val( articleId )
				.text( label || epkbQuizAdmin.strings.unavailableArticle )
		);
	}

	function updateQuestionsEmptyState() {
		const hasQuestions = $questions.find( '.epkb-quiz-question' ).length > 0;
		$questionsEmpty.prop( 'hidden', hasQuestions );
	}

	function getTrueFalseLabels() {
		return {
			trueText: String( $( '#quizzes_true_text' ).val() || epkbQuizAdmin.strings.trueText || '' ),
			falseText: String( $( '#quizzes_false_text' ).val() || epkbQuizAdmin.strings.falseText || '' )
		};
	}

	function buildCorrectChoiceOptions( type ) {
		if ( type === 'true_false' ) {
			const labels = getTrueFalseLabels();

			return [
				{ value: 0, label: labels.trueText },
				{ value: 1, label: labels.falseText }
			];
		}

		return [
			{ value: 0, label: epkbQuizAdmin.strings.choiceA },
			{ value: 1, label: epkbQuizAdmin.strings.choiceB },
			{ value: 2, label: epkbQuizAdmin.strings.choiceC },
			{ value: 3, label: epkbQuizAdmin.strings.choiceD }
		];
	}

	function syncRowByType( $row ) {
		const type = $row.find( '.epkb-quiz-question__type' ).val();
		const $choiceInputs = $row.find( '.epkb-quiz-question__choice-input' );
		const $correctSelect = $row.find( '.epkb-quiz-question__correct-choice' );

		if ( type === 'true_false' ) {
			const labels = getTrueFalseLabels();

			$choiceInputs.eq( 0 ).val( labels.trueText ).prop( 'readonly', true );
			$choiceInputs.eq( 1 ).val( labels.falseText ).prop( 'readonly', true );
			$choiceInputs.eq( 2 ).val( '' ).prop( 'readonly', true );
			$choiceInputs.eq( 3 ).val( '' ).prop( 'readonly', true );
			$row.find( '.epkb-quiz-question__choice' ).eq( 2 ).prop( 'hidden', true );
			$row.find( '.epkb-quiz-question__choice' ).eq( 3 ).prop( 'hidden', true );
		} else {
			$choiceInputs.prop( 'readonly', false );
			$row.find( '.epkb-quiz-question__choice' ).prop( 'hidden', false );
		}

		const currentValue = $correctSelect.val();
		$correctSelect.empty();
		$.each( buildCorrectChoiceOptions( type ), function( _, option ) {
			$correctSelect.append(
				$( '<option></option>' )
					.val( option.value )
					.text( option.label )
			);
		} );

		if ( $correctSelect.find( 'option[value="' + currentValue + '"]' ).length ) {
			$correctSelect.val( currentValue );
		} else {
			$correctSelect.val( '0' );
		}
	}

	function refreshQuestionRows() {
		$questions.find( '.epkb-quiz-question' ).each( function( index ) {
			const $row = $( this );
			$row.find( '.epkb-quiz-question__number' ).text( epkbQuizAdmin.strings.question + ' ' + ( index + 1 ) );
			syncRowByType( $row );
		} );

		updateQuestionsEmptyState();
	}

	function createQuestionRow( question ) {
		const data = $.extend( {
			id: '',
			type: 'multiple_choice',
			question: '',
			choices: [ '', '', '', '' ],
			correct_choice: 0,
			explanation: ''
		}, question || {} );

		const $row = $( `
			<div class="epkb-quiz-question" data-question-id="">
				<div class="epkb-quiz-question__head">
					<div class="epkb-quiz-question__number"></div>
					<div class="epkb-quiz-question__head-actions">
						<select class="epkb-quiz-question__type">
							<option value="multiple_choice">${epkbQuizAdmin.strings.multipleChoice}</option>
							<option value="true_false">${epkbQuizAdmin.strings.trueFalse}</option>
						</select>
						<button type="button" class="epkb-btn epkb-error-btn epkb-quiz-question__remove">${epkbQuizAdmin.strings.remove}</button>
					</div>
				</div>
				<div class="epkb-quizzes-admin__field">
					<label>${epkbQuizAdmin.strings.questionText}</label>
					<textarea class="epkb-quiz-question__text" rows="3"></textarea>
				</div>
				<div class="epkb-quiz-question__choices">
					<div class="epkb-quiz-question__choice">
						<label>${epkbQuizAdmin.strings.choiceA}</label>
						<input type="text" class="epkb-quiz-question__choice-input" data-choice-index="0">
					</div>
					<div class="epkb-quiz-question__choice">
						<label>${epkbQuizAdmin.strings.choiceB}</label>
						<input type="text" class="epkb-quiz-question__choice-input" data-choice-index="1">
					</div>
					<div class="epkb-quiz-question__choice">
						<label>${epkbQuizAdmin.strings.choiceC}</label>
						<input type="text" class="epkb-quiz-question__choice-input" data-choice-index="2">
					</div>
					<div class="epkb-quiz-question__choice">
						<label>${epkbQuizAdmin.strings.choiceD}</label>
						<input type="text" class="epkb-quiz-question__choice-input" data-choice-index="3">
					</div>
				</div>
				<div class="epkb-quizzes-admin__field-grid">
					<div class="epkb-quizzes-admin__field">
						<label>${epkbQuizAdmin.strings.correctAnswer}</label>
						<select class="epkb-quiz-question__correct-choice"></select>
					</div>
					<div class="epkb-quizzes-admin__field">
						<label>${epkbQuizAdmin.strings.internalId}</label>
						<input type="text" class="epkb-quiz-question__id" maxlength="80">
					</div>
				</div>
				<div class="epkb-quizzes-admin__field">
					<label>${epkbQuizAdmin.strings.explanation}</label>
					<textarea class="epkb-quiz-question__explanation" rows="3"></textarea>
				</div>
			</div>
		` );

		$row.attr( 'data-question-id', data.id || '' );
		$row.find( '.epkb-quiz-question__type' ).val( data.type );
		$row.find( '.epkb-quiz-question__text' ).val( data.question );
		$row.find( '.epkb-quiz-question__id' ).val( data.id || '' );
		$row.find( '.epkb-quiz-question__explanation' ).val( data.explanation );

		$row.find( '.epkb-quiz-question__choice-input' ).each( function( choiceIndex ) {
			$( this ).val( data.choices && typeof data.choices[ choiceIndex ] !== 'undefined' ? data.choices[ choiceIndex ] : '' );
		} );

		syncRowByType( $row );
		$row.find( '.epkb-quiz-question__correct-choice' ).val( String( data.correct_choice ) );

		return $row;
	}

	function buildQuestionsPayload() {
		const questions = [];

		$questions.find( '.epkb-quiz-question' ).each( function() {
			const $row = $( this );
			const type = $row.find( '.epkb-quiz-question__type' ).val();
			const choices = [];

			$row.find( '.epkb-quiz-question__choice-input' ).each( function( index ) {
				if ( type === 'true_false' && index > 1 ) {
					return;
				}

				choices.push( $( this ).val() );
			} );

			questions.push( {
				id: $row.find( '.epkb-quiz-question__id' ).val(),
				type: type,
				question: $row.find( '.epkb-quiz-question__text' ).val(),
				choices: choices,
				correct_choice: parseInt( $row.find( '.epkb-quiz-question__correct-choice' ).val(), 10 ),
				explanation: $row.find( '.epkb-quiz-question__explanation' ).val()
			} );
		} );

		return questions;
	}

	function resetForm() {
		isHydrating = true;
		hideNotice();
		showWarning( '' );
		$form.trigger( 'reset' );
		$( '#epkb-quiz-id' ).val( '0' );
		$( '#epkb-quiz-title' ).val( '' );
		$sourceSelect.val( '0' );
		$( '#epkb-quiz-question-count' ).val( 'auto' );
		$questions.empty();
		setEditorContent( '' );
		updateStatusBadge( 'draft' );
		updateViewLink( null );
		$editorTitle.text( epkbQuizAdmin.strings.newQuiz );
		$editorAddBtn.prop( 'hidden', true );
		$( '#epkb-quiz-delete' ).prop( 'disabled', true );
		$quizzesList.find( '.epkb-quiz-list-row--active' ).removeClass( 'epkb-quiz-list-row--active' );
		refreshQuestionRows();
		lastSourceArticleId = getCurrentSourceArticleId();
		isHydrating = false;
		setDirty( false );
	}

	function loadQuizData( quiz ) {
		if ( ! quiz ) {
			resetForm();
			return;
		}

		isHydrating = true;
		hideNotice();
		$( '#epkb-quiz-id' ).val( quiz.quiz_id );
		$( '#epkb-quiz-title' ).val( quiz.title || '' );
		$( '#epkb-quiz-question-count' ).val( quiz.question_count_mode || 'auto' );
		ensureSourceOption( quiz.source_article_id, quiz.source_article_label );
		$sourceSelect.val( String( quiz.source_article_id || 0 ) );
		setEditorContent( quiz.intro || '' );
		updateStatusBadge( quiz.status || 'draft' );
		updateViewLink( quiz );
		$editorTitle.text( quiz.title || epkbQuizAdmin.strings.newQuiz );
		$editorAddBtn.prop( 'hidden', true );
		$( '#epkb-quiz-delete' ).prop( 'disabled', false );
		showWarning( quiz.source_warning_message || '' );

		$questions.empty();
		$.each( quiz.questions || [], function( _, question ) {
			$questions.append( createQuestionRow( question ) );
		} );
		refreshQuestionRows();
		updateQuizRow( quiz.quiz_id );
		lastSourceArticleId = getCurrentSourceArticleId();
		isHydrating = false;
		setDirty( false );
	}

	function updateQuizRow( quizId, rowHtml ) {
		if ( rowHtml ) {
			const $newRow = $( rowHtml );
			const $existingRow = $quizzesList.find( '.epkb-quiz-list-row[data-quiz-id="' + quizId + '"]' );

			if ( $existingRow.length ) {
				$existingRow.replaceWith( $newRow );
			} else {
				$quizzesList.find( '.epkb-quizzes-list__empty' ).remove();
				$quizzesList.prepend( $newRow );
			}
		}

		$quizzesList.find( '.epkb-quiz-list-row--active' ).removeClass( 'epkb-quiz-list-row--active' );
		$quizzesList.find( '.epkb-quiz-list-row[data-quiz-id="' + quizId + '"]' ).addClass( 'epkb-quiz-list-row--active' );
	}

	function collectFormPayload( postStatus ) {
		return {
			quiz_id: $( '#epkb-quiz-id' ).val(),
			quiz_title: $( '#epkb-quiz-title' ).val(),
			quiz_intro: getEditorContent(),
			source_article_id: $sourceSelect.val(),
			question_count_mode: $( '#epkb-quiz-question-count' ).val(),
			post_status: postStatus,
			questions_json: JSON.stringify( buildQuestionsPayload() )
		};
	}

	function saveQuiz( postStatus ) {
		hideNotice();
		sendAjax( 'epkb_save_quiz', collectFormPayload( postStatus ), function( data ) {
			loadQuizData( data.quiz );
			updateQuizRow( data.quiz.quiz_id, data.quiz_row_html );
			showSaveSuccessDialog( data.message, function() {
				maybeShowInterestModal( data.show_interest_modal );
			} );
		} );
	}

	function generateQuiz() {
		hideNotice();
		const $btn = $( '#epkb-quiz-generate' );
		$btn.prop( 'disabled', true ).find( '.epkbfa' ).removeClass( 'epkbfa-magic' ).addClass( 'epkbfa-spinner epkbfa-spin' );

		sendAjax( 'epkb_generate_quiz', collectFormPayload( 'draft' ), function( data ) {
			$btn.prop( 'disabled', false ).find( '.epkbfa' ).removeClass( 'epkbfa-spinner epkbfa-spin' ).addClass( 'epkbfa-magic' );
			loadQuizData( data.quiz );
			updateQuizRow( data.quiz.quiz_id, data.quiz_row_html );
			showNotice( data.message, false );
			maybeShowInterestModal( data.show_interest_modal );
		}, function( errorData ) {
			$btn.prop( 'disabled', false ).find( '.epkbfa' ).removeClass( 'epkbfa-spinner epkbfa-spin' ).addClass( 'epkbfa-magic' );
			handleAjaxError( errorData );
		} );
	}

	function deleteQuiz() {
		const quizId = $( '#epkb-quiz-id' ).val();
		if ( ! quizId || quizId === '0' ) {
			return;
		}

		if ( ! window.confirm( epkbQuizAdmin.strings.deleteConfirm ) ) {
			return;
		}

		sendAjax( 'epkb_delete_quiz', { quiz_id: quizId }, function( data ) {
			$quizzesList.find( '.epkb-quiz-list-row[data-quiz-id="' + data.quiz_id + '"]' ).remove();
			if ( ! $quizzesList.find( '.epkb-quiz-list-row' ).length ) {
				$quizzesList.html( '<div class="epkb-quizzes-list__empty">' + epkbQuizAdmin.strings.noQuizzes + '</div>' );
			}
			resetForm();
			showNotice( data.message, false );
		} );
	}

	function maybeLoadExistingQuizForSource( previousSourceArticleId ) {
		const sourceArticleId = getCurrentSourceArticleId();
		if ( ! sourceArticleId || sourceArticleId === '0' ) {
			lastSourceArticleId = sourceArticleId;
			return;
		}

		sendAjax( 'epkb_get_quiz_by_article', { source_article_id: sourceArticleId }, function( data ) {
			if ( getCurrentSourceArticleId() !== sourceArticleId ) {
				return;
			}

			const currentQuizId = $( '#epkb-quiz-id' ).val();
			if ( ! data.quiz || String( data.quiz.quiz_id ) === String( currentQuizId ) ) {
				lastSourceArticleId = sourceArticleId;
				return;
			}

			if ( ! confirmDiscardChanges() ) {
				isHydrating = true;
				$sourceSelect.val( previousSourceArticleId );
				lastSourceArticleId = previousSourceArticleId;
				isHydrating = false;
				return;
			}

			loadQuizData( data.quiz );
			showNotice( epkbQuizAdmin.strings.openedExistingQuiz, false );
		}, function( errorData ) {
			if ( getCurrentSourceArticleId() === sourceArticleId ) {
				lastSourceArticleId = sourceArticleId;
			}

			handleAjaxError( errorData );
		} );
	}

	function loadQuizById( quizId ) {
		sendAjax( 'epkb_get_quiz', { quiz_id: quizId }, function( data ) {
			loadQuizData( data.quiz );
		} );
	}

	function maybeShowInterestModal( showModal ) {
		if ( ! showModal ) {
			return;
		}

		if ( $page.find( '.epkb-quizzes-admin' ).data( 'interest-submitted' ) === 1 ) {
			return;
		}

		if ( sessionStorage.getItem( 'epkbQuizInterestSkipped' ) === '1' ) {
			return;
		}

		openInterestModal();
	}

	function openInterestModal() {
		$( '#epkb-quiz-interest-first-name' ).val( epkbQuizAdmin.currentUser.firstName || '' );
		$( '#epkb-quiz-interest-email' ).val( epkbQuizAdmin.currentUser.email || '' );
		$interestMessage.prop( 'hidden', true ).html( '' );
		$interestModal.prop( 'hidden', false );
	}

	function closeInterestModal() {
		$interestModal.prop( 'hidden', true );
	}

	$form.on( 'input change', 'input, select, textarea', function() {
		if ( ! isHydrating && this.id !== 'epkb-quiz-source-article' ) {
			setDirty( true );
		}
	} );

	$( document ).on( 'click', '.epkb-quiz-create-trigger', function() {
		if ( ! confirmDiscardChanges() ) {
			return;
		}

		openEditorTab();
		resetForm();
	} );

	$( document ).on( 'click', '.epkb-quiz-list-row', function() {
		const $row = $( this );
		const quizId = $row.data( 'quiz-id' );
		const currentQuizId = String( $( '#epkb-quiz-id' ).val() || '0' );

		if ( ! quizId ) {
			return;
		}

		if ( currentQuizId === String( quizId ) ) {
			openEditorTab();
			return;
		}

		if ( ! confirmDiscardChanges() ) {
			return;
		}

		openEditorTab();
		loadQuizById( quizId );
	} );

	$( document ).on( 'change', '#epkb-quiz-source-article', function() {
		if ( isHydrating ) {
			return;
		}

		const previousSourceArticleId = lastSourceArticleId;
		lastSourceArticleId = getCurrentSourceArticleId();
		maybeLoadExistingQuizForSource( previousSourceArticleId );
	} );

	$( document ).on( 'click', '#epkb-quiz-add-question', function() {
		$questions.append( createQuestionRow() );
		refreshQuestionRows();
		setDirty( true );
	} );

	$( document ).on( 'input change', '#quizzes_true_text, #quizzes_false_text', function() {
		refreshQuestionRows();
	} );

	$( document ).on( 'change', '.epkb-quiz-question__type', function() {
		syncRowByType( $( this ).closest( '.epkb-quiz-question' ) );
		setDirty( true );
	} );

	$( document ).on( 'click', '.epkb-quiz-question__remove', function() {
		$( this ).closest( '.epkb-quiz-question' ).remove();
		refreshQuestionRows();
		setDirty( true );
	} );

	$( document ).on( 'click', '#epkb-quiz-save-draft', function() {
		saveQuiz( 'draft' );
	} );

	$( document ).on( 'click', '#epkb-quiz-publish', function() {
		saveQuiz( 'publish' );
	} );

	$( document ).on( 'click', '#epkb-quiz-generate', function() {
		generateQuiz();
	} );

	$( document ).on( 'click', '#epkb-quiz-delete', function() {
		deleteQuiz();
	} );

	$( document ).on( 'click', '.epkb-quiz-feedback-trigger', function() {
		openInterestModal();
	} );

	$( document ).on( 'click', '#epkb-quiz-interest-close, #epkb-quiz-interest-skip', function() {
		sessionStorage.setItem( 'epkbQuizInterestSkipped', '1' );
		closeInterestModal();
	} );

	$( document ).on( 'click', '#epkb-quiz-interest-submit', function() {
		sendAjax( 'epkb_submit_quiz_interest', {
			first_name: $( '#epkb-quiz-interest-first-name' ).val(),
			email: $( '#epkb-quiz-interest-email' ).val(),
			feedback: $( '#epkb-quiz-interest-feedback' ).val()
		}, function( data ) {
			$page.find( '.epkb-quizzes-admin' ).data( 'interest-submitted', 1 );
			sessionStorage.removeItem( 'epkbQuizInterestSkipped' );
			$interestMessage
				.removeClass( 'epkb-quizzes-admin__notice--error' )
				.addClass( 'epkb-quizzes-admin__notice--success' )
				.html( data.message )
				.prop( 'hidden', false );
			window.setTimeout( closeInterestModal, 900 );
		}, function( errorData ) {
			$interestMessage
				.removeClass( 'epkb-quizzes-admin__notice--success' )
				.addClass( 'epkb-quizzes-admin__notice--error' )
				.html( errorData && errorData.message ? errorData.message : epkbQuizAdmin.strings.genericError )
				.prop( 'hidden', false );
		} );
	} );

	initializeIntroEditorDirtyTracking();
	resetForm();

	// Show interest modal if redirected from feature enable on Dashboard
	if ( new URLSearchParams( window.location.search ).get( 'epkb_show_feedback' ) === '1' ) {
		var cleanUrl = new URL( window.location.href );
		cleanUrl.searchParams.delete( 'epkb_show_feedback' );
		window.history.replaceState( {}, '', cleanUrl.toString() );
		maybeShowInterestModal( true );
	}

	// Show interest modal when Quizzes feature is enabled via Settings tab save
	$( document ).ajaxComplete( function( event, jqXHR, settings ) {
		if ( ! settings || ! settings.data ) {
			return;
		}

		var isSettingsSaveRequest = false;
		if ( typeof settings.data === 'string' ) {
			isSettingsSaveRequest = settings.data.indexOf( 'action=epkb_apply_settings_changes' ) !== -1;
		} else if ( typeof settings.data === 'object' ) {
			isSettingsSaveRequest = settings.data.action === 'epkb_apply_settings_changes';
		}

		if ( ! isSettingsSaveRequest ) {
			return;
		}

		var response = jqXHR.responseJSON;
		if ( ! response ) {
			try {
				response = JSON.parse( jqXHR.responseText );
			} catch ( e ) {
				return;
			}
		}

		if ( ! response || response.error ) {
			return;
		}

		var $toggle = $( '#quizzes_enable input[type="checkbox"]' );
		if ( $toggle.length && $toggle.prop( 'checked' ) ) {
			maybeShowInterestModal( true );
		}
	} );
} );
