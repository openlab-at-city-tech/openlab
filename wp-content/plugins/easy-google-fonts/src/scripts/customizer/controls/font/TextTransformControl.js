import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

const TextTransformControl = props => {
  const { className, textTransform, setTextTransform } = props;

  return (
    <div className={`egf-text-transform-control ${className}`}>
      <SelectControl
        label={__('Text Transform', 'easy-google-fonts')}
        value={textTransform}
        options={[
          { value: '', label: __('Theme Default', 'easy-google-fonts') },
          { value: 'none', label: __('None', 'easy-google-fonts') },
          { value: 'uppercase', label: __('Uppercase', 'easy-google-fonts') },
          { value: 'lowercase', label: __('Lowercase', 'easy-google-fonts') },
          { value: 'capitalize', label: __('Capitalize', 'easy-google-fonts') }
        ]}
        onChange={setTextTransform}
      />
    </div>
  );
};

export default TextTransformControl;
