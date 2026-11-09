<template>
    <tr>
        <th id="cb" class="manage-column column-cb check-column trp-check-column" style="width: 20px">
<!--            <select ref="selectAllOrVisible" class="trp-select-all" :value="value.selectAllOrVisible"-->
<!--                    @input="updateValue()" :title="stEditorStrings.select_all_tooltip">-->
<!--                <option value="select_all">{{stEditorStrings.select_all}}</option>-->
<!--                <option value="select_visible">{{stEditorStrings.select_visible}}</option>-->
<!--            </select>-->
        </th>
        <template v-for="(column, column_key) in currentTab['table_columns']" :key="column_key">
            <th v-if="currentLanguage !== 'trp_default' || ( column_key !== 'translated' &&  column_key !== 'id' )"
                scope="col"
                :id="'trp-column-' + column_key"
                class="manage-column column-primary trp-fixed-columns"
                :class="{'sorted' : orderBy === column_key,
                'sortable' : orderBy !== column_key,
                'asc' : orderBy === column_key && order === 'asc',
                'desc' : ( orderBy === column_key && order === 'desc' ) || ( orderBy !== column_key )
            }"
                :title="column_key === 'id' ? stEditorStrings.post_id_column_tooltip : null"
                @click="sortByColumn(column_key)"
            >
               <span class="trp-tooltip-toggle trp-tooltip-toggle-table-head" :data-tooltip="(column_key === 'original')?stEditorStrings.sort_by_column:''" style="visibility: hidden">
                    <a v-if="column_key === 'original'" class="trp-anchor-action">
                        <span>{{column}}</span>
                        <span class="sorting-indicator"></span>
                    </a>
                    <span v-else>{{column}}</span>
               </span>
            </th>
        </template>

        <th scope="col"
            class="manage-column trp-translation-status-column" v-if="currentLanguage !== 'trp_default'"
        >
            {{languageNames[currentLanguage]}} {{stEditorStrings.translation_status}}
        </th>
    </tr>
</template>
<script>
    export default {
        props   : [
            'modelValue',
            'stEditorStrings',
            'currentLanguage',
            'currentTab',
            'languageNames'
        ],
        data() {
            return {
                order   : '',
                orderBy : ''
            }
        },
        created() {
            this.setOrderValues()
        },
        watch : {
            $route( to, from ) {
                this.setOrderValues()
            }
        },
        methods : {
            setOrderValues : function (){
                if ( this.$route.query[ 'order' ] && ( this.$route.query[ 'order' ] === 'asc' || this.$route.query[ 'order' ] === 'desc' ) ){
                    this.order = this.$route.query[ 'order' ]
                }
                if (  this.$route.query[ 'orderby' ] && this.currentTab['table_columns'][this.$route.query[ 'orderby' ]] ){
                    this.orderBy = this.$route.query[ 'orderby' ]
                }
            },
            sortByColumn  : function ( columnKey ) {
                if ( columnKey !== 'original' ){
                    return
                }
                let newOrder
                switch ( this.order ) {
                    case 'asc':
                        newOrder = 'desc'
                        break
                    case 'desc' :
                    default:
                        newOrder = 'asc'
                        break
                }
                this.order   = newOrder
                this.orderBy = columnKey

                // Order and orderby are modified based on queryurl. Make sure we are not detecting that change.
                if ( this.$route.query[ 'order' ] != this.order ){
                    // deep copy is needed because Vue seems to not refresh the variable when adding page
                    let query          = Object.assign( {}, this.$route.query )
                    query[ 'order' ]   = this.order
                    query[ 'orderby' ] = this.orderBy
                    query[ 'page' ] = '1'
                    this.$router.push( { path : this.$router.path, query : query } ).catch( err => {
                        console.log( err )
                    } )
                }
            },
            updateValue   : function () {
                this.$emit( 'update:modelValue', {
                    selectAllOrVisible : this.$refs.selectAllOrVisible.value
                } )
            }
        }
    }
</script>