import { __ } from '@wordpress/i18n';
import {
  Button,
  Panel,
  PanelBody,
  PanelRow,
  RangeControl,
  __experimentalUnitControl as UnitControl
} from '@wordpress/components';

import { parseUnit, PADDING_UNITS } from '../../utils/units';

const PaddingControl = props => {
  const {
    control,
    className = '',
    paddingBottom,
    setPaddingBottom,
    paddingLeft,
    setPaddingLeft,
    paddingRight,
    setPaddingRight,
    paddingTop,
    setPaddingTop
  } = props;

  /**
   * Reset Padding Functions
   */
  const resetPaddingTop = () => {
    const defaultSettings = control.settings.default.default;
    setPaddingTop(defaultSettings.padding_top);
  };

  const resetPaddingBottom = () => {
    const defaultSettings = control.settings.default.default;
    setPaddingBottom(defaultSettings.padding_bottom);
  };

  const resetPaddingLeft = () => {
    const defaultSettings = control.settings.default.default;
    setPaddingLeft(defaultSettings.padding_left);
  };

  const resetPaddingRight = () => {
    const defaultSettings = control.settings.default.default;
    setPaddingRight(defaultSettings.padding_right);
  };

  /**
   * Update Padding (Range Controls)
   */
  const updateRangeControlPaddingTop = amount => setPaddingTop({ amount, unit: topUnitSelected });
  const updateRangeControlPaddingBottom = amount => setPaddingBottom({ amount, unit: bottomUnitSelected });
  const updateRangeControlPaddingLeft = amount => setPaddingLeft({ amount, unit: leftUnitSelected });
  const updateRangeControlPaddingRight = amount => setPaddingRight({ amount, unit: rightUnitSelected });

  /**
   * Update Padding (Unit Control)
   */
  const updateUnitControlPaddingTop = value => {
    const [amount, unit] = parseUnit(value, Object.values(PADDING_UNITS));
    if (amount > 0) {
      setPaddingTop({ amount, unit });
    }
  };

  const updateUnitControlPaddingBottom = value => {
    const [amount, unit] = parseUnit(value, Object.values(PADDING_UNITS));
    if (amount > 0) {
      setPaddingBottom({ amount, unit });
    }
  };

  const updateUnitControlPaddingLeft = value => {
    const [amount, unit] = parseUnit(value, Object.values(PADDING_UNITS));
    if (amount > 0) {
      setPaddingLeft({ amount, unit });
    }
  };

  const updateUnitControlPaddingRight = value => {
    const [amount, unit] = parseUnit(value, Object.values(PADDING_UNITS));
    if (amount > 0) {
      setPaddingRight({ amount, unit });
    }
  };

  /**
   * On Unit Change Callback
   */
  const onUnitChangePaddingTop = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setPaddingTop({
      amount: PADDING_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangePaddingBottom = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setPaddingBottom({
      amount: PADDING_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangePaddingLeft = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setPaddingLeft({
      amount: PADDING_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangePaddingRight = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setPaddingRight({
      amount: PADDING_UNITS[unit].initial,
      unit
    });
  };

  const sanitizeUnit = unit => (unit === '%' ? 'percent' : unit);
  let topUnitSelected = sanitizeUnit(paddingTop.unit) || 'px';
  let bottomUnitSelected = sanitizeUnit(paddingBottom.unit) || 'px';
  let leftUnitSelected = sanitizeUnit(paddingLeft.unit) || 'px';
  let rightUnitSelected = sanitizeUnit(paddingRight.unit) || 'px';

  return (
    <Panel className={`egf-padding-control ${className}`}>
      <PanelBody title={__('Padding', 'easy-google-fonts')} initialOpen={false}>
        {/* Padding Top */}
        <div className="egf-padding-control__top">
          <RangeControl
            label={__('Padding Top', 'easy-google-fonts')}
            value={paddingTop.amount}
            min={PADDING_UNITS[topUnitSelected].min}
            max={PADDING_UNITS[topUnitSelected].max}
            step={PADDING_UNITS[topUnitSelected].step}
            initialPosition={paddingTop.amount}
            onChange={updateRangeControlPaddingTop}
            renderTooltipContent={() => `${paddingTop.amount}${paddingTop.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={PADDING_UNITS[topUnitSelected].min}
              max={PADDING_UNITS[topUnitSelected].max}
              step={PADDING_UNITS[topUnitSelected].step}
              size="small"
              onChange={updateUnitControlPaddingTop}
              onUnitChange={onUnitChangePaddingTop}
              value={`${paddingTop.amount}${paddingTop.unit}`}
              units={Object.values(PADDING_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetPaddingTop}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>

        <hr className="my-4" />

        {/* Padding Bottom */}
        <div className="egf-padding-control__bottom">
          <RangeControl
            label={__('Padding Bottom', 'easy-google-fonts')}
            value={paddingBottom.amount}
            min={PADDING_UNITS[bottomUnitSelected].min}
            max={PADDING_UNITS[bottomUnitSelected].max}
            step={PADDING_UNITS[bottomUnitSelected].step}
            initialPosition={paddingBottom.amount}
            onChange={updateRangeControlPaddingBottom}
            renderTooltipContent={() => `${paddingBottom.amount}${paddingBottom.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={PADDING_UNITS[bottomUnitSelected].min}
              max={PADDING_UNITS[bottomUnitSelected].max}
              step={PADDING_UNITS[bottomUnitSelected].step}
              size="small"
              onChange={updateUnitControlPaddingBottom}
              onUnitChange={onUnitChangePaddingBottom}
              value={`${paddingBottom.amount}${paddingBottom.unit}`}
              units={Object.values(PADDING_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetPaddingBottom}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>

        <hr className="my-4" />

        {/* Padding Left */}
        <div className="egf-padding-control__left">
          <RangeControl
            label={__('Padding Left', 'easy-google-fonts')}
            value={paddingLeft.amount}
            min={PADDING_UNITS[leftUnitSelected].min}
            max={PADDING_UNITS[leftUnitSelected].max}
            step={PADDING_UNITS[leftUnitSelected].step}
            initialPosition={paddingLeft.amount}
            onChange={updateRangeControlPaddingLeft}
            renderTooltipContent={() => `${paddingLeft.amount}${paddingLeft.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={PADDING_UNITS[leftUnitSelected].min}
              max={PADDING_UNITS[leftUnitSelected].max}
              step={PADDING_UNITS[leftUnitSelected].step}
              size="small"
              onChange={updateUnitControlPaddingLeft}
              onUnitChange={onUnitChangePaddingLeft}
              value={`${paddingLeft.amount}${paddingLeft.unit}`}
              units={Object.values(PADDING_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetPaddingLeft}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>

        <hr className="my-4" />

        {/* Padding Right */}
        <div className="egf-padding-control__right">
          <RangeControl
            label={__('Padding Right', 'easy-google-fonts')}
            value={paddingRight.amount}
            min={PADDING_UNITS[rightUnitSelected].min}
            max={PADDING_UNITS[rightUnitSelected].max}
            step={PADDING_UNITS[rightUnitSelected].step}
            initialPosition={paddingRight.amount}
            onChange={updateRangeControlPaddingRight}
            renderTooltipContent={() => `${paddingRight.amount}${paddingRight.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={PADDING_UNITS[rightUnitSelected].min}
              max={PADDING_UNITS[rightUnitSelected].max}
              step={PADDING_UNITS[rightUnitSelected].step}
              size="small"
              onChange={updateUnitControlPaddingRight}
              onUnitChange={onUnitChangePaddingRight}
              value={`${paddingRight.amount}${paddingRight.unit}`}
              units={Object.values(PADDING_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetPaddingRight}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>
      </PanelBody>
    </Panel>
  );
};

export default PaddingControl;
