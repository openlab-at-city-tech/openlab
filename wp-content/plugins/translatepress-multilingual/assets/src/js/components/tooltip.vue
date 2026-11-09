<template>
    <div class="trp-tooltip-container" style="visibility:hidden;" v-if="!this.userMeta[this.userMetaHandle]">
        <div class="trp-tooltip-triangle"></div>
        <div class="trp-dismissible-tooltip">
            <div class="trp-close-tooltip" @click="dismiss" :title="editorStrings.dismiss_tooltip_title_attribute"></div>
            <div class="trp-tooltip-text">{{text}}</div>
        </div>
    </div>
</template>

<script>
export default {
    props : [
        'text',
        'editorStrings',
        'userMetaHandle',
        'userMeta'
    ],
    methods : {
        dismiss : function(){
            let self = this
            document.dispatchEvent( new CustomEvent( 'trp_update_user_meta', {
                'detail' : {
                    'userMetaKey' : self.userMetaHandle,
                    'userMetaValue' : true,
                }
            } ) )
        }
    }
}
</script>