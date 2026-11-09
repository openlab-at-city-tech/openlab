<template>
    <div>
        <div class="trp-controls-section" v-if="showLanguagesMessage()">
            <div id="trp-translation-section" class="trp-controls-section-content">
                <p v-html="editorStrings.extra_lang_row1"></p>
                <p v-html="editorStrings.extra_lang_row2"></p>
                <p v-html="editorStrings.extra_lang_row3"></p>
            </div>
        </div>

        <div class="trp-controls-section wp-core-ui" id="trp-upsell-section-container" v-if="showUpsellMessage()">
          <h3 id="trp-upsell-section-title">{{ editorStrings.extra_upsell_title }}</h3>
            <div id="trp-upsell-section" class="trp-controls-section-content">
                <strong v-if="showBlackFridayMessage()">
                    {{ editorStrings.extra_upsell_bf_row1 }}
                </strong>

                <p v-if="showBlackFridayMessage()">
                    {{ editorStrings.extra_upsell_bf_row2 }}
                </p>

                <ul>
                    <li>{{ editorStrings.extra_upsell_row1 }}</li>
                    <li>{{ editorStrings.extra_upsell_row2 }}</li>
                    <li>{{ editorStrings.extra_upsell_row3 }}</li>
                    <li>{{ editorStrings.extra_upsell_row4 }}</li>
                    <li>{{ editorStrings.extra_upsell_row5 }}</li>
                    <li>{{ editorStrings.extra_upsell_row6 }}</li>
                    <li>{{ editorStrings.extra_upsell_row7 }}</li>
                </ul>
                <p v-html="editorStrings.extra_upsell_button" v-if="!showBlackFridayMessage()"></p>
                <p v-html="editorStrings.extra_upsell_bf_button" v-if="showBlackFridayMessage()"></p>
            </div>
        </div>
    </div>
</template>
<script>
export default{
    props:[
        'languageNames',
        'editorStrings',
        'paidVersion',
        'blackFriday',
        'licenseStatus',
    ],
    methods:{
        showLanguagesMessage(){
            if( Object.keys( this.languageNames ).length == 1 )
                return true

            return false
        },
        showUpsellMessage(){
            if( this.paidVersion != 'true' )
                return true
            
            // when a license is expired we show a different message which sends them to the account page so we don't want to show this one
            if( this.licenseStatus == 'expired' || this.licenseStatus == 'revoked' )
                return false

            if( this.showBlackFridayMessage() == true )
                return true

            return false
        },
        showBlackFridayMessage(){
            if( this.blackFriday == 'true' )
                return true

            return false
        }
    }
}
</script>
