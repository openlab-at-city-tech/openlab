import React from 'react';

export default function BoxSection( props ) {
    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';

    return <div
        {... id ? {id} : {}}
        className={`sui-box-settings-row ${classes}`} >
        <div className="sui-box-settings-col-2">
            {props.children}
        </div>
    </div>
}