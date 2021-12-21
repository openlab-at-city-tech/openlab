import { __ } from '@wordpress/i18n';
import {
  Button,
  Panel,
  PanelBody,
  PanelRow,
  RangeControl,
  __experimentalUnitControl as UnitControl
} from '@wordpress/components';

import { parseUnit, MARGIN_UNITS } from '../../utils/units';

const MarginControl = props => {
  const {
    control,
    className = '',
    marginBottom,
    setMarginBottom,
    marginLeft,
    setMarginLeft,
    marginRight,
    setMarginRight,
    marginTop,
    setMarginTop
  } = props;

  /**
   * Reset Margin Functions
   */
  const resetMarginTop = () => {
    const defaultSettings = control.settings.default.default;
    setMarginTop(defaultSettings.margin_top);
  };

  const resetMarginBottom = () => {
    const defaultSettings = control.settings.default.default;
    setMarginBottom(defaultSettings.margin_bottom);
  };

  const resetMarginLeft = () => {
    const defaultSettings = control.settings.default.default;
    setMarginLeft(defaultSettings.margin_left);
  };

  const resetMarginRight = () => {
    const defaultSettings = control.settings.default.default;
    setMarginRight(defaultSettings.margin_right);
  };

  /**
   * Update Margin (Range Controls)
   */
  const updateRangeControlMarginTop = amount => setMarginTop({ amount, unit: topUnitSelected });
  const updateRangeControlMarginBottom = amount => setMarginBottom({ amount, unit: bottomUnitSelected });
  const updateRangeControlMarginLeft = amount => setMarginLeft({ amount, unit: leftUnitSelected });
  const updateRangeControlMarginRight = amount => setMarginRight({ amount, unit: rightUnitSelected });

  /**
   * Update Margin (Unit Control)
   */
  const updateUnitControlMarginTop = value => {
    const [amount, unit] = parseUnit(value, Object.values(MARGIN_UNITS));
    if (amount > 0) {
      setMarginTop({ amount, unit });
    }
  };

  const updateUnitControlMarginBottom = value => {
    const [amount, unit] = parseUnit(value, Object.values(MARGIN_UNITS));
    if (amount > 0) {
      setMarginBottom({ amount, unit });
    }
  };

  const updateUnitControlMarginLeft = value => {
    const [amount, unit] = parseUnit(value, Object.values(MARGIN_UNITS));
    if (amount > 0) {
      setMarginLeft({ amount, unit });
    }
  };

  const updateUnitControlMarginRight = value => {
    const [amount, unit] = parseUnit(value, Object.values(MARGIN_UNITS));
    if (amount > 0) {
      setMarginRight({ amount, unit });
    }
  };

  /**
   * On Unit Change Callback
   */
  const onUnitChangeMarginTop = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setMarginTop({
      amount: MARGIN_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangeMarginBottom = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setMarginBottom({
      amount: MARGIN_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangeMarginLeft = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setMarginLeft({
      amount: MARGIN_UNITS[unit].initial,
      unit
    });
  };

  const onUnitChangeMarginRight = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setMarginRight({
      amount: MARGIN_UNITS[unit].initial,
      unit
    });
  };

  const sanitizeUnit = unit => (unit === '%' ? 'percent' : unit);
  let topUnitSelected = sanitizeUnit(marginTop.unit) || 'px';
  let bottomUnitSelected = sanitizeUnit(marginBottom.unit) || 'px';
  let leftUnitSelected = sanitizeUnit(marginLeft.unit) || 'px';
  let rightUnitSelected = sanitizeUnit(marginRight.unit) || 'px';

  return (
    <Panel className={`egf-margin-control ${className}`}>
      <PanelBody title={__('Margin', 'easy-google-fonts')} initialOpen={false}>
        {/* Margin Top */}
        <div className="egf-margin-control__top">
          <RangeControl
            label={__('Margin Top', 'easy-google-fonts')}
            value={marginTop.amount}
            min={MARGIN_UNITS[topUnitSelected].min}
            max={MARGIN_UNITS[topUnitSelected].max}
            step={MARGIN_UNITS[topUnitSelected].step}
            initialPosition={marginTop.amount}
            onChange={updateRangeControlMarginTop}
            renderTooltipContent={() => `${marginTop.amount}${marginTop.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={MARGIN_UNITS[topUnitSelected].min}
              max={MARGIN_UNITS[topUnitSelected].max}
              step={MARGIN_UNITS[topUnitSelected].step}
              size="small"
              onChange={updateUnitControlMarginTop}
              onUnitChange={onUnitChangeMarginTop}
              value={`${marginTop.amount}${marginTop.unit}`}
              units={Object.values(MARGIN_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetMarginTop}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>

        <hr className="my-4" />

        {/* Margin Bottom */}
        <div className="egf-margin-control__bottom">
          <RangeControl
            label={__('Margin Bottom', 'easy-google-fonts')}
            value={marginBottom.amount}
            min={MARGIN_UNITS[bottomUnitSelected].min}
            max={MARGIN_UNITS[bottomUnitSelected].max}
            step={MARGIN_UNITS[bottomUnitSelected].step}
            initialPosition={marginBottom.amount}
            onChange={updateRangeControlMarginBottom}
            renderTooltipContent={() => `${marginBottom.amount}${marginBottom.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={MARGIN_UNITS[bottomUnitSelected].min}
              max={MARGIN_UNITS[bottomUnitSelected].max}
              step={MARGIN_UNITS[bottomUnitSelected].step}
              size="small"
              onChange={updateUnitControlMarginBottom}
              onUnitChange={onUnitChangeMarginBottom}
              value={`${marginBottom.amount}${marginBottom.unit}`}
              units={Object.values(MARGIN_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetMarginBottom}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>

        <hr className="my-4" />

        {/* Margin Left */}
        <div className="egf-margin-control__left">
          <RangeControl
            label={__('Margin Left', 'easy-google-fonts')}
            value={marginLeft.amount}
            min={MARGIN_UNITS[leftUnitSelected].min}
            max={MARGIN_UNITS[leftUnitSelected].max}
            step={MARGIN_UNITS[leftUnitSelected].step}
            initialPosition={marginLeft.amount}
            onChange={updateRangeControlMarginLeft}
            renderTooltipContent={() => `${marginLeft.amount}${marginLeft.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={MARGIN_UNITS[leftUnitSelected].min}
              max={MARGIN_UNITS[leftUnitSelected].max}
              step={MARGIN_UNITS[leftUnitSelected].step}
              size="small"
              onChange={updateUnitControlMarginLeft}
              onUnitChange={onUnitChangeMarginLeft}
              value={`${marginLeft.amount}${marginLeft.unit}`}
              units={Object.values(MARGIN_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetMarginLeft}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>

        <hr className="my-4" />

        {/* Margin Right */}
        <div className="egf-margin-control__right">
          <RangeControl
            label={__('Margin Right', 'easy-google-fonts')}
            value={marginRight.amount}
            min={MARGIN_UNITS[rightUnitSelected].min}
            max={MARGIN_UNITS[rightUnitSelected].max}
            step={MARGIN_UNITS[rightUnitSelected].step}
            initialPosition={marginRight.amount}
            onChange={updateRangeControlMarginRight}
            renderTooltipContent={() => `${marginRight.amount}${marginRight.unit}`}
            withInputField={false}
          />
          <PanelRow className="mt-2">
            <UnitControl
              min={MARGIN_UNITS[rightUnitSelected].min}
              max={MARGIN_UNITS[rightUnitSelected].max}
              step={MARGIN_UNITS[rightUnitSelected].step}
              size="small"
              onChange={updateUnitControlMarginRight}
              onUnitChange={onUnitChangeMarginRight}
              value={`${marginRight.amount}${marginRight.unit}`}
              units={Object.values(MARGIN_UNITS)}
            />
            <Button isSecondary isSmall onClick={resetMarginRight}>
              {__('Reset', 'easy-google-fonts')}
            </Button>
          </PanelRow>
        </div>
      </PanelBody>
    </Panel>
  );
};

export default MarginControl;
