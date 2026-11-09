<template>
    <div class="trp-help-panel" :class="{'trp-help-panel-open': helpPanelOpen }">
        <div class="trp-inner-panel">
            <div class="trp-help-panel-title">
                {{helpPanelContent[page].title}}
            </div>
            <div class="trp-help-panel-content" v-html="helpPanelContent[page].content"></div>
        </div>
        <div class="trp-help-panel-pagination">
            <span>{{page + 1}}/{{helpPanelContent.length}}</span>
            <span>
                    <a class="trp-link-button trp-link-previous" :class="{'trp-link-button-disabled': ( page <= 0 ) }"
                       @click="page = (page <= 0 ) ? 0 : page - 1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width='25' height="25" aria-hidden="true" focusable="false"><path d="M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"></path></svg></a>

                    <a class="trp-link-button"
                       :class="{'trp-link-button-disabled': ( page >= helpPanelContent.length - 1 ) }"
                       @click="page = (page >= helpPanelContent.length - 1) ? page : page + 1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="25" height="25" aria-hidden="true" focusable="false"><path d="M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"></path></svg></a>
                </span>
        </div>
    </div>
</template>

<script>
    export default {
        props : [
            'helpPanelContent',
            'editorStrings',
            'helpPanelOpen'
        ],
        data() {
            return {
                page : 0
            }
        },
        watch : {
            page : function () {
                window.dispatchEvent(new Event('trp_help_panel_changed'));
                window.dispatchEvent(new Event(this.helpPanelContent[this.page].event));
            },
            helpPanelOpen : function () {
                if( this.helpPanelOpen ){
                    window.dispatchEvent( new Event( this.helpPanelContent[ this.page ].event ) );
                }else{
                    window.dispatchEvent(new Event('trp_help_panel_changed'));
                }
            }
        }
    }
</script>