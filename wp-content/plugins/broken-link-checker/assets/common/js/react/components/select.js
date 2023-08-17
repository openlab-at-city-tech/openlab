import React, {useEffect, useRef} from 'react';
import '@wpmudev/shared-ui/js/select2.full';


const {__} = wp.i18n;

export function Select(props) {
    let multiple = props.multiple !== undefined ? props.multiple : false;

    const selectElement = useRef(null);
    useEffect(() => {
        let $el = selectElement.current;

        if ($el !== null) {
            jQuery(`#${selectElement.current.id}`).SUIselect2(
                suiSelectArgs()
            ).on('change', function (e) {
                handleChange(e);
            });

            jQuery(`#${selectElement.current.id}`).val(props.selected).trigger('change');
        }

    }, []);

    const suiSelectArgs = () => {
        let params = {
                dropdownCssClass: 'sui-select-dropdown',
            },
            search = props.search !== undefined && props.search;

        if (!search) {
            params['minimumResultsForSearch'] = 'Infinity';
        }

        return params;
    }

    const handleChange = (e) => {
        if (props.multiple) {
            let selectedOptions = [];

            for (var option of e.currentTarget.options) {
                if (option.selected) {
                    selectedOptions.push(option.value);
                }
            }
            if (props.callback) {
                props.callback(selectedOptions);
            }
        } else {
            if (props.callback) {
                props.callback(e.currentTarget.value)
            }
        }
    }

    return (
        <div className="sui-form-field">
            {props.label && <label className="sui-label">{props.label}</label>}

            <select
                id={props.id}
                className={`sui-select ${props.classes}`}
                ref={selectElement}
                multiple={multiple}
                data-theme={props.theme}
            >
                {props.children}
            </select>

            {props.description &&
                <div className="sui-description" dangerouslySetInnerHTML={{__html: props.description}}/>}
        </div>
    )

}
