import { __, _x } from '@wordpress/i18n';
import { Button, PanelRow, RangeControl, __experimentalUnitControl as UnitControl } from '@wordpress/components';
import { parseUnit, FONT_SIZE_UNITS } from '../../utils/units';

const FontSizeControl = props => {
  const { control, className = '', fontSize, setFontSize } = props;

  /**
   * Reset Font Size
   */
  const resetFontSize = () => {
    const defaultSettings = control.settings.default.default;
    setFontSize(defaultSettings.font_size);
  };

  /**
   * Update Font Size (Range Control)
   */
  const updateRangeControlFontSize = amount => setFontSize({ amount, unit: unitSelected });

  /**
   * Update Font Size (Unit Control)
   */
  const updateUnitControlFontSize = value => {
    const [amount, unit] = parseUnit(value, Object.values(FONT_SIZE_UNITS));
    if (amount > 0) {
      setFontSize({ amount, unit });
    }
  };

  /**
   * On Unit Change Callback
   */
  const onUnitChange = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setFontSize({
      amount: FONT_SIZE_UNITS[unit].initial,
      unit
    });
  };

  let unitSelected = fontSize.unit || 'px';

  if (fontSize.unit === '%') {
    unitSelected = 'percent';
  }

  return (
    <div className={`egf-font-size-control ${className}`}>
      <RangeControl
        label={__('Font Size', 'easy-google-fonts')}
        value={fontSize.amount}
        min={FONT_SIZE_UNITS[unitSelected].min}
        max={FONT_SIZE_UNITS[unitSelected].max}
        step={FONT_SIZE_UNITS[unitSelected].step}
        initialPosition={fontSize.amount}
        onChange={updateRangeControlFontSize}
        renderTooltipContent={() => `${fontSize.amount}${fontSize.unit}`}
        withInputField={false}
      />
      <PanelRow className="mt-2">
        <UnitControl
          min={FONT_SIZE_UNITS[unitSelected].min}
          max={FONT_SIZE_UNITS[unitSelected].max}
          step={FONT_SIZE_UNITS[unitSelected].step}
          size="small"
          onChange={updateUnitControlFontSize}
          onUnitChange={onUnitChange}
          value={`${fontSize.amount}${fontSize.unit}`}
          units={Object.values(FONT_SIZE_UNITS)}
        />
        <Button isSecondary isSmall onClick={resetFontSize}>
          {__('Reset', 'easy-google-fonts')}
        </Button>
      </PanelRow>
    </div>
  );
};

export default FontSizeControl;
