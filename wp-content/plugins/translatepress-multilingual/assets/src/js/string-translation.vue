<template>
    <div id="trp-editor" class="wp-core-ui">

        <div id="trp-controls">

            <div id="trp-close-save">
                <div class="trp-button-container-close">
                    <span class="trp-tooltip-toggle trp-tooltip-toggle-current-page" :data-tooltip="editorStrings.close">
                        <a id="trp-controls-close" :href="closeURL"><svg id="trp-close-symbol" xmlns="http://www.w3.org/2000/svg" border=" 1px solid #FFFFFF" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path></svg></a>
                    </span>
                </div>

                <div id="trp-save-and-loader-spinner" class="trp-button-container-save">
                    <span class="trp-ajax-loader" v-show="loading_strings > 0" id="trp-string-saved-ajax-loader">
                        <div class="trp-spinner"></div>
                    </span>
                    <save-translations
                            :selectedIndexesArray="selectedIndexesArray"
                            :dictionary="dictionary"
                            :settings="settings"
                            :nonces="nonces"
                            :ajax_url="ajaxUrl"
                            :currentLanguage="currentLanguage"
                            :onScreenLanguage="onScreenLanguage"
                            :currentURL="currentURL"
                            @translations-saved="showChangesUnsavedMessage = false"
                            :editorStrings="editorStrings"
                            :stringTypes="stringTypes"
                            :userMeta="userMeta"
                    >
                    </save-translations>
                </div>
            </div>

            <div class="trp-controls-container" id="trp-controls-container-string-translation" :class="{'trp-show-editors-navigation' : editorsNavigation.show }">
              <editors-navigation :editorsNavigation="editorsNavigation"
                                  :selectedTab="'stringtranslation'"></editors-navigation>
                <div class="trp-controls-section-string-translation" id="trp-controls-section-first" :style= "[{'border-bottom': $route.matched[ 0 ]?.props.default.currentTab.type === 'upsale-slugs' ? 'none !important;' : '1px solid #CCCCCC'}]">

                    <div class="trp-controls-section-content" id="trp-controls-section-content-string-translation">

                        <div id="trp-next-previous" class="trp-next-previous-string-translation">
                            <div class="trp-button-container">
                                <span class="trp-tooltip-toggle trp-tooltip-toggle-previous-tooltip" :data-tooltip="editorStrings.previous_title_attr">
                                <a href="#" type="button" v-if="$route.matched[ 0 ]?.props.default.currentTab.type !== 'upsale-slugs'" id="trp-previous" class="trp-next-previous-buttons" v-on:click.prevent="previousString()"><span><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width='25' height="25" aria-hidden="true" focusable="false"><path d="M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"></path></svg></span></span> {{ editorStrings.previous }}</a>
                                </span>
                            </div>
                            <div class="trp-button-container">
                                <span class="trp-tooltip-toggle trp-tooltip-toggle-next-tooltip" :data-tooltip="editorStrings.next_title_attr">
                                <a href="#" type="button" v-if="$route.matched[ 0 ]?.props.default.currentTab.type !== 'upsale-slugs'" id="trp-next" class="trp-next-previous-buttons" v-on:click.prevent="nextString()">{{ editorStrings.next }} <span><span style="z-index: 20"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="25" height="25" aria-hidden="true" focusable="false"><path d="M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"></path></svg></span></span></a>
                                </span>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="trp-controls-section" v-show="selectedIndexesArray.length > 0">
                    <language-boxes
                            :selectedIndexesArray="selectedIndexesArray"
                            :dictionary="dictionary"
                            :currentLanguage="currentLanguage"
                            :onScreenLanguage="onScreenLanguage"
                            :languageNames="languageNames"
                            :settings="settings"
                            :showChangesUnsavedMessage="showChangesUnsavedMessage"
                            @discarded-changes="hasUnsavedChanges()"
                            :editorStrings="editorStrings"
                            :flagsPath="flagsPath"
                            :flags-file-name="flagsFileName"
                            :nonces="nonces"
                            :ajax_url="ajaxUrl"
                            :userMeta="userMeta"
                    >
                    </language-boxes>
                </div>

                <extra-content :languageNames="languageNames" :editorStrings="editorStrings"
                               :paidVersion="paidVersion" class="trp-upsell-string-translation" :blackFriday="blackFriday" :licenseStatus="licenseStatus"></extra-content>

                <div class="trp-controls-section" v-show="translationNotLoadedYet">
                    <div id="trp-translation-not-ready-section" class="trp-controls-section-content">
                        <p v-html="editorStrings.translation_not_loaded_yet"></p>
                    </div>
                </div>
            </div>

        </div>

        <div id="trp-preview" class="trp-string-translation-container" @click="hasUnsavedChanges()">
            <div class="navigation-tab-wrapper">
                <router-link
                        v-for="(tab, tab_slug) in stringTypesConfig"
                        v-bind:key="'trp-tab-key-' + tab_slug"
                        :to="(tab['category_based'] )  ? '/'+ tab_slug + '/': '/'+ tab_slug + '/'"
                        :id="'trp-tab-' + tab_slug"
                        class="navigation-tab"
                        active-class="navigation-tab-active"
                >
                    {{tab['tab_name']}}
                </router-link>
                    <!--                    <router-link to="/import-export" class="nav-tab">-->
                    <!--                        {{stEditorStrings.importexport}}-->
                    <!--                    </router-link>-->
            </div>
            <div class="trp-string-translation-inner-container" :class="{'trp-screen-overlay' : showChangesUnsavedMessage }">
                <router-view
                        :dictionary="dictionary"
                        :totalItems="totalItems"
                >
                </router-view>
            </div>
        </div>
    </div>
</template>

<script>
    import 'select2/dist/js/select2.min.js'
    import utils             from './utils'
    import axios             from 'axios'
    import languageBoxes     from './components/language-boxes.vue'
    import saveTranslations  from './components/save-translations.vue'
    import hoverActions      from './components/hover-actions.vue'
    import extraContent      from './components/extra-content.vue'
    import editorsNavigation from './components/editors-navigation.vue'
    import he                from 'he'
    import Tooltip           from "./components/tooltip"

    export default {
        components : {
            Tooltip,
            languageBoxes,
            saveTranslations,
            hoverActions,
            extraContent,
            editorsNavigation
        },
        data() {
            return {
                //trp_editor_data
                settings                  : trp_editor_data.trp_settings,
                languageNames             : trp_editor_data.language_names,
                orderedSecondaryLanguages : trp_editor_data.ordered_secondary_languages,
                nonces                    : trp_editor_data.editor_nonces,
                editorsNavigation         : trp_editor_data.editors_navigation,
                editorStrings             : trp_editor_data.trp_localized_strings,
                stringTypes               : trp_editor_data.string_types,
                flagsPath                 : trp_editor_data.flags_path,
                flagsFileName             : trp_editor_data.flags_file_name,
                currentLanguage           : trp_editor_data.current_language,
                onScreenLanguage          : trp_editor_data.on_screen_language,
                ajaxUrl                   : trp_editor_data.ajax_url,
                currentURL                : trp_editor_data.url_to_load,
                paidVersion               : trp_editor_data.paid_version,
                blackFriday               : trp_editor_data.black_friday,
                licenseStatus             : trp_editor_data.trp_license_status,
                userMeta                  : trp_editor_data.user_meta,

                //trp_string_translation_data
                stringTypesConfig : trp_string_translation_data.string_types_config,
                stEditorStrings   : trp_string_translation_data.st_editor_strings,

                dictionary                : {},
                selectedString            : null,
                selectedIndexesArray      : [],
                detectedSelectorAndId     : [],
                stringGroups              : [],
                showChangesUnsavedMessage : false,
                loading_strings           : 0,
                translationNotLoadedYet   : false,
                totalItems                : null,
                gettextOriginalIds        : {},
                gettextRequestsLeft       : 0,
                upgradedGettext           : trp_editor_data.upgraded_gettext,
                noticeUpgradeSlugs        : trp_editor_data.notice_upgrade_slugs,
            }
        },
        created() {
            this.settings[ 'default-language-name' ] = this.languageNames[ this.settings[ 'default-language' ] ]

            this.currentLanguage = this.settings[ 'default-language' ]

            document.addEventListener( 'trp_trigger_perform_action_event', this.editString )
        },
        mounted() {
            this.getStrings( this.$route.query )

            this.addKeyboardShortcutsListener()
            document.addEventListener( 'trp_update_user_meta',  this.updateUserMeta )
            document.addEventListener( 'trp_trigger_get_missing_gettext', this.getMissingGettextTranslations )
            let self = this
            // resize sidebar and consequently the iframe
            let previewContainer = jQuery( '#trp-preview' )
            let total_width      = jQuery( window ).width()
            jQuery( '#trp-controls' ).resizable({
                start    : function () {
                    previewContainer.toggle()
                },
                stop     : function () {
                    previewContainer.toggle()
                },
                handles  : 'e',
                minWidth : 327,
                maxWidth : total_width - 20
            })

        },
        watch      : {
            $route( to, from ) {
                this.getStrings( to.query )
            },
            selectedString : function ( newString, oldString ) {
                if ( !this.hasUnsavedChanges() ){
                    if ( this.selectedString === null ){
                        this.selectedIndexesArray = [ ]
                    }else {
                        this.selectedIndexesArray = [ ]

                        let self = this
                        let stringFromDictionary = this.dictionary[this.selectedString]
                        if ( stringFromDictionary.originalPlural ){
                            this.dictionary.forEach( function ( string, index ) {
                                if ( string.originalId === stringFromDictionary.originalId ){
                                    self.selectedIndexesArray.push( index )
                                }
                            } )
                            self.selectedIndexesArray.sort((a,b) => (self.dictionary[a].pluralForm > self.dictionary[b].pluralForm) ? 1 : ((self.dictionary[b].pluralForm > self.dictionary[a].pluralForm) ? -1 : 0))
                        }else{
                            this.selectedIndexesArray = [ this.selectedString ]
                        }

                    }
                }
            },
            dictionary : {
                handler(newVal, oldVal){
                    if ( !this.hasUnsavedChanges() ){
                        this.selectedString = null
                        this.selectedIndexesArray = [ ]
                    }
                },
                deep: false
            },
            gettextRequestsLeft : function( newValue, oldValue ){
                if ( oldValue > 0 && newValue === 0 ){
                    this.getGettextStringsDictionaries()
                }
            }
        },
        computed   : {
            closeURL : function () {
                return this.cleanURL( this.currentURL )
            }
        },
        methods    : {
            getStrings( query ) {
                if ( !this.$route.matched[ 0 ]?.props.default.translationTab ){
                    return
                }

                let self            = this
                let currentTab      = this.$route.matched[ 0 ].props.default.currentTab
                let translationType = this.$route.matched[ 0 ].props.default.translationType
                if ( translationType == 'emails' ){
                    translationType = 'gettext';
                    query.type = 'email'
                }

                if ( ( !this.upgradedGettext && (currentTab.type === 'gettext' || currentTab.type === 'emails' ) ) || currentTab.type === 'upsale-slugs' || ( !currentTab.type && this.noticeUpgradeSlugs ) ){
                    return
                }

                let data = new FormData()
                data.append( 'action', 'trp_string_translation_get_strings_' + translationType )
                data.append( 'query', JSON.stringify( query ) )
                data.append( 'security', currentTab[ 'nonces' ][ 'get_strings' ] )

                window.dispatchEvent( new Event( 'trp_trigger_show_loading_table_event' ) )

                axios.post( this.ajaxUrl, data )
                     .then( function ( response ) {

                         if ( response != null ){
                             self.onScreenLanguage = (query.language && utils.arrayContainsItem( self.settings[ 'translation-languages' ], query.language ) && query.language !== 'trp-default') ?
                                 query.language : ''
                             self.currentLanguage  = (query.language && utils.arrayContainsItem( self.settings[ 'translation-languages' ], query.language ) && query.language !== 'trp-default') ?
                                 query.language : self.settings[ 'default-language' ]

                             if ( translationType === 'gettext' && response.data.totalItems > 0 ){
                                 if ( response.data.originalIds ){
                                     self.gettextOriginalIds = response.data.originalIds
                                     document.dispatchEvent( new Event( 'trp_trigger_get_missing_gettext' ) )
                                 }
                             }else{
                                 let newDictionary = (response.data.dictionary) ? response.data.dictionary : {};
                                 if ( self.hasUnsavedChanges() ){
                                     // copy the unsaved string from the current dictionary to the new dictionary to allow user to discard or save changes
                                     let newSelectedIndexesArray = []
                                     self.selectedIndexesArray.forEach( function ( item ) {
                                         self.dictionary[ item ].unsavedChanges = 'yes'
                                         newSelectedIndexesArray.push( newDictionary.push( self.dictionary[ item ] ) - 1 )
                                     } )
                                     self.selectedIndexesArray = newSelectedIndexesArray
                                     self.selectedString       = null
                                 }

                                 self.dictionary = newDictionary
                                 window.dispatchEvent( new Event( 'trp_trigger_hide_loading_table_event' ) )
                             }
                             if ( response.data.totalItems || response.data.totalItems === 0 ){
                                 self.totalItems = response.data.totalItems
                             }
                         }
                     } )
                     .catch( function ( error ) {
                         window.dispatchEvent( new Event( 'trp_trigger_hide_loading_table_event' ) )
                         self.dictionary = {}

                         console.log( error )
                         let reload = confirm( self.stEditorStrings.request_error )
                         if( reload === false ){
                             return
                         }else{
                             window.location.reload();
                         }

                     } )
            },
            getMissingGettextTranslations(){

                let self = this
                let currentTab      = this.$route.matched[ 0 ].props.default.currentTab
                if ( currentTab.type === 'gettext' ){
                    self.settings[ 'translation-languages' ].forEach( function ( languageCode ) {
                        self.gettextRequestsLeft++
                        let data = new FormData()
                        data.append( 'action', 'trp_string_translation_get_missing_gettext_strings' )
                        data.append( 'original_ids', JSON.stringify( self.gettextOriginalIds ) )
                        data.append( 'trp_ajax_language', languageCode )
                        data.append( 'security', currentTab[ 'nonces' ][ 'get_missing_strings' ] )
                        axios.post( self.ajaxUrl, data )
                             .then( function ( response ) {
                                 self.gettextRequestsLeft--
                             } )
                             .catch( function ( error ) {
                                 self.gettextRequestsLeft--
                                 console.log( error )

                             } )

                    } )
                }
            },
            getGettextStringsDictionaries(){
                let self = this
                let currentTab      = this.$route.matched[ 0 ].props.default.currentTab
                if ( currentTab.type === 'gettext' ){
                    let data = new FormData()
                    data.append( 'action', 'trp_string_translation_get_strings_by_original_ids_gettext' )
                    data.append( 'original_ids', JSON.stringify( self.gettextOriginalIds ) )
                    data.append( 'security', currentTab[ 'nonces' ][ 'get_strings_by_original_id' ] )
                    axios.post( self.ajaxUrl, data )
                         .then( function ( response ) {
                             if ( response.data.dictionary ){
                                 let newDictionary = response.data.dictionary

                                 if ( self.hasUnsavedChanges() ){
                                     // copy the unsaved string from the current dictionary to the new dictionary to allow user to discard or save changes
                                     let newSelectedIndexesArray = []
                                     self.selectedIndexesArray.forEach( function ( item ) {
                                         self.dictionary[ item ].unsavedChanges = 'yes'
                                         newSelectedIndexesArray.push( newDictionary.push( self.dictionary[ item ] ) - 1 )
                                     } )
                                     self.selectedIndexesArray = newSelectedIndexesArray
                                     self.selectedString       = null
                                 }

                                 self.dictionary = newDictionary
                                 window.dispatchEvent( new Event( 'trp_trigger_hide_loading_table_event' ) )
                             }
                         } )
                         .catch( function ( error ) {
                             window.dispatchEvent( new Event( 'trp_trigger_hide_loading_table_event' ) )
                             self.dictionary = {}

                             console.log( error )
                             let reload = confirm( self.stEditorStrings.request_error )
                             if ( reload === false ){
                                 return
                             } else {
                                 window.location.reload();
                             }

                         } )
                }

            },
            editString( data ) {
                if ( data.detail.action === 'edit' ){
                    this.selectedString = data.detail.stringIndex
                }
            },
            cleanURL( url ) {
                //make removeUrlParameter recursive and only call it once with all the parameters that
                //need to stripped ?
                url = utils.removeUrlParameter( url, 'lang' )
                url = utils.removeUrlParameter( url, 'trp-view-as' )
                url = utils.removeUrlParameter( url, 'trp-view-as-nonce' )
                url = utils.removeUrlParameter( url, 'trp-edit-translation' )
                url = utils.removeUrlParameter( url, 'trp-string-translation' )

                return url
            },
            hasUnsavedChanges() {
                let unsavedChanges = false
                let self           = this
                if ( this.selectedIndexesArray.length > 0 ){
                    this.selectedIndexesArray.forEach( function ( selectedIndex ) {
                        self.settings[ 'translation-languages' ].forEach( function ( languageCode ) {
                            if ( self.dictionary[ selectedIndex ] &&
                                self.dictionary[ selectedIndex ].translationsArray[ languageCode ] &&
                                (self.dictionary[ selectedIndex ].translationsArray[ languageCode ].translated !== self.dictionary[ selectedIndex ].translationsArray[ languageCode ].editedTranslation) ){
                                unsavedChanges = true
                            }
                        } )
                    } )
                }

                if ( unsavedChanges === false ){
                    this.selectedIndexesArray.forEach( function ( selectedIndex ) {
                        if ( self.dictionary.hasOwnProperty(selectedIndex) && self.dictionary[ selectedIndex ].unsavedChanges && self.dictionary[ selectedIndex ].unsavedChanges === 'yes'){
                            self.dictionary[ selectedIndex ].unsavedChanges = 'no'
                        }
                    })
                }

                this.showChangesUnsavedMessage = unsavedChanges
                return unsavedChanges
            },
            iframeLoader( status ) {
                let loader = document.getElementById( 'trp-preview-loader' )

                if ( status == 'show' )
                    loader.style.display = 'flex'
                else if ( status == 'hide' )
                    loader.style.display = 'none'
            },
            previousString() {
                if ( this.dictionary.length === 0 || this.hasUnsavedChanges() )
                    return

                let currentValue = parseInt( this.selectedString ), newValue = this.selectedString

                if( this.selectedString === null){
                    newValue = 0
                }else if ( 0 < currentValue ){
                    newValue = currentValue - 1
                }

                this.selectedString = newValue.toString()
            },
            nextString() {
                if ( this.dictionary.length === 0 || this.hasUnsavedChanges() )
                    return

                let currentValue = parseInt( this.selectedString ), newValue = this.selectedString

                if( this.selectedString === null){
                    newValue = 0
                }else if ( this.dictionary.length > (currentValue + 1) ){
                    newValue = currentValue + 1
                }

                this.selectedString = newValue.toString()

            },
            addKeyboardShortcutsListener() {
                document.addEventListener( "keydown", function ( e ) {
                    if ( (window.navigator.platform.match( "Mac" ) ? e.metaKey : e.ctrlKey) && e.altKey ){
                        // CTRL + ALT + right arrow
                        if ( e.keyCode === 39 ){
                            e.preventDefault()
                            window.dispatchEvent( new Event( 'trp_trigger_next_string_event' ) )
                        } else {
                            // CTRL + ALT + left arrow
                            if ( e.keyCode === 37 ){
                                e.preventDefault()
                                window.dispatchEvent( new Event( 'trp_trigger_previous_string_event' ) )
                            }
                        }
                    }
                }, false )

                window.addEventListener( 'trp_trigger_next_string_event', this.nextString )
                window.addEventListener( 'trp_trigger_previous_string_event', this.previousString )
            },
            updateUserMeta( data ){
                let key = data.detail.userMetaKey
                let value = data.detail.userMetaValue
                this.userMeta[key] = value

                let formData = new FormData()
                formData.append( 'action', 'trp_save_editor_user_meta' )
                formData.append( 'security', this.nonces[ 'trp_editor_user_meta' ] )
                formData.append( 'user_meta', JSON.stringify({[key] : value } ) )
                axios.post(this.ajaxUrl, formData)
                     .catch(function (error) {
                         console.log(error);
                     });
            }
        }
    }
</script>
