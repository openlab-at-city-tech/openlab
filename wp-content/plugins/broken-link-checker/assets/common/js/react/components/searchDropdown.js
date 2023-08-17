import React, {useEffect, useState} from 'react';

const { __ } = wp.i18n;

export function SearchDropdown( props ) {
    const [searchResultsFieldStatus, setSearchResultsFieldStatus] = useState( 'closed' );
    const [textareaValue, setTextareaValue] = useState( '' );
    const [dropdownMarkup, setDropdownMarkup] = useState( '' );
    const [reset, setReset] = useState( false );

    let searchResultsItems = [];

    /**
     * Sets the dropdown content when there's a change on any of props.searchResults, props.showDefaultMessage, props.showNoResultsMessage
     */
    useEffect(() => {
        if ( props.searchResults ) {
            searchResultsItems = props.searchResults;
        } else {
            searchResultsItems = [];
        }

        if ( ! props.showDefaultMessage && ! props.showNoResultsMessage ) {
            setDropdownMarkup( printItems() );
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
        setSearchResultsFieldStatus( 'open' );
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
        //props.searchResults = [];
        setDropdownMarkup( '');
        //props.showDefaultMessage = true;
        //props.showNoResultsMessage = false;
        setDropdownMarkup( getDefaultMessage() );
        setReset( true )
    }

    const printItems = () => {
        if ( typeof searchResultsItems !== 'undefined' && searchResultsItems.length > 0 ) {
            return (
                searchResultsItems.map(
                    (listItem) => (
                        ( typeof listItem !== 'undefined' && listItem.hasOwnProperty( 'key' ) ) &&
                        <li
                            key={`${props.id}-${listItem.key}`}
                            className={"select2-results__option select2-results__option--selectable"}
                            onClick={() => {
                                listItem.onClick();
                                clearDropdown();
                            } }
                        >
                            {listItem.display}
                        </li>
                    )
                )
            )
        }
    }

    const getDefaultMessage = () => {
        return <li
            role="alert"
            aria-live="assertive"
            className="select2-results__option select2-results__message"
        >
            {props.defaultMessage}
        </li>;
    }

    const dropdownContent = () => {

        if ( props.showDefaultMessage && props.defaultMessage ) {
            return getDefaultMessage();
        } else if ( props.showNoResultsMessage && props.noResultsMessage ) {
            return <li
                role="alert"
                aria-live="assertive"
                className="select2-results__option select2-results__message"
            >
                {props.noResultsMessage}
            </li>;
        }
        return printItems();
    }


    return(
        <div
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
                        aria-haspopup="true"
                        aria-expanded="false"
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
                                    aria-describedby={`select2-${props.id}-container`}
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
                        <ul
                            className="select2-results__options"
                            role="listbox"
                            aria-multiselectable="true"
                            id={`select2-${props.id}-results1`}
                            aria-expanded="true"
                            aria-hidden="false"
                        >
                            {dropdownMarkup}
                        </ul>
                    </span>
                </span>
            </span>
            </div>

        </div>
    )
}
