const { __ } = wp.i18n;
const { Component } = wp.element;
const { InspectorControls } = wp.editor;
const {
    PanelBody,
    TextControl,
    ToggleControl,
    RadioControl
} = wp.components;

class DroprLinkInspector extends Component {
    constructor() {
        super(...arguments);
    }

    setLinkAttributes(attr, val) {
        let linkAttrs = { ...this.props.attributes.link };
        linkAttrs[attr] = val;
        this.props.setAttributes({
            link: linkAttrs
        });
    }

    render() {
        const { attributes: { link } } = this.props;
        return (
            <InspectorControls>
                <PanelBody title={ __( 'Link Settings', 'dropr' ) }>
                    <TextControl label={ __( 'Link Text', 'dropr' ) } value={ link.text } onChange={ (text) => this.setLinkAttributes('text', text) } />

                    <RadioControl label={ __( 'Link Style', 'dropr' ) } help={ __( 'Choose a style for the download link', 'dropr' ) } selected={ link.class } options={[
                        { value: 'plain', label: __( 'Plain', 'dropr' ) },
                        { value: 'wpdropbox-button', label: __( 'Button', 'dropr' ) }
                    ]} onChange={ (style) => this.setLinkAttributes('class', style) } />

                    <ToggleControl label={ __( 'Open in New Tab', 'dropr' ) } checked={ link.target } onChange={ (target) => this.setLinkAttributes('target', target) } />
                </PanelBody>
            </InspectorControls>
        );
    }
}

export default DroprLinkInspector;