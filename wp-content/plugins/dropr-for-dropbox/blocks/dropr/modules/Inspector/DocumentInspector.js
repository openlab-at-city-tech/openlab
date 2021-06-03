import DroprHelper from '../helper';

const { __ } = wp.i18n;
const { Component } = wp.element;
const { InspectorControls } = wp.editor;
const {
    PanelBody,
    TextControl,
    SelectControl
} = wp.components;

class DroprDocumentInspector extends Component {
    constructor() {
        super(...arguments);
    }

    setDocumentAttributes(attr, val) {
        let documentAttrs = { ...this.props.attributes.document };
        documentAttrs[attr] = val;
        this.props.setAttributes({
            document: documentAttrs
        });
    }

    render() {
        const { attributes: { document } } = this.props;
        let viewerOptions = [
            { value: 'dropbox', label: __( 'Dropbox', 'dropr' ) },
            { value: 'google', label: __( 'Google Docs Viewer', 'dropr' ) }
        ];
        if( DroprHelper.isValidMSExtension(document.url) ) {
            viewerOptions.push({ value: 'microsoft', label: __( 'Microsoft Office Online', 'dropr' ) });
        }

        return (
            <InspectorControls>
                <PanelBody title={ __( 'Document Settings', 'dropr' ) }>
                    <TextControl label={ __( 'Width', 'dropr' ) } help={ __( 'Width of document either in px or in %', 'dropr' ) } value={ document.width } onChange={ (width) => this.setDocumentAttributes('width', width) } />

                    <TextControl label={ __( 'Height', 'dropr' ) } help={ __( 'Height of document either in px or in %', 'dropr' ) } value={ document.height } onChange={ (height) => this.setDocumentAttributes('height', height) } />

                    <SelectControl label={ __( 'Show Download Link', 'dropr' ) } options={[
                        { value: 'all', label: __( 'For all users', 'dropr' ) },
                        { value: 'logged', label: __( 'For Logged-in users', 'dropr' ) },
                        { value: 'none', label: __( 'No Download', 'dropr' ) }
                    ]} value={ document.download } onChange={ (download) => this.setDocumentAttributes('download', download) } />

                    <SelectControl label={ __( 'Viewer', 'dropr' ) } options={ viewerOptions } value={ document.viewer } onChange={ (viewer) => this.setDocumentAttributes('viewer', viewer) } />
                </PanelBody>
            </InspectorControls>
        );
    }
}

export default DroprDocumentInspector;