import { _x } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

const FontLanguageControl = props => {
  const { className, subset, setSubset } = props;

  const languageOptions = [
    {
      value: 'latin,all',
      label: _x(
        'All Languages',
        'Label for the all languages option in the language dropdown in the customizer.',
        'easy-google-fonts'
      )
    },
    ...window.egfGoogleFontLanguages
      .map(language => ({ value: language, label: language }))
      .sort((a, b) => (a.value > b.value ? 1 : -1))
  ];

  return (
    <div className={`egf-font-language-control ${className}`}>
      <SelectControl
        label={_x('Language', 'Language field label for the customizer font control.', 'easy-google-fonts')}
        value={subset}
        options={languageOptions}
        onChange={setSubset}
      />
    </div>
  );
};

export default FontLanguageControl;
