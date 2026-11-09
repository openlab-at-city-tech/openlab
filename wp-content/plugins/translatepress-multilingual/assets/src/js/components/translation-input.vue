<template>
    <div class="translation-input" :class="{'trp-highlight-unsaved-changes':highlightUnsavedChanges}">
        <div v-if="inputType == 'textarea'" class="trp-translation-input-parent">
            <textarea class="trp-translation-input trp-textarea" :readonly="readonly" ref="textarea" :value="getValue()" @input="updateValue()"></textarea>
        </div>
        <div v-if="inputType == 'input'" class="trp-translation-input-parent">
            <input class="trp-translation-input trp-input" readonly :value="getValue()" type="text">
        </div>
        <div v-if="inputType == 'inputmedia'" class="trp-translation-input-parent trp-input-media-parent">
            <input v-show="inputType == 'inputmedia'" type="button" class="trp-add-media button" :value="editorStrings.add_media" @click="uploadMediaFrame.open()">
            <div class="trp-input-media-container">
                <input class="trp-translation-input trp-input trp-input-media" type="text" :placeholder="this.placeholder" :readonly="readonly" ref="inputmedia" :value="getValue()" @input="updateValue( null )">
            </div>
        </div>
    </div>
</template>
<script>
import he from 'he'
import autosize from 'autosize'
import utils from '../utils'

export default{
    props:[
        'modelValue',
        'string',
        'readonly',
        'highlightUnsavedChanges',
        'editorStrings',
        'nonces',
    ],
    data(){
        return{
            inputType        : 'textarea',
            uploadMediaFrame : null,
            placeholder      : ''
        }
    },
    mounted(){
        let inputTypeArray = {
            ''            : 'textarea',
            'content'     : 'textarea',
            'alt'         : 'textarea',
            'title'       : 'textarea',
            'placeholder' : 'textarea',
            'outertext'   : 'textarea',
            'value'       : 'textarea',
            'src'         : 'inputmedia',
            'href'        : 'inputmedia',
            'poster'      : 'inputmedia'
        };
        this.inputType = ( inputTypeArray[this.string.attribute] ) ? inputTypeArray[this.string.attribute] : 'textarea'
        this.inputType = ( utils.isURL( this.string.original ) && this.string.attribute == "content" ) ? "inputmedia" : this.inputType;
        this.inputType = (this.readonly && this.inputType === 'inputmedia' ) ? 'input' : this.inputType;

        this.$nextTick(() => {
            autosize(document.querySelectorAll('.trp-textarea'))
        });

        if ( this.inputType === 'inputmedia' ) {
            this.setupMediaUploader()
            if ( this.string.attribute === 'href' ) {
                this.placeholder = 'http://example.com/'
            }
        }

    },
    updated() {
        // Remeasure every time content is updated
        autosize.update(this.$refs.textarea);
    },
    methods:{
        getValue(){
            if( this.modelValue ){
                let decoded = he.decode( this.modelValue )

                // if we are on the String Translation, try transforming the slug into readable characters
                if ( window.tpStringTranslationApp ){
                  try {
                      return decodeURI( decoded )
                  } catch ( err ) {
                      return decoded
                  }
                }else {
                    return decoded
                }
            }
            return this.modelValue
        },
        updateValue( value ){
            value = ( value ) ? value : this.$refs[this.inputType].value
            this.$emit( 'update:modelValue', value )
        },
        setupMediaUploader(){
            // Create a new media frame
            let self = this

            this.uploadMediaFrame = wp.media({
                title: self.editorStrings.select_or_upload,
                button: {
                    text: self.editorStrings.use_this_media
                },
                multiple: false  // Set to true to allow multiple files to be selected
            })

            // When an image is selected in the media frame...
            this.uploadMediaFrame.on( 'select', function() {
                // Get media attachment details from the frame state
                let attachment = self.uploadMediaFrame.state().get('selection').first().toJSON();

                // Send the attachment URL to our custom image input field.
                self.updateValue(attachment.url)
            });
        },
    }
}
</script>
