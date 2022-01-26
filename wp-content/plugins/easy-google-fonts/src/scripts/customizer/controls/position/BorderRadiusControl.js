import { __ } from '@wordpress/i18n';
import {
  Button,
  Panel,
  PanelBody,
  PanelRow,
  RangeControl,
  __experimentalUnitControl as UnitControl
} from '@wordpress/components';

import { parseUnit, BORDER_RADIUS_UNITS } from '../../utils/units';

const BorderRadiusControl = props => {
  const {
    className,
    control,
    borderRadiusBottomLeft,
    setBorderRadiusBottomLeft,
    borderRadiusBottomRight,
    setBorderRadiusBottomRight,
    borderRadiusTopLeft,
    setBorderRadiusTopLeft,
    borderRadiusTopRight,
    setBorderRadiusTopRight
  } = props;

  /**
   * Reset Border Radius Functions
   */
  const resetBorderRadiusTopLeft = () => {
    const defaultSettings = control.settings.default.default;
    setBorderRadiusTopLeft(defaultSettings.border_radius_top_left);
  };

  const resetBorderRadiusTopRight = () => {
    const defaultSettings = control.settings.default.default;
    setBorderRadiusTopRight(defaultSettings.border_radius_top_right);
  };

  const resetBorderRadiusBottomLeft = () => {
    const defaultSettings = control.settings.default.default;
    setBorderRadiusBottomLeft(defaultSettings.border_radius_bottom_left);
  };

  const resetBorderRadiusBottomRight = () => {
    const defaultSettings = control.settings.default.default;
    setBorderRadiusBottomRight(defaultSettings.border_radius_bottom_right);
  };

  /**
   * Update Border Radius (Range Controls)
   */
  const updateRangeControlBorderRadiusTopLeft = amount => setBorderRadiusTopLeft({ amount, unit: topLeftUnitSelected });
  const updateRangeControlBorderRadiusTopRight = amount =>
    setBorderRadiusTopRight({ amount, unit: topRightUnitSelected });
  const updateRangeControlBorderRadiusBottomLeft = amount =>
    setBorderRadiusBottomLeft({ amount, unit: bottomLeftUnitSelected });
  const updateRangeControlBorderRadiusBottomRight = amount =>
    setBorderRadiusBottomRight({ amount, unit: bottomRightUnitSelected });

  /**
   * Update Border Radius (Unit Control)
   */
  const updateUnitControlBorderRadiusTopLeft = value => {
    const [amount, unit] = parseUnit(value, Object.values(BORDER_RADIUS_UNITS));
    if (amount > 0) {
      setBorderRadiusTopLeft({ amount, unit });
    }
  };

  const updateUnitControlBorderRadiusTopRight = value => {
    const [amount, unit] = parseUnit(value, Object.values(BORDER_RADIUS_UNITS));
    if (amount > 0) {
      setBorderRadiusTopRight({ amount, unit });
    }
  };

  const updateUnitControlBorderRadiusBottomLeft = value => {
    const [amount, unit] = parseUnit(value, Object.values(BORDER_RADIUS_UNITS));
    if (amount > 0) {
      setBorderRadiusBottomLeft({ amount, unit });
    }
  };

  const updateUnitControlBorderRadiusBottomRight = value => {
    const [amount, unit] = parseUnit(value, Object.values(BORDER_RADIUS_UNITS));
    if (amount > 0) {
      setBorderRadiusBottomRight({ amount, unit });
    }
  };

  /**
   * On Unit Change Callback
   */
  const onUnitChangeBorderRadiusTopLeft = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setBorderRadiusTopLeft({
      amount: BORDER_RADIUS_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangeBorderRadiusTopRight = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setBorderRadiusTopRight({
      amount: BORDER_RADIUS_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangeBorderRadiusBottomLeft = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setBorderRadiusBottomLeft({
      amount: BORDER_RADIUS_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangeBorderRadiusBottomRight = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setBorderRadiusBottomRight({
      amount: BORDER_RADIUS_UNITS[unit].initial,
      unit
    });
  };

  const sanitizeUnit = unit => (unit === '%' ? 'percent' : unit);
  let topLeftUnitSelected = sanitizeUnit(borderRadiusTopLeft.unit) || 'px';
  let topRightUnitSelected = sanitizeUnit(borderRadiusTopRight.unit) || 'px';
  let bottomLeftUnitSelected = sanitizeUnit(borderRadiusBottomLeft.unit) || 'px';
  let bottomRightUnitSelected = sanitizeUnit(borderRadiusBottomRight.unit) || 'px';

  return (
    <Panel className={`egf-border-radius-control ${className}`}>
      <PanelBody title={__('Border Radius', 'easy-google-fonts')} initialOpen={false}>
        {/* Top Left Border Radius */}
        <div className="egf-border-radius-control__top-left">
          <RangeControl
            label={__('Border Radius - Top Left', 'easy-google-fonts')}
            value={borderRadiusTopLeft.amount}
            min={BORDER_RADIUS_UNITS[topLeftUnitSelected].min}
            max={BORDER_RADIUS_UNITS[topLeftUnitSelected].max}
            step={BORDER_RADIUS_UNITS[topLeftUnitSelected].step}
            initialPosition={borderRadiusTopLeft.amount}
            onChange={updateRangeControlBorderRadiusTopLeft}
            renderTooltipContent={() => `${borderRadiusTopLeft.amount}${borderRadiusTopLeft.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={BORDER_RADIUS_UNITS[topLeftUnitSelected].min}
              max={BORDER_RADIUS_UNITS[topLeftUnitSelected].max}
              step={BORDER_RADIUS_UNITS[topLeftUnitSelected].step}
              size="small"
              onChange={updateUnitControlBorderRadiusTopLeft}
              onUnitChange={onUnitChangeBorderRadiusTopLeft}
              value={`${borderRadiusTopLeft.amount}${borderRadiusTopLeft.unit}`}
              units={Object.values(BORDER_RADIUS_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetBorderRadiusTopLeft}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>

        <hr className="my-4" />

        {/* Top Right Border Radius */}
        <div className="egf-border-radius-control__top-right">
          <RangeControl
            label={__('Border Radius - Top Right', 'easy-google-fonts')}
            value={borderRadiusTopRight.amount}
            min={BORDER_RADIUS_UNITS[topRightUnitSelected].min}
            max={BORDER_RADIUS_UNITS[topRightUnitSelected].max}
            step={BORDER_RADIUS_UNITS[topRightUnitSelected].step}
            initialPosition={borderRadiusTopRight.amount}
            onChange={updateRangeControlBorderRadiusTopRight}
            renderTooltipContent={() => `${borderRadiusTopRight.amount}${borderRadiusTopRight.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={BORDER_RADIUS_UNITS[topRightUnitSelected].min}
              max={BORDER_RADIUS_UNITS[topRightUnitSelected].max}
              step={BORDER_RADIUS_UNITS[topRightUnitSelected].step}
              size="small"
              onChange={updateUnitControlBorderRadiusTopRight}
              onUnitChange={onUnitChangeBorderRadiusTopRight}
              value={`${borderRadiusTopRight.amount}${borderRadiusTopRight.unit}`}
              units={Object.values(BORDER_RADIUS_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetBorderRadiusTopRight}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>

        <hr className="my-4" />

        {/* Bottom Left Border Radius */}
        <div className="egf-border-radius-control__bottom-left">
          <RangeControl
            label={__('Border Radius - Bottom Left', 'easy-google-fonts')}
            value={borderRadiusBottomLeft.amount}
            min={BORDER_RADIUS_UNITS[bottomLeftUnitSelected].min}
            max={BORDER_RADIUS_UNITS[bottomLeftUnitSelected].max}
            step={BORDER_RADIUS_UNITS[bottomLeftUnitSelected].step}
            initialPosition={borderRadiusBottomLeft.amount}
            onChange={updateRangeControlBorderRadiusBottomLeft}
            renderTooltipContent={() => `${borderRadiusBottomLeft.amount}${borderRadiusBottomLeft.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={BORDER_RADIUS_UNITS[bottomLeftUnitSelected].min}
              max={BORDER_RADIUS_UNITS[bottomLeftUnitSelected].max}
              step={BORDER_RADIUS_UNITS[bottomLeftUnitSelected].step}
              size="small"
              onChange={updateUnitControlBorderRadiusBottomLeft}
              onUnitChange={onUnitChangeBorderRadiusBottomLeft}
              value={`${borderRadiusBottomLeft.amount}${borderRadiusBottomLeft.unit}`}
              units={Object.values(BORDER_RADIUS_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetBorderRadiusBottomLeft}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>

        <hr className="my-4" />

        {/* Bottom Right Border Radius */}
        <div className="egf-border-radius-control__bottom-right">
          <RangeControl
            label={__('Border Radius - Bottom Right', 'easy-google-fonts')}
            value={borderRadiusBottomRight.amount}
            min={BORDER_RADIUS_UNITS[bottomRightUnitSelected].min}
            max={BORDER_RADIUS_UNITS[bottomRightUnitSelected].max}
            step={BORDER_RADIUS_UNITS[bottomRightUnitSelected].step}
            initialPosition={borderRadiusBottomRight.amount}
            onChange={updateRangeControlBorderRadiusBottomRight}
            renderTooltipContent={() => `${borderRadiusBottomRight.amount}${borderRadiusBottomRight.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={BORDER_RADIUS_UNITS[bottomRightUnitSelected].min}
              max={BORDER_RADIUS_UNITS[bottomRightUnitSelected].max}
              step={BORDER_RADIUS_UNITS[bottomRightUnitSelected].step}
              size="small"
              onChange={updateUnitControlBorderRadiusBottomRight}
              onUnitChange={onUnitChangeBorderRadiusBottomRight}
              value={`${borderRadiusBottomRight.amount}${borderRadiusBottomRight.unit}`}
              units={Object.values(BORDER_RADIUS_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetBorderRadiusBottomRight}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>
      </PanelBody>
    </Panel>
  );
};

export default BorderRadiusControl;
