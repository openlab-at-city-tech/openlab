import {
  Button,
  ColorIndicator,
  ColorPalette,
  ColorPicker,
  PanelRow,
  RangeControl,
  __experimentalUnitControl as UnitControl
} from '@wordpress/components';
import { useState } from '@wordpress/element';

import BackgroundColorControl from './style/BackgroundColorControl';
import FontColorControl from './style/FontColorControl';
import FontSizeControl from './style/FontSizeControl';
import LineHeightControl from './style/LineHeightControl';
import LetterSpacingControl from './style/LetterSpacingControl';

const customTooltipContent = value => `${value}`;

const { theme_colors } = egfCustomize;

const StyleSettings = ({
  control,
  fontColor,
  setFontColor,
  backgroundColor,
  setBackgroundColor,
  fontSize,
  setFontSize,
  lineHeight,
  setLineHeight,
  letterSpacing,
  setLetterSpacing
}) => {
  const [value, setValue] = useState('10px');

  return (
    <div className="egf-style-settings__settings" style={{ paddingTop: 16, paddingBottom: 8 }}>
      <FontColorControl className="mb-4" control={control} fontColor={fontColor} setFontColor={setFontColor} />

      <hr />

      <BackgroundColorControl
        className="mt-3 mb-4"
        control={control}
        backgroundColor={backgroundColor}
        setBackgroundColor={setBackgroundColor}
      />

      <hr />

      <FontSizeControl className="mt-3 mb-4" control={control} fontSize={fontSize} setFontSize={setFontSize} />

      <hr />

      <LineHeightControl
        className="mt-3 mb-4"
        control={control}
        lineHeight={lineHeight}
        setLineHeight={setLineHeight}
      />

      <hr />

      <LetterSpacingControl
        className="mt-3 mb-3"
        control={control}
        letterSpacing={letterSpacing}
        setLetterSpacing={setLetterSpacing}
      />
    </div>
  );
};

export default StyleSettings;
