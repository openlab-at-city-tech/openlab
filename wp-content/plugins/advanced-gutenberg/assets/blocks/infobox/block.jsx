import {AdvColorControl} from "../0-adv-components/components.jsx";
import {IconListPopupHook} from "../0-adv-components/icon-class.jsx";

(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, BlockControls, AlignmentToolbar, RichText } = wpBlockEditor;
    const { BaseControl, PanelBody, RangeControl, SelectControl, Button } = wpComponents;

    const previewImageData = 'data:image/png;base64,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';

    const blockIcon = (
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 250 250"><path d="M114,110h23v69.1h-23V110z M114,63.9v23h23v-23H114z M225,22v11v177v11h-11H37H26v-11V33V22h11h177H225z M214,33H37v177h177V33z"/></svg>
    );

    const MARGIN_PADDING_CONTROLS = [
        {
            label:'Top',
            icon: (<svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414"><rect x="2.714" y="5.492" width="1.048" height="9.017" fill="#555d66"></rect><rect x="16.265" y="5.498" width="1.023" height="9.003" fill="#555d66"></rect><rect x="5.518" y="2.186" width="8.964" height="2.482" fill="#272b2f"></rect><rect x="5.487" y="16.261" width="9.026" height="1.037" fill="#555d66"></rect></svg>)
        },
        {
            label:'Right',
            icon: (<svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414"><rect x="2.714" y="5.492" width="1.046" height="9.017" fill="#555d66"></rect><rect x="15.244" y="5.498" width="2.518" height="9.003" fill="#272b2f"></rect><rect x="5.518" y="2.719" width="8.964" height="0.954" fill="#555d66"></rect><rect x="5.487" y="16.308" width="9.026" height="0.99" fill="#555d66"></rect></svg>)
        },
        {
            label:'Bottom',
            icon: (<svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414"><rect x="2.714" y="5.492" width="1" height="9.017" fill="#555d66"></rect><rect x="16.261" y="5.498" width="1.027" height="9.003" fill="#555d66"></rect><rect x="5.518" y="2.719" width="8.964" height="0.968" fill="#555d66"></rect><rect x="5.487" y="15.28" width="9.026" height="2.499" fill="#272b2f"></rect></svg>)
        },
        {
            label:'Left',
            icon: (<svg width="20px" height="20px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414"><rect x="2.202" y="5.492" width="2.503" height="9.017" fill="#272b2f"></rect><rect x="16.276" y="5.498" width="1.012" height="9.003" fill="#555d66"></rect><rect x="5.518" y="2.719" width="8.964" height="0.966" fill="#555d66"></rect><rect x="5.487" y="16.303" width="9.026" height="0.995" fill="#555d66"></rect></svg>)
        },
    ];

    class InfoBoxEdit extends Component {

        constructor() {
            super( ...arguments );
            this.state = {
                showPopup: false,
                iconSelected: '',
                selectedIcon: false,
                iconThemeSelected: 'outlined',
                selectedIconTheme: false,
            };
            this.togglePopup = this.togglePopup.bind(this);
            this.handleIcon = this.handleIcon.bind(this);
            this.handleIconTheme = this.handleIconTheme.bind(this);
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-infobox'];

            // No override attributes of blocks inserted before
            if (attributes.changed !== true) {
                if (typeof currentBlockConfig === 'object' && currentBlockConfig !== null) {
                    Object.keys(currentBlockConfig).map((attribute) => {
                        if (typeof attributes[attribute] === 'boolean') {
                            attributes[attribute] = !!currentBlockConfig[attribute];
                        } else {
                            attributes[attribute] = currentBlockConfig[attribute];
                        }
                    });
                }

                // Finally set changed attribute to true, so we don't modify anything again
                setAttributes( { changed: true } );
            }
        }

        componentDidMount() {
            const { attributes, setAttributes, clientId } = this.props;
            const { blockIDX } = attributes;

            if (!blockIDX) {
                setAttributes( { blockIDX: `advgb-infobox-${clientId}` } );
            }
        }

        componentDidUpdate() {
            const {iconSelected, selectedIcon, iconThemeSelected, selectedIconTheme} = this.state;
            const {attributes, setAttributes} = this.props;
            if(selectedIcon) {

                this.setState({
                    selectedIcon: false
                });
                setAttributes({
                    icon: iconSelected,
                    iconTheme: iconThemeSelected
                });
            }

            if(selectedIconTheme) {
                this.setState({
                    selectedIconTheme: false
                });
                setAttributes({
                    iconTheme: iconThemeSelected
                });
            }
        }

        togglePopup() {
            const {showPopup} = this.state;

            this.setState( {
                showPopup: !showPopup
            } );
        }

        handleIcon(iconValue) {
            this.setState({
                iconSelected: iconValue,
                selectedIcon: true,
            });
        }

        handleIconTheme(iconThemeValue) {
            this.setState({
                iconThemeSelected: iconThemeValue,
                selectedIconTheme: true,
            });
        }

        render() {
            const {attributes, setAttributes} = this.props;
            const {
                blockIDX,
                isPreview,
                align,
                containerBorderWidth,
                containerBorderRadius,
                containerPaddingTop,
                containerPaddingBottom,
                containerPaddingLeft,
                containerPaddingRight,
                containerBackground,
                containerBorderBackground,
                containerPaddingUnit,
                iconBorderWidth,
                iconBorderRadius,
                iconPaddingTop,
                iconPaddingBottom,
                iconPaddingLeft,
                iconPaddingRight,
                iconMarginTop,
                iconMarginBottom,
                iconMarginLeft,
                iconMarginRight,
                iconBackground,
                iconBorderBackground,
                iconPaddingUnit,
                iconMarginUnit,
                icon,
                iconSize,
                iconColor,
                iconTheme,
                title,
                titleColor,
                titleSize,
                titleSizeUnit,
                titleLineHeight,
                titleLineHeightUnit,
                titleHtmlTag,
                titlePaddingTop,
                titlePaddingBottom,
                titlePaddingLeft,
                titlePaddingRight,
                titleMarginTop,
                titleMarginBottom,
                titleMarginLeft,
                titleMarginRight,
                titlePaddingUnit,
                titleMarginUnit,
                text,
                textColor,
                textSize,
                textSizeUnit,
                textLineHeight,
                textLineHeightUnit,
                textPaddingTop,
                textPaddingBottom,
                textPaddingLeft,
                textPaddingRight,
                textMarginTop,
                textMarginBottom,
                textMarginLeft,
                textMarginRight,
                textPaddingUnit,
                textMarginUnit,
            } = attributes;

            const {showPopup} = this.state;

            const blockWrapClass = [
                'advgb-infobox-wrapper',
                `has-text-align-${align}`
            ].filter( Boolean ).join( ' ' );

            const blockClass = [
                'advgb-infobox-wrap',
            ].filter( Boolean ).join( ' ' );

            const iconClass = [
                'material-icons',
                iconTheme !== '' && `-${iconTheme}`
            ].filter( Boolean ).join('');

            const containerPadding = containerPaddingTop + containerPaddingUnit + ' ' + containerPaddingRight + containerPaddingUnit + ' ' + containerPaddingBottom + containerPaddingUnit + ' ' + containerPaddingLeft + containerPaddingUnit;
            const iconPadding = iconPaddingTop + iconPaddingUnit + ' ' + iconPaddingRight + iconPaddingUnit + ' ' + iconPaddingBottom + iconPaddingUnit + ' ' + iconPaddingLeft + iconPaddingUnit;
            const iconMargin = iconMarginTop + iconMarginUnit + ' ' + iconMarginRight + iconMarginUnit + ' ' + iconMarginBottom + iconMarginUnit + ' ' + iconMarginLeft + iconMarginUnit;
            const titlePadding = titlePaddingTop + titlePaddingUnit + ' ' + titlePaddingRight + titlePaddingUnit + ' ' + titlePaddingBottom + titlePaddingUnit + ' ' + titlePaddingLeft + titlePaddingUnit;
            const titleMargin = titleMarginTop + titleMarginUnit + ' ' + titleMarginRight + titleMarginUnit + ' ' + titleMarginBottom + titleMarginUnit + ' ' + titleMarginLeft + titleMarginUnit;
            const textPadding = textPaddingTop + textPaddingUnit + ' ' + textPaddingRight + textPaddingUnit + ' ' + textPaddingBottom + textPaddingUnit + ' ' + textPaddingLeft + textPaddingUnit;
            const textMargin = textMarginTop + textMarginUnit + ' ' + textMarginRight + textMarginUnit + ' ' + textMarginBottom + textMarginUnit + ' ' + textMarginLeft + textMarginUnit;

            return (
                isPreview ?
                    <img alt={__('Info Box', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                    <Fragment>
                        <BlockControls>
                            <AlignmentToolbar
                                value={ align }
                                onChange={ ( newAlign ) => setAttributes( { align: newAlign } ) }
                            />
                        </BlockControls>
                        <InspectorControls>
                            <PanelBody
                                title={ __( 'Container Settings', 'advanced-gutenberg' ) }
                            >
                                <AdvColorControl
                                    label={ __('Background', 'advanced-gutenberg') }
                                    value={ containerBackground }
                                    onChange={ (value) => setAttributes( {containerBackground: value} ) }
                                />
                                <AdvColorControl
                                    label={ __('Border Color', 'advanced-gutenberg') }
                                    value={ containerBorderBackground }
                                    onChange={ (value) => setAttributes( {containerBorderBackground: value} ) }
                                />
                                <RangeControl
                                    label={ __( 'Border Width (px)', 'advanced-gutenberg' ) }
                                    min={ 0 }
                                    max={ 40 }
                                    value={ containerBorderWidth }
                                    onChange={ (value) => setAttributes( { containerBorderWidth: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Border Radius (px)', 'advanced-gutenberg' ) }
                                    min={ 0 }
                                    max={ 200 }
                                    value={ containerBorderRadius }
                                    onChange={ (value) => setAttributes( { containerBorderRadius: value } ) }
                                />
                                <BaseControl
                                    label={ __( 'Padding', 'advanced-gutenberg' ) }
                                    className="advgb-control-header"
                                />
                                <div className="advgb-controls-title">
                                    <span>{__( 'Unit', 'advanced-gutenberg' )}</span>
                                    <div className="advgb-unit-wrapper" key="unit">
                                        {[ 'px', 'em', 'vh', '%' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${containerPaddingUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { containerPaddingUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                {
                                    MARGIN_PADDING_CONTROLS.map((pos, mpIdx) => (
                                        <RangeControl
                                            className="advgb-padding-margin-control"
                                            key={mpIdx}
                                            label={pos.icon}
                                            beforeIcon={pos.icon}
                                            value={attributes['containerPadding' + pos.label]}
                                            min={0}
                                            max={180}
                                            onChange={( value ) => setAttributes( { ['containerPadding' + pos.label]: value } )}
                                        />
                                    ) )
                                }
                            </PanelBody>
                            <PanelBody
                                title={ __( 'Icon Settings', 'advanced-gutenberg' ) }
                                initialOpen={false}
                            >
                                <BaseControl
                                    label={ __( 'Icon Library (Material Icon)', 'advanced-gutenberg' )}
                                >
                                    <Button
                                        className="button button-large advgb-browse-icon-btn"
                                        onClick={ () => {
                                            if(!showPopup) {
                                                this.togglePopup();
                                            }
                                        } }
                                    >
                                        { __( 'Icon Selection', 'advanced-gutenberg' ) }
                                    </Button>
                                </BaseControl>
                                <AdvColorControl
                                    label={ __( 'Icon Color', 'advanced-gutenberg' ) }
                                    value={ iconColor }
                                    onChange={ (value) => setAttributes( {iconColor: value} ) }
                                />
                                <RangeControl
                                    label={ __( 'Icon Size (px)', 'advanced-gutenberg' ) }
                                    value={iconSize}
                                    min={1}
                                    max={200}
                                    onChange={( value ) => setAttributes( { iconSize: value } )}
                                />
                                <AdvColorControl
                                    label={ __('Background', 'advanced-gutenberg') }
                                    value={ iconBackground }
                                    onChange={ (value) => setAttributes( {iconBackground: value} ) }
                                />
                                <AdvColorControl
                                    label={ __('Border Color', 'advanced-gutenberg') }
                                    value={ iconBorderBackground }
                                    onChange={ (value) => setAttributes( {iconBorderBackground: value} ) }
                                />
                                <RangeControl
                                    label={ __( 'Border Width (px)', 'advanced-gutenberg' ) }
                                    min={ 0 }
                                    max={ 40 }
                                    value={ iconBorderWidth }
                                    onChange={ (value) => setAttributes( { iconBorderWidth: value } ) }
                                />
                                <RangeControl
                                    label={ __( 'Border Radius (px)', 'advanced-gutenberg' ) }
                                    min={ 0 }
                                    max={ 200 }
                                    value={ iconBorderRadius }
                                    onChange={ (value) => setAttributes( { iconBorderRadius: value } ) }
                                />
                                <BaseControl
                                    label={ __( 'Padding', 'advanced-gutenberg' ) }
                                    className="advgb-control-header"
                                />
                                <div className="advgb-controls-title">
                                    <span>{__( 'Unit', 'advanced-gutenberg' )}</span>
                                    <div className="advgb-unit-wrapper" key="unit">
                                        {[ 'px', 'em', 'vh', '%' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${iconPaddingUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { iconPaddingUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                {
                                    MARGIN_PADDING_CONTROLS.map((pos, mpIdx) => (
                                        <RangeControl
                                            className="advgb-padding-margin-control"
                                            key={mpIdx}
                                            label={pos.icon}
                                            beforeIcon={pos.icon}
                                            value={attributes['iconPadding' + pos.label]}
                                            min={0}
                                            max={180}
                                            onChange={( value ) => setAttributes( { ['iconPadding' + pos.label]: value } )}
                                        />
                                    ) )
                                }
                                <BaseControl
                                    label={ __( 'Margin', 'advanced-gutenberg' ) }
                                    className="advgb-control-header"
                                />
                                <div className="advgb-controls-title">
                                    <span>{__( 'Unit', 'advanced-gutenberg' )}</span>
                                    <div className="advgb-unit-wrapper" key="unit">
                                        {[ 'px', 'em', 'vh', '%' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${iconMarginUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { iconMarginUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                {
                                    MARGIN_PADDING_CONTROLS.map((pos, mpIdx) => (
                                        <RangeControl
                                            className="advgb-padding-margin-control"
                                            key={mpIdx}
                                            label={pos.icon}
                                            beforeIcon={pos.icon}
                                            value={attributes['iconMargin' + pos.label]}
                                            min={0}
                                            max={180}
                                            onChange={( value ) => setAttributes( { ['iconMargin' + pos.label]: value } )}
                                        />
                                    ) )
                                }
                            </PanelBody>
                            <PanelBody
                                title={ __( 'Title Settings', 'advanced-gutenberg' ) }
                                initialOpen={false}
                            >
                                <AdvColorControl
                                    label={ __( 'Color', 'advanced-gutenberg' ) }
                                    value={ titleColor }
                                    onChange={ (value) => setAttributes( {titleColor: value} ) }
                                />
                                <div className="advgb-controls-title">
                                    <div className="advgb-unit-wrapper advgb-unit-2" key="unit">
                                        {[ 'px', 'em' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${titleSizeUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { titleSizeUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                <RangeControl
                                    label={ __( 'Font Size', 'advanced-gutenberg' ) }
                                    value={titleSize}
                                    min={ titleSizeUnit === 'px' ? 1 : 0.2 }
                                    max={ titleSizeUnit === 'px' ? 200 : 12.0 }
                                    step={ titleSizeUnit === 'px' ? 1 : 0.1 }
                                    onChange={( value ) => setAttributes( { titleSize: value } )}
                                />
                                <div className="advgb-controls-title">
                                    <div className="advgb-unit-wrapper advgb-unit-2" key="unit">
                                        {[ 'px', 'em' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${titleLineHeightUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { titleLineHeightUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                <RangeControl
                                    label={ __( 'Line Height', 'advanced-gutenberg' ) }
                                    value={titleLineHeight}
                                    min={ titleLineHeightUnit === 'px' ? 1 : 0.2 }
                                    max={ titleLineHeightUnit === 'px' ? 200 : 12.0 }
                                    step={ titleLineHeightUnit === 'px' ? 1 : 0.1 }
                                    onChange={( value ) => setAttributes( { titleLineHeight: value } )}
                                />
                                <SelectControl
                                    label={ __('HTML Tag', 'advanced-gutenberg') }
                                    value={ titleHtmlTag }
                                    options={ [
                                        { label: __('H1', 'advanced-gutenberg'), value: 'h1' },
                                        { label: __('H2', 'advanced-gutenberg'), value: 'h2' },
                                        { label: __('H3', 'advanced-gutenberg'), value: 'h3' },
                                        { label: __('H4', 'advanced-gutenberg'), value: 'h4' },
                                        { label: __('H5', 'advanced-gutenberg'), value: 'h5' },
                                        { label: __('H6', 'advanced-gutenberg'), value: 'h6' },
                                    ] }
                                    onChange={ ( value ) => setAttributes( { titleHtmlTag: value } ) }
                                />
                                <BaseControl
                                    label={ __( 'Padding', 'advanced-gutenberg' ) }
                                    className="advgb-control-header"
                                />
                                <div className="advgb-controls-title">
                                    <span>{__( 'Unit', 'advanced-gutenberg' )}</span>
                                    <div className="advgb-unit-wrapper" key="unit">
                                        {[ 'px', 'em', 'vh', '%' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${titlePaddingUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { titlePaddingUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                {
                                    MARGIN_PADDING_CONTROLS.map((pos, mpIdx) => (
                                        <RangeControl
                                            className="advgb-padding-margin-control"
                                            key={mpIdx}
                                            label={pos.icon}
                                            beforeIcon={pos.icon}
                                            value={attributes['titlePadding' + pos.label]}
                                            min={0}
                                            max={180}
                                            onChange={( value ) => setAttributes( { ['titlePadding' + pos.label]: value } )}
                                        />
                                    ) )
                                }
                                <BaseControl
                                    label={ __( 'Margin', 'advanced-gutenberg' ) }
                                    className="advgb-control-header"
                                />
                                <div className="advgb-controls-title">
                                    <span>{__( 'Unit', 'advanced-gutenberg' )}</span>
                                    <div className="advgb-unit-wrapper" key="unit">
                                        {[ 'px', 'em', 'vh', '%' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${titleMarginUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { titleMarginUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                {
                                    MARGIN_PADDING_CONTROLS.map((pos, mpIdx) => (
                                        <RangeControl
                                            className="advgb-padding-margin-control"
                                            key={mpIdx}
                                            label={pos.icon}
                                            beforeIcon={pos.icon}
                                            value={attributes['titleMargin' + pos.label]}
                                            min={0}
                                            max={180}
                                            onChange={( value ) => setAttributes( { ['titleMargin' + pos.label]: value } )}
                                        />
                                    ) )
                                }
                            </PanelBody>
                            <PanelBody
                                title={ __( 'Text Settings', 'advanced-gutenberg' ) }
                                initialOpen={false}
                            >
                                <AdvColorControl
                                    label={ __( 'Color', 'advanced-gutenberg' ) }
                                    value={ textColor }
                                    onChange={ (value) => setAttributes( {textColor: value} ) }
                                />
                                <div className="advgb-controls-title">
                                    <div className="advgb-unit-wrapper advgb-unit-2" key="unit">
                                        {[ 'px', 'em' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${textSizeUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { textSizeUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                <RangeControl
                                    label={ __( 'Font Size', 'advanced-gutenberg' ) }
                                    value={textSize}
                                    min={ textSizeUnit === 'px' ? 1 : 0.2 }
                                    max={ textSizeUnit === 'px' ? 200 : 12.0 }
                                    step={ textSizeUnit === 'px' ? 1 : 0.1 }
                                    onChange={( value ) => setAttributes( { textSize: value } )}
                                />
                                <div className="advgb-controls-title">
                                    <div className="advgb-unit-wrapper advgb-unit-2" key="unit">
                                        {[ 'px', 'em' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${textLineHeightUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { textLineHeightUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                <RangeControl
                                    label={ __( 'Line Height', 'advanced-gutenberg' ) }
                                    value={textLineHeight}
                                    min={ textLineHeightUnit === 'px' ? 1 : 0.2 }
                                    max={ textLineHeightUnit === 'px' ? 200 : 12.0 }
                                    step={ textLineHeightUnit === 'px' ? 1 : 0.1 }
                                    onChange={( value ) => setAttributes( { textLineHeight: value } )}
                                />
                                <BaseControl
                                    label={ __( 'Padding', 'advanced-gutenberg' ) }
                                    className="advgb-control-header"
                                />
                                <div className="advgb-controls-title">
                                    <span>{__( 'Unit', 'advanced-gutenberg' )}</span>
                                    <div className="advgb-unit-wrapper" key="unit">
                                        {[ 'px', 'em', 'vh', '%' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${textPaddingUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { textPaddingUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                {
                                    MARGIN_PADDING_CONTROLS.map((pos, mpIdx) => (
                                        <RangeControl
                                            className="advgb-padding-margin-control"
                                            key={mpIdx}
                                            label={pos.icon}
                                            beforeIcon={pos.icon}
                                            value={attributes['textPadding' + pos.label]}
                                            min={0}
                                            max={180}
                                            onChange={( value ) => setAttributes( { ['textPadding' + pos.label]: value } )}
                                        />
                                    ) )
                                }
                                <BaseControl
                                    label={ __( 'Margin', 'advanced-gutenberg' ) }
                                    className="advgb-control-header"
                                />
                                <div className="advgb-controls-title">
                                    <span>{__( 'Unit', 'advanced-gutenberg' )}</span>
                                    <div className="advgb-unit-wrapper" key="unit">
                                        {[ 'px', 'em', 'vh', '%' ].map( ( unit, uIdx ) => (
                                            <span
                                                className={`advgb-unit ${textMarginUnit === unit ? 'selected' : ''}`}
                                                key={uIdx}
                                                onClick={() => setAttributes( { textMarginUnit: unit } )}
                                            >
                                                    {unit}
                                                </span>
                                        ) )}
                                    </div>
                                </div>
                                {
                                    MARGIN_PADDING_CONTROLS.map((pos, mpIdx) => (
                                        <RangeControl
                                            className="advgb-padding-margin-control"
                                            key={mpIdx}
                                            label={pos.icon}
                                            beforeIcon={pos.icon}
                                            value={attributes['textMargin' + pos.label]}
                                            min={0}
                                            max={180}
                                            onChange={( value ) => setAttributes( { ['textMargin' + pos.label]: value } )}
                                        />
                                    ) )
                                }
                            </PanelBody>
                        </InspectorControls>
                        <div className={blockWrapClass}
                             style={ {
                                 backgroundColor: containerBackground,
                                 padding: containerPadding,
                                 border: `${containerBorderWidth}px solid ${containerBorderBackground}`,
                                 borderRadius: `${containerBorderRadius}px`,
                             } }
                             id={blockIDX}
                        >
                            <div className={ blockClass }>
                                <div
                                    className="advgb-infobox-icon-container"
                                    style={ {
                                        backgroundColor: iconBackground,
                                        padding: iconPadding,
                                        margin: iconMargin,
                                        border: `${iconBorderWidth}px solid ${iconBorderBackground}`,
                                        borderRadius: `${iconBorderRadius}px`,
                                    } }
                                >
                                    <div className="advgb-infobox-icon-inner-container">
                                        <i className={iconClass} style={ {color: iconColor, fontSize: iconSize, display: 'block'} }>{icon}</i>
                                    </div>
                                </div>
                                <div className="advgb-infobox-textcontent">
                                    <RichText
                                        tagName={titleHtmlTag}
                                        className="advgb-infobox-title"
                                        onChange={ (value) => setAttributes({ title: value}) }
                                        value={ title }
                                        style={ {
                                            color: titleColor,
                                            fontSize: titleSize+titleSizeUnit,
                                            lineHeight: titleLineHeight+titleLineHeightUnit,
                                            padding: titlePadding,
                                            margin: titleMargin,
                                            whiteSpace: 'pre-wrap'
                                        } }
                                    />
                                    <RichText
                                        tagName="p"
                                        className="advgb-infobox-text"
                                        onChange={ (value) => setAttributes({ text: value}) }
                                        value={ text }
                                        style={ {
                                            color: textColor,
                                            fontSize: textSize+textSizeUnit,
                                            lineHeight: textLineHeight+textLineHeightUnit,
                                            padding: textPadding,
                                            margin: textMargin,
                                            whiteSpace: 'pre-wrap'
                                        } }
                                    />
                                </div>
                            </div>
                            {
                                showPopup ?
                                    <IconListPopupHook
                                        content='iconpopup'
                                        closePopup={ () => {
                                            if(showPopup) {
                                                this.togglePopup();
                                            }
                                        }
                                        }
                                        onSelectIcon={ this.handleIcon }
                                        onSelectIconTheme={ this.handleIconTheme }
                                        selectedIcon={icon}
                                        selectedIconTheme={iconTheme}
                                    />
                                    :
                                    null
                            }
                        </div>
                    </Fragment>
            );
        }
    }

    const blockAttrs = {
        blockIDX: {
            type: 'string',
        },
        align: {
            type: 'string',
            default: 'center'
        },
        containerBorderWidth: {
            type: 'number',
            default: 0
        },
        containerBorderRadius: {
            type: 'number',
            default: 0
        },
        containerPaddingTop: {
            type: 'number',
            default: 20
        },
        containerPaddingBottom: {
            type: 'number',
            default: 20
        },
        containerPaddingLeft: {
            type: 'number',
            default: 20
        },
        containerPaddingRight: {
            type: 'number',
            default: 20
        },
        containerPaddingUnit: {
            type: 'string',
            default: 'px',
        },
        containerBackground: {
            type: 'string',
            default: '#f5f5f5'
        },
        containerBorderBackground: {
            type: 'string',
            default: '#e8e8e8'
        },
        iconBorderWidth: {
            type: 'number',
            default: 0
        },
        iconBorderRadius: {
            type: 'number',
            default: 0
        },
        iconPaddingTop: {
            type: 'number',
            default: 0
        },
        iconPaddingBottom: {
            type: 'number',
            default: 0
        },
        iconPaddingLeft: {
            type: 'number',
            default: 0
        },
        iconPaddingRight: {
            type: 'number',
            default: 0
        },
        iconMarginTop: {
            type: 'number',
            default: 0
        },
        iconMarginBottom: {
            type: 'number',
            default: 0
        },
        iconMarginLeft: {
            type: 'number',
            default: 0
        },
        iconMarginRight: {
            type: 'number',
            default: 0
        },
        iconPaddingUnit: {
            type: 'string',
            default: 'px',
        },
        iconMarginUnit: {
            type: 'string',
            default: 'px',
        },
        iconBackground: {
            type: 'string',
            default: '#f5f5f5'
        },
        iconBorderBackground: {
            type: 'string',
            default: '#e8e8e8'
        },
        icon: {
            type: 'string',
            default: 'beenhere'
        },
        iconSize: {
            type: 'number',
            default: 70
        },
        iconColor: {
            type: 'string',
            default: '#333'
        },
        iconTheme: {
            type: 'string',
            default: 'outlined'
        },
        title: {
            type: 'string',
            default: 'Title',
        },
        titleColor: {
            type: 'string',
            default: '#333'
        },
        titleSize: {
            type: 'number'
        },
        titleSizeUnit: {
            type: 'string',
            default: 'px'
        },
        titleLineHeight: {
            type: 'number'
        },
        titleLineHeightUnit: {
            type: 'string',
            default: 'px'
        },
        titleHtmlTag: {
            type: 'string',
            default: 'h3'
        },
        titlePaddingTop: {
            type: 'number',
            default: 0
        },
        titlePaddingBottom: {
            type: 'number',
            default: 0
        },
        titlePaddingLeft: {
            type: 'number',
            default: 0
        },
        titlePaddingRight: {
            type: 'number',
            default: 0
        },
        titleMarginTop: {
            type: 'number',
            default: 5
        },
        titleMarginBottom: {
            type: 'number',
            default: 10
        },
        titleMarginLeft: {
            type: 'number',
            default: 0
        },
        titleMarginRight: {
            type: 'number',
            default: 0
        },
        titlePaddingUnit: {
            type: 'string',
            default: 'px',
        },
        titleMarginUnit: {
            type: 'string',
            default: 'px',
        },
        text: {
            type: 'string',
            default: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean diam dolor, accumsan sed rutrum vel, dapibus et leo.',
        },
        textColor: {
            type: 'string',
            default: '#333'
        },
        textSize: {
            type: 'number'
        },
        textSizeUnit: {
            type: 'string',
            default: 'px'
        },
        textLineHeight: {
            type: 'number'
        },
        textLineHeightUnit: {
            type: 'string',
            default: 'px'
        },
        textPaddingTop: {
            type: 'number',
            default: 0
        },
        textPaddingBottom: {
            type: 'number',
            default: 0
        },
        textPaddingLeft: {
            type: 'number',
            default: 0
        },
        textPaddingRight: {
            type: 'number',
            default: 0
        },
        textMarginTop: {
            type: 'number',
            default: 0
        },
        textMarginBottom: {
            type: 'number',
            default: 0
        },
        textMarginLeft: {
            type: 'number',
            default: 0
        },
        textMarginRight: {
            type: 'number',
            default: 0
        },
        textPaddingUnit: {
            type: 'string',
            default: 'px',
        },
        textMarginUnit: {
            type: 'string',
            default: 'px',
        },
        changed: {
            type: 'boolean',
            default: false,
        },
        isPreview: {
            type: 'boolean',
            default: false,
        }
    };

    registerBlockType( 'advgb/infobox', {
        title: __( 'Info Box', 'advanced-gutenberg' ),
        description: __( 'Advanced icon block with more options and styles.', 'advanced-gutenberg' ),
        icon: {
            src: blockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'info', 'advanced-gutenberg' ), __( 'icon', 'advanced-gutenberg') , __( 'box', 'advanced-gutenberg' ) ],
        attributes: blockAttrs,
        example: {
            attributes: {
                isPreview: true
            },
        },
        edit: InfoBoxEdit,
        save: ( { attributes } ) => {
            const {
                blockIDX,
                className,
                title,
                titleHtmlTag,
                text,
                icon,
                iconTheme,
                align,
            } = attributes;

            const blockWrapClass = [
                'wp-block-advgb-infobox',
                'advgb-infobox-wrapper',
                `has-text-align-${align}`,
                className,
                blockIDX
            ].filter( Boolean ).join( ' ' );

            const blockClass = [
                'advgb-infobox-wrap',
            ].filter( Boolean ).join( ' ' );

            const iconClass = [
                'material-icons',
                iconTheme !== '' && `-${iconTheme}`
            ].filter( Boolean ).join('');

            return (
                <Fragment>
                    <div className={blockWrapClass}>
                        <div className={ blockClass }>
                            <div className="advgb-infobox-icon-container">
                                <div className="advgb-infobox-icon-inner-container">
                                    <i className={iconClass}>{icon}</i>
                                </div>
                            </div>
                            <div className="advgb-infobox-textcontent">
                                <RichText.Content
                                    tagName={titleHtmlTag}
                                    className="advgb-infobox-title"
                                    value={ title }
                                />
                                <RichText.Content
                                    tagName="p"
                                    className="advgb-infobox-text"
                                    value={ text }
                                />
                            </div>
                        </div>
                    </div>
                </Fragment>
            )
        },
        deprecated: [
            {
                attributes: blockAttrs,
                save: ( { attributes } ) => {
                    const {
                        blockIDX,
                        title,
                        titleHtmlTag,
                        text,
                        icon,
                        iconTheme,
                        align,
                    } = attributes;

                    const blockWrapClass = [
                        'wp-block-advgb-infobox',
                        'advgb-infobox-wrapper',
                        `has-text-align-${align}`,
                    ].filter( Boolean ).join( ' ' );

                    const blockClass = [
                        'advgb-infobox-wrap',
                    ].filter( Boolean ).join( ' ' );

                    const iconClass = [
                        'material-icons',
                        iconTheme !== '' && `-${iconTheme}`
                    ].filter( Boolean ).join('');

                    return (
                        <Fragment>
                            <div className={blockWrapClass} id={blockIDX}>
                                <div className={ blockClass }>
                                    <div className="advgb-infobox-icon-container">
                                        <div className="advgb-infobox-icon-inner-container">
                                            <i className={iconClass}>{icon}</i>
                                        </div>
                                    </div>
                                    <div className="advgb-infobox-textcontent">
                                        <RichText.Content
                                            tagName={titleHtmlTag}
                                            className="advgb-infobox-title"
                                            value={ title }
                                        />
                                        <RichText.Content
                                            tagName="p"
                                            className="advgb-infobox-text"
                                            value={ text }
                                        />
                                    </div>
                                </div>
                            </div>
                        </Fragment>
                    )
                }
            }
        ]
    });
}) ( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );