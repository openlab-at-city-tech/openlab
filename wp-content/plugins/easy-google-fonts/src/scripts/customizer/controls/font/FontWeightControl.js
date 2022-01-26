import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';
import { getFontById } from '../../utils/getFontById';

const FontWeightControl = props => {
  const { className, fontId, fontWeightStyle, setFontWeightStyle, setFontWeight, setFontStyle } = props;

  const fontSelected = getFontById(fontId);

  const variants = fontSelected ? fontSelected.variants.map(variant => ({ value: variant, label: variant })) : [];

  const fontWeightStyleOptions = [
    {
      value: '',
      label: __('Theme Default', 'easy-google-fonts')
    },
    ...variants
  ];

  const setFontWeightStyleSettings = (newFontWeightStyle = '') => {
    const newFontWeight = newFontWeightStyle
      ? Number.isNaN(parseInt(newFontWeightStyle, 10))
        ? 400
        : parseInt(newFontWeightStyle, 10)
      : '';
    const newFontStyle = newFontWeightStyle.includes('italic') ? 'italic' : 'normal';

    setFontWeightStyle(newFontWeightStyle);
    setFontWeight(newFontWeight);
    setFontStyle(newFontStyle);
  };

  return (
    <div className={`egf-font-weight-control ${className}`}>
      <SelectControl
        label={__('Font Weight/Style', 'easy-google-fonts')}
        value={fontWeightStyle}
        options={fontWeightStyleOptions}
        onChange={setFontWeightStyleSettings}
      />
    </div>
  );
};

export default FontWeightControl;
