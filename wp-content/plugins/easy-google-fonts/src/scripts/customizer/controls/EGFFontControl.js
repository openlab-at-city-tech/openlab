import React from 'react';

import { __, sprintf } from '@wordpress/i18n';
import { Button, Panel, PanelBody, PanelRow, TabPanel } from '@wordpress/components';
import { useEffect, useState, useRef } from '@wordpress/element';

import FontSettings from './FontSettings';
import StyleSettings from './StyleSettings';
import PositionSettings from './PositionSettings';

const EGFFontControl = props => {
  const { control } = props;
  const { label, description, properties } = control.params;

  const {
    background_color,
    border_bottom_color,
    border_bottom_style,
    border_bottom_width,
    border_left_color,
    border_left_style,
    border_left_width,
    border_radius_bottom_left,
    border_radius_bottom_right,
    border_radius_top_left,
    border_radius_top_right,
    border_right_color,
    border_right_style,
    border_right_width,
    border_top_color,
    border_top_style,
    border_top_width,
    display: displaySetting,
    font_color,
    font_id,
    font_name,
    font_size,
    font_style,
    font_weight,
    font_weight_style,
    letter_spacing,
    line_height,
    margin_bottom,
    margin_left,
    margin_right,
    margin_top,
    padding_bottom,
    padding_left,
    padding_right,
    padding_top,
    stylesheet_url,
    subset: subsetSetting,
    text_decoration,
    text_transform
  } = control.setting();

  // Control state.
  const [isOpen, setIsOpen] = useState(false);
  const [backgroundColor, setBackgroundColor] = useState(background_color);
  const [borderBottomColor, setBorderBottomColor] = useState(border_bottom_color);
  const [borderBottomStyle, setBorderBottomStyle] = useState(border_bottom_style);
  const [borderBottomWidth, setBorderBottomWidth] = useState(border_bottom_width);
  const [borderLeftColor, setBorderLeftColor] = useState(border_left_color);
  const [borderLeftStyle, setBorderLeftStyle] = useState(border_left_style);
  const [borderLeftWidth, setBorderLeftWidth] = useState(border_left_width);
  const [borderRadiusBottomLeft, setBorderRadiusBottomLeft] = useState(border_radius_bottom_left);
  const [borderRadiusBottomRight, setBorderRadiusBottomRight] = useState(border_radius_bottom_right);
  const [borderRadiusTopLeft, setBorderRadiusTopLeft] = useState(border_radius_top_left);
  const [borderRadiusTopRight, setBorderRadiusTopRight] = useState(border_radius_top_right);
  const [borderRightColor, setBorderRightColor] = useState(border_right_color);
  const [borderRightStyle, setBorderRightStyle] = useState(border_right_style);
  const [borderRightWidth, setBorderRightWidth] = useState(border_right_width);
  const [borderTopColor, setBorderTopColor] = useState(border_top_color);
  const [borderTopStyle, setBorderTopStyle] = useState(border_top_style);
  const [borderTopWidth, setBorderTopWidth] = useState(border_top_width);
  const [display, setDisplay] = useState(displaySetting);
  const [fontColor, setFontColor] = useState(font_color);
  const [fontId, setFontId] = useState(font_id);
  const [fontName, setFontName] = useState(font_name);
  const [fontSize, setFontSize] = useState(font_size);
  const [fontStyle, setFontStyle] = useState(font_style);
  const [fontWeight, setFontWeight] = useState(font_weight);
  const [fontWeightStyle, setFontWeightStyle] = useState(font_weight_style);
  const [letterSpacing, setLetterSpacing] = useState(letter_spacing);
  const [lineHeight, setLineHeight] = useState(line_height);
  const [marginBottom, setMarginBottom] = useState(margin_bottom);
  const [marginLeft, setMarginLeft] = useState(margin_left);
  const [marginRight, setMarginRight] = useState(margin_right);
  const [marginTop, setMarginTop] = useState(margin_top);
  const [paddingBottom, setPaddingBottom] = useState(padding_bottom);
  const [paddingLeft, setPaddingLeft] = useState(padding_left);
  const [paddingRight, setPaddingRight] = useState(padding_right);
  const [paddingTop, setPaddingTop] = useState(padding_top);
  const [stylesheetUrl, setStylesheetUrl] = useState(stylesheet_url);
  const [subset, setSubset] = useState(subsetSetting);
  const [textDecoration, setTextDecoration] = useState(text_decoration);
  const [textTransform, setTextTransform] = useState(text_transform);

  // Track initial renders for subsettings.
  const syncCustomizerWithState = (setting, state) => {
    wp.customize(`${control.id}[${setting}]`)(state);
  };

  // Keep component state in sync with the customizer.
  useEffect(() => syncCustomizerWithState('background_color', backgroundColor), [backgroundColor]);
  useEffect(() => syncCustomizerWithState('border_bottom_color', borderBottomColor), [borderBottomColor]);
  useEffect(() => syncCustomizerWithState('border_bottom_style', borderBottomStyle), [borderBottomStyle]);
  useEffect(() => syncCustomizerWithState('border_bottom_width', borderBottomWidth), [borderBottomWidth]);
  useEffect(() => syncCustomizerWithState('border_left_color', borderLeftColor), [borderLeftColor]);
  useEffect(() => syncCustomizerWithState('border_left_style', borderLeftStyle), [borderLeftStyle]);
  useEffect(() => syncCustomizerWithState('border_left_width', borderLeftWidth), [borderLeftWidth]);
  useEffect(() => syncCustomizerWithState('border_radius_bottom_left', borderRadiusBottomLeft), [
    borderRadiusBottomLeft
  ]);
  useEffect(() => syncCustomizerWithState('border_radius_bottom_right', borderRadiusBottomRight), [
    borderRadiusBottomRight
  ]);
  useEffect(() => syncCustomizerWithState('border_radius_top_left', borderRadiusTopLeft), [borderRadiusTopLeft]);
  useEffect(() => syncCustomizerWithState('border_radius_top_right', borderRadiusTopRight), [borderRadiusTopRight]);
  useEffect(() => syncCustomizerWithState('border_right_color', borderRightColor), [borderRightColor]);
  useEffect(() => syncCustomizerWithState('border_right_style', borderRightStyle), [borderRightStyle]);
  useEffect(() => syncCustomizerWithState('border_right_width', borderRightWidth), [borderRightWidth]);
  useEffect(() => syncCustomizerWithState('border_top_color', borderTopColor), [borderTopColor]);
  useEffect(() => syncCustomizerWithState('border_top_style', borderTopStyle), [borderTopStyle]);
  useEffect(() => syncCustomizerWithState('border_top_width', borderTopWidth), [borderTopWidth]);
  useEffect(() => syncCustomizerWithState('display', display), [display]);
  useEffect(() => syncCustomizerWithState('font_color', fontColor), [fontColor]);
  useEffect(() => syncCustomizerWithState('font_id', fontId), [fontId]);
  useEffect(() => syncCustomizerWithState('font_name', fontName), [fontName]);
  useEffect(() => syncCustomizerWithState('font_size', fontSize), [fontSize]);
  useEffect(() => syncCustomizerWithState('font_style', fontStyle), [fontStyle]);
  useEffect(() => syncCustomizerWithState('font_weight', fontWeight), [fontWeight]);
  useEffect(() => syncCustomizerWithState('font_weight_style', fontWeightStyle), [fontWeightStyle]);
  useEffect(() => syncCustomizerWithState('letter_spacing', letterSpacing), [letterSpacing]);
  useEffect(() => syncCustomizerWithState('line_height', lineHeight), [lineHeight]);
  useEffect(() => syncCustomizerWithState('margin_bottom', marginBottom), [marginBottom]);
  useEffect(() => syncCustomizerWithState('margin_left', marginLeft), [marginLeft]);
  useEffect(() => syncCustomizerWithState('margin_right', marginRight), [marginRight]);
  useEffect(() => syncCustomizerWithState('margin_top', marginTop), [marginTop]);
  useEffect(() => syncCustomizerWithState('padding_bottom', paddingBottom), [paddingBottom]);
  useEffect(() => syncCustomizerWithState('padding_left', paddingLeft), [paddingLeft]);
  useEffect(() => syncCustomizerWithState('padding_right', paddingRight), [paddingRight]);
  useEffect(() => syncCustomizerWithState('padding_top', paddingTop), [paddingTop]);
  useEffect(() => syncCustomizerWithState('stylesheet_url', stylesheetUrl), [stylesheetUrl]);
  useEffect(() => syncCustomizerWithState('subset', subset), [subset]);
  useEffect(() => syncCustomizerWithState('text_decoration', textDecoration), [textDecoration]);
  useEffect(() => syncCustomizerWithState('text_transform', textTransform), [textTransform]);

  // Send google stylesheet data to the preview.
  useEffect(() => {
    wp.customize.previewer.send(control.id, {
      fontName,
      fontWeightStyle
    });
  }, [fontName, fontWeightStyle]);

  /**
   * Reset Control
   */
  const resetControl = () => {
    const {
      background_color,
      border_bottom_color,
      border_bottom_style,
      border_bottom_width,
      border_left_color,
      border_left_style,
      border_left_width,
      border_radius_bottom_left,
      border_radius_bottom_right,
      border_radius_top_left,
      border_radius_top_right,
      border_right_color,
      border_right_style,
      border_right_width,
      border_top_color,
      border_top_style,
      border_top_width,
      display: displaySetting,
      font_color,
      font_id,
      font_name,
      font_size,
      font_style,
      font_weight,
      font_weight_style,
      letter_spacing,
      line_height,
      margin_bottom,
      margin_left,
      margin_right,
      margin_top,
      padding_bottom,
      padding_left,
      padding_right,
      padding_top,
      stylesheet_url,
      subset: subsetSetting,
      text_decoration,
      text_transform
    } = control.settings.default.default;

    setBackgroundColor(background_color);
    setBorderBottomColor(border_bottom_color);
    setBorderBottomStyle(border_bottom_style);
    setBorderBottomWidth(border_bottom_width);
    setBorderLeftColor(border_left_color);
    setBorderLeftStyle(border_left_style);
    setBorderLeftWidth(border_left_width);
    setBorderRadiusBottomLeft(border_radius_bottom_left);
    setBorderRadiusBottomRight(border_radius_bottom_right);
    setBorderRadiusTopLeft(border_radius_top_left);
    setBorderRadiusTopRight(border_radius_top_right);
    setBorderRightColor(border_right_color);
    setBorderRightStyle(border_right_style);
    setBorderRightWidth(border_right_width);
    setBorderTopColor(border_top_color);
    setBorderTopStyle(border_top_style);
    setBorderTopWidth(border_top_width);
    setDisplay(displaySetting);
    setFontColor(font_color);
    setFontId(font_id);
    setFontName(font_name);
    setFontSize(font_size);
    setFontStyle(font_style);
    setFontWeight(font_weight);
    setFontWeightStyle(font_weight_style);
    setLetterSpacing(letter_spacing);
    setLineHeight(line_height);
    setMarginBottom(margin_bottom);
    setMarginLeft(margin_left);
    setMarginRight(margin_right);
    setMarginTop(margin_top);
    setPaddingBottom(padding_bottom);
    setPaddingLeft(padding_left);
    setPaddingRight(padding_right);
    setPaddingTop(padding_top);
    setStylesheetUrl(stylesheet_url);
    setSubset(subsetSetting);
    setTextDecoration(text_decoration);
    setTextTransform(text_transform);
  };

  const isMediaQueryControl = properties.min_screen.amount || properties.max_screen.amount;

  return (
    <div>
      <Panel>
        <PanelBody title={label} icon="more" initialOpen={false} opened={isOpen} onToggle={() => setIsOpen(!isOpen)}>
          {/* Description */}
          {description && <p className="description customize-control-description mb-2">{description}</p>}

          {isMediaQueryControl && (
            <div className="mt-3">
              <span className="dashicons dashicons-desktop mr-2"></span>
              {properties.min_screen.amount && (
                <code className="d-inline-block mr-1 mb-2" style={{ borderRadius: 2 }}>
                  {sprintf(
                    __('Min: %s', 'easy-google-fonts'),
                    `${properties.min_screen.amount}${properties.min_screen.unit}`
                  )}
                </code>
              )}
              {properties.max_screen.amount && (
                <code className="d-inline-block mr-1 mb-2" style={{ borderRadius: 2 }}>
                  {sprintf(
                    __('Max: %s', 'easy-google-fonts'),
                    `${properties.max_screen.amount}${properties.max_screen.unit}`
                  )}
                </code>
              )}
            </div>
          )}

          {/* Settings */}
          <TabPanel
            className="egf-font-control__tabs"
            tabs={[
              {
                name: 'font-settings',
                title: 'Font',
                isActive: true,
                className: 'egf-font-control__tab',
                component: (
                  <FontSettings
                    control={control}
                    fontId={fontId}
                    setFontId={setFontId}
                    fontName={fontName}
                    setFontName={setFontName}
                    fontWeightStyle={fontWeightStyle}
                    setFontWeightStyle={setFontWeightStyle}
                    fontWeight={fontWeight}
                    setFontWeight={setFontWeight}
                    fontStyle={fontStyle}
                    setFontStyle={setFontStyle}
                    stylesheetUrl={stylesheetUrl}
                    setStylesheetUrl={setStylesheetUrl}
                    subset={subset}
                    setSubset={setSubset}
                    textDecoration={textDecoration}
                    setTextDecoration={setTextDecoration}
                    textTransform={textTransform}
                    setTextTransform={setTextTransform}
                  />
                )
              },
              {
                name: 'style-settings',
                title: 'Style',
                className: 'egf-font-control__tab',
                component: (
                  <StyleSettings
                    control={control}
                    fontColor={fontColor}
                    setFontColor={setFontColor}
                    backgroundColor={backgroundColor}
                    setBackgroundColor={setBackgroundColor}
                    fontSize={fontSize}
                    setFontSize={setFontSize}
                    lineHeight={lineHeight}
                    setLineHeight={setLineHeight}
                    letterSpacing={letterSpacing}
                    setLetterSpacing={setLetterSpacing}
                  />
                )
              },
              {
                name: 'position-settings',
                title: 'Position',
                className: 'egf-font-control__tab',
                component: (
                  <PositionSettings
                    control={control}
                    marginBottom={marginBottom}
                    setMarginBottom={setMarginBottom}
                    marginLeft={marginLeft}
                    setMarginLeft={setMarginLeft}
                    marginRight={marginRight}
                    setMarginRight={setMarginRight}
                    marginTop={marginTop}
                    setMarginTop={setMarginTop}
                    paddingBottom={paddingBottom}
                    setPaddingBottom={setPaddingBottom}
                    paddingLeft={paddingLeft}
                    setPaddingLeft={setPaddingLeft}
                    paddingRight={paddingRight}
                    setPaddingRight={setPaddingRight}
                    paddingTop={paddingTop}
                    setPaddingTop={setPaddingTop}
                    borderTopColor={borderTopColor}
                    setBorderTopColor={setBorderTopColor}
                    borderTopStyle={borderTopStyle}
                    setBorderTopStyle={setBorderTopStyle}
                    borderTopWidth={borderTopWidth}
                    setBorderTopWidth={setBorderTopWidth}
                    borderBottomColor={borderBottomColor}
                    setBorderBottomColor={setBorderBottomColor}
                    borderBottomStyle={borderBottomStyle}
                    setBorderBottomStyle={setBorderBottomStyle}
                    borderBottomWidth={borderBottomWidth}
                    setBorderBottomWidth={setBorderBottomWidth}
                    borderLeftColor={borderLeftColor}
                    setBorderLeftColor={setBorderLeftColor}
                    borderLeftStyle={borderLeftStyle}
                    setBorderLeftStyle={setBorderLeftStyle}
                    borderLeftWidth={borderLeftWidth}
                    setBorderLeftWidth={setBorderLeftWidth}
                    borderRightColor={borderRightColor}
                    setBorderRightColor={setBorderRightColor}
                    borderRightStyle={borderRightStyle}
                    setBorderRightStyle={setBorderRightStyle}
                    borderRightWidth={borderRightWidth}
                    setBorderRightWidth={setBorderRightWidth}
                    borderRadiusBottomLeft={borderRadiusBottomLeft}
                    setBorderRadiusBottomLeft={setBorderRadiusBottomLeft}
                    borderRadiusBottomRight={borderRadiusBottomRight}
                    setBorderRadiusBottomRight={setBorderRadiusBottomRight}
                    borderRadiusTopLeft={borderRadiusTopLeft}
                    setBorderRadiusTopLeft={setBorderRadiusTopLeft}
                    borderRadiusTopRight={borderRadiusTopRight}
                    setBorderRadiusTopRight={setBorderRadiusTopRight}
                    display={display}
                    setDisplay={setDisplay}
                  />
                )
              }
            ]}
          >
            {tab => tab.component}
          </TabPanel>

          {/* Actions */}
          <PanelRow className="egf-font-control__actions">
            <Button isTertiary onClick={() => setIsOpen(!isOpen)}>
              {__('Close', 'easy-google-fonts')}
            </Button>

            <Button isDestructive onClick={() => resetControl()}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </PanelBody>
      </Panel>
    </div>
  );
};

export default EGFFontControl;
