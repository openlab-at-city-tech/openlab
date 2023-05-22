import React from 'react';
import {Row} from "./grid";

require('@wpmudev/shared-ui/dist/js/_src/accordion');

export default function Accordion(props) {
    let id = props.id !== undefined ? props.id : 'blc-accordion';
    let classes = props.classes !== undefined ? props.classes : '';
    let accordionData = props.accordionData !== undefined ? props.accordionData : '';
    let accordionIcon = props.accordionIcon !== undefined ? props.accordionIcon : 'sui-icon-chevron-down';
    let active = props.active !== undefined && props.active ? true : false;

    //SUI.suiAccordion( document.querySelector(`#${id}.sui-accordion`) );
    SUI.suiAccordion( document.querySelector(`#${id}`) );

    return <table
        {... id ? {id} : {}}
        className={`sui-table sui-accordion ${classes}`}
        >
        <tbody>
        {
            ( accordionData.length > 0 ) &&
            accordionData.map(
                (accordionItem, i) => {
                    return [
                        <tr
                            key={`${accordionItem.id}-title`}
                            id={`${accordionItem.id}-title`}
                            className={`sui-accordion-item sui-accordion-item-head ${active ? 'sui-accordion-item--open' : ''}`}>

                            <td className="sui-table-item-title">{accordionItem.title}
                                <span
                                    className="sui-accordion-open-indicator">
                                    <span className={accordionIcon} aria-hidden="true"></span>
                                </span>
                            </td>
                        </tr>,
                        <tr
                            key={`${accordionItem.id}-content`}
                            id={`${accordionItem.id}-content`}
                            className={`sui-accordion-item-content ${active ? 'sui-accordion-item--open' : ''}`}
                        >
                            <td>
                                <div className="accordion-content" tabIndex={i}>
                                    {accordionItem.description}
                                </div>
                            </td>
                        </tr>
                    ]
                }
            )
        }
        </tbody>
    </table>
}