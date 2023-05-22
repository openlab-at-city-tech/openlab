import React from 'react';

export default function Notification( props ) {
	/**
	 * Valid props:
	 * 	design: general, informative, success, warning, error, upsell. Default: general
	 * 	type: floating, inline. Default: inline
	 * 	content: The content. Default: ''
	 * 	classes: Additional classes. Default: ''
	 * 	id: Element id. Default: ''
	 */
	let design = props.design ? props.design : 'general',
		type = props.type ? props.type : 'inline',
		visibility = props.visibility ? props.visibility : 'visible',
		content = props.content ? props.content : '',
		classes = props.classes ? props.classes : '',
		id = props.id ? props.id : '';

	let notificationClasses = 'sui-notice sui-active'
	
	let style = visibility === 'visible' ? {
		display: 'block',
		textAlign: 'left'
	} : '';

	switch ( design ) {
		case 'general':
			break;
		case 'informative' :
			notificationClasses += ' sui-notice-blue';
			break;
		case 'success' :
			notificationClasses += ' sui-notice-green';
			break;
		case 'warning':
			notificationClasses += ' sui-notice-yellow';
			break;
		case 'error':
			notificationClasses += ' sui-notice-red';
			break;
		case 'upsell':
			notificationClasses += ' sui-notice-purple';
			break;
	}

	let noticeTypeClass = type === 'floating' ? 'sui-floating-notices' : '';

	if ( classes.length ) {
		notificationClasses += ' ' + classes;
	}

	const handleCloseAction = ( el ) => {
		props.dismissibleCallback( props.id, el );
	}

	return (
		<div className={noticeTypeClass}>
			<div role="alert" 
				id={id} 
				className={notificationClasses} 
				aria-live="assertive" 
				style={style}
				>
				<div className="sui-notice-content">
					<div className="sui-notice-message">
						<span className="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></span>
						<p>{content}</p>
					</div>
					{
						props.dismissible && <div className="sui-notice-actions">
							<div className="sui-tooltip sui-tooltip-bottom" data-tooltip="Dismiss">
								<button
									className="sui-button-icon"
									onClick={(e)=>handleCloseAction( e )}
								>
									<i className="sui-icon-check" aria-hidden="true"></i>
									<span
										className="sui-screen-reader-text"
									>
									{props.dismissLabel}
								</span>
								</button>
							</div>
						</div>
					}
				</div>

			</div>
		</div>
	);
  }
