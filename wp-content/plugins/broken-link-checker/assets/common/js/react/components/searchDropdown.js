import React, {useEffect, useState} from 'react';

const { __ } = wp.i18n;

export function SearchDropdown( props ) {
    const [searchResultsFieldStatus, setSearchResultsFieldStatus] = useState( 'closed' );
    const [textareaValue, setTextareaValue] = useState( '' );
    const [dropdownMarkup, setDropdownMarkup] = useState( '' );
    const [selectedItemIdx, setSelectedItemIdx] = useState( -1 );
    const [reset, setReset] = useState( false );

    let searchResultsItems = [];
    if ( props.searchResults ) {
        searchResultsItems = props.searchResults;
    }

    /**
     * Sets the dropdown content when there's a change on any of props.searchResults, props.showDefaultMessage, props.showNoResultsMessage
     */
    useEffect(() => {
        if ( ! props.showDefaultMessage && ! props.showNoResultsMessage ) {
            setDropdownMarkup( printItems( selectedItemIdx ) );
        } else {
            setDropdownMarkup( dropdownContent() );
        }

    }, [props.searchResults, props.showDefaultMessage, props.showNoResultsMessage], reset);

    /**
     * Initially sets the dropdown content to the default message.
     */
    useEffect( () => {
        setDropdownMarkup( getDefaultMessage() );
    }, [] );

    /**
     * Resets selected item index when loading new results.
     */
    useEffect(() => {
        if(props.isLoading) {
            setSelectedItemIdx(-1);
        }
    });
    /**
     * On dropdown container focus, the textarea gets focused so user can start typing immediately.
     * @param el
     */
    const handleSearchDropdownFocus = ( el ) => {
        if ( 'closed' === searchResultsFieldStatus ) {
            const textarea = el.currentTarget.querySelector( 'textarea' );

            if ( 'undefined' !== typeof textarea && textarea !== null ) {
                textarea.tabIndex = '-1';
                textarea.focus();
            }
        }
    }

    const handleSearchDropdownClick = ( el ) => {

    }

    /**
     * On keydown sets the value of textarea.
     * @param el
     */
    const handleSearchDropdownKeyDown = ( el ) => {
        setTextareaValue( el.currentTarget.value )
        
        // Handle ArrowDown
        if ( el.key === 'ArrowDown' && selectedItemIdx < searchResultsItems.length - 1 ) {
            el.preventDefault();
            const searchResult = el.currentTarget.querySelector( `.sui-search-dropdown` );
            if ( searchResult ) {
                searchResult.focus();
                const selected = selectedItemIdx + 1;
                setSelectedItemIdx( selected );
                
                setDropdownMarkup( printItems( selected ) );
            }
        }

        // Handle ArrowUp
        if ( el.key === 'ArrowUp' && selectedItemIdx >= -1 ) {
            el.preventDefault();
            const searchResult = el.currentTarget.querySelector( `.sui-search-dropdown` );
            if ( searchResult ) {
                searchResult.focus();
                const selected = selectedItemIdx - 1;
                setSelectedItemIdx( selected );

                setDropdownMarkup( printItems( selected ) );
            }
        }

        // Handle Home key - select first item
        if ( el.key === 'Home' && searchResultsItems.length > 0 ) {
            el.preventDefault();
            setSelectedItemIdx( 0 );
            setDropdownMarkup( printItems( 0 ) );
        }

        // Handle End key - select last item
        if ( el.key === 'End' && searchResultsItems.length > 0 ) {
            el.preventDefault();
            const lastIdx = searchResultsItems.length - 1;
            setSelectedItemIdx( lastIdx );
            setDropdownMarkup( printItems( lastIdx ) );
        }

        // Handle Enter
        if ( el.key === 'Enter' && selectedItemIdx >= 0 ) {
            el.preventDefault();
            const selectedItem = searchResultsItems[ selectedItemIdx ];
            if ( selectedItem && typeof selectedItem.onClick === 'function' ) {
                selectedItem.onClick();

                clearDropdown();
                setSearchResultsFieldStatus( 'closed' );
                el.currentTarget.querySelector( 'textarea' ).blur();
                el.currentTarget.blur();
                return;
            }
        }

        // Handle Escape - close dropdown
        if ( el.key === 'Escape' ) {
            el.preventDefault();
            clearDropdown();
            setSearchResultsFieldStatus( 'closed' );
            const textarea = el.currentTarget.querySelector( 'textarea' );
            if ( textarea ) {
                textarea.blur();
            }
            return;
        }

        // Don't open dropdown on Tab or other non-printing keys
        if ( el.key !== 'Tab' && el.key !== 'Shift' && el.key !== 'Control' && 
             el.key !== 'Alt' && el.key !== 'Meta' && el.key !== 'CapsLock' ) {
            setSearchResultsFieldStatus( 'open' );
        }
    }

    /**
     * Expands results area upon textarea focus.
     * @param el
     */
    const handleTextareaFocus = ( el ) => {
        setSearchResultsFieldStatus( 'open' );
    }

    const handleSearchDropdownBlur = ( el ) => {
        const blurTimer = setInterval(function() {
            const resultsWrapperId = `sui-select-dropdown-wrap-${props.id}`,
                textareaId = `search-dropdown-textbox-${props.id}`,
                rendererWrapId = `blc-search-dropdown__renderer_wrap-${props.id}`;

            if (
                resultsWrapperId !== document.activeElement.id &&
                textareaId !== document.activeElement.id &&
                rendererWrapId !== document.activeElement.id
            ) {
                setSearchResultsFieldStatus( 'closed' );
                setTextareaValue( '' );
                setSelectedItemIdx( -1 );
            }

            clearInterval( blurTimer );
        }, 160);
    }

    const handleResultsContainerClick = ( el ) => {
        el.currentTarget.tabIndex = '-1';
        el.currentTarget.focus();
    }

    const clearDropdown = () => {
        setSearchResultsFieldStatus( 'closed' );
        setTextareaValue( '' );
        setSelectedItemIdx( -1 );
        //props.searchResults = [];
        setDropdownMarkup( '');
        //props.showDefaultMessage = true;
        //props.showNoResultsMessage = false;
        setDropdownMarkup( getDefaultMessage() );
        setReset( true );
    }

    const printItems = ( selected ) => {
        if ( typeof searchResultsItems !== 'undefined' && searchResultsItems.length > 0 ) {
            let selectedItem;
            if (props.isLoading) {
                selectedItem = -1;
            } else {
                selectedItem = selected;
            }
            return (
                searchResultsItems.map(
                    (listItem, index) => (
                        ( typeof listItem !== 'undefined' && listItem.hasOwnProperty( 'key' ) ) &&
                        <li
                            key={`${props.id}-${listItem.key}`}
                            id={`${props.id}-option-${index}`}
                            className={`select2-results__option select2-results__option--selectable${selectedItem === index ? ' select2-results__option--selected' : ''}`}
                            onClick={() => {
                                listItem.onClick();
                                clearDropdown();
                            } }
                            onKeyDown={(e) => {
                                if ( e.key === 'Enter' || e.key === ' ' ) {
                                    e.preventDefault();
                                    listItem.onClick();
                                    clearDropdown();
                                }
                            }}
                            role="option"
                            aria-selected={selectedItem === index ? 'true' : 'false'}
                        >
                            {listItem.display}
                        </li>
                    )
                )
            )
        }
    }

    const getDefaultMessage = () => {
        return <div className="select2-results__option select2-results__message">
            {props.defaultMessage}
        </div>;
    }

    const getNoResultsMessage = () => {
        return <div className="select2-results__option select2-results__message">
            {props.noResultsMessage}
        </div>;
    }

    const dropdownContent = () => {
        if ( props.showDefaultMessage && props.defaultMessage ) {
            return getDefaultMessage();
        } else if ( props.showNoResultsMessage && props.noResultsMessage ) {
            return getNoResultsMessage();
        }
        return printItems(selectedItemIdx);
    }

    let searchStatus;
    if (props.isLoading) {
        searchStatus = __('Loading results', 'broken-link-checker');
    } else if (props.showNoResultsMessage && props.noResultsMessage){
        searchStatus = props.noResultsMessage;
    } else if (props.showDefaultMessage && props.defaultMessage) {
        searchStatus = props.defaultMessage;
    } else {
        searchStatus = `${searchResultsItems.length} ${searchResultsItems.length === 1 ? 'result' : 'results'} available`;
    }

    return(
        <div
            ref={props.containerRef}
            className={`blc-search-dropdown-container blc-search-dropdown-container-${searchResultsFieldStatus}`}
            id={`blc-search-dropdown-container-${props.id}`}
            onFocus={(e)=> { handleSearchDropdownFocus(e) }}
            onClick={(e)=> { handleSearchDropdownClick(e) }}
            onKeyDown={(e)=> { handleSearchDropdownKeyDown(e) }}
            onBlur={(e)=> { handleSearchDropdownBlur(e) }}
        >
            <select
                multiple="" id={props.id}
                data-placeholder={props.placeholder}
                data-theme="search"
                data-search="true"
                aria-required="true"
                aria-labelledby={`${props.id}-label`}
                className="sui-select select2-hidden-accessible sui-screen-reader-text"
                tabIndex="-1"
                aria-hidden="true"
                data-select2-id={`select2-data-${props.id}`}>
            </select>

            <span
                className="select2 select2-container sui-select sui-select-theme--search sui-select-dropdown-container--below sui-select-dropdown-container--above"
                dir="ltr"
                data-select2-id="select2-data-43-c80h"
            >
                <span className="selection">
                    <span
                        className="select2-selection select2-selection--multiple"
                        id={`blc-search-dropdown__renderer_wrap-${props.id}`}
                        role="combobox"
                        aria-haspopup="listbox"
                        aria-expanded={searchResultsFieldStatus === 'open' ? 'true' : 'false'}
                        aria-controls={`select2-${props.id}-results1`}
                        tabIndex="-1"
                        aria-disabled="false"
                    >
                        <ul
                            className="select2-selection__rendered"
                            id={`select2-${props.id}-container`}
                        >
                        </ul>
                            <span className="select2-search select2-search--inline">
                                <textarea
                                    id={`search-dropdown-textbox-${props.id}`}
                                    className="select2-search__field"
                                    type="search"
                                    tabIndex="0"
                                    autoCorrect="off"
                                    autoCapitalize="none"
                                    spellCheck="false"
                                    role="searchbox"
                                    aria-autocomplete="list"
                                    autoComplete="off"
                                    aria-label={props.ariaLabel}
                                    aria-required={props.required ? 'true' : 'false'}
                                    aria-activedescendant={selectedItemIdx >= 0 ? `${props.id}-option-${selectedItemIdx}` : ''}
                                    placeholder={props.placeholder}
                                    onKeyDown={props.onKeyDown}
                                    onClick={props.onClick}
                                    onChange={props.onChange}
                                    onFocus={(e)=>handleTextareaFocus(e)}
                                    value={textareaValue}
                                >
                                </textarea>
                            </span>
                        </span>
                    </span>
                <span className="dropdown-wrapper" aria-hidden="true"></span>
            </span>

            <div
                id={`sui-select-dropdown-wrap-${props.id}`}
                onClick={(e)=>{ handleResultsContainerClick(e) }}
            >
            <span
                className={`select2-container sui-select sui-select-theme--search sui-select-dropdown-container--${searchResultsFieldStatus} select2-container--${searchResultsFieldStatus}`}
            >
                <span
                    className="sui-select-dropdown sui-search-dropdown sui-select-dropdown--above"
                    dir="ltr"
                >
                    <span className="select2-results">
                        <span 
                            role="status"
                            aria-live="polite"
                            className="sui-screen-reader-text">
                            {searchStatus}
                            </span>
                        {( props.showDefaultMessage || props.showNoResultsMessage ) ? (
                            <div className="select2-results__options" style={{display: 'flex'}}>
                            {dropdownMarkup}
                            </div>
                        ) : (
                            <ul
                                className="select2-results__options"
                                role="listbox"
                                aria-multiselectable="true"
                                id={`select2-${props.id}-results1`}
                                aria-label={props.ariaLabel || props.placeholder}
                                aria-hidden={searchResultsFieldStatus === 'closed' ? 'true' : 'false'}
                            >
                                {dropdownMarkup}
                            </ul>
                        )}
                    </span>
                </span>
            </span>
            </div>

        </div>
    )
}
