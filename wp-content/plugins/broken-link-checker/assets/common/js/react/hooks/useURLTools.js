const useGetQueryVar = ( queryVarName, url ) => {
    if ( ! url ) {
        return false;
    }

    queryVarName = queryVarName.replace(/[\[\]]/g, "\\$&");

    var regex = new RegExp(queryVarName + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);

    if (!results) return false;
    if (!results[2]) return false;

    return decodeURIComponent( results[2].replace(/\+/g, " ") );
}

// Removes the given parameter from browser's address bar url.
// Copied from https://stackoverflow.com/a/1634841
const useRemoveParamFromURL = ( parameter, url ) => {
    var urlparts = url.split('?');
    if (urlparts.length >= 2) {

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i = pars.length; i-- > 0;) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }

        let newUrl = urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');

        if ( 0 < newUrl.length ) {
            window.history.replaceState(history.state, document.title, newUrl);
        }
    }
}

export {useGetQueryVar, useRemoveParamFromURL}
