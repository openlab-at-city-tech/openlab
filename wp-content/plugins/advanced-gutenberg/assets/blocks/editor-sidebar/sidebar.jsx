(function ( wpI18n, wpPlugins, wpElement, wpData, wpComponents, wpEditPost ) {
    const { __ } = wpI18n;
    const { registerPlugin } = wpPlugins;
    const { Component, Fragment } = wpElement;
    const { select, withSelect, withDispatch } = wpData;
    const { PanelBody, ButtonGroup, Button } = wpComponents;
    const { PluginSidebar, PluginSidebarMoreMenuItem } = wpEditPost;
    const { compose } = wp.compose;

    const sidebarName  = "advgb-editor-sidebar";
    const sidebarTitle = __( 'PublishPress Blocks Settings', 'advanced-gutenberg' );
    const sidebarIcon  = "layout";
    const VISUAL_GUIDE_SETTINGS = [
        { label: __( 'Inherit from global settings', 'advanced-gutenberg' ), value: '' },
        { label: __( 'Enable', 'advanced-gutenberg' ), value: 'enable' },
        { label: __( 'Disable', 'advanced-gutenberg' ), value: 'disable' },
    ];
    const EDITOR_WIDTH_SETTINGS = [
        { label: __( 'Inherit from global settings', 'advanced-gutenberg' ), value: '' },
        { label: __( 'Original', 'advanced-gutenberg' ), value: 'default' },
        { label: __( 'Large', 'advanced-gutenberg' ), value: 'large' },
        { label: __( 'Full width', 'advanced-gutenberg' ), value: 'full' },
    ];

    const updateBodyClass = function () {
        const postMetaData = select( 'core/editor' ).getEditedPostAttribute( 'meta' );
        if (!postMetaData) return null;
        const { advgb_blocks_editor_width, advgb_blocks_columns_visual_guide } = postMetaData;
        const bodyClass = window.document.body.classList;

        bodyClass.remove(
            'advgb-editor-width-default',
            'advgb-editor-width-large',
            'advgb-editor-width-full',
            'advgb-editor-col-guide-enable',
            'advgb-editor-col-guide-disable',
        );
        
        // Editor width
        if (!!advgb_blocks_editor_width) {
            bodyClass.add( 'advgb-editor-width-' + advgb_blocks_editor_width );
        } else {
            // Global
            bodyClass.add( 'advgb-editor-width-' + advg_settings.editor_width_global );
        }
        
        // Columns visual guide
        if (!!advgb_blocks_columns_visual_guide) {
            bodyClass.add( 'advgb-editor-col-guide-' + advgb_blocks_columns_visual_guide );
        } else {
            // Global
            bodyClass.add( 'advgb-editor-col-guide-' + advg_settings.enable_columns_visual_guide_global );
        }
    };

    // Line below stopped working - https://github.com/WordPress/gutenberg/issues/28032#issuecomment-759723289
    // window.document.addEventListener("DOMContentLoaded", updateBodyClass);

    class AdvSidebar extends Component {
        constructor() {
            super( ...arguments );
        }

        onUpdateMeta( metaData ) {
            const { metaValues, updateMetaField } = this.props;
            const meta = { ...metaValues, ...metaData };

            updateMetaField( meta );
            updateBodyClass();
        }

        render() {
            const { columnsVisualGuide, editorWidth } = this.props;

            return (
                <Fragment>
                    <div className="advgb-editor-sidebar-note">
                        { __( 'These settings will override the PublishPress Blocks global settings.', 'advanced-gutenberg' ) }
                    </div>
                    <PanelBody title={ __( 'Editor width', 'advanced-gutenberg' ) }>
                        <div className="advgb-editor-sidebar-note">
                            { __( 'Change your editor width', 'advanced-gutenberg' ) }
                        </div>
                        <ButtonGroup className="advgb-button-group">
                            {EDITOR_WIDTH_SETTINGS.map((setting, index) => (
                                <Button className="advgb-button"
                                        key={ index }
                                        isSecondary={ setting.value !== editorWidth }
                                        isPrimary={ setting.value === editorWidth }
                                        onClick={ () => this.onUpdateMeta( { advgb_blocks_editor_width: setting.value } ) }
                                >
                                    { setting.label }
                                </Button>
                            ) ) }
                        </ButtonGroup>
                    </PanelBody>
                    <PanelBody title={ __( 'Columns Visual Guide', 'advanced-gutenberg' ) } initialOpen={ false }>
                        <div className="advgb-editor-sidebar-note">
                            { __( 'Border to materialize PublishPress Blocks Column block', 'advanced-gutenberg' ) }
                        </div>
                        <ButtonGroup className="advgb-button-group">
                            {VISUAL_GUIDE_SETTINGS.map((setting, index) => (
                                <Button className="advgb-button"
                                        key={ index }
                                        isSecondary={ setting.value !== columnsVisualGuide }
                                        isPrimary={ setting.value === columnsVisualGuide }
                                        onClick={ () => this.onUpdateMeta( { advgb_blocks_columns_visual_guide: setting.value } ) }
                                >
                                    { setting.label }
                                </Button>
                            ) ) }
                        </ButtonGroup>
                    </PanelBody>
                </Fragment>
            )
        }
    }

    const AdvSidebarRender = compose(
        withDispatch( ( dispatch ) => {
            return {
                updateMetaField: ( data ) => {
                    dispatch( 'core/editor' ).editPost(
                        { meta: data }
                    );
                },
            }
        } ),
        withSelect( ( select ) => {
            const metaValues = select( 'core/editor' ).getEditedPostAttribute( 'meta' );

            return {
                metaValues: metaValues,
                columnsVisualGuide: metaValues.advgb_blocks_columns_visual_guide,
                editorWidth: metaValues.advgb_blocks_editor_width,
            }
        } )
    )( AdvSidebar );

    registerPlugin( 'advgb-editor-sidebar', {
        render: function () {
            return (
                <Fragment>
                    <PluginSidebarMoreMenuItem
                        target={ sidebarName }
                        icon={ sidebarIcon }
                    >
                        { sidebarTitle }
                    </PluginSidebarMoreMenuItem>
                    <PluginSidebar
                        name={ sidebarName }
                        title={ sidebarTitle }
                        icon={ sidebarIcon }
                    >
                        <div className="advgb-editor-sidebar-content">
                            <AdvSidebarRender />
                        </div>
                    </PluginSidebar>
                </Fragment>
            )
        }
    } );
})( wp.i18n, wp.plugins, wp.element, wp.data, wp.components, wp.editPost );