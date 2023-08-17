import React, {useEffect, useState} from 'react';

export default function Toggle( props ) {
    const [checked, updateStatus] = useState(false);

    useEffect(() => {
        if ( props.active !== undefined ) {
            updateStatus( props.active );
        }
    }, [props.active]);

    let active = props.active ? props.active : false,
        id = props.id ? props.id : '',
        classes = props.classes ? props.classes : '',
        label = props.label ? props.label : '',
        description = props.description ? props.description : '',
        callback = props.onClick !== undefined ? props.onClick : '';

    return (
        <div className={`sui-form-field ${classes}`}>

            <label htmlFor={id} className="sui-toggle">
                <input
                    type="checkbox"
                    id={id}
                    defaultChecked={active}
                    aria-labelledby={`${id}-label`}
                    aria-describedby={`${id}-description`}
                    onClick={callback}
                />

                <span className="sui-toggle-slider" aria-hidden="true"></span>
                <span id={`${id}-label`} className="sui-toggle-label">{label}</span>
                <span id={`${id}-description`} className="sui-description">{description}</span>
            </label>

        </div>
    );
}
