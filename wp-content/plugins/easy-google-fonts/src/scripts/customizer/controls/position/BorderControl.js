import { __ } from '@wordpress/i18n';
import {
  Button,
  ColorIndicator,
  ColorPalette,
  Panel,
  PanelBody,
  PanelRow,
  RangeControl,
  SelectControl,
  __experimentalUnitControl as UnitControl
} from '@wordpress/components';

import { parseUnit, BORDER_UNITS } from '../../utils/units';

const { theme_colors } = egfCustomize;

const BorderControl = props => {
  const {
    className,
    control,
    borderTopColor,
    setBorderTopColor,
    borderTopStyle,
    setBorderTopStyle,
    borderTopWidth,
    setBorderTopWidth,
    borderBottomColor,
    setBorderBottomColor,
    borderBottomStyle,
    setBorderBottomStyle,
    borderBottomWidth,
    setBorderBottomWidth,
    borderLeftColor,
    setBorderLeftColor,
    borderLeftStyle,
    setBorderLeftStyle,
    borderLeftWidth,
    setBorderLeftWidth,
    borderRightColor,
    setBorderRightColor,
    borderRightStyle,
    setBorderRightStyle,
    borderRightWidth,
    setBorderRightWidth
  } = props;

  /**
   * Reset Border Functions
   */
  const resetBorderTop = () => {
    const defaultSettings = control.settings.default.default;
    setBorderTopColor(defaultSettings.border_top_color);
    setBorderTopStyle(defaultSettings.border_top_style);
    setBorderTopWidth(defaultSettings.border_top_width);
  };

  const resetBorderBottom = () => {
    const defaultSettings = control.settings.default.default;
    setBorderBottomColor(defaultSettings.border_bottom_color);
    setBorderBottomStyle(defaultSettings.border_bottom_style);
    setBorderBottomWidth(defaultSettings.border_bottom_width);
  };

  const resetBorderLeft = () => {
    const defaultSettings = control.settings.default.default;
    setBorderLeftColor(defaultSettings.border_left_color);
    setBorderLeftStyle(defaultSettings.border_left_style);
    setBorderLeftWidth(defaultSettings.border_left_width);
  };

  const resetBorderRight = () => {
    const defaultSettings = control.settings.default.default;
    setBorderRightColor(defaultSettings.border_right_color);
    setBorderRightStyle(defaultSettings.border_right_style);
    setBorderRightWidth(defaultSettings.border_right_width);
  };

  /**
   * Update Border (Range Controls)
   */
  const updateRangeControlBorderTopWidth = amount => setBorderTopWidth({ amount, unit: topUnitSelected });
  const updateRangeControlBorderBottomWidth = amount => setBorderBottomWidth({ amount, unit: bottomUnitSelected });
  const updateRangeControlBorderLeftWidth = amount => setBorderLeftWidth({ amount, unit: leftUnitSelected });
  const updateRangeControlBorderRightWidth = amount => setBorderRightWidth({ amount, unit: rightUnitSelected });

  /**
   * Update Border (Unit Control)
   */
  const updateUnitControlBorderTopWidth = value => {
    const [amount, unit] = parseUnit(value, Object.values(BORDER_UNITS));
    if (amount > 0) {
      setBorderTopWidth({ amount, unit });
    }
  };

  const updateUnitControlBorderBottomWidth = value => {
    const [amount, unit] = parseUnit(value, Object.values(BORDER_UNITS));
    if (amount > 0) {
      setBorderBottomWidth({ amount, unit });
    }
  };

  const updateUnitControlBorderLeftWidth = value => {
    const [amount, unit] = parseUnit(value, Object.values(BORDER_UNITS));
    if (amount > 0) {
      setBorderLeftWidth({ amount, unit });
    }
  };

  const updateUnitControlBorderRightWidth = value => {
    const [amount, unit] = parseUnit(value, Object.values(BORDER_UNITS));
    if (amount > 0) {
      setBorderRightWidth({ amount, unit });
    }
  };

  /**
   * On Unit Change Callback
   */
  const onUnitChangeBorderTopWidth = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setBorderTopWidth({
      amount: BORDER_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangeBorderBottomWidth = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setBorderBottomWidth({
      amount: BORDER_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangeBorderLeftWidth = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setBorderLeftWidth({
      amount: BORDER_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangeBorderRightWidth = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setBorderRightWidth({
      amount: BORDER_UNITS[unit].initial,
      unit
    });
  };

  const sanitizeUnit = unit => (unit === '%' ? 'percent' : unit);
  let topUnitSelected = sanitizeUnit(borderTopWidth.unit) || 'px';
  let bottomUnitSelected = sanitizeUnit(borderBottomWidth.unit) || 'px';
  let leftUnitSelected = sanitizeUnit(borderLeftWidth.unit) || 'px';
  let rightUnitSelected = sanitizeUnit(borderRightWidth.unit) || 'px';

  const borderOptions = [
    { value: '', label: __('Theme Default', 'easy-google-fonts') },
    { value: 'none', label: __('None', 'easy-google-fonts') },
    { value: 'solid', label: __('Solid', 'easy-google-fonts') },
    { value: 'dashed', label: __('Dashed', 'easy-google-fonts') },
    { value: 'dotted', label: __('Dotted', 'easy-google-fonts') },
    { value: 'double', label: __('Double', 'easy-google-fonts') }
  ];

  return (
    <Panel className={`egf-border-control ${className}`}>
      <PanelBody title={__('Border', 'easy-google-fonts')} initialOpen={false}>
        {/* Top Border */}
        <div className="egf-border-control__top">
          <PanelRow className="mb-3">
            <div className="d-flex align-items-center">
              <label className="mr-0">{__('Border Top', 'easy-google-fonts')}</label>
              <ColorIndicator colorValue={borderTopColor} />
            </div>
            <Button isSecondary isSmall onClick={resetBorderTop}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>

          <RangeControl
            value={borderTopWidth.amount}
            initialPosition={borderTopWidth.amount}
            min={BORDER_UNITS[topUnitSelected].min}
            max={BORDER_UNITS[topUnitSelected].max}
            step={BORDER_UNITS[topUnitSelected].step}
            onChange={updateRangeControlBorderTopWidth}
            renderTooltipContent={() => `${borderTopWidth.amount}${borderTopWidth.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              value={`${borderTopWidth.amount}${borderTopWidth.unit}`}
              min={BORDER_UNITS[topUnitSelected].min}
              max={BORDER_UNITS[topUnitSelected].max}
              step={BORDER_UNITS[topUnitSelected].step}
              size="small"
              onChange={updateUnitControlBorderTopWidth}
              onUnitChange={onUnitChangeBorderTopWidth}
              units={Object.values(BORDER_UNITS)}
            />
            <SelectControl value={borderTopStyle} options={borderOptions} size="small" onChange={setBorderTopStyle} />
          </PanelRow>
          <ColorPalette
            className="mt-4"
            color={borderTopColor}
            colors={theme_colors}
            onChange={setBorderTopColor}
            clearable={false}
          />
        </div>

        <hr className="my-4" />

        {/* Bottom Border */}
        <div className="egf-border-control__bottom">
          <PanelRow className="mb-3">
            <div className="d-flex align-items-center">
              <label className="mr-0">{__('Border Bottom', 'easy-google-fonts')}</label>
              <ColorIndicator colorValue={borderBottomColor} />
            </div>
            <Button isSecondary isSmall onClick={resetBorderBottom}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>

          <RangeControl
            value={borderBottomWidth.amount}
            initialPosition={borderBottomWidth.amount}
            min={BORDER_UNITS[bottomUnitSelected].min}
            max={BORDER_UNITS[bottomUnitSelected].max}
            step={BORDER_UNITS[bottomUnitSelected].step}
            onChange={updateRangeControlBorderBottomWidth}
            renderTooltipContent={() => `${borderBottomWidth.amount}${borderBottomWidth.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              value={`${borderBottomWidth.amount}${borderBottomWidth.unit}`}
              min={BORDER_UNITS[bottomUnitSelected].min}
              max={BORDER_UNITS[bottomUnitSelected].max}
              step={BORDER_UNITS[bottomUnitSelected].step}
              size="small"
              onChange={updateUnitControlBorderBottomWidth}
              onUnitChange={onUnitChangeBorderBottomWidth}
              units={Object.values(BORDER_UNITS)}
            />
            <SelectControl
              value={borderBottomStyle}
              options={borderOptions}
              size="small"
              onChange={setBorderBottomStyle}
            />
          </PanelRow>
          <ColorPalette
            className="mt-4"
            color={borderBottomColor}
            colors={theme_colors}
            onChange={setBorderBottomColor}
            clearable={false}
          />
        </div>

        <hr className="my-4" />

        {/* Left Border */}
        <div className="egf-border-control__left">
          <PanelRow className="mb-3">
            <div className="d-flex align-items-center">
              <label className="mr-0">{__('Border Left', 'easy-google-fonts')}</label>
              <ColorIndicator colorValue={borderLeftColor} />
            </div>
            <Button isSecondary isSmall onClick={resetBorderLeft}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>

          <RangeControl
            value={borderLeftWidth.amount}
            initialPosition={borderLeftWidth.amount}
            min={BORDER_UNITS[leftUnitSelected].min}
            max={BORDER_UNITS[leftUnitSelected].max}
            step={BORDER_UNITS[leftUnitSelected].step}
            onChange={updateRangeControlBorderLeftWidth}
            renderTooltipContent={() => `${borderLeftWidth.amount}${borderLeftWidth.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              value={`${borderLeftWidth.amount}${borderLeftWidth.unit}`}
              min={BORDER_UNITS[leftUnitSelected].min}
              max={BORDER_UNITS[leftUnitSelected].max}
              step={BORDER_UNITS[leftUnitSelected].step}
              size="small"
              onChange={updateUnitControlBorderLeftWidth}
              onUnitChange={onUnitChangeBorderLeftWidth}
              units={Object.values(BORDER_UNITS)}
            />
            <SelectControl value={borderLeftStyle} options={borderOptions} size="small" onChange={setBorderLeftStyle} />
          </PanelRow>
          <ColorPalette
            className="mt-4"
            color={borderLeftColor}
            colors={theme_colors}
            onChange={setBorderLeftColor}
            clearable={false}
          />
        </div>

        <hr className="my-4" />

        {/* Right Border */}
        <div className="egf-border-control__right">
          <PanelRow className="mb-3">
            <div className="d-flex align-items-center">
              <label className="mr-0">{__('Border Right', 'easy-google-fonts')}</label>
              <ColorIndicator colorValue={borderRightColor} />
            </div>
            <Button isSecondary isSmall onClick={resetBorderRight}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>

          <RangeControl
            value={borderRightWidth.amount}
            initialPosition={borderRightWidth.amount}
            min={BORDER_UNITS[rightUnitSelected].min}
            max={BORDER_UNITS[rightUnitSelected].max}
            step={BORDER_UNITS[rightUnitSelected].step}
            onChange={updateRangeControlBorderRightWidth}
            renderTooltipContent={() => `${borderRightWidth.amount}${borderRightWidth.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              value={`${borderRightWidth.amount}${borderRightWidth.unit}`}
              min={BORDER_UNITS[rightUnitSelected].min}
              max={BORDER_UNITS[rightUnitSelected].max}
              step={BORDER_UNITS[rightUnitSelected].step}
              size="small"
              onChange={updateUnitControlBorderRightWidth}
              onUnitChange={onUnitChangeBorderRightWidth}
              units={Object.values(BORDER_UNITS)}
            />
            <SelectControl
              value={borderRightStyle}
              options={borderOptions}
              size="small"
              onChange={setBorderRightStyle}
            />
          </PanelRow>
          <ColorPalette
            className="mt-4"
            color={borderRightColor}
            colors={theme_colors}
            onChange={setBorderRightColor}
            clearable={false}
          />
        </div>
      </PanelBody>
    </Panel>
  );
};

export default BorderControl;
