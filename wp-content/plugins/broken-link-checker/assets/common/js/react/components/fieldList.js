import React, {useEffect, useState} from 'react';
import {Column, Grid, Row} from "./grid";

const { __ } = wp.i18n;

export function FieldList( props ) {
    /**
     * Fields structure :
     * [
     *         {
     *             key: 'FieldKey',
     *             data: {
     *                 ColumnKey1: 'ColumnValue1',
     *                 ColumnKey2: 'ColumnValue2'
     *             }
     *         },
     *         {
     *             key: 'FieldKey2',
     *             data: {
     *                 ColumnKey3: 'ColumnValue3',
     *                 ColumnKey4: 'ColumnValue4'
     *             }
     *         }
     *     ];
     */
    const [fields, setFields] = useState([]);
    useEffect(() => {
        if ( props.fields ) {
            setFields( props.fields );
        } else {setFields([])}
    }, [props.fields]);

    const getFieldColumn = ( columnData, rowKey ) => {
        const columns = [];

        Object.entries( columnData ).map(([key, value]) => {
            columns.push( <Column key={`${props.id}-${rowKey}-${key}`}>{value}</Column> );
        })

        return columns;
    }

    const printFields = () => {
        if ( typeof fields !== 'undefined' && fields.length > 0 ) {
            return (
                fields.map(
                    (fieldItem) => (
                        ( typeof fieldItem !== 'undefined' && fieldItem.hasOwnProperty( 'key' ) ) &&
                        <Row key={`${props.id}-${fieldItem.key}`} classes={"field-list-row"}>{getFieldColumn( fieldItem.data, fieldItem.key )}</Row>
                    )
                )
            )
        }
    }

    return(
        <Grid id={props.id} className={props.classes}>
            { printFields() }
        </Grid>
    )
}
