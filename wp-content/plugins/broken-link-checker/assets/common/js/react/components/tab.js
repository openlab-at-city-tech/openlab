import React, {useState, useEffect} from 'react';
//import '@wpmudev/shared-ui/js/tabs';

export function Tab( props ) {
    const [activeTab, setActiveTab] = useState(false);

    let id = props.id !== undefined ? props.id : '';
    let classes = props.classes !== undefined ? props.classes : '';
    let tabData = props.tabData !== undefined ? props.tabData : '';
    let sideTab = props.sideTab !== undefined ? props.sideTab : false;

/*
    SUI.tabs({
        callback: function( tab, panel ) {}
    });

 */

    if ( '' === tabData ) {
        return <></>
    }

    if ( sideTab ) {
        classes += ' sui-side-tabs';
    }

    const handleTabClick = ( item ) => {
        setActiveTab( item.id );
        if ( typeof props.callback !== 'undefined' ) {
            props.callback( item );
        }

        if ( typeof item.callback !== 'undefined' ) {
            item.callback();
        }
    }

    return <div
        {... id ? {id} : {}}
        className={`sui-tabs ${classes}`}
    >

        <div role="tablist" className="sui-tabs-menu">
            {
                tabData.map(
                    (arrayItem) => (
                        <button
                            key={arrayItem.id}
                            type="button"
                            role="tab"
                            id={arrayItem.id}
                            className={`sui-tab-item ${ ( ( ! activeTab && arrayItem.selected ) || activeTab === arrayItem.id ) ? 'active' : ''}`}
                            aria-controls={`${arrayItem.id}__content`}
                            aria-selected={arrayItem.selected}
                            onClick={ () => handleTabClick( arrayItem ) }
                        >
                            {arrayItem.title}
                        </button>
                    )
                )
            }
        </div>

        <div className="sui-tabs-content">
            {
                tabData.map(
                    (arrayItem ) => (
                        <div
                            key={arrayItem.id}
                            role="tabpanel"
                            tabIndex="0"
                            id={`${arrayItem.id}__content`}
                            className={`sui-tab-content ${ ( ( ! activeTab && arrayItem.selected) || activeTab === arrayItem.id ) ? 'active' : ''}`}
                            aria-labelledby={arrayItem.id}
                        >
                            <div>{arrayItem.content}</div>
                        </div>
                    )
                )
            }
        </div>

    </div>
}
