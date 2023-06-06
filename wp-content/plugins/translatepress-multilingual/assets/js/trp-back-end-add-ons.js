/*
 * Script used in Settings-> Add-ons Page
 *
 * It sends request to install and activate recommended plugins
 */

function TRP_Plugins_Installer() {
    var _this = this

    function ajax_request( pluginSlug, element ) {
        var request = new XMLHttpRequest()
        request.open( 'POST', trp_addons_localized[ 'admin_ajax_url' ], true )
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8')
        request.onload = function () {
            if ( this.status >= 200 && this.status < 400 ){
                var data = JSON.parse( this.response )
                element.innerHTML = data
                element.setAttribute('disabled', true )
                location.reload();
            }
        }

        request.send(  encodeURI('security=' + trp_addons_localized[ 'nonce' ] + '&action=trp_install_plugins&plugin_slug=' + pluginSlug))
    }

    function triggerUpdate( event ) {
        var pluginSlug      = event.target.getAttribute( 'data-trp-plugin-slug' )
        var actionPerformed = event.target.getAttribute( 'data-trp-action-performed' )

        event.target.removeEventListener( 'click', triggerUpdate )
        event.target.innerHTML = actionPerformed

        ajax_request( pluginSlug, event.target )

    }

    function init( selector ) {
        document.querySelectorAll( selector ).forEach( item => {
            item.addEventListener( 'click', triggerUpdate )
        } )
    }

    return {
        init : init
    }
}

var trp_plugin_installer = new TRP_Plugins_Installer()
trp_plugin_installer.init( '.trp-install-and-activate' )
