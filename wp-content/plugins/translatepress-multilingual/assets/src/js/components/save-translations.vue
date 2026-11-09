<template>
    <div id="trp-save-container">
        <transition>
            <span id="trp-translation-saved" v-show="showTranslationsSavedText">{{ editorStrings.saved }}</span>
        </transition>

        <transition>
            <span id="trp-saving-translation" v-show="showSavingTranslations">{{editorStrings.saving_translation}}</span>
        </transition>

        <span class="trp-button-container" id="trp-button-container-save-button">
            <span class="trp-tooltip-toggle-save-button" :data-tooltip="editorStrings.save_title_attr">
            <button id="trp-save" :disabled="disabledSaveButton || (typeof mergingString === 'undefined' && $route.matched[ 0 ] && $route.matched[ 0 ].props.default.currentTab.type === 'upsale-slugs')" type="submit" class="button-primary trp-save-string"
                    :class="{'trp-highlight-for-panel' : highlightButton}" @click="save">{{ saveButtonText }}</button>
            </span>
        </span>
    </div>
</template>
<script>
import axios   from 'axios'
import Tooltip from "./tooltip"

    export default{
        components : { Tooltip },
        props: [
            'selectedIndexesArray',
            'selectedString',
            'dictionary',
            'settings',
            'nonces',
            'ajax_url',
            'currentLanguage',
            'onScreenLanguage',
            'iframe',
            'currentURL',
            'mergingString',
            'mergeData',
            'editorStrings',
            'stringTypes',
            'userMeta'
        ],
        data(){
            return {
                'saveButtonText'            : this.editorStrings.save_translation,
                'saveStringsRequestsLeft'   : 0,
                'disabledSaveButton'        : false,
                'highlightButton'           : false,
                'showTranslationsSavedText' : false,
                'showSavingTranslations'    : false
            }
        },
        mounted(){
            this.addKeyboardShortcutsListener()

            let self = this;
            window.addEventListener( 'trp_save_translation_help_panel', function(){
                self.highlightButton = true
            } )
            window.addEventListener( 'trp_help_panel_changed', function(){
                self.highlightButton = false
            } )
        },
        watch:{
            saveStringsRequestsLeft : function( newValue, oldValue ){
                if ( newValue > 0 ) {
                    this.showSavingTranslations = true;

                    setTimeout( () => {
                        this.showSavingTranslations = false;
                    }, 500);


                    this.disabledSaveButton = true
                    // this.saveButtonText = this.editorStrings.saving_translation
                }else{
                    this.disabledSaveButton = false
                    this.saveButtonText = this.editorStrings.save_translation

                    this.showTranslationsSaved()
                }
            }
        },
        methods:{
            save(){
                if ( this.mergingString )
                    this.createTranslationBlock()
                else {
                    for ( let type in this.stringTypes ){
                        this.saveStringType( this.stringTypes[type] )
                    }
                }
                if ( this.saveStringsRequestsLeft === 0 ) {
                    // no saving action was triggered
                    this.showTranslationsSaved()
                }
            },
            throwAlertMultipleTypes( occurrencesArray ){
                const multipleTypesNotice = this.editorStrings.multiple_types_alert.replace( '%s%', occurrencesArray.join(', ') );

                alert( multipleTypesNotice );
            },
          changeSavedValuesToResponse( updateIframeData, response ) {
            this.settings['translation-languages'].forEach( function( languageCode  ) {
              if ( updateIframeData[languageCode].length > 0 ) {
                updateIframeData[languageCode].forEach(function( string ) {
                  response.data[languageCode].forEach(function( data ) {

                    if ( string.translationsArray[languageCode].original_id == data.original_id ) {
                        let newTranslationID = data.translation_id;
                        let oldTranslationID = string.translationsArray[languageCode].translation_id;

                        if ( newTranslationID != oldTranslationID ) {
                            string.translationsArray[languageCode].translation_id = newTranslationID;
                        }

                        string.translationsArray[languageCode].translated = data.translated
                    }
                  })
                })
              }
            })
          },
          changeShownValuesToResponse( updateIframeData, response, self ) {
            this.selectedIndexesArray.forEach( function( selectedIndex ){
              self.settings['translation-languages'].forEach( function( languageCode  ) {
                if ( updateIframeData[languageCode].length > 0 ) {
                  response.data[languageCode].forEach(function( data ) {
                    if ( data.hasOwnProperty( 'other_type_occurrences' ) ) self.throwAlertMultipleTypes( data.other_type_occurrences );

                    if (self.dictionary[selectedIndex].translationsArray[languageCode].id == data.id) {
                        let newTranslationID = data.translation_id;
                        let oldTranslationID = self.dictionary[selectedIndex].translationsArray[languageCode].translation_id;

                        if ( newTranslationID != oldTranslationID ) {
                            self.dictionary[selectedIndex].translationsArray[languageCode].translation_id = newTranslationID;
                        }

                        self.dictionary[selectedIndex].translationsArray[languageCode].translated = data.translated
                        self.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation = data.translated
                    }
                  })
                }
              })
            })
          },
            saveStringType( typeSlug ){
                this.saveStringsRequestsLeft++
                let self = this
                let saveData = {}
                let updateIframeData  = {}
                let foundStringsToSave = false

                // construct an array of the necessary information
                this.selectedIndexesArray.forEach( function( selectedIndex ){
                    if ( typeSlug === self.dictionary[selectedIndex].type ) {
                        self.settings['translation-languages'].forEach( function( languageCode  ){
                            saveData[languageCode] = ( saveData[languageCode] ) ? saveData[languageCode] : []
                            updateIframeData[languageCode] = ( updateIframeData[languageCode] ) ? updateIframeData[languageCode] : []

                            if ( self.dictionary[selectedIndex].translationsArray[languageCode] && (self.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation != self.dictionary[selectedIndex].translationsArray[languageCode].translated ) ) {
                                self.dictionary[selectedIndex].translationsArray[languageCode].status = ( self.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation === '' ) ? 0 : 2
                                self.dictionary[selectedIndex].translationsArray[languageCode].translated = self.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation

                                saveData[languageCode].push( self.dictionary[selectedIndex].translationsArray[languageCode] )
                                saveData[languageCode][saveData[languageCode].length - 1 ].original = self.dictionary[selectedIndex].original
                                updateIframeData[languageCode].push( self.dictionary[selectedIndex] )

                                foundStringsToSave = true
                            }
                        })
                    }
                })

                // send request to save strings in database
                if ( foundStringsToSave ) {
                    let data = new FormData()
                        data.append('action', 'trp_save_translations_' + typeSlug)
                        data.append('security', this.nonces['savetranslationsnonce' + typeSlug])
                        data.append('strings', JSON.stringify(saveData))
                        data.append('url', window.location)

                    axios.post(this.ajax_url, data)
                        .then(function (response) {
                            if ( typeSlug === 'gettext' ) {
                                axios.get(self.currentURL).then( function( reloadedIframeResponse) {
                                    self.updateIframe(updateIframeData, reloadedIframeResponse.data)
                                    self.saveStringsRequestsLeft--
                                })
                            }else {
                                if ( Object.keys(response.data).length > 0 )
                                  self.changeSavedValuesToResponse(updateIframeData, response)
                                self.updateIframe(updateIframeData)
                                self.saveStringsRequestsLeft--
                            }
                            if ( Object.keys(response.data).length > 0 )
                              self.changeShownValuesToResponse(updateIframeData, response, self)
                            self.$emit('translations-saved')
                        })
                        .catch(function (error) {
                            console.log(error)
                        });
                }else{
                    self.saveStringsRequestsLeft--
                }
            },
            updateIframe( updateIframeData, reloadedIframeResponse = null ){
                if ( typeof this.iframe === 'undefined' ){
                    return
                }
                let self = this
                this.settings['translation-languages'].forEach( function( languageCode  ){
                    if ( updateIframeData[languageCode].length > 0 ){
                        updateIframeData[languageCode].forEach(function( string ){
                            if ( self.currentLanguage === languageCode ) {
                                self.setTextInIframe( string, languageCode, reloadedIframeResponse )
                            }
                        })
                    }
                })
            },
            setTextInIframe( string, languageCode, reloadedIframeResponse ){
                let nodes = this.iframe.querySelectorAll( "[" + string.selector + "='" + string.dbID + "']" )
                let textToSet = null
                if ( reloadedIframeResponse ){
                    let translatedNode = document.createRange().createContextualFragment(reloadedIframeResponse).querySelector( "[" + string.selector + "='" + string.dbID + "']" )
                    if ( translatedNode ) {
                        textToSet = (typeof string.attribute === 'undefined' || string.attribute === "") ? translatedNode.textContent : translatedNode.getAttribute(string.attribute)
                    }
                }
                if ( textToSet === null ) {
                    textToSet = ( string.translationsArray[languageCode].translated === '' ) ? string.original : string.translationsArray[languageCode].translated
                }

                nodes.forEach(function(node){
                    if (['picture', 'audio', 'video'].includes(node.tagName.toLowerCase())) {
                        // Handle media tags (picture, audio, video)
                        // We don't do anything with these nodes. We ignore them since the actual place the change happens is in their source children.

                        // Video & Audio can contain src on the tag itself, so we check against that in particular
                        let possibleSrc = node.hasAttribute(string.attribute)
                        if (possibleSrc) {
                            node.setAttribute(string.attribute, textToSet);
                        }
                    } else if (typeof string.attribute === 'undefined' || string.attribute === "" || string.attribute === 'innertext') {
                        let initialValue = node.textContent;
                        textToSet = initialValue.replace(initialValue.trim(), textToSet);
                        node.innerHTML = textToSet;
                    } else {
                        let initialValue = node.getAttribute(string.attribute)
                        textToSet = initialValue.replace(initialValue.trim(), textToSet)
                        node.setAttribute(string.attribute, textToSet)
                        if( string.attribute === 'src' ){
                            node.setAttribute('srcset', '');
                        }
                    }
                })
            },
            createTranslationBlock(){
                this.saveStringsRequestsLeft++
                let self = this
                let saveData = {}, translation = {}, original
                let foundStringsToSave = false

                this.selectedIndexesArray.forEach( function( selectedIndex ){
                    self.settings['translation-languages'].forEach( function( languageCode  ){
                        saveData[languageCode] = ( saveData[languageCode] ) ? saveData[languageCode] : []

                        if( self.dictionary[selectedIndex] && self.dictionary[selectedIndex].translationsArray[languageCode] ) {

                            translation = self.dictionary[selectedIndex].translationsArray[languageCode]

                            translation.block_type = self.dictionary[selectedIndex].block_type
                            translation.id         = self.dictionary[selectedIndex].dbID
                            translation.original   = self.dictionary[selectedIndex].original

                            if( self.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation != self.dictionary[selectedIndex].translationsArray[languageCode].translated ) {
                                self.dictionary[selectedIndex].translationsArray[languageCode].translated = self.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation

                                if( self.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation !== '' )
                                    self.dictionary[selectedIndex].translationsArray[languageCode].status = 2
                            }

                            saveData[languageCode].push( translation )


                            foundStringsToSave = true
                        }
                    })

                    original = self.dictionary[selectedIndex].original
                })

                if( foundStringsToSave ) {
                    let data = new FormData()
                        data.append( 'action'       , 'trp_create_translation_block' )
                        data.append( 'security'     , this.nonces['mergetbnonce'] )
                        data.append( 'language'     , this.currentLanguage )
                        data.append( 'strings'      , JSON.stringify( saveData ) )
                        data.append( 'original'     , original )
                        data.append( 'all_languages', 'true' )

                    axios.post(this.ajax_url, data)
                        .then(function (response) {
                            self.saveStringsRequestsLeft--
                            self.$parent.mergingString = false
                            let item = self.dictionary[self.selectedIndexesArray[0]]

                            //update dictionary string ids
                            Object.keys( item.translationsArray ).forEach( function(key) {
                                Object.keys( response.data[key] ).forEach( function(index) {
                                    if ( key === self.onScreenLanguage ){
                                        self.dictionary[self.selectedIndexesArray[0]].dbID = response.data[key][index].id
                                    }
                                    item.translationsArray[key].id = response.data[key][index].id
                                    item.translationsArray[key].translated = response.data[key][index].translated
                                })
                            })

                            self.$parent.mergeData = []

                            //get merged string
                            let mergedString

                            if( typeof item.translationsArray[self.currentLanguage] !== 'undefined' && item.translationsArray[self.currentLanguage].translated )
                                mergedString = item.translationsArray[self.onScreenLanguage].translated
                            else
                                mergedString = item.original

                          //replace HTML in iFrame
                            let translationBlock = self.iframe.querySelector( '.trp-create-translation-block' )
                                translationBlock.innerHTML = mergedString
                                translationBlock.setAttribute( 'data-trp-translate-id', item.dbID )
                                translationBlock.classList.remove( 'trp-create-translation-block' )

                            if ( Object.keys(response.data).length > 0 )
                                Object.keys( item.translationsArray ).forEach( function(key) {
                                  Object.keys( response.data[key] ).forEach( function(index) {
                                    self.dictionary[self.selectedIndexesArray[0]].translationsArray[key].translated = response.data[key][index].translated
                                    self.dictionary[self.selectedIndexesArray[0]].translationsArray[key].editedTranslation = response.data[key][index].translated
                                  })
                                })
                            //setup event listener for new block
                            self.$parent.setupEventListener( translationBlock )
                        })
                        .catch(function (error) {
                            self.$parent.mergingString = false
                            console.log(error)
                        });
                }else{
                    this.saveStringsRequestsLeft--
                }
            },
            showTranslationsSaved : function(){
                setTimeout( () => {
                    this.showTranslationsSavedText = true;
                }, 500);

                setTimeout( () => {
                    this.showTranslationsSavedText = false;
                }, 2500);
            },
            addKeyboardShortcutsListener(){
                document.addEventListener("keydown", function(e) {

                    // CTRL + S
                    if ((window.navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)  && e.keyCode === 83) {
                        e.preventDefault();

                        window.dispatchEvent( new Event( 'trp_trigger_save_translations_event' ) );
                    }
                }, false);

                window.addEventListener( 'trp_trigger_save_translations_event', this.save )

            }
        }
    }
</script>

<style>

.v-enter-active,
.v-leave-active {
    transition: opacity 0.05s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}

</style>
