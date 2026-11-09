function removeUrlParameter( url, parameter ) {
    let parts = url.split( '?' )

    if ( parts.length >= 2 ) {

        let prefix = encodeURIComponent( parameter ) + '='
        let pairs = parts[1].split( /[&;]/g )

        //reverse iteration as may be destructive
        for ( let i = pairs.length; i-- > 0; ) {
            //idiom for string.startsWith
            if ( pairs[i].lastIndexOf(prefix, 0) !== -1 ) {
                pairs.splice(i, 1)
            }
        }

        url = parts[0] + ( pairs.length > 0 ? '?' + pairs.join('&') : "" )

        return url

    } else {
        return url
    }
}

/**
 * Escapes HTML-special characters so the given string is safe to inject as HTML.
 *
 * @param string
 */
function escapeHtml( string ){
    if ( string === null || string === undefined )
        return ""

    return String( string )
        .replace( /&/g, '&amp;' )
        .replace( /</g, '&lt;' )
        .replace( />/g, '&gt;' )
        .replace( /"/g, '&quot;' )
        .replace( /'/g, '&#039;' )
}

/**
 * Decodes HTML entities into their plain-text representation.
 *
 * WARNING: the returned value is plain text that may contain characters such as
 * '<' or '>'. It must only ever be inserted into the DOM as text (textContent /
 * document.createTextNode) and must NEVER be parsed as HTML (e.g. jQuery( str ),
 * innerHTML), otherwise it becomes an XSS sink.
 *
 * @param string
 */
function decodeEntities( string ){
    let doc = new DOMParser().parseFromString( string, 'text/html' )

    return doc.body.textContent || ""
}


function getFilename( url ){
    if ( url )
        return url.substring( url.lastIndexOf( "/" ) + 1 )

    return url
}

function unwrap( wrapper ) {
    let docFrag = document.createDocumentFragment();

    while (wrapper.firstChild) {
        let child = wrapper.removeChild( wrapper.firstChild );
        docFrag.appendChild( child );
    }

    wrapper.parentNode.replaceChild( docFrag, wrapper );
}

function arrayContainsItem( array, item ){
    let i
    let length = array.length
    for ( i = length -1; i >= 0; i-- ){
        if ( array[i] === item ){
            return true
        }
    }
    return false
}

//Adds or updates an existing query parameter in an url
function updateUrlParameter(uri, key, value) {
    let regex = new RegExp("([?&])" + key + "=.*?(&|#|$)", "i")

    if ( uri.match(regex) )
        return uri.replace(regex, '$1' + key + "=" + value + '$2')
    else {
        let hash = ''

        if( uri.indexOf('#') !== -1 ){
            hash = uri.replace(/.*#/, '#')
            uri = uri.replace(/#.*/, '')
        }

        let separator = uri.indexOf('?') !== -1 ? "&" : "?"

        return uri + separator + key + "=" + value + hash
    }
}

//Given an arbitrary URL, returns an array with the URL parameters
function getUrlParameters( url ){
    let query = url.split('?')

    if( !query[1] )
        return null

    let vars = query[1].split('&'), query_string = {}, i

    for ( i = 0; i < vars.length; i++ ) {
        let pair  = vars[i].split('='),
            key   = decodeURIComponent(pair[0]),
            value = decodeURIComponent(pair[1])

        if ( typeof query_string[key] === 'undefined' )
            query_string[key] = decodeURIComponent(value)
        else if ( typeof query_string[key] === 'undefined' )
            query_string[key] = [ query_string[key], decodeURIComponent(value) ]
        else
            query_string[key].push(decodeURIComponent(value) )
    }

    return query_string
}

//Given a string, returns true if it is a URL
function isURL( string ) {
    let url;

    try {
        url = new URL(string);
    } catch (_) {
        return false;
    }

    return url.protocol === "http:" || url.protocol === "https:";
}

/**
 * Used to determine which icon to use based on string translation status
 *
 * @param status
 *
 */
function getIconBasedOnStatus( status ){
    let iconHtml;

    switch ( status ){
        case "2":
            iconHtml = "<svg class='trp-manual-or-human-translation-icon' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='25' height='25' aria-hidden='true' focusable='false'><path d='M15.5 9.5a1 1 0 100-2 1 1 0 000 2zm0 1.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5zm-2.25 6v-2a2.75 2.75 0 00-2.75-2.75h-4A2.75 2.75 0 003.75 15v2h1.5v-2c0-.69.56-1.25 1.25-1.25h4c.69 0 1.25.56 1.25 1.25v2h1.5zm7-2v2h-1.5v-2c0-.69-.56-1.25-1.25-1.25H15v-1.5h2.5A2.75 2.75 0 0120.25 15zM9.5 8.5a1 1 0 11-2 0 1 1 0 012 0zm1.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'></path></svg>";
        break;

        case "1":
            iconHtml = "<svg class='trp-manual-or-human-translation-icon' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='25' height='25' aria-hidden='true' focusable='false'><path d='M17.3 10.1c0-2.5-2.1-4.4-4.8-4.4-2.2 0-4.1 1.4-4.6 3.3h-.2C5.7 9 4 10.7 4 12.8c0 2.1 1.7 3.8 3.7 3.8h9c1.8 0 3.2-1.5 3.2-3.3.1-1.6-1.1-2.9-2.6-3.2zm-.5 5.1h-4v-2.4L14 14l1-1-3-3-3 3 1 1 1.2-1.2v2.4H7.7c-1.2 0-2.2-1.1-2.2-2.3s1-2.4 2.2-2.4H9l.3-1.1c.4-1.3 1.7-2.2 3.2-2.2 1.8 0 3.3 1.3 3.3 2.9v1.3l1.3.2c.8.1 1.4.9 1.4 1.8 0 1-.8 1.8-1.7 1.8z\'></path></svg>";
        break;

        case "4":
            iconHtml = "<span class='dashicons dashicons-translation trp-manual-or-human-translation-icon trp-language-file-translation-icon' aria-hidden='true'></span>";
        break;

        default:
            iconHtml = '';
        break;
    }

    return iconHtml;
}

/**
 * Check if the given language code is of an English language
 *
 * @param languageCode
 *
 */
function isEnglishLanguage( languageCode ){
    return /^en_/.test( languageCode );
}

export default {
    removeUrlParameter,
    updateUrlParameter,
    getUrlParameters,
    escapeHtml,
    decodeEntities,
    getFilename,
    arrayContainsItem,
    unwrap,
    isURL,
    getIconBasedOnStatus,
    isEnglishLanguage
}
