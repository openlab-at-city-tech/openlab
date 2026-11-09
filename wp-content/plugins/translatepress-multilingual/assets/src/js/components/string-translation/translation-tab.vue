<template>
    <div class="">
        <div class="trp-title-rescan">

            <h3 class="trp-translation-tab-title-name">
              {{ (parentTab !== false) ? parentTab.name : currentTab.name }}
            </h3>
            <div class="trp-rescan-wrap">
                <button :disabled=scanningInProgress class="button"
                        :class="{'trp-button-busy-animation': scanningInProgress}" v-if="currentTab.scan_gettext"
                        @click="startGettextScan()">{{ rescanButtonText }}
                </button>
            </div>


        </div>
            <div v-if="showFiltersAndTable">
            <a href="#" class="page-title-action" v-if="currentTab.add_new">{{stEditorStrings.add_new}}</a>

            <!--<hr class="wp-header-end">-->
            <ul class="trp-subnavigation" v-show="parentTab !== false">
                <li v-for="(category, categoryPath, index ) in parentTab.categories">
                    <span v-show="category.name === 'Other Slugs'" :title = stEditorStrings.other_slugs_tooltip class="trp-tooltip-other-slugs"></span>
                    <router-link :to="'/' + parentTranslationType + '/' + categoryPath + '/'"
                                 :class="{'trp-subnavigation-active': categoryPath === translationType }">
                        {{category.name}}
                        <!--                    <span class="count">(1)</span>-->
                    </router-link>
                    <span v-show="index !== Object.keys(parentTab.categories).length - 1">|</span>
                </li>
            </ul>


            <div class="trp-filters-container">
                <div class="trp-filter" id="trp-filter-translation-status">
                    <label :for="'trp-filter-translation-status-' + status_key"
                           class="trp-translation-status-checkbox"
                           v-for="(status, status_key) in visibleTranslationStatusFilters">
                        <input type="checkbox" :id="'trp-filter-translation-status-' + status_key"
                               v-model="filterValues[status_key]">
                        {{ status }}
                    </label>
                </div>

                <div class="trp-filter-dropdowns">
                    <span class="trp-filter">
                          <span class="trp-tooltip-toggle trp-tooltip-toggle-language"
                                :data-tooltip="stEditorStrings.filter_by_language_tooltip">
                                <select name="trp-language" id="trp-filter-language" class="trp-filter-select" tabindex="0"
                                        v-model="filterValues['language']">
                                    <option value="trp_default">{{ stEditorStrings.filter_by_language }}</option>
                                    <option v-for="language in settings['translation-languages']"
                                            v-show="currentTab['show_original_language'] || language !== settings['default-language']"
                                            :value="language">
                                        {{ languageNames[language] }}
                                    </option>
                                </select>
                          </span>
                    </span>

                    <span class="trp-filter" v-for="(filter, filter_key) in currentTab.filters">
                          <span class="trp-tooltip-toggle trp-tooltip-toggle-filter-by-post"
                                :data-tooltip="filter.trp_default" style="visibility: hidden">
                                <select :name="filter_key" :id="'trp-filter-' + filter_key " class="trp-filter-select"
                                        v-model="filterValues[filter_key]">
                                    <option v-for="(option, option_key ) in filter"
                                            :selected="option_key === 'trp_default' ? true : null"
                                            :value="option_key">
                                        {{ option }}
                                    </option>
                                </select>
                          </span>
                    </span>

                    <span class="trp-tooltip-toggle trp-tooltip-toggle-filter-button"
                          :data-tooltip="stEditorStrings.filter_tooltip">
                        <input type="submit" class="button trp-filter-button"
                               :value="stEditorStrings.filter"
                               @click="filter()" >
                    </span>

                    <span class="trp-tooltip-toggle trp-tooltip-toggle-clear-filters"
                          :data-tooltip="stEditorStrings.clear_filter_tooltip">
                        <a href="#" id="trp-clear-filter-button"
                           :class="{'trp-clear-filter-disabled': clearFilterDisabled }"
                           @click.prevent="clear_filter()">
                            <span id="trp-clear-filter-x"></span>{{ stEditorStrings.clear_filter }}
                        </a>
                    </span>
                </div>

                <div class="trp-search-box">
                    <label class="screen-reader-text" for="post-search-input">{{currentTab['search_name']}}</label>
                    <input type="search" id="trp-string-search-input" :placeholder="stEditorStrings.search_placeholder" name="s" v-model="filterValues['s']" @keyup.enter="filter">
                    <span class="trp-tooltip-toggle trp-tooltip-toggle-search-submit" :data-tooltip="stEditorStrings.search_tooltip">
                        <input type="submit" class="button trp-filter-button" :value="currentTab['search_name']" @click="filter">
                    </span>
                </div>
            </div>

            <div class="trp-table-actions">
                <bulk-actions
                        :stEditorStrings="stEditorStrings"
                        :defaultActions="defaultActions"
                        :tableControls="tableControls"
                        :currentTab="currentTab"
                        :ajaxUrl="ajaxUrl"
                        :listenForEvents="true"
                        :dictionary="dictionary"
                >
                </bulk-actions>
                <pagination v-model.lazy.number="currentPage"
                            :stEditorStrings="stEditorStrings"
                            :totalItems="totalItems"
                            :totalNumberOfPages="totalNumberOfPages"
                            :wrongPageValue="wrongPageValue">
                </pagination>

                <br class="clear">
            </div>

            <strings-table
                    v-model="tableControls"
                    :dictionary="dictionary"
                    :currentTab="currentTab"
                    :settings="settings"
                    :languageNames="languageNames"
                    :translationStatusFilters="translationStatusFilters"
                    :defaultActions="defaultActions"
                    :flagsPath="flagsPath"
                    :stEditorStrings="stEditorStrings"
                    :currentLanguage="currentLanguage"
                    :config="config"
            >
            </strings-table>
            <div class="trp-table-actions">
                <bulk-actions
                        :stEditorStrings="stEditorStrings"
                        :defaultActions="defaultActions"
                        :tableControls="tableControls"
                        :currentTab="currentTab"
                        :ajaxUrl="ajaxUrl"
                        :listenForEvents="false"
                        :dictionary="dictionary"
                >
                </bulk-actions>
                <pagination v-model.lazy.number="currentPage"
                            :stEditorStrings="stEditorStrings"
                            :totalItems="totalItems"
                            :totalNumberOfPages="totalNumberOfPages"
                            :wrongPageValue="wrongPageValue">
                </pagination>
            </div>
            <div class="trp-string-translation-end">

            </div>
        </div>
        <div v-if="!showFiltersAndTable" v-html="extraText" >

        </div>
    </div>
</template>

<script>
    import axios        from "axios"
    import he           from "he"
    import StringsTable from "./strings-table"
    import Pagination   from "./pagination"
    import BulkActions  from "./bulk-actions"

    export default {
        name       : "TranslationTab",
        components : { BulkActions, Pagination, StringsTable },
        props      : [
            'translationType',
            'parentTranslationType',
            'currentTab',
            'parentTab',
            'dictionary',
            'totalItems'
        ],
        data() {
            return {
                // trp_string_translation_data
                stEditorStrings          : trp_string_translation_data.st_editor_strings,
                defaultActions           : trp_string_translation_data.default_actions,
                translationStatusFilters : trp_string_translation_data.translation_status_filters,
                config                   : trp_string_translation_data.config,
                // trp_editor_data
                settings                 : trp_editor_data.trp_settings,
                languageNames            : trp_editor_data.language_names,
                ajaxUrl                  : trp_editor_data.ajax_url,
                flagsPath                : trp_editor_data.flags_path,
                nonces                   : trp_editor_data.editor_nonces,
                // data
                currentQuery             : this.$route.query,
                presentationData         : [],
                filterValues             : {},
                currentPage              : 1,
                wrongPageValue           : false,
                currentLanguage          : 'trp_default',
                tableControls            : { checkedStrings : [], selectAllOrVisible : '' },
                rescanButtonText         : trp_string_translation_data.st_editor_strings.rescan_gettext,
                scanningInProgress       : false,
                upgradedGettext          : trp_editor_data.upgraded_gettext,
                noticeUpgradeGettext     : trp_editor_data.notice_upgrade_gettext,
                noticeUpgradeSlugs       : trp_editor_data.notice_upgrade_slugs,
                upsaleSlugs              : trp_editor_data.upsale_slugs,
                upsaleSlugsText          : trp_editor_data.upsale_slugs_text,
                showFiltersAndTable      : false,
                extraText                : '',
                clearFilterDisabled      : true
            }
        },
        watch      : {
            currentPage : function ( newPage, oldPage ) {
                if ( newPage !== oldPage ){
                    let page = this.validatePage( newPage )
                    if ( page === null ){
                        this.wrongPageValue = true
                    } else {
                        this.wrongPageValue = false

                        // CurrentPage is modified based on queryurl. Make sure we are not detecting that change.
                        if ( this.$route.query[ 'page' ] != page ){
                            // deep copy is needed because Vue seems to not refresh the variable when adding page
                            let query       = Object.assign( {}, this.$route.query )
                            query[ 'page' ] = page

                            this.$router.push( { path : this.$router.path, query : query } ).catch( err => {
                                console.log( err )
                            } )
                        }
                    }
                }
            },
            $route( to, from ) {
                this.setFilterValues()
                this.setExtraText()
            }
        },
        computed   : {
            totalNumberOfPages : function () {
                if( this.totalItems === null ){
                    return 0
                }else {
                    return Math.ceil( this.totalItems / this.config.items_per_page )
                }
            },
            visibleTranslationStatusFilters : function () {
                let filters = Object.assign( {}, this.translationStatusFilters.translation_status )

                if ( this.currentTab.type !== 'gettext' ) {
                    delete filters.gettext_translated_in_language_file
                }

                return filters
            }
        },
        created() {
            this.setFilterValues()
            this.currentLanguage = this.filterValues[ 'language' ]
            this.setExtraText()
        },
        mounted() {

        },
        methods    : {
            navigate() {
                // vue throws navigation duplicate error
                // this.$router.push( { path : $this.route.path, query : { plan : 'private' } } ).catch( err => {
                // } )
            },
            filter() {
                let query = this.buildQuery( this.filterValues )
                this.clearFilterDisabled = ( Object.keys(query).length === 0 )

                this.$router.push( { "path": this.$router.path, "query": query } ).catch(err => {
                } )

                this.currentLanguage = this.filterValues[ 'language' ]

                this.currentPage = 1
            },
            clear_filter() {
                if ( ! this.clearFilterDisabled ){
                    this.clearFilterDisabled = true
                    this.$router.push( { "path": this.$router.path, query: {} } ).catch(err => {
                    } )

                    this.currentLanguage = 'trp_default'

                    this.currentPage = 1
                }
            },
            buildQuery(filterValues ) {
                let query = {}

                // translation status needs special treatment transforming filterValues into a single query value
                let statusValue          = null
                let boolAddStatusToQuery = false


                // check if all translation status values are the same (all checked, or all unchecked)
                for ( let status_key in this.visibleTranslationStatusFilters ) {
                    if ( this.visibleTranslationStatusFilters.hasOwnProperty( status_key ) ){
                        if ( statusValue === null ){
                            statusValue = filterValues[ status_key ]
                        }
                        if ( statusValue !== filterValues[ status_key ] ){
                            boolAddStatusToQuery = true
                        }
                    }
                }

                // if translation status are different then include them in the query
                if ( boolAddStatusToQuery ){
                    query = Object.assign( query, this.buildQueryForFilter( this.visibleTranslationStatusFilters, filterValues ) )
                }

                // rest of the filter are added only if different from trp_default
                query = Object.assign( query, this.buildQueryForFilter( this.currentTab.filters, filterValues ) )

                // set query for language
                if ( filterValues[ 'language' ] !== 'trp_default' ){
                    query[ 'language' ] = filterValues[ 'language' ]
                }

                // set query for search term
                if ( filterValues[ 's' ] !== '' ){
                    query[ 's' ] = filterValues[ 's' ]
                }

                // keep sorting parameters from query
                if ( this.$route.query[ 'order' ] && (this.$route.query[ 'order' ] === 'asc' || this.$route.query[ 'order' ] === 'desc') ){
                    query[ 'order' ] = this.$route.query[ 'order' ]
                }
                if ( this.$route.query[ 'orderby' ] && this.currentTab[ 'table_columns' ][ this.$route.query[ 'orderby' ] ] ){
                    query[ 'orderby' ] = this.$route.query[ 'orderby' ]
                }

                return query

            },
            buildQueryForFilter(filter, filterValues ) {
                let returnQuery = {}
                for ( let option_key in filter ) {
                    if ( filter.hasOwnProperty( option_key ) && filterValues[ option_key ] !== 'trp_default' ){
                        returnQuery[ option_key ] = filterValues[ option_key ]
                    }
                }
                return returnQuery
            },

            /*
             * Set binded variables for filters, paging and sorting to either default or query arguments if exists
             */
            setFilterValues() {

                // translation status defaults
                this.filterValues.translation_status = {}
                for ( let status_key in this.visibleTranslationStatusFilters ) {
                    if ( this.visibleTranslationStatusFilters.hasOwnProperty( status_key ) ){

                        if ( typeof this.$route.query[ status_key ] !== 'undefined' ){
                            /* If the url query parameter is written by the user in url then it's a string.
                             * If we programatically set the query then it's a boolean.
                             */
                            this.filterValues[ status_key ] = (!(this.$route.query[ status_key ] === 'false' || this.$route.query[ status_key ] === false))
                        } else {
                            this.filterValues[ status_key ] = true
                        }
                    }
                }

                // language default
                if ( typeof this.$route.query[ 'language' ] !== 'undefined' && this.settings[ 'translation-languages' ].includes( this.$route.query[ 'language' ] ) ){
                    //make sure language from url is correct
                    this.filterValues[ 'language' ] = this.$route.query[ 'language' ]
                } else {
                    this.filterValues[ 'language' ] = 'trp_default'
                }


                // specific filters
                for ( let filter_key in this.currentTab.filters ) {
                    if ( this.currentTab.filters.hasOwnProperty( filter_key ) ){
                        if ( typeof this.$route.query[ filter_key ] !== 'undefined' && typeof this.currentTab.filters[ filter_key ][ this.$route.query[ filter_key ] ] !== 'undefined' ){
                            // make sure the url has correct query argument
                            this.filterValues[ filter_key ] = this.$route.query[ filter_key ]
                        } else {
                            // Set trp_default if exists. Else first value.
                            if ( this.currentTab.filters[ filter_key ][ 'trp_default' ] ){
                                this.filterValues[ filter_key ] = 'trp_default'
                            } else {
                                this.filterValues[ filter_key ] = Object.keys( this.currentTab.filters[ filter_key ] )[ 0 ]
                            }
                        }
                    }
                }

                //search term
                if ( typeof this.$route.query[ 's' ] !== 'undefined' && this.$route.query[ 's' ] !== '' ){
                    //todo maybe do some sanitizing
                    this.filterValues[ 's' ] = this.$route.query[ 's' ]
                } else {
                    this.filterValues[ 's' ] = ''
                }

                //paging
                if ( typeof this.$route.query[ 'page' ] !== 'undefined' && this.validatePage( this.$route.query[ 'page' ] ) !== null ){
                    this.currentPage = this.validatePage( this.$route.query[ 'page' ] )
                } else {
                    this.currentPage = 1
                }

            },
            validatePage(pageNumber ) {
                let parsedPageNumber = parseInt( pageNumber )
                if ( (1 <= parsedPageNumber) && (this.totalItems === null || parsedPageNumber <= this.totalNumberOfPages ) ){
                    return parsedPageNumber
                } else {
                    return null
                }
            },
            startGettextScan() {
                this.scanningInProgress = true
                this.rescanButtonText   = this.stEditorStrings.scanning_gettext;
                this.sendAjaxToScanGettext()
            },
            sendAjaxToScanGettext(){
                let data = new FormData()
                let self = this
                data.append('action', 'trp_scan_gettext')
                data.append('security', this.nonces['scangettextnonce'])
                axios.post(this.ajaxUrl, data)
                     .then(function (response) {
                         if ( response != null && response.data.progress_message ){
                             if ( response.data.completed === true ){
                                 self.rescanButtonText = self.stEditorStrings.gettext_scan_completed
                                 self.scanningInProgress = false
                             }else{
                                 self.rescanButtonText = response.data.progress_message
                                 self.sendAjaxToScanGettext()
                             }
                         }else{
                             self.rescanButtonText = self.stEditorStrings.gettext_scan_error
                             self.scanningInProgress = false
                         }
                         //self.$emit('translations-saved')
                     })
                     .catch(function (error) {
                         console.log(error)
                     });
            },
            setExtraText(){
                this.showFiltersAndTable = ! ( ( !this.upgradedGettext && (this.currentTab.type === 'gettext' || this.currentTab.type === 'emails' ) ) || this.currentTab.type === 'upsale-slugs'
                                           || ( !['upsale-slugs', 'gettext', 'emails'].includes( this.currentTab.type ) && this.noticeUpgradeSlugs ) )
                this.extraText = (this.currentTab.type === 'gettext') ? this.noticeUpgradeGettext : this.extraText
                this.extraText = (this.currentTab.type === 'upsale-slugs') ? this.upsaleSlugsText : this.extraText
                this.extraText = ( !this.currentTab.type  && this.noticeUpgradeSlugs ) ? this.noticeUpgradeSlugs : this.extraText

                let query = this.buildQuery( this.filterValues )
                this.clearFilterDisabled = ( Object.keys(query).length === 0 )
            }
        }
    }
</script>
