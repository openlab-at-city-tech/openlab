<template>
    <div class="trp-pagination">
        <span class="displaying-num">{{( totalItems === null) ? 0 : totalItems }} {{stEditorStrings.items}}</span>
        <span class="pagination-links">
            <a href="#" :title="stEditorStrings.previous_page"
              @click.prevent="$emit( 'update:modelValue',  ( modelValue <= 1 ) ? modelValue : modelValue - 1 )"
              :class="{ 'disabled' : modelValue <= 1 }"><span><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width='30' height="30" aria-hidden="true" focusable="false"><path d="M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"></path></svg></span></a>

            <span class="trp-tooltip-toggle trp-tooltip-toggle-pagination" :data-tooltip="(wrongPageValue) ? stEditorStrings.wrong_page : stEditorStrings.navigate_to_page">
                <span class="paging-input">
                    <input class="current-page" :class="{ 'wrong-value' : wrongPageValue }" type="text" name="paged" size="1"
                       aria-describedby="table-paging" :value="modelValue" @change="$emit( 'update:modelValue', $event.target.value)">
                </span>
                <span class="tablenav-paging-text">
                    {{stEditorStrings.of}}
                    <span class="total-pages">{{ totalNumberOfPages }}</span>
                </span>
            </span>

            <a href="#" :title="stEditorStrings.next_page"
              :class="{ 'disabled' : modelValue >= totalNumberOfPages }"
              @click.prevent="$emit( 'update:modelValue',( modelValue >= totalNumberOfPages ) ? modelValue : modelValue + 1 )"><span><span style="z-index: 20"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30" aria-hidden="true" focusable="false"><path d="M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"></path></svg></span></span></a>
        </span>
    </div>
</template>

<script>

  import Tooltip from "../tooltip"
    export default {
        components : { Tooltip },
        props : [
            'modelValue',
            'totalNumberOfPages',
            'stEditorStrings',
            'totalItems',
            'wrongPageValue',
            'editorStrings'
        ],
        data() {
            return {
                page : this.modelValue
            }
        },
        watch : {
            modelValue : function ( newValue, oldValue ) {
                this.page = newValue
            }
        }
    }
</script>