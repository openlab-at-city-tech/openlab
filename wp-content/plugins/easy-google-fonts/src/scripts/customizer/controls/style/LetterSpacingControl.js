import { __, _x } from '@wordpress/i18n';
import { Button, PanelRow, RangeControl, __experimentalUnitControl as UnitControl } from '@wordpress/components';
import { parseUnit, LETTER_SPACING_UNITS } from '../../utils/units';

const LetterSpacingControl = props => {
  const { control, className = '', letterSpacing, setLetterSpacing } = props;

  /**
   * Reset Letter Spacing
   */
  const resetLetterSpacing = () => {
    const defaultSettings = control.settings.default.default;
    setLetterSpacing(defaultSettings.letter_spacing);
  };

  /**
   * Update Letter Spacing (Range Control)
   */
  const updateRangeControlFontSize = amount => setLetterSpacing({ amount, unit: unitSelected });

  /**
   * Update Letter Spacing (Unit Control)
   */
  const updateUnitControlLetterSpacing = value => {
    const [amount, unit] = parseUnit(value, Object.values(LETTER_SPACING_UNITS));
    if (amount > 0) {
      setLetterSpacing({ amount, unit });
    }
  };

  /**
   * On Unit Change Callback
   */
  const onUnitChange = (unit = 'px') => {
    if (unit === '%') {
      unit = 'percent';
    }

    setLetterSpacing({
      amount: LETTER_SPACING_UNITS[unit].initial,
      unit
    });
  };

  let unitSelected = letterSpacing.unit || 'px';

  if (letterSpacing.unit === '%') {
    unitSelected = 'percent';
  }

  return (
    <div className={`egf-letter-spacing-control ${className}`}>
      <RangeControl
        label={__('Letter Spacing', 'easy-google-fonts')}
        value={letterSpacing.amount}
        min={LETTER_SPACING_UNITS[unitSelected].min}
        max={LETTER_SPACING_UNITS[unitSelected].max}
        step={LETTER_SPACING_UNITS[unitSelected].step}
        initialPosition={letterSpacing.amount}
        onChange={updateRangeControlFontSize}
        renderTooltipContent={() => `${letterSpacing.amount}${letterSpacing.unit}`}
        withInputField={false}
      />
      <PanelRow className="mt-2">
        <UnitControl
          min={LETTER_SPACING_UNITS[unitSelected].min}
          max={LETTER_SPACING_UNITS[unitSelected].max}
          step={LETTER_SPACING_UNITS[unitSelected].step}
          size="small"
          onChange={updateUnitControlLetterSpacing}
          onUnitChange={onUnitChange}
          value={`${letterSpacing.amount}${letterSpacing.unit}`}
          units={Object.values(LETTER_SPACING_UNITS)}
        />
        <Button isSecondary isSmall onClick={resetLetterSpacing}>
          {__('Reset', 'easy-google-fonts')}
        </Button>
      </PanelRow>
    </div>
  );
};

export default LetterSpacingControl;
