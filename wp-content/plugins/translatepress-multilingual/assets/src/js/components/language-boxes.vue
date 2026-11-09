<template>
    <div id="trp-translation-section" class="trp-controls-section-content" v-if="selectedIndexesArray">
        <div v-show="showChangesUnsavedMessage" class="trp-changes-unsaved-message">
            {{ editorStrings.unsaved_changes }}
            <span class="trp-button-container">
                <span class="trp-tooltip-toggle trp-tooltip-toggle-discard-changes" :data-tooltip="editorStrings.discard_all_title_attr">
                <a href="#" class="trp-unsaved-changes trp-discard-changes discard-all"@click.prevent="discardAll" >{{ editorStrings.discard_all }}?</a>
                </span>
            </span>

        </div>
        <div v-for="(languageCode, key) in languages" :id="'trp-language-' + languageCode">
            <div v-show="( (key <= othersButtonPosition) || showOtherLanguages ) && ( selectedIndexesArray && selectedIndexesArray.length > 0 )"  class="trp-language-container">
                <div class="trp-language-name">
                    <span v-if="key == 0 ">{{ editorStrings.from }} </span>
                    <span v-else>{{ editorStrings.to }} </span>
                    {{ completeLanguageNames[languageCode] }}
                  <span class="trp-button-container trp-languages-name">
                  <span v-for="i in selectedIndexesArray">
                    <span v-if="key !== 0 && selectedIndexesArray.length === 1 && typeof dictionary[i].translationsArray[languageCode] !== 'undefined'&& dictionary[i].translationsArray[languageCode].status == '2'"><span class="trp-tooltip-toggle trp-tooltip-toggle-reviewed" :data-tooltip="editorStrings.human_translation"><svg class="trp_reviewed_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width='25' height="25" aria-hidden="true" focusable="false"><path d="M15.5 9.5a1 1 0 100-2 1 1 0 000 2zm0 1.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5zm-2.25 6v-2a2.75 2.75 0 00-2.75-2.75h-4A2.75 2.75 0 003.75 15v2h1.5v-2c0-.69.56-1.25 1.25-1.25h4c.69 0 1.25.56 1.25 1.25v2h1.5zm7-2v2h-1.5v-2c0-.69-.56-1.25-1.25-1.25H15v-1.5h2.5A2.75 2.75 0 0120.25 15zM9.5 8.5a1 1 0 11-2 0 1 1 0 012 0zm1.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                    </span>
                      </span>

                    <span v-else-if="key !== 0 && selectedIndexesArray.length === 1 && typeof dictionary[i].translationsArray[languageCode] !== 'undefined' && dictionary[i].translationsArray[languageCode].status == '1'"><span class="trp-tooltip-toggle trp-tooltip-toggle-reviewed" :data-tooltip="editorStrings.machine_translation"><svg class="trp_reviewed_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width='25' height="25" aria-hidden="true" focusable="false"><path d="M17.3 10.1c0-2.5-2.1-4.4-4.8-4.4-2.2 0-4.1 1.4-4.6 3.3h-.2C5.7 9 4 10.7 4 12.8c0 2.1 1.7 3.8 3.7 3.8h9c1.8 0 3.2-1.5 3.2-3.3.1-1.6-1.1-2.9-2.6-3.2zm-.5 5.1h-4v-2.4L14 14l1-1-3-3-3 3 1 1 1.2-1.2v2.4H7.7c-1.2 0-2.2-1.1-2.2-2.3s1-2.4 2.2-2.4H9l.3-1.1c.4-1.3 1.7-2.2 3.2-2.2 1.8 0 3.3 1.3 3.3 2.9v1.3l1.3.2c.8.1 1.4.9 1.4 1.8 0 1-.8 1.8-1.7 1.8z"></path></svg>
                    </span>
                    </span>
                    <span v-else-if="key !== 0 && selectedIndexesArray.length === 1 && typeof dictionary[i].translationsArray[languageCode] !== 'undefined' && dictionary[i].translationsArray[languageCode].status == '4'"><span class="trp-tooltip-toggle trp-tooltip-toggle-reviewed" :data-tooltip="editorStrings.language_file_translation"><span class="dashicons dashicons-translation trp_reviewed_icon trp-language-file-translation-icon" aria-hidden="true"></span>
                    </span>
                    </span>
                  </span>
                  </span>
                  <span class="trp-button-container">
                      <span class="trp-tooltip-toggle trp-tooltip-toggle-flags" :data-tooltip="completeLanguageNames[languageCode]">
                  <img v-if="languageCode != 'original'" class="trp-language-box-flag-image" id="trp-flags" :src="flagsPath[languageCode] + flagsFileName[languageCode]" width="18" height="12" :alt="languageCode">
                      </span>
                      </span>
                </div>
                <table class="trp-translations-for-language">
                    <tbody>
                        <tr>
                            <td class="trp-translation-icon-container" v-if="showImageIcon">
                                <span class="trp-translation-icon"></span>
                            </td>
                            <td class="trp-translations-container">
                                <div class="trp-string-container" v-for="selectedIndex in selectedIndexesArray">
                                    <div v-if="dictionary[selectedIndex] && dictionary[selectedIndex].translationsArray[languageCode]" >
                                        <translation-input :string="dictionary[selectedIndex]" v-model="dictionary[selectedIndex].translationsArray[languageCode].editedTranslation" :highlightUnsavedChanges="showChangesUnsavedMessage && hasUnsavedChanges( selectedIndex, languageCode )" :editorStrings="editorStrings"></translation-input>
                                    </div>
                                    <div v-else-if="dictionary[selectedIndex]">
                                        <div v-if="!dictionary[selectedIndex].originalPlural || (dictionary[selectedIndex].originalPlural && dictionary[selectedIndex].pluralForm === '0' )">
                                            <translation-input :readonly="true" :string="dictionary[selectedIndex]" :modelValue="dictionary[selectedIndex].original" :editorStrings="editorStrings"></translation-input>
                                        </div>
                                        <div v-if="dictionary[selectedIndex].originalPlural && dictionary[selectedIndex].pluralForm === '1' ">
                                            <translation-input :readonly="true" :string="dictionary[selectedIndex]" :modelValue="dictionary[selectedIndex].originalPlural" :editorStrings="editorStrings"></translation-input>
                                        </div>
                                    </div>

                                    <div v-if="dictionary[selectedIndex].translationsArray[languageCode] || !dictionary[selectedIndex].originalPlural || (dictionary[selectedIndex].originalPlural && ( dictionary[selectedIndex].pluralForm === '0' || dictionary[selectedIndex].pluralForm === '1' ) )" class="trp-translation-input-footer" :data-dictionary-entry="JSON.stringify(dictionary[selectedIndex])">
                                        <div class="trp-attribute-name">
                                            {{ ( editorStrings[ dictionary[selectedIndex].attribute ] && ( (dictionary[selectedIndex].attribute != 'content' || dictionary[selectedIndex].attribute != '') ) ) ? editorStrings[ dictionary[selectedIndex].attribute ] : ( isURL( dictionary[selectedIndex].original ) && dictionary[selectedIndex].attribute === 'content' ) ? "Image source" : editorStrings.text }}
                                            <span class="trp-plural-form-name" v-if="dictionary[selectedIndex].originalPlural"> ({{ editorStrings.plural_form_text }}: {{ getPluralFormName(dictionary[selectedIndex].pluralForm) }})</span>
                                            <span v-if="typeof dictionary[selectedIndex].translationsArray[languageCode] !== 'undefined'&&  selectedIndexesArray.length > 1 && dictionary[selectedIndex].translationsArray[languageCode].status == '2'">
                                                <span class="trp-tooltip-toggle trp-tooltip-toggle-reviewed" :data-tooltip="editorStrings.human_translation"><svg class="trp_reviewed_icon_plural" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width='25' height="25" aria-hidden="true" focusable="false"><path d="M15.5 9.5a1 1 0 100-2 1 1 0 000 2zm0 1.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5zm-2.25 6v-2a2.75 2.75 0 00-2.75-2.75h-4A2.75 2.75 0 003.75 15v2h1.5v-2c0-.69.56-1.25 1.25-1.25h4c.69 0 1.25.56 1.25 1.25v2h1.5zm7-2v2h-1.5v-2c0-.69-.56-1.25-1.25-1.25H15v-1.5h2.5A2.75 2.75 0 0120.25 15zM9.5 8.5a1 1 0 11-2 0 1 1 0 012 0zm1.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg></span>
                                            </span>

                                            <span v-else-if="typeof dictionary[selectedIndex].translationsArray[languageCode] !== 'undefined' &&  selectedIndexesArray.length > 1 && dictionary[selectedIndex].translationsArray[languageCode].status == '1'">
                                                <span class="trp-tooltip-toggle trp-tooltip-toggle-reviewed" :data-tooltip="editorStrings.machine_translation"><svg class="trp_reviewed_icon_plural" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width='25' height="25" aria-hidden="true" focusable="false"><path d="M17.3 10.1c0-2.5-2.1-4.4-4.8-4.4-2.2 0-4.1 1.4-4.6 3.3h-.2C5.7 9 4 10.7 4 12.8c0 2.1 1.7 3.8 3.7 3.8h9c1.8 0 3.2-1.5 3.2-3.3.1-1.6-1.1-2.9-2.6-3.2zm-.5 5.1h-4v-2.4L14 14l1-1-3-3-3 3 1 1 1.2-1.2v2.4H7.7c-1.2 0-2.2-1.1-2.2-2.3s1-2.4 2.2-2.4H9l.3-1.1c.4-1.3 1.7-2.2 3.2-2.2 1.8 0 3.3 1.3 3.3 2.9v1.3l1.3.2c.8.1 1.4.9 1.4 1.8 0 1-.8 1.8-1.7 1.8z"></path></svg></span>
                                            </span>
                                            <span v-else-if="typeof dictionary[selectedIndex].translationsArray[languageCode] !== 'undefined' &&  selectedIndexesArray.length > 1 && dictionary[selectedIndex].translationsArray[languageCode].status == '4'">
                                                <span class="trp-tooltip-toggle trp-tooltip-toggle-reviewed" :data-tooltip="editorStrings.language_file_translation"><span class="dashicons dashicons-translation trp_reviewed_icon_plural trp-language-file-translation-icon" aria-hidden="true"></span></span>
                                            </span>
                                        </div>
                                        <span class="trp-button-container">
                                            <span class="trp-tooltip-toggle trp-tooltip-toggle-discard-changes" :data-tooltip="editorStrings.discard_individual_changes_title_attribute">
                                                <div v-if="dictionary[selectedIndex] && dictionary[selectedIndex].translationsArray[languageCode]" class="trp-discard-changes trp-discard-individual-changes" @click.prevent="discardChanges(selectedIndex,languageCode)" :class="{'trp-unsaved-changes': hasUnsavedChanges( selectedIndex, languageCode ) }">{{ editorStrings.discard }}</div>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="trp-context" v-if="languageCode == 'original' && ( dictionary[selectedIndex].context ) && dictionary[selectedIndex].context != 'trp_context' && (!dictionary[selectedIndex].originalPlural || (dictionary[selectedIndex].originalPlural && dictionary[selectedIndex].pluralForm === '1' ))">{{ editorStrings.context + ': ' + dictionary[selectedIndex].context }}</div>
                                    <div class="trp-translation-memory-wrap" v-if="dictionary[selectedIndex] && dictionary[selectedIndex].translationsArray[languageCode] && !dictionary[selectedIndex].type.includes('slug')" :key="'trp_tmw_' + selectedIndex">
                                        <translation-memory :string="dictionary[selectedIndex]" :editorStrings="editorStrings" :ajax_url="ajax_url" :nonces="nonces" :languageCode="languageCode"></translation-memory>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-show="key == othersButtonPosition">
                    <div class="trp-toggle-languages button" @click="showOtherLanguages = !showOtherLanguages" :class="{ 'trp-show-other-languages': showOtherLanguages, 'trp-hide-other-languages': !showOtherLanguages }">
                        <span>{{ (showOtherLanguages)?  '&#9660;' : '&#9654;'}} {{ editorStrings.other_lang }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import translationInput  from './translation-input.vue'
    import translationMemory from './translation-memory.vue'
    import Tooltip           from "./tooltip"
    import utils from '../utils'
    import axios             from 'axios'
    import he                from 'he'

    export default{
        props:[
            'selectedIndexesArray',
            'dictionary',
            'currentLanguage',
            'onScreenLanguage',
            'languageNames',
            'settings',
            'showChangesUnsavedMessage',
            'editorStrings',
            'flagsPath',
            'flagsFileName',
            'iframe',
            'nonces',
            'ajax_url',
            'userMeta',
        ],
        data(){
            return{
                languages                  : [],
                completeLanguageNames      : Object.assign( { 'original': 'Original String' }, this.languageNames ),
                othersButtonPositionOffset : 1,
                showOtherLanguages         : false,
                orderedLanguages           : [],
                firefox                    : false,
                showImageIcon              : true,
            }
        },
        components:{
            Tooltip,
            translationInput,
            translationMemory
        },
        mounted(){
            this.determineLanguageOrder()
            this.addKeyboardShortcutsListener()
        },
        updated(){
            // if already active do nothing
            if ( document.activeElement.classList.contains( 'trp-translation-input' ) ||
                document.activeElement.classList.contains( 'trp-editor-body' ) ) // when clicking translation memory result, don't move cursor
            {
                return
            }
            // place the cursor in the first textarea or input for translation
            let translationSection = document.getElementById( 'trp-translation-section' )
            if ( translationSection )  {
                let focusableSelectors = ['textarea:not([readonly])', 'input[type="text"]:not([readonly])']
                for ( var i = 0; i<focusableSelectors.length; i++ ){
                    let focusable = document.getElementById( 'trp-translation-section' ).querySelector(focusableSelectors[i])
                    if ( focusable ) {
                        focusable.focus()
                        break;
                    }
                }
            }
        },
        watch: {
            selectedIndexesArray: {
                handler() {
                    this.updateLanguages()
                },
                deep: true
            },
            onScreenLanguage: function(){
                this.determineLanguageOrder()
                this.updateLanguages()
            }
        },
        computed:{
            othersButtonPosition: function (){
                if (this.currentLanguage === this.settings['default-language'] || this.settings['translation-languages'].length <= 2 ) {
                    // don't display it
                    return 999
                }else{
                    return this.othersButtonPositionOffset
                }
            }
        },
        methods:{
            determineLanguageOrder: function () {
                let self = this
                let filteredLanguages = this.settings['translation-languages'].filter(function(language, index, array){
                    // all languages except default and current or on screen language.
                    return ( self.settings['default-language'] !== language ) && ( self.onScreenLanguage !== language )
                });
                this.orderedLanguages = []
                this.orderedLanguages.push( this.settings['default-language'] )
                if ( this.onScreenLanguage !== '' )
                    this.orderedLanguages.push( this.onScreenLanguage )
                this.orderedLanguages = this.orderedLanguages.concat( filteredLanguages )
            },
            updateLanguages: function () {
                this.languages                  = []
                let self                        = this
                let defaultLanguage             = this.settings['default-language']
                let translateToDefault          = false
                this.showImageIcon              = false
                this.othersButtonPositionOffset = 1

                this.selectedIndexesArray.forEach(function (selectedIndex) {
                  if( self.dictionary[selectedIndex] && self.dictionary[selectedIndex].translationsArray && self.dictionary[selectedIndex].translationsArray[defaultLanguage] )
                        translateToDefault = true
                    if( ( self.dictionary[selectedIndex] && self.dictionary[selectedIndex].attribute === 'src' )
                        || ( self.dictionary[selectedIndex] && self.isURL( self.dictionary[selectedIndex].original ) && self.dictionary[selectedIndex].attribute === 'content' ) ){
                        self.showImageIcon = true
                    }
                })

                if (translateToDefault) {
                    this.languages.push('original')
                    this.othersButtonPositionOffset++
                }

                this.languages = this.languages.concat(this.orderedLanguages)
            },
            discardChanges: function(selectedIndex,languageCode){
                this.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation = this.dictionary[selectedIndex].translationsArray[languageCode].translated
                this.$emit('discarded-changes')
            },
            hasUnsavedChanges: function(selectedIndex, languageCode){
                return (this.dictionary[selectedIndex].translationsArray[languageCode].translated !== this.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation)
            },
            discardAll: function(){
                let self = this
                this.selectedIndexesArray.forEach(function(selectedIndex){
                    self.settings['translation-languages'].forEach( function( languageCode  ) {
                        if ( self.dictionary[selectedIndex].translationsArray[languageCode] &&
                            self.dictionary[selectedIndex].translationsArray &&
                            (self.dictionary[selectedIndex].translationsArray[languageCode].translated !== self.dictionary[selectedIndex].translationsArray[languageCode].editedTranslation) ) {
                            self.discardChanges(selectedIndex,languageCode)
                        }
                    })
                })

                if ( this.$parent.mergingString === true ){
                    this.$parent.selectedString = null
                    let previouslyHighlighted = this.iframe.getElementsByClassName( 'trp-create-translation-block' )
                    if( previouslyHighlighted.length > 0 ) {
                        let i
                        for ( i = 0; i < previouslyHighlighted.length; i++ ) {
                            previouslyHighlighted[i].classList.remove('trp-highlight')
                            previouslyHighlighted[i].classList.remove('trp-create-translation-block')
                        }
                    }
                    this.$parent.mergingString = false
                    this.$parent.mergeData = []
                }
            },
            addKeyboardShortcutsListener(){
                document.addEventListener("keydown", function(e) {
                    // CTRL + ALT + Z
                    if ((window.navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey) && e.altKey && e.keyCode === 90 ) {
                        e.preventDefault();
                        window.dispatchEvent(new Event('trp_trigger_discard_all_changes_event'));
                    }
                }, false);

                window.addEventListener( 'trp_trigger_discard_all_changes_event', this.discardAll )
            },
            isURL(string) {
              return utils.isURL(string)
            },
            getPluralFormName(pluralForm){
                let text
                switch ( pluralForm ){
                    case null :
                    case '' :
                    case '0': {
                        text = this.editorStrings.plural_form_one
                        break
                    }
                    case '1': {
                        text = this.editorStrings.plural_form_few
                        break
                    }
                    case '2': {
                        text = this.editorStrings.plural_form_many
                        break
                    }
                    default : {
                        text = this.editorStrings.plural_form_other + "(" + pluralForm + ")"
                        break
                    }
                }
                return text
            }
        }
    }
</script>
