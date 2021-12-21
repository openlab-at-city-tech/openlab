import { __, _x } from '@wordpress/i18n';
import { Button, PanelRow, RangeControl, __experimentalNumberControl as NumberControl } from '@wordpress/components';

const LINE_HEIGHT_UNITS = {
  min: 0.8,
  max: 4,
  step: 0.1
};

const LineHeightControl = props => {
  const { control, className = '', lineHeight, setLineHeight } = props;

  /**
   * Reset Font Size
   */
  const resetLineHeight = () => {
    const defaultSettings = control.settings.default.default;
    setLineHeight(defaultSettings.line_height);
  };

  const updateLineHeight = lineHeight => {
    setLineHeight(lineHeight);
  };

  return (
    <div className={`egf-line-height-control ${className}`}>
      <RangeControl
        label={__('Line Height', 'easy-google-fonts')}
        value={lineHeight}
        initialPosition={lineHeight}
        onChange={updateLineHeight}
        min={LINE_HEIGHT_UNITS.min}
        max={LINE_HEIGHT_UNITS.max}
        step={LINE_HEIGHT_UNITS.step}
        renderTooltipContent={() => lineHeight}
        withInputField={false}
      />

      <PanelRow className="mt-2">
        <NumberControl
          value={lineHeight}
          onChange={updateLineHeight}
          min={LINE_HEIGHT_UNITS.min}
          max={LINE_HEIGHT_UNITS.max}
          step={LINE_HEIGHT_UNITS.step}
        />

        <Button isSecondary isSmall onClick={resetLineHeight}>
          {__('Reset', 'easy-google-fonts')}
        </Button>
      </PanelRow>
    </div>
  );
};

export default LineHeightControl;
