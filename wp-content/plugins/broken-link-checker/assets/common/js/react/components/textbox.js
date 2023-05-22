import React, {useEffect, useState} from 'react';

const { __ } = wp.i18n;

export function TextBox( props ) {
    let autoComplete = props.autoComplete !== undefined ? props.autoComplete : 'off';
    let type = props.type !== undefined ? props.type : 'text';
    let classes = props.classes !== undefined ? props.classes : '';

    return(
        <div className={`sui-form-field ${props.classes}`}>
            {
            props.label &&
                <label
                    htmlFor={props.id}
                    className="sui-label"
                    aria-label={props.label}
                >
                    {props.label}
                </label>
            }

            <input
                type={type}
                id={props.id}
                className={`sui-form-control ${classes}`}
                name={props.name}
                placeholder={props.placeholder}
                autoComplete={autoComplete}
                value={props.value}
                onKeyDown={props.onKeyDown}
                onKeyUp={props.onKeyUp}
                onBlur={props.onBlur}
                onChange={props.onChange}

            />
        </div>
    )
}
