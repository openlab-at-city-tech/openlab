const { __ } = wp.i18n;
const { Component } = wp.element;
const { InspectorControls } = wp.editor;
const {
    PanelBody,
    ToggleControl
} = wp.components;

class DroprMediaInspector extends Component {
    constructor() {
        super(...arguments);
    }

    setMediaAttributes(attr, val) {
        let mediaAttrs = { ...this.props.attributes.media };
        mediaAttrs[attr] = val;
        this.props.setAttributes({
            media: mediaAttrs
        });
    }

    render() {
        const { attributes: { media } } = this.props;
        return (
            <InspectorControls>
                <PanelBody title={ __( 'Media Settings', 'dropr' ) }>
                    <ToggleControl label={ __( 'Autoplay', 'dropr' ) } checked={ media.autoplay } onChange={ (autoplay) => this.setMediaAttributes('autoplay', autoplay) } />

                    <ToggleControl label={ __( 'Loop', 'dropr' ) } checked={ media.loop } onChange={ (loop) => this.setMediaAttributes('loop', loop) } />
                </PanelBody>
            </InspectorControls>
        );
    }
}

export default DroprMediaInspector;