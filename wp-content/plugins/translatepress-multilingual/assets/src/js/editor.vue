<template>
    <div id="trp-editor" class="wp-core-ui">

        <div id="trp-controls">

            <div id="trp-close-save">
                <div class="trp-button-container-close">
                      <span class="trp-tooltip-toggle trp-tooltip-toggle-current-page" :data-tooltip="editorStrings.close">
                            <a id="trp-controls-close" :href="closeURL"><svg id="trp-close-symbol" xmlns="http://www.w3.org/2000/svg" border=" 1px solid #FFFFFF" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path></svg></a>
                      </span>
                </div>

                <div class="trp-button-container-help">
                    <a href="#" type="button" class="trp-help-toggle" :title="editorStrings.quick_intro_title_attribute" :class="{'trp-help-toggle-open' : helpPanelOpen, 'trp-help-toggle-never-opened' : !userMeta.helpPanelOpened }" @click.prevent="helpPanelOpen = !helpPanelOpen" aria-expanded="true"></a>
                </div>

                <div id="trp-save-and-loader-spinner" class="trp-button-container-save">
                    <span class="trp-ajax-loader" v-show="loadingStrings > 0" id="trp-string-saved-ajax-loader">
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
                            :iframe="iframe"
                            :currentURL="currentURL"
                            :mergingString="mergingString"
                            :mergeData="mergeData"
                            @translations-saved="showChangesUnsavedMessage = false; updatePercentage();"
                            :editorStrings="editorStrings"
                            :stringTypes="stringTypes"
                            :userMeta="userMeta"
                    >
                    </save-translations>
                </div>
            </div>

            <help-panel :helpPanelContent="helpPanelContent" :editorStrings="editorStrings" :helpPanelOpen="helpPanelOpen"></help-panel>

            <license-notice v-show="licenseNoticeContent" :licenseNoticeContent="licenseNoticeContent"></license-notice>


            <div class="trp-controls-container" :class="{'trp-show-editors-navigation' : editorsNavigation.show, 'help-panel-open':helpPanelOpen, 'trp-license-notice-shown':(licenseNoticeContent) }">
              <editors-navigation :editorsNavigation="editorsNavigation" :selectedTab="'visualeditor'"></editors-navigation>

                <div class="trp-controls-section" id="trp-controls-section-first">

                    <div class="trp-controls-section-content">
                        <span class="trp-tooltip-percentage-bar" :data-tooltip="PercentageBarLogic.percentageBarText({defaultLanguage: settings['default-language'], percentage, languageNames, currentLanguage, percentageBarStrings: editorStrings['percentage_bar']}).getTooltipText()">
                            <div id="trp-language-switch">
                              <div :class="{'trp-highlight-for-panel': highlightLanguageSwitcher}">
                                <select id="trp-language-select" name="lang" v-model="currentLanguage" v-select2>
                                    <option v-for="(lang, langIndex) in languageNames" :value="langIndex">{{lang}}</option>
                                </select>
                                  <percentage-bar :defaultLanguage="settings['default-language']" :percentage="percentage" :currentLanguage="currentLanguage" />
                              </div>
                            </div>
                        </span>

                        <div id="trp-string-list">
                            <div :class="{'trp-highlight-for-panel': highlightStringList}">
                                <select id="trp-string-categories" v-model="selectedString" v-select2>
                                    <optgroup v-for="(group) in stringGroups" :label="group">
                                        <template v-for="(string, index) in dictionary" :key="index">
                                            <option v-if="showString(string, group)"
                                                    :value="index"
                                                    :title="string.description"
                                                    :data-database-id="string.dbID"
                                                    :data-group="string.group"
                                                    :data-string-status="PercentageBarLogic.percentageBarText({
                                                        defaultLanguage: settings['default-language'],
                                                        currentLanguage,
                                                        stringObject: string,
                                                        percentageBarStrings: editorStrings['percentage_bar']
                                                    }).getStringStatus()"
                                            >
                                                {{ processOptionName( string.original, group ) }}
                                            </option>
                                        </template>
                                    </optgroup>
                                </select>
                            </div>
                        </div>


                        <div id="trp-next-previous">
                            <div class="trp-button-container">
                                <span class="trp-tooltip-toggle trp-tooltip-toggle-previous-tooltip" :data-tooltip="editorStrings.previous_title_attr">
                                <a href="#" type="button" id="trp-previous" class="trp-next-previous-buttons" v-on:click.prevent="previousString()"><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width='25' height="25" aria-hidden="true" focusable="false"><path d="M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"></path></svg></span> {{ editorStrings.previous }}</a>
                                </span>
                            </div>
                            <div class="trp-button-container">
                                <span class="trp-tooltip-toggle trp-tooltip-toggle-next-tooltip" :data-tooltip="editorStrings.next_title_attr">
                                <a href="#" type="button" id="trp-next" class="trp-next-previous-buttons" v-on:click.prevent="nextString()">{{ editorStrings.next }} <span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="25" height="25" aria-hidden="true" focusable="false"><path d="M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"></path></svg></span></a>
                                </span>
                            </div>
                        </div>

                        <div id="trp-view-as">
                            <div id="trp-view-as-description">{{ editorStrings.view_as }}</div>
                            <select id="trp-view-as-select" v-model="viewAs" v-select2>
                                <option class="trp-view-as-options" v-for="(role, roleIndex) in roles" :value="role" :disabled="!role" :title="!role ? editorStrings.view_as_pro : ''">{{roleIndex}}</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="trp-controls-section" v-show="selectedString !== null">
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
                            :iframe="iframe"
                            :nonces="nonces"
                            :ajax_url="ajaxUrl"
                            :userMeta="userMeta"
                    >
                    </language-boxes>
                </div>

                <extra-content :languageNames="languageNames" :editorStrings="editorStrings" :paidVersion="paidVersion" :blackFriday="blackFriday" :licenseStatus="licenseStatus"></extra-content>

                <div class="trp-controls-section" v-show="translationNotLoadedYet">
                    <div id="trp-translation-not-ready-section" class="trp-controls-section-content">
                        <p v-html="editorStrings.translation_not_loaded_yet"></p>
                    </div>
                </div>
            </div>

            <div id="trp_select2_overlay"></div>

            <hover-actions
                ref="hoverActions"
                :dictionary="dictionary"
                :settings="settings"
                :iframe="iframe"
                :dataAttributes="dataAttributes"
                :mergeRules="mergeRules"
                :nonces="nonces"
                :ajax_url="ajaxUrl"
                :mergeData="mergeData"
                :editorStrings="editorStrings"
                :currentLanguage="currentLanguage"
            >
            </hover-actions>
        </div>

        <div id="trp-preview">
            <iframe id="trp-preview-iframe" :src="urlToLoad" v-on:load="iFrameLoaded"></iframe>

            <div id="trp-preview-loader" class="trp-loading-screen">
                <svg class="trp-loader" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                    <circle class="trp-circle" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                </svg>
            </div>
        </div>
    </div>
</template>

<script>
import 'select2/dist/js/select2.min.js'
import utils              from './utils'
import axios              from 'axios'
import languageBoxes      from './components/language-boxes.vue'
import saveTranslations   from './components/save-translations.vue'
import hoverActions       from './components/hover-actions.vue'
import extraContent       from './components/extra-content.vue'
import editorsNavigation  from './components/editors-navigation.vue'
import Tooltip            from "./components/tooltip"
import HelpPanel          from "./components/help-panel"
import LicenseNotice      from "./components/ai-api-key-notice"
import PercentageBar      from "./components/percentage-bar.vue"
import PercentageBarLogic from "./components/percentage-bar-logic"

    export default {
        components:{
            HelpPanel,
            Tooltip,
            languageBoxes,
            saveTranslations,
            hoverActions,
            extraContent,
            editorsNavigation,
            LicenseNotice,
            PercentageBar
        },
        data(){
            return {

                settings                  : trp_editor_data.trp_settings,
                languageNames             : trp_editor_data.language_names,
                orderedSecondaryLanguages : trp_editor_data.ordered_secondary_languages,
                roles                     : trp_editor_data.view_as_roles,
                nonces                    : trp_editor_data.editor_nonces,
                stringGroupOrder          : trp_editor_data.string_group_order,
                selectors                 : trp_editor_data.string_selectors,
                stringTypes               : trp_editor_data.string_types,
                dataAttributes            : trp_editor_data.data_attributes,
                mergeRules                : trp_editor_data.merge_rules,
                editorsNavigation         : trp_editor_data.editors_navigation,
                editorStrings             : trp_editor_data.trp_localized_strings,
                flagsPath                 : trp_editor_data.flags_path,
                flagsFileName             : trp_editor_data.flags_file_name,
                helpPanelContent          : trp_editor_data.help_panel_content,
                licenseNoticeContent      : trp_editor_data.license_notice_content,
                //data
                currentLanguage           : trp_editor_data.current_language,
                interfaceLocale           : trp_editor_data.interface_locale,
                onScreenLanguage          : trp_editor_data.on_screen_language,
                currentURL                : trp_editor_data.url_to_load,
                urlToLoad                 : trp_editor_data.url_to_load,
                ajaxUrl                   : trp_editor_data.ajax_url,
                paidVersion               : trp_editor_data.paid_version,
                blackFriday               : trp_editor_data.black_friday,
                licenseStatus             : trp_editor_data.trp_license_status,
                userMeta                  : trp_editor_data.user_meta,
                upgradedGettext           : trp_editor_data.upgraded_gettext,
                noticeUpgradeSlugs        : trp_editor_data.notice_upgrade_slugs,
                iframe                    : '',
                dictionary                : [],
                selectedString            : null,
                selectedStringNode        : null,
                selectedIndexesArray      : [],
                detectedSelectorAndId     : [],
                stringGroups              : [],
                mergingString             : false,
                mergeData                 : [],
                showChangesUnsavedMessage : false,
                viewAs                    : '',
                loadingStrings           : 0,
                translationNotLoadedYet   : false,
                helpPanelOpen             : false,
                highlightLanguageSwitcher : false,
                highlightStringList       : false,
                gettextOriginalIds        : [],
                gettextNodeData           : [],
                gettextRequestsLeft       : 0,
                triggerAnotherScan        : false,
                percentage                : 0,
                PercentageBarLogic
            }
        },
        created(){
            this.settings['default-language-name'] = this.languageNames[ this.settings['default-language'] ]

            //set default value for the View As select
            let params = utils.getUrlParameters( this.currentURL )

            if( Object.keys(params).length > 1 && params['trp-view-as'] )
                this.viewAs = params['trp-view-as']
            else
                this.viewAs = 'current_user'
        },
        mounted(){
            this.addKeyboardShortcutsListener()
            this.addHelpPanelListeners()
            let self = this
            // initialize select2

            jQuery( '#trp-language-select, #trp-view-as-select' ).select2( { width : '100%', templateResult: function(option){
                const props = {
                    percentage           : self.percentage,
                    defaultLanguage      : self.settings['default-language'],
                    option               : option,
                    percentageBarStrings : self.editorStrings['percentage_bar']
                };

                return jQuery( PercentageBarLogic.miniBar(props).getMinibarHTML() );
            }});

            jQuery('#trp-language-switch .select2-selection__rendered').hover(function () {
                jQuery(this).removeAttr('title'); // Remove title attribute so the tooltip is not shown. We display our own tooltip in that place
            });

            jQuery('#trp-view-as-select').select2({
                dropdownAutoWidth: false,
                width: '92%',
            })

            //init strings dropdown
            this.stringsDropdownLoading()

            // show overlay when select is opened
            jQuery( '#trp-language-select, #trp-string-categories, #trp-view-as-select' ).on( 'select2:open', function() {
                jQuery( '#trp_select2_overlay' ).fadeIn( '100' )
            }).on( 'select2:close', function() {
                jQuery( '#trp_select2_overlay' ).hide()
            }).on( 'select2:opening', function(e) {
                /* when we have unsaved changes prevent the strings dropdown from opening so we do not have a disconnect between the textareas and the dropdown */
                if (self.hasUnsavedChanges()) {
                    e.preventDefault()
                }
            })

            // resize sidebar and consequently the iframe
            let previewContainer = jQuery( '#trp-preview' );
            let total_width = jQuery(window).width();
            jQuery( '#trp-controls' ).resizable({
                start: function( ) { previewContainer.toggle(); },
                stop: function( ) { previewContainer.toggle(); },
                handles: 'e',
                minWidth: 327,
                maxWidth: total_width - 20
            });

            document.addEventListener( 'trp_trigger_get_missing_gettext', this.getMissingGettextTranslations )
        },
        watch: {
            currentLanguage: function( currentLanguage ) {
                let self = this
                //grab the correct URL from the iFrame
                let langCode = currentLanguage.replace('_', '-'); // Convert underscores to hyphens
                let newURL = this.iframe.querySelector(`link[hreflang="${langCode}"]`)?.getAttribute('href');

                if (!newURL) {
                    let baseLang = langCode.split('-')[0]; // Extract only the language part (e.g., "en" from "en-US")
                    newURL = this.iframe.querySelector(`link[hreflang="${baseLang}"]`)?.getAttribute('href');
                }

                // Redirect the entire page to reload the Translation Editor for the new language
                // This ensures proper functionality with Multiple Domains addon
                if (newURL) {
                    // Carry the locale the interface is currently displayed in so the editor UI
                    // stays in that language after the reload. A direct URL entry has no such
                    // param and localizes normally. See change_locale() in class-languages.php.
                    let target = this.parentURL(newURL);
                    target = utils.updateUrlParameter(target, 'trp-editor-locale', this.interfaceLocale);
                    window.location.href = target;
                    return;
                }
            },
            currentURL: function ( newUrl, oldUrl ) {
                window.history.replaceState( null, null, this.parentURL( newUrl ) )
            },
            viewAs: function( role ) {
                if( !this.currentURL || !this.iframe )
                    return

                let url = this.cleanURL( this.currentURL )

                url = utils.updateUrlParameter( url, 'trp-edit-translation', 'preview' )

                if( role == 'current_user' ) {
                    this.iframe.location = url
                    return
                }

                //if nonce not available, an update to the Browse as Other Roles add-on is required
                if( !this.nonces[role] ) {
                    alert( this.editorStrings.bor_update_notice )
                    return
                }

                url = utils.updateUrlParameter( url, 'trp-view-as', role )
                url = utils.updateUrlParameter( url, 'trp-view-as-nonce', this.nonces[role] )

                this.iframe.location = url
            },
            selectedString: function ( selectedStringArrayIndex, oldString ){

                if( this.hasUnsavedChanges() ) {
                    this.selectedStringNode = null
                    return
                }

                if( !selectedStringArrayIndex && selectedStringArrayIndex !== 0 )
                    return

                jQuery( '#trp-string-categories' ).val( selectedStringArrayIndex !== null ? selectedStringArrayIndex : '' ).trigger( 'change' )

                let selectedString       = this.dictionary[selectedStringArrayIndex]

                if( !selectedString )
                    return

                let selectedStringNode    = this.getSelectedStringNode( selectedString )
                let currentNodes          = selectedStringNode ? [ selectedStringNode ] : this.iframe.querySelectorAll( "[" + selectedString.selector + "='" + selectedString.dbID + "']")
                let selectedIndexesArray = []
                let self = this

                //when merging we do not have a valid current node, so we just add the fake id
                if( currentNodes.length > 0 ) {
                    let selectors = self.getAllSelectors()
                    let nodes = []

                    currentNodes.forEach( function ( currentNode ) {
                        nodes.push( currentNode )

                        if ( currentNode.tagName != "A"){
                            // include the anchor's translatable attributes
                            let anchorParent  = currentNode.closest('a')
                            if(  anchorParent != null ) {
                                nodes.push(anchorParent)
                            }
                        }

                        if ( currentNode.tagName == "A" && currentNode.children.length > 0 ){
                            // include all the translatable attributes inside the anchor0
                            let childrenArray = [ ...currentNode.children ];
                            childrenArray.forEach( function ( child ) {
                                nodes.push(child)
                            })
                        }

                        if ( currentNode.tagName != "VIDEO"){
                            // include the video's translatable attributes and all the video's children.
                            let videoParent  = currentNode.closest('video');
                            if(  videoParent != null ) {
                                nodes.push(videoParent)
                                let videoChildren = [ ...videoParent.children];
                                addAllChildren(videoChildren);
                            }
                        }

                        if ( currentNode.tagName == "VIDEO" && currentNode.children.length > 0 ){
                            // include all the translatable attributes inside the video as well as their grand children. No point going recursive.
                            let childrenArray = [ ...currentNode.children ];
                            addAllChildren(childrenArray);
                        }

                        if ( currentNode.tagName != "AUDIO"){
                            // include the audio's translatable attributes and all the audio's children.
                            let audioParent  = currentNode.closest('audio');
                            if(  audioParent != null ) {
                                nodes.push(audioParent)
                                let audioChildren = [ ...audioParent.children];
                                addAllChildren(audioChildren);
                            }
                        }

                        if ( currentNode.tagName == "AUDIO" && currentNode.children.length > 0 ){
                            // include all the translatable attributes inside the audio as well as their grand children. No point going recursive.
                            let childrenArray = [ ...currentNode.children ];
                            addAllChildren(childrenArray);
                        }

                        if ( currentNode.tagName != "PICTURE"){
                            // include the picture's translatable attributes and all the picture's children.
                            let pictureParent  = currentNode.closest('picture');
                            if(  pictureParent != null ) {
                                nodes.push(pictureParent)
                                let pictureChildren = [ ...pictureParent.children];
                                addAllChildren(pictureChildren);
                            }
                        }

                        if ( currentNode.tagName == "PICTURE" && currentNode.children.length > 0 ){
                            // include all the translatable attributes inside the audio as well as their grand children. No point going recursive.
                            let childrenArray = [ ...currentNode.children ];
                            addAllChildren(childrenArray);
                        }

                        function addAllChildren(childrenArray){
                            childrenArray.forEach(function(child){
                                nodes.push(child);
                                addAllChildren([ ...child.children ])
                            })
                        }

                        nodes.forEach( function( node ) {
                            selectors.forEach(function (selector) {
                                let stringId = node.getAttribute(selector)
                                if (stringId) {
                                    let found = false
                                    let i
                                    for( i = 0; i < selectedIndexesArray.length; i++ ){
                                        if ( typeof self.dictionary[selectedIndexesArray[i]] !== 'undefined' && self.dictionary[selectedIndexesArray[i]].dbID !== 'undefined' && self.dictionary[selectedIndexesArray[i]].dbID === stringId ){
                                            found = true
                                            break;
                                        }
                                    }
                                    if ( ! found ) {
                                        selectedIndexesArray.push(self.getStringIndex(selector, stringId))
                                    }
                                }
                            })
                        })
                    })
                } else
                    selectedIndexesArray.push( selectedStringArrayIndex )

                if ( selectedString.originalPlural ){
                    this.dictionary.forEach( function ( string, index ) {
                        if ( string.originalId === selectedString.originalId && string.dbID !== selectedString.dbID ){
                            selectedIndexesArray.push( index )
                        }
                    } )
                    selectedIndexesArray.sort((a,b) => (self.dictionary[a].pluralForm > self.dictionary[b].pluralForm) ? 1 : ((self.dictionary[b].pluralForm > self.dictionary[a].pluralForm) ? -1 : 0))
                }


                this.selectedIndexesArray = selectedIndexesArray
                this.selectedStringNode = null
            },
            helpPanelOpen : function(){
                if ( this.userMeta.helpPanelOpened !== true ){
                    document.dispatchEvent( new CustomEvent( 'trp_update_user_meta', {
                        'detail' : {
                            'userMetaKey' : 'helpPanelOpened',
                            'userMetaValue' : true,
                        }
                    } ) )
                }
            },
            gettextRequestsLeft : function( newValue, oldValue ){
                if ( oldValue > 0 && newValue === 0 ){
                    this.getGettextStringsDictionaries()
                }
            },
            loadingStrings : function( newValue, oldValue ){
                if ( oldValue > 0 && newValue === 0 && this.triggerAnotherScan ){
                    this.triggerAnotherScan = false
                    this.scanIframeForStrings()
                }
            }
        },
        computed: {
            closeURL: function() {
                return this.cleanURL( this.currentURL )
            }
        },
        methods: {
            getSelectedStringNode( selectedString ){
                if ( !this.selectedStringNode )
                    return null

                let selectedStringNode = this.selectedStringNode.nodeType === 1 ? this.selectedStringNode : this.selectedStringNode.parentElement

                if ( !selectedStringNode )
                    return null

                let selector = "[" + selectedString.selector + "='" + selectedString.dbID + "']"

                if ( typeof selectedStringNode.getAttribute === 'function' && selectedStringNode.getAttribute( selectedString.selector ) === selectedString.dbID )
                    return selectedStringNode

                if ( typeof selectedStringNode.closest === 'function' ) {
                    let closestNode = selectedStringNode.closest( selector )

                    if ( closestNode )
                        return closestNode
                }

                if ( typeof selectedStringNode.querySelector === 'function' ) {
                    let childNode = selectedStringNode.querySelector( selector )

                    if ( childNode )
                        return childNode
                }

                return null
            },
            iFrameLoaded(){
                let self = this
                let iframeElement = document.querySelector('#trp-preview-iframe')

                this.iframe = iframeElement.contentDocument || iframeElement.contentWindow.document

                //sync iFrame URL with parent
                if ( this.currentURL != this.iframe.URL )
                    this.currentURL = this.iframe.URL

                //hide iFrame loader
                this.iframeLoader( 'hide' )

                self.detectedSelectorAndId = []
                self.dictionary            = []
                this.scanIframeForStrings()

                window.addEventListener( 'trp_iframe_page_updated', this.iframePageUpdated )

                //event that is fired when the iFrame is navigated
                iframeElement.contentWindow.onbeforeunload = function() {
                    self.iframeLoader( 'show' )

                    self.selectedString = null
                    self.selectedIndexesArray = []
                    self.translationNotLoadedYet = false

                    self.stringsDropdownLoading()
                }

            },
            iframePageUpdated(){
                if ( this.loadingStrings > 0 ){
                    this.triggerAnotherScan = true
                }else{
                    this.scanIframeForStrings()
                }
            },
            scanIframeForStrings(){
                this.scanForSelector( 'data-trp-translate-id', 'regular', this.onScreenLanguage )
                if( this.upgradedGettext ){
                    this.scanForSelector( 'data-trpgettextoriginal', 'gettext', this.currentLanguage )
                }
                if ( ! this.noticeUpgradeSlugs ){
                    this.scanForSelector( 'data-trp-post-slug', 'postslug', this.currentLanguage )
                }
            },
            scanForSelector( baseSelector, typeSlug, languageOfIds ){
                this.loadingStrings++
                let self           = this
                let selectors      = this.prepareSelectorStrings( baseSelector )
                let nodes          = [...this.iframe.querySelectorAll( '[' + selectors.join('],[') + ']' )]
                let stringIdsArray = [], nodeData = [], nodeEntries = []

                nodes.forEach( function ( node ){
                    nodeEntries = self.getNodeInfo( node, baseSelector )

                    nodeEntries.forEach( function( entry ) {
                        // this check ensures that we don't create duplicates when rescanning after ajax complete
                        if ( !self.alreadyDetected( entry.selector, entry.dbID ) ) {
                            stringIdsArray.push(entry.dbID)
                            nodeData.push(entry)
                        }
                    })

                    self.setupEventListener( node )
                })

                //unique ids only
                stringIdsArray = [...new Set(stringIdsArray)]
                if ( stringIdsArray.length > 0 ) {
                    let data = new FormData()
                    data.append('action'       , 'trp_get_translations_' + typeSlug)
                    data.append('all_languages', 'true')
                    data.append('security'     , this.nonces['gettranslationsnonce' + typeSlug])
                    data.append('language'     , languageOfIds)
                    data.append('string_ids'   , JSON.stringify(stringIdsArray))

                    axios.post(this.ajaxUrl, data)
                        .then(function (response) {
                            if ( typeSlug === 'gettext' ){
                                if ( response.data.originalIds ){
                                    self.gettextOriginalIds = response.data.originalIds
                                    self.gettextNodeData = nodeData
                                    document.dispatchEvent( new Event( 'trp_trigger_get_missing_gettext' ) )
                                }else{
                                    self.loadingStrings--
                                }
                            }else {
                                self.loadingStrings--
                                self.addToDictionary( response.data, nodeData )
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }else{
                    self.loadingStrings--
                }

            },
            getMissingGettextTranslations(){

                let self = this
                self.settings[ 'translation-languages' ].forEach( function ( languageCode ) {
                    self.gettextRequestsLeft++
                    let data = new FormData()
                    data.append( 'action', 'trp_string_translation_get_missing_gettext_strings' )
                    data.append( 'original_ids', JSON.stringify( self.gettextOriginalIds ) )
                    data.append( 'trp_ajax_language', languageCode )
                    data.append( 'security', self.nonces['get_missing_strings'] )
                    axios.post( self.ajaxUrl, data )
                         .then( function ( response ) {
                             self.gettextRequestsLeft--
                         })
                         .catch( function ( error ) {
                             self.gettextRequestsLeft--
                             console.log( error )

                         } )

                })
            },
            getGettextStringsDictionaries(){
                let self = this

                let data = new FormData()
                data.append( 'action', 'trp_string_translation_get_strings_by_original_ids_gettext' )
                data.append( 'original_ids', JSON.stringify( self.gettextOriginalIds ) )
                data.append('language'     , this.currentLanguage)
                data.append( 'security', self.nonces['get_strings_by_original_id'] )
                axios.post( self.ajaxUrl, data )
                     .then( function ( response ) {
                         self.loadingStrings--
                         self.addToDictionary( response.data.dictionary, self.gettextNodeData )
                     })
                     .catch( function ( error ) {
                         self.loadingStrings--
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
            alreadyDetected( selector, dbId ){
                let combined = selector + '=' + dbId
                if ( utils.arrayContainsItem( this.detectedSelectorAndId, combined ) ) {
                    return true
                }else {
                    this.detectedSelectorAndId.push(combined)
                    return false
                }
            },
            setupEventListener( node ){
                if ( node.tagName == 'A' && !node.hasAttribute( 'data-trpgettextoriginal' ) )
                    return false

                let self = this

                node.addEventListener( 'mouseenter', self.$refs.hoverActions.showPencilIcon )
            },
            addToDictionary( responseData, nodeInfo = null ){
                let self = this

                if ( responseData != null ) {
                    if ( nodeInfo ){
                        let responseIndexesFound = []
                        nodeInfo.forEach(function ( infoRow, index ){
                            responseData.some( function ( responseDataRow, responseIndex ) {

                                if ( infoRow.dbID == responseDataRow.dbID ) {
                                    //bring block_type to the top level object
                                    if ( responseDataRow.type != 'gettext' && typeof responseDataRow.block_type == 'undefined' ) {
                                        let firstLanguage = self.orderedSecondaryLanguages[0]

                                        if ( typeof responseDataRow.translationsArray[firstLanguage].block_type != 'undefined' )
                                            responseDataRow.block_type = responseDataRow.translationsArray[firstLanguage].block_type
                                    }

                                    nodeInfo[index] = Object.assign( {}, responseDataRow, infoRow )
                                    responseIndexesFound.push(responseIndex)
                                    return true // a sort of break
                                }
                            })
                        })
                        let restOfResponseData = []
                        responseData.forEach(function ( row, index ){
                            if( !responseIndexesFound.includes(index)){
                                restOfResponseData.push(responseData[index]);
                            }
                        })
                        nodeInfo = nodeInfo.concat(restOfResponseData)
                    }else{
                        nodeInfo = responseData
                    }

                    this.stringGroups = this.addToStringGroups( nodeInfo )
                    this.dictionary = this.dictionary.concat( nodeInfo )

                    this.initStringsDropdown()
                    this.updatePercentage()
                }
            },
            addToStringGroups( strings ){

                // see what node groups are found
                let foundStringGroups = this.stringGroups;
                strings.forEach( function ( string ) {
                    if ( foundStringGroups.indexOf( string.group ) === -1 && ( ( typeof string.blockType === 'undefined' ) || string.blockType !== '2' ) ){
                        foundStringGroups.push( string.group )
                    }
                })

                // put the node groups in the order that we want, according to the prop this.stringGroupOrder
                let orderedStringGroups = [];

                if ( this.editorStrings.seo_update_notice != 'seo_pack_update_not_needed' ){
                    orderedStringGroups.push( this.editorStrings.seo_update_notice );
                }

                this.stringGroupOrder.forEach( function( group ){
                    if ( foundStringGroups.indexOf( group ) !== -1 ){
                        orderedStringGroups.push( group )
                    }
                })

                // if there were any other string groups that were not in the prop, add them at the end.
                foundStringGroups.forEach( function (group) {
                    if ( orderedStringGroups.indexOf( group ) === -1 ){
                        orderedStringGroups.push(group);
                    }
                })

                return orderedStringGroups;
            },
            getStringIndex( selector, dbID ){
                let found = null

                this.dictionary.some(function ( string, index ) {
                    if ( string.dbID == dbID && string.selector == selector ){
                        found = index
                        return true
                    }
                })

                return found
            },
            getNodeInfo( node, baseSelector = '' ){
                let stringId
                let nodeData  = []
                let selectors = this.prepareSelectorStrings( baseSelector )

                selectors.forEach( function ( selector ) {

                    stringId = node.getAttribute( selector )

                    if ( stringId ) {

                        let nodeAttribute   = selector.replace( baseSelector, '' )
                        let nodeGroup       = node.getAttribute( 'data-trp-node-group' + nodeAttribute )
                        let nodeDescription = node.getAttribute( 'data-trp-node-description' + nodeAttribute )

                        let entry = {
                            dbID      : stringId,
                            selector  : selector,
                            attribute : nodeAttribute.substr(1), // substr(1) is used to trim prefixing line - ex. -alt will result in alt (no line)
                        }

                        if ( nodeGroup )
                            entry.group = nodeGroup

                        if ( nodeDescription )
                            entry.description = nodeDescription

                        nodeData.push( entry )
                    }

                })

                return nodeData
            },
            getAllSelectors(){
                let selectors = []
                let self      = this

                this.dataAttributes.forEach( function ( dataAttribute ){
                    selectors = selectors.concat( self.prepareSelectorStrings( dataAttribute ) )
                })

                return selectors
            },
            prepareSelectorStrings( baseNameSelector ){
                let parsed_selectors = []

                this.selectors.forEach( function ( selectorSuffix, index ){
                    parsed_selectors.push( baseNameSelector + selectorSuffix  )
                })

                return parsed_selectors
            },
            parentURL( url ){
                return url.replace( 'trp-edit-translation=preview', 'trp-edit-translation=true' )
            },
            cleanURL( url ){
                //make removeUrlParameter recursive and only call it once with all the parameters that
                //need to stripped ?
                url = utils.removeUrlParameter( url, 'lang' )
                url = utils.removeUrlParameter( url, 'trp-view-as' )
                url = utils.removeUrlParameter( url, 'trp-view-as-nonce' )
                url = utils.removeUrlParameter( url, 'trp-edit-translation' )

                return url
            },
            showString( string, type ){
                if ( typeof string.blockType !== 'undefined' && string.blockType === '2' ){
                    // don't show deprecated translation blocks in the dropdown
                    return false
                }

                // hide href from string drop-down in the editor
                if ( typeof string.attribute !== 'undefined' && ( string.attribute == 'href' ) )
                    return false

                if ( string.group === type )
                    return true

                return false
            },
            initStringsDropdown(){
                let self = this

                if ( !this.isStringsDropdownOpen() ) {
                    jQuery( '#trp-string-categories' ).select2( 'destroy' )

                    jQuery( '#trp-string-categories' ).select2( { placeholder : self.editorStrings.select_string, templateResult: function(option){
                        let original     = option.text.substring(0, 90) + ( ( option.text.length <= 90) ? '' : '...' )
                        let description  = ( option.title ) ?  '(' + option.title + ')' : ''
                        let stringStatus = option.element ? option.element.getAttribute( 'data-string-status') : ''
                        let iconHtml     = utils.getIconBasedOnStatus( stringStatus );

                        // Build the row with DOM APIs so the untrusted strings ( original, description )
                        // are always inserted as text and can never be parsed as HTML. Only the status
                        // icon, which is a trusted hard-coded SVG from utils.getIconBasedOnStatus(), is
                        // inserted as markup.
                        let firstRow = document.createElement( 'div' )
                        firstRow.appendChild( document.createTextNode( original ) )

                        if ( iconHtml ) {
                            let iconWrapper = document.createElement( 'span' )
                            iconWrapper.innerHTML = iconHtml
                            while ( iconWrapper.firstChild ) {
                                firstRow.appendChild( iconWrapper.firstChild )
                            }
                        }

                        let secondRow = document.createElement( 'div' )
                        secondRow.className = 'string-selector-description'
                        secondRow.appendChild( document.createTextNode( description ) )

                        return jQuery( [ firstRow, secondRow ] );
                    }, width : '100%' } ).prop( 'disabled', false )

                    jQuery( '#trp_select2_overlay' ).hide()
                }
            },
            stringsDropdownLoading(){
                jQuery( '#trp-string-categories' ).select2( { placeholder : this.editorStrings.strings_loading, width : '100%' } ).prop( 'disabled', true )
            },
            processOptionName( name, type ){
                if ( type == 'Images' || type == 'Videos' || type == 'Audios' || ( utils.isURL( name ) && type == 'Meta Information' ) )
                    return utils.getFilename( name )

                // Decode entities so the option shows the human-readable string. This value is bound
                // through Vue's text interpolation and rendered as a text node in the dropdown
                // ( see initStringsDropdown ), so it is never parsed as HTML.
                return utils.decodeEntities( name )
            },
            isStringsDropdownOpen(){
                return jQuery( '#trp-string-categories' ).select2( 'isOpen' )
            },
            hasUnsavedChanges(){
                let unsavedChanges = false
                let self = this
                if ( this.selectedIndexesArray.length > 0 ) {
                    this.selectedIndexesArray.forEach(function (selectedIndex) {
                        self.settings['translation-languages'].forEach(function (languageCode) {
                            if (self.dictionary[selectedIndex] &&
                                self.dictionary[selectedIndex].translationsArray &&
                                self.dictionary[selectedIndex].translationsArray[languageCode] &&
                                (self.dictionary[selectedIndex].translationsArray[languageCode].translated !== self.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation)) {
                                unsavedChanges = true
                            }
                        })
                    })
                }
                this.showChangesUnsavedMessage = unsavedChanges

                return unsavedChanges
            },
            iframeLoader( status ) {
                let loader = document.getElementById( 'trp-preview-loader' )

                if( status == 'show' )
                    loader.style.display = 'flex'
                else if( status == 'hide' )
                    loader.style.display = 'none'
            },
            previousString(){
                let currentValue = document.getElementById('trp-string-categories').value

                let newValue = +currentValue - 1

                while( newValue >= 0 && document.querySelectorAll('#trp-string-categories option[value="' + newValue + '"]').length === 0 ){
                    newValue--;
                }

                if( newValue < 0 )
                    return

                this.selectedString = newValue.toString()
            },
            nextString(){
                let currentValue = document.getElementById('trp-string-categories').value, newValue = 0

                if( currentValue != '' )
                    newValue = +currentValue + 1

                while( newValue < this.dictionary.length && document.querySelectorAll('#trp-string-categories option[value="' + newValue + '"]').length === 0 ){
                    newValue++;
                }

                if ( newValue >= this.dictionary.length ){
                    return
                }

                this.selectedString = newValue.toString()
            },
            addKeyboardShortcutsListener(){
                document.addEventListener("keydown", function(e) {
                    if ((window.navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey) && e.altKey ) {
                        // CTRL + ALT + right arrow
                        if( e.keyCode === 39 ){
                            e.preventDefault();
                            window.dispatchEvent( new Event( 'trp_trigger_next_string_event' ) );
                        }else{
                            // CTRL + ALT + left arrow
                            if( e.keyCode === 37 ) {
                                e.preventDefault();
                                window.dispatchEvent( new Event( 'trp_trigger_previous_string_event' ) );
                            }
                        }
                    }
                }, false);

                window.addEventListener( 'trp_trigger_next_string_event', this.nextString )
                window.addEventListener( 'trp_trigger_previous_string_event', this.previousString )
            },
            addHelpPanelListeners(){
                let self = this
                window.addEventListener( 'trp_switch_language_help_panel', function(){
                    self.highlightLanguageSwitcher = true
                } )
                window.addEventListener( 'trp_search_string_help_panel', function(){
                    self.highlightStringList = true
                } )
                window.addEventListener( 'trp_help_panel_changed', function(){
                    self.highlightLanguageSwitcher = false
                    self.highlightStringList = false
                } )

                document.addEventListener( 'trp_update_user_meta',  this.updateUserMeta )
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
            },
            updatePercentage() {
                this.percentage = PercentageBarLogic.calculateTranslationPercentage( this.dictionary, this.orderedSecondaryLanguages );
            },

        },
        //add support for v-model in select2
        directives: {
            select2: {
                mounted(el) {
                    jQuery(el).on('select2:select', () => {
                        const event = new Event('change', { bubbles: true, cancelable: true })
                        el.dispatchEvent(event)
                    })

                    jQuery(el).on('select2:unselect', () => {
                        const event = new Event('change', { bubbles: true, cancelable: true })
                        el.dispatchEvent(event)
                    })
                },
            }
        }
    }
</script>
