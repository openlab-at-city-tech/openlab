import { __ } from '@wordpress/i18n';
import { Button, ColorIndicator, ColorPalette, PanelRow } from '@wordpress/components';

const { theme_colors } = egfCustomize;

const BackgroundColorControl = props => {
  const { control, className = '', backgroundColor, setBackgroundColor } = props;

  const resetBackgroundColor = () => {
    const defaultSettings = control.settings.default.default;
    setBackgroundColor(defaultSettings.background_color);
  };

  return (
    <div className={`egf-background-color-control ${className}`}>
      <PanelRow className="mb-3">
        <div className="d-flex align-items-center">
          <label className="mr-0">{__('Background Color', 'easy-google-fonts')}</label>
          <ColorIndicator colorValue={backgroundColor} />
        </div>
        <Button isSecondary isSmall onClick={resetBackgroundColor}>
          {__('Reset', 'easy-google-fonts')}
        </Button>
      </PanelRow>
      <ColorPalette
        color={backgroundColor}
        colors={theme_colors}
        onChange={setBackgroundColor}
        clearable={false}
        disableAlpha={false}
      />
    </div>
  );
};

export default BackgroundColorControl;
