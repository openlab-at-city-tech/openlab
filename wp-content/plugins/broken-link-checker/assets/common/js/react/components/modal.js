import React, { useState, useEffect } from 'react';
import {
    Box,
    BoxHeader,
    BoxSection,
} from '@wpmudev/react-box';

const { __ } = wp.i18n;

export default function Modal( props ) {
    const [modalActive, setModalActive] = useState(false);

    useEffect(() => {
        if ( props.isActive !== undefined ) {
            setModalActive( props.isActive );
        }
    }, [props.isActive]);

    let closeButtonCallback = props.closeButtonCallback !== undefined ? props.closeButtonCallback : () => {setModalActive(false)};
    let classes = props.classes !== undefined ? props.classes : '';
    let headerTitleAlign = props.headerTitleAlign !== undefined ? props.headerTitleAlign : 'center';
    let modalSize = 'md';

    if ( props.modalSize !== undefined ) {
        switch( props.modalSize ) {
            case 'large' : modalSize = 'lg'; break;
            case 'medium' : modalSize = 'md'; break;
            case 'small' : modalSize = 'sm'; break;
        }
    }

    return (
        <div className={`sui-modal sui-modal-${modalSize} ${modalActive && 'sui-active'} ${classes}`}>
            <div
                role="dialog"
                id={props.modalID}
                className={`sui-modal-content ${props.classes}`}
                aria-modal="true"
                aria-labelledby={props.ariaLabelledbBy}
                aria-describedby={props.ariaDescribedBy}
            >
                <Box>
                    <div className="header-wrap">
                        <BoxHeader>
                            <div className={`sui-box-header sui-flatten sui-content-${headerTitleAlign} modal-header`}>
                                {
                                    props.closeButton &&
                                    <button
                                        className="sui-button-icon sui-button-float--right"
                                        data-modal-close
                                        onClick={closeButtonCallback}
                                    >
                                        <i className="sui-icon-close sui-md" aria-hidden="true"></i>
                                        <span
                                            className="sui-screen-reader-text">{__( 'Close this modal', 'broken-link-checker' )}
                                    </span>
                                    </button>
                                }
                                {props.modalTitle &&
                                    <h3>{props.modalTitle}</h3>
                                }
                                {props.header}
                            </div>
                        </BoxHeader>
                    </div>

                    <Box className="sui-box-body sui-modal-content modal-content sui-box-center">
                        <BoxSection>
                            {props.content}
                        </BoxSection>
                    </Box>

                    <Box className="sui-box-footer sui-modal-content modal-content sui-box-center">
                        <BoxSection>
                            {props.footer}
                        </BoxSection>
                    </Box>
                </Box>

            </div>

        </div>
    )
}
