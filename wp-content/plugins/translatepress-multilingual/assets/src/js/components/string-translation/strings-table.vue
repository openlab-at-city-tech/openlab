<template>
    <div id="trp-string-tables-root">
        <div id="trp-string-tables-container">
            <table class="wp-list-table widefat striped trp-strings-table">
                <thead>
                <table-head
                        v-model="tableHeadControls"
                        :stEditorStrings="stEditorStrings"
                        :currentLanguage="currentLanguage"
                        :languageNames="languageNames"
                        :currentTab="currentTab">
                </table-head>
                </thead>
                <tbody>
                <tr v-show="showLoadingScreen">
                    <td :colspan="numberOfColumns">
                        <div id="trp-table-loader" class="trp-loading-screen">
                            <svg class="trp-loader" width="65px" height="65px" viewBox="0 0 66 66"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle class="trp-circle" fill="none" stroke-width="6" stroke-linecap="round" cx="33"
                                        cy="33" r="30"></circle>
                            </svg>
                        </div>
                    </td>
                </tr>
                <tr v-show="( Object.entries(dictionary).length === 0 ) && !showLoadingScreen">
                    <td :colspan="numberOfColumns">
                        {{stEditorStrings.no_strings_match_query}} {{(currentTab.scan_gettext) ? stEditorStrings.no_strings_match_rescan : ''}}
                    </td>
                </tr>
                <template v-for="(string, stringIndex) in dictionary" :key="stringIndex">
                    <tr v-if="!string.hasOwnProperty('pluralForm') || string.pluralForm == 0"
                        class="trp-table-row trp-string-table-row"
                        :id="'trp-string-table-row-' + stringIndex">
                        <td style="width: 20px">
                            <input type="checkbox" :value="stringIndex" v-model="checkedStrings">
                        </td>
                        <template v-for="(column, column_key) in currentTab['table_columns']" :key="column_key">
                            <td v-if="(column_key !== 'translated' && column_key !== 'id') || currentLanguage !== 'trp_default'"
                                :class="'trp-table-data-' + column_key">
                                <div v-if="column_key === 'original'">
                                    <strong>
                                        <a href="#" class="row-title trp-anchor-action"
                                           @click.prevent="performAction('edit', stringIndex)">
                                            <seemore-string :string="string[column_key]" :stEditorStrings="stEditorStrings"
                                                            :config="config" :foundInTranslation="string['foundInTranslation']">
                                            </seemore-string>
                                        </a>
                                    </strong>
                                    <div class="row-actions">
                                        <span v-for="(option, option_key, index) in defaultActions.actions" :class="option_key">
                                            <a href="#" class="trp-anchor-action" @click.prevent="performAction(option_key, stringIndex)">{{ option }}</a>
                                            <span v-show="index !== Object.keys(defaultActions.bulk_actions).length - 1"> | </span>
                                        </span>
                                    </div>
                                </div>
                                <div v-else-if="column_key === 'translated'">
                                    <seemore-string
                                        :string="( string.translationsArray && string.translationsArray[currentLanguage] ) ? maybeDecode(string.translationsArray[currentLanguage][column_key]) : ''"
                                        :stEditorStrings="stEditorStrings" @click="performAction('edit', stringIndex)"
                                        :config="config">
                                    </seemore-string>
                                </div>
                                <div v-else-if="column_key === 'id'">
                                    <seemore-string
                                        :string="getPostIdColumnValue(string)"
                                        :stEditorStrings="stEditorStrings" @click="performAction('edit', stringIndex)"
                                        :config="config">
                                    </seemore-string>
                                </div>
                                <div v-else>
                                    {{ string[column_key] }}
                                </div>
                            </td>
                        </template>
                        <td class="trp-translation-status-entry-wrapper" v-if="currentLanguage !== 'trp_default'">
                            <div class="trp-translation-status-entry">
                                <template v-for="(language, language_key) in translationLanguages" :key="language_key">
                                    <template v-if="currentTab['show_original_language'] || language !== settings['default-language']">
                                        <span class="trp-language-translation-status">
                                            <span class="trp-language-translation-status-item"
                                                :title="translationStatusFilters.translation_status[statusName[string.translationsArray?.[language]?.status]] + ' ' + stEditorStrings.in + ' ' + languageNames[language]">
                                                <span :class="{
                                                    'trp-human-reviewed-green': string.translationsArray?.[language]?.status === '2',
                                                    'trp-automatic-translated-blue': string.translationsArray?.[language]?.status === '1',
                                                    'trp-language-file-purple': string.translationsArray?.[language]?.status === '4',
                                                    'trp-untranslated-red': !string.translationsArray?.[language] || string.translationsArray[language].status === '0'
                                                }">
                                                  {{ translationStatusFilters.translation_status[statusName[string.translationsArray?.[language]?.status]] }}
                                                </span>
                                            </span>
                                        </span>
                                    </template>
                                </template>

                            </div>
                        </td>
                    </tr>
                </template>

                </tbody>
                <tfoot>
                <table-head
                        v-model="tableHeadControls"
                        :stEditorStrings="stEditorStrings"
                        :currentLanguage="currentLanguage"
                        :languageNames="languageNames"
                        :currentTab="currentTab">
                </table-head>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script>
    import TableHead     from "./table-head"
    import SeemoreString from "./seemore-string"

    export default {
        name       : "StringsTable",
        components : { SeemoreString, TableHead },
        props      : [
            'modelValue',
            'currentTab',
            'dictionary',
            'settings',
            'languageNames',
            'translationStatusFilters',
            'defaultActions',
            'flagsPath',
            'stEditorStrings',
            'currentLanguage',
            'config'
        ],
        data() {
            return {
                translationLanguages : this.settings[ 'translation-languages' ],
                checkedStrings       : [],
                tableHeadControls    : { selectAllOrVisible : '' },
                statusName           : {
                    '2' : 'human_reviewed',
                    '1' : 'machine_translated',
                    '4' : 'gettext_translated_in_language_file',
                    '0' : 'not_translated'
                },
                showLoadingScreen    : true
            }
        },
        watch      : {
            currentLanguage                        : function () {
                this.updateColumns()
            },
            'tableHeadControls.selectAllOrVisible' : function () {
                let self            = this
                self.checkedStrings = []
                this.dictionary.forEach( function ( string, stringIndex ) {
                    self.checkedStrings.push( stringIndex )
                } )
            },
            checkedStrings                         : function () {
                this.$emit( 'update:modelValue', {
                    checkedStrings     : this.checkedStrings,
                    selectAllOrVisible : this.tableHeadControls.selectAllOrVisible
                } )
            }
        },
        computed   : {
            numberOfColumns : function () {
                let count = 1
                for ( let column_key in this.currentTab[ 'table_columns' ] ) {
                    if ( this.currentTab[ 'table_columns' ].hasOwnProperty( column_key ) &&
                        ( this.currentLanguage !== 'trp_default' || ( column_key !== 'translated' &&  column_key !== 'id' ))){
                        // id and translated column are shown only when current language is not set to default (all languages)
                        ++count
                    }
                }
                if ( this.currentLanguage !== 'trp_default' ){
                    // translation status column
                    ++count
                }
                return count
            }
        },
        mounted() {
            this.updateColumns()
            window.addEventListener( 'trp_trigger_show_loading_table_event', this.setLoadingScreen )
            window.addEventListener( 'trp_trigger_hide_loading_table_event', this.hideLoadingScreen )
        },
        methods    : {
            updateColumns() {
                this.translationLanguages = (this.currentLanguage === 'trp_default') ? this.settings[ 'translation-languages' ] : [ this.currentLanguage ]
            },
            performAction( action, stringIndex ) {
                document.dispatchEvent( new CustomEvent( 'trp_trigger_perform_action_event', {
                    'detail' : {
                        'stringIndex' : stringIndex,
                        'action'      : action
                    }
                } ) )
            },
            setLoadingScreen() {
                this.showLoadingScreen = true
                this.checkedStrings = []
            },
            hideLoadingScreen() {
                this.showLoadingScreen = false
            },
            maybeDecode(value){
              try {
                  return decodeURI( value )
              } catch ( err ) {
                  return value
              }
            },
            getPostIdColumnValue( string ) {
                if ( !string.translationsArray || !string.translationsArray[ this.currentLanguage ] ) {
                    return ''
                }
                if ( string.post_id === undefined || string.post_id === null || string.post_id === '' ) {
                    return ''
                }
                return this.maybeDecode( string.post_id )
            }
        }
    }
</script>
