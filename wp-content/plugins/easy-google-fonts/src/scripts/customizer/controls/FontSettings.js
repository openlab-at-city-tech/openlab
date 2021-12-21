import { useState } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';

import FontLanguageControl from './font/FontLanguageControl';
import FontFamilyControl from './font/FontFamilyControl';
import FontWeightControl from './font/FontWeightControl';
import TextDecorationControl from './font/TextDecorationControl';
import TextTransformControl from './font/TextTransformControl';

const FontSettings = ({
  control,
  fontId,
  setFontId,
  fontName,
  setFontName,
  fontWeightStyle,
  setFontWeightStyle,
  fontWeight,
  setFontWeight,
  fontStyle,
  setFontStyle,
  stylesheetUrl,
  setStylesheetUrl,
  subset,
  setSubset,
  textDecoration,
  setTextDecoration,
  textTransform,
  setTextTransform
}) => {
  return (
    <div className="egf-font-settings__settings">
      <FontLanguageControl control={control} subset={subset} setSubset={setSubset} className="mb-3" />
      <FontFamilyControl
        control={control}
        subset={subset}
        fontId={fontId}
        setFontId={setFontId}
        fontName={fontName}
        setFontName={setFontName}
        fontWeight={fontWeight}
        setFontWeight={setFontWeight}
        fontStyle={fontStyle}
        setFontStyle={setFontStyle}
        fontWeightStyle={fontWeightStyle}
        setFontWeightStyle={setFontWeightStyle}
        stylesheetUrl={stylesheetUrl}
        setStylesheetUrl={setStylesheetUrl}
        className="mb-3"
      />

      {/* Font Weight/Style */}
      <FontWeightControl
        control={control}
        fontId={fontId}
        setFontWeight={setFontWeight}
        setFontStyle={setFontStyle}
        fontWeightStyle={fontWeightStyle}
        setFontWeightStyle={setFontWeightStyle}
        className="mb-3"
      />

      {/* Text Decoration */}
      <TextDecorationControl textDecoration={textDecoration} setTextDecoration={setTextDecoration} className="mb-3" />

      {/* Text Transform */}
      <TextTransformControl textTransform={textTransform} setTextTransform={setTextTransform} className="mb-3" />
    </div>
  );
};

export default FontSettings;
