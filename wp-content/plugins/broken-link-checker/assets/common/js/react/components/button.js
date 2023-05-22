import React from 'react';

export default function Button( props ) {
    const buttonVariants = new Array( 'text', 'contained' );
    let variant =
        ( props.variant !== undefined && buttonVariants.indexOf( props.variant ) != -1 ) ?
            props.variant :
            'contained';
    let suiButtonClass = variant === 'contained' ? 'sui-button' : 'sui-button-icon';
    let content = props.content !== undefined ? props.content : '';
    let ariaLabel = props.ariaLabel !== undefined ? props.ariaLabel : content;
    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';
    let disabled = props.disabled !== undefined && props.disabled ? 'disabled' : '';
    let outlined = props.outlined !== undefined && props.outlined ? true : false;
    let dashed = props.dashed !== undefined && props.dashed ? true : false;
    let large = props.large !== undefined && props.large ? true : false;
    let color = props.color !== undefined ? props.color : '';
    let callback = props.onClick !== undefined ? props.onClick : '';
    let tooltip = {};
    let colorClass = '';

    if ( color !== '' ) {
        colorClass = `sui-button-${color}`;
    }

    if ( outlined ) {
        classes += ' sui-button-ghost';
    }

    if ( dashed ) {
        classes += ' sui-button-dashed';
    }

    if ( large ) {
        classes += ' sui-button-lg';
    }


    if ( props.tooltip !== undefined && props.tooltip.length > 0 ) {
        tooltip = {'data-tooltip': props.tooltip}
        classes += ' sui-tooltip';

        if ( props.tooltip_constrained !== undefined && props.tooltip_constrained ) {
            classes += ' sui-tooltip-constrained';
        }

        if ( props.tooltip_bottom !== undefined && props.tooltip_bottom ) {
            classes += ' sui-tooltip-bottom';
        }
    }

    if ( props.isLoading ) {
        classes += ' sui-button-onload';
        content = <>
            <span className="sui-icon-loader sui-loading" aria-hidden="true"></span>
            <span className="sui-loading-text">{content}</span>
        </>
    }

    return <button
        {... id ? {id} : {}}
        aria-label={ariaLabel}
        className={`${suiButtonClass} ${colorClass} ${classes}`}
        onClick = {props.onClick}
        disabled = {props.disabled}
        {... tooltip}
        ref={props.mainReference}
    >
        {props.buttonIcon &&
            <span ref={props.iconReference} className={`sui-icon-${props.buttonIcon}`} aria-hidden="true"/>
        }
        {content}
        {ariaLabel && <span className="sui-screen-reader-text">{ariaLabel}</span>}
    </button>
}
