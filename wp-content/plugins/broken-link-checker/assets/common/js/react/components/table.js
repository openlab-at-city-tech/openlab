import React from 'react';

export function Table( props ) {
    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';

    return <div className="sui-table-wrap">
        <table
            {... id ? {id} : {}}
            className={`sui-table ${classes}`}
        >
            {props.children}
        </table>
    </div>
}

export function TableRow( props ) {
    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';

    return <tr
        {... id ? {id} : {}}
        className={classes}
        >
        {props.children}
    </tr>
}

export function TableColumn( props ) {
    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';
    let colspan = props.colspan !== undefined ? props.colspan : '';
    let rowspan = props.rowspan !== undefined ? props.colspan : '';
    let isTitle = props.isTitle !== undefined ? props.isTitle : false;

    if ( isTitle ) {
        classes += ' sui-table-item-title';
    }

    return <td
        {... id ? {id} : {}}
        className={classes}
        {... colspan ? {colspan} : {}}
        {... rowspan ? {rowspan} : {}}
        >
        {props.children}
    </td>
}

export function HeaderCell( props ) {
    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';
    let colspan = props.colspan !== undefined ? props.colspan : '';
    let rowspan = props.rowspan !== undefined ? props.colspan : '';

    return <th
        {... id ? {id} : {}}
        className={classes}
        {... colspan ? {colspan} : {}}
        {... rowspan ? {rowspan} : {}}
        >
        {props.children}
    </th>
}

export function TableHead( props ) {
    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';

    return <thead
        {... id ? {id} : {}}
        className={classes}
        >
        {props.children}
    </thead>
}

export function TableBody( props ) {
    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';

    return <tbody
        {... id ? {id} : {}}
        className={classes}
    >
    {props.children}
    </tbody>
}

export function TableFooter( props ) {
    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';

    return <tfoot
        {... id ? {id} : {}}
        className={classes}
    >
    {props.children}
    </tfoot>
}