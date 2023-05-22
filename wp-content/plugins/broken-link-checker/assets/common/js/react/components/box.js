import React from 'react';

export default function Box( props ) {
    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';
    let title = props.title !== undefined ? props.title : false;
    let footer = props.footer !== undefined ? props.footer : false;
    let headerAction = props.headerAction !== undefined ? props.headerAction : false;
    let footerAction = props.footerActions !== undefined ? props.footerActions : false;

    return <div
        {... id ? {id} : {}}
        className={`sui-box ${classes}`} >
        {title &&
            <div className="sui-box-header">
                <h2 className="sui-box-title">{title}</h2>
                {headerAction && <div className="sui-actions-right">{headerAction}</div>}
            </div>
        }

        {props.children}
        {footer &&
            <div className="sui-box-footer">
                {footer}
                {footerAction && <div className="sui-actions-right">{footerAction}</div>}
            </div>

        }
    </div>
}