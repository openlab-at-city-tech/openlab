<template>
    <span class="trp-view-more-string">
        <span @click="$emit('click')">{{(seeMore) ? string : shortString }}</span>
        <span class="trp-see-more" @click="seeMore= !seeMore" v-if="isLongString">{{(seeMore) ? stEditorStrings.see_less : stEditorStrings.see_more}} </span>
        <span @click="$emit('click')" v-if="foundInTranslation">({{stEditorStrings.found_in_translation}})</span>
    </span>
</template>
<script>
    export default {
        props    : [
            'string',
            'stEditorStrings',
            'config',
            'foundInTranslation'
        ],
        data() {
            return {
                seeMore   : false,
                maxLength : this.config.see_more_max_length
            }
        },
        computed : {
            shortString  : function () {
                if ( this.isLongString ){
                    return this.string.substr( 0, this.maxLength ) + '...'
                } else {
                    return this.string
                }
            },
            isLongString : function () {
                return (this.string.length > this.maxLength)
            }
        }
    }
</script>