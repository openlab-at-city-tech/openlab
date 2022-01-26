import { __ } from '@wordpress/i18n';
import { Button, ColorIndicator, ColorPalette, PanelRow } from '@wordpress/components';

const { theme_colors } = egfCustomize;

const FontColorControl = props => {
  const { control, className = '', fontColor, setFontColor } = props;

  const resetColor = () => {
    const defaultSettings = control.settings.default.default;
    setFontColor(defaultSettings.font_color);
  };

  return (
    <div className={`egf-font-color-control ${className}`}>
      <PanelRow className="mb-3">
        <div className="d-flex align-items-center">
          <label className="mr-0">{__('Font Color', 'easy-google-fonts')}</label>
          <ColorIndicator colorValue={fontColor} />
        </div>
        <Button isSecondary isSmall onClick={resetColor}>
          {__('Reset', 'easy-google-fonts')}
        </Button>
      </PanelRow>
      <ColorPalette color={fontColor} colors={theme_colors} onChange={setFontColor} clearable={false} />
    </div>
  );
};

export default FontColorControl;
