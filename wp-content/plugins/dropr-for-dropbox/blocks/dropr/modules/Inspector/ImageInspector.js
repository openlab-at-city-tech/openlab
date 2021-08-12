const { __ } = wp.i18n;
const { Component } = wp.element;
const { InspectorControls } = wp.editor;
const {
    PanelBody,
    TextControl,
    TextareaControl
} = wp.components;

class DroprImageInspector extends Component {
    constructor() {
        super(...arguments);
    }

    setImageAttributes(attr, val) {
        let imageAttrs = { ...this.props.attributes.image };
        imageAttrs[attr] = val;
        this.props.setAttributes({
            image: imageAttrs
        });
    }

    render() {
        const { attributes: { image } } = this.props;
        return (
            <InspectorControls>
                <PanelBody title={ __( 'Image Settings', 'dropr' ) }>
                    <TextControl label={ __( 'Width', 'dropr' ) } help={ __( 'Width of Image in px', 'dropr' ) } value={ image.width } onChange={ (width) => this.setImageAttributes('width', width) } />

                    <TextControl label={ __( 'Height', 'dropr' ) } help={ __( 'Height of Image in px', 'dropr' ) } value={ image.height } onChange={ (height) => this.setImageAttributes('height', height) } />

                    <TextareaControl
						label={ __( 'Alt Text (Alternative Text)' ) }
						value={ image.alt }
						onChange={ (alt) => this.setImageAttributes('alt', alt) }
						help={ __( 'Alternative text describes your image to people who can’t see it. Add a short description with its key details.' ) }
					/>
                </PanelBody>
            </InspectorControls>
        );
    }
}

export default DroprImageInspector;