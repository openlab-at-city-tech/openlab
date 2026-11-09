<template>
    <div class="trp-bulk-actions" >
        <select name="action" id="bulk-action-selector-top" v-model="actionToApply">
            <option v-for="(action, action_key) in defaultActions.bulk_actions" :value="action_key">
                {{action.name}}
            </option>
        </select>
        <input type="submit" class="button" :value="stEditorStrings.apply" @click="applyAction(actionToApply, tableControls.checkedStrings)">
    </div>
</template>

<script>
    import axios from "axios"
    import he    from "he"

    export default {
        props   : [
            'defaultActions',
            'currentTab',
            'stEditorStrings',
            'tableControls',
            'ajaxUrl',
            'listenForEvents',
            'dictionary'
        ],
        data() {
            return {
                actionToApply : 'trp_default',
            }
        },
        created(){
            if ( this.listenForEvents ){
                document.addEventListener( 'trp_trigger_perform_action_event', this.applyIndividualAction )
            }
        },
        methods : {
            applyIndividualAction( data ){
                this.applyAction( data.detail.action, [ data.detail.stringIndex ] )
            },
            applyAction( action, strings ) {
                let self = this
                if ( this.defaultActions.bulk_actions.hasOwnProperty( action ) &&
                    action !== 'trp_default' &&
                    strings.length >= 1
                ){
                    // The warning strings come from esc_html__() on the PHP side, so chars like
                    // apostrophes/quotes arrive HTML-encoded (e.g. &#039;). Native prompt()/alert()
                    // dialogs render plain text, not HTML, so decode the entities back to real
                    // characters here — without altering the underlying translation.
                    let message = he.decode(
                        this.stEditorStrings[ action + "_warning" ] + " \n \n" +
                        ( (this.tableControls.selectAllOrVisible === '' ) ? '' : this.stEditorStrings[ this.tableControls.selectAllOrVisible + "_warning" ] + " \n \n ") +
                        this.stEditorStrings.type_a_word_for_security + " " + action
                    )

                    let wordTyped = prompt( message, '' )

                    if ( wordTyped === action ){
                        let selectedStringsDetails = []
                        let stringType
                        strings.forEach( function (stringIndex) {
                            selectedStringsDetails.push(self.dictionary[stringIndex])
                            stringType = self.dictionary[stringIndex].type
                        })

                        let data = new FormData()
                        data.append( 'action', 'trp_string_translation_' + action + '_' + stringType )
                        data.append( 'strings', JSON.stringify( selectedStringsDetails ) )
                        data.append( 'select_all_or_visible', this.tableControls.selectAllOrVisible )
                        data.append( 'query', JSON.stringify( this.$route.query ) )
                        data.append( 'security', this.defaultActions.bulk_actions[action].nonce )

                        axios.post( this.ajaxUrl, data )
                             .then( function ( response ) {
                                 if ( response != null ){
                                     if ( response ){
                                        alert( he.decode( self.stEditorStrings[ 'entries_deleted' ].replace("%d", parseInt( response.data ) ) ) )
                                        window.location.reload()
                                     }
                                 }
                             } )
                             .catch( function ( error ) {
                                 console.log( error )
                             } )
                    } else {
                        alert( he.decode( this.stEditorStrings.incorect_word_typed ) )
                    }
                }
            }
        }
    }
</script>