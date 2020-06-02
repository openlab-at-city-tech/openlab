const { __ }        = wp.i18n;
const { memoize }   = lodash

// Given jQuery and the Freeform block settings,  creates the Ngg Editor Component
export const createNggEditComponent = memoize(($, Freeform) => {
    class NggEditor extends Freeform {
        constructor(props) {
            super(props)
            this.openIGW            = this.openIGW.bind(this)
            this.updateContent      = this.updateContent.bind(this)
            this.hasGallery         = this.hasGallery.bind(this)
            this.toggleAddGalleryBtn= this.toggleAddGalleryBtn.bind(this)
        }

        hasGallery() {
            return this.props.attributes.content && this.props.attributes.content.length > 0
        }

        componentDidMount() {
            super.componentDidMount()
            $(this.ref).addClass('freeform-toolbar').addClass('ngg-freeform-toolbar')
            this.toggleAddGalleryBtn();

        }

        componentDidUpdate() {
            this.toggleAddGalleryBtn();
        }

        toggleAddGalleryBtn() {
            // Determine the UI state
            if (this.props.attributes.content) {
                $(this.ref).addClass('hidden');
                $(this.ref).siblings('.add-ngg-gallery-wrap').hide()
            }
            else {
                $(this.ref).removeClass('hidden');
                $(this.ref).siblings('.add-ngg-gallery-wrap').show()
            }
        }

        updateContent(content) {
            this.props.setAttributes({content})        
        }

        onSetup(editor){
            super.onSetup(editor);
            const updateContent     = this.updateContent.bind(this)

            // When NGG is added or removed, we must set the classic "content" attribute, 
            // which will re-render our component
            editor.on('ngg-removed', () => updateContent(""))
            editor.on('ngg-inserted', ({shortcode}) => updateContent(shortcode))
        }

        openIGW() {
            this.editor.execCommand('ngg_attach_to_post')
        }

        render(){
            const classic = super.render()
            return [
                classic,
                <div className="add-ngg-gallery-wrap">
                    <div className="add-ngg-gallery" onClick={this.openIGW}>
                        {ngg_tinymce_plugin.i18n.button_label}
                    </div>
                </div>
            ]
        }
    }

    return NggEditor
})