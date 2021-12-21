import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

const TextDecorationControl = props => {
  const { className, textDecoration, setTextDecoration } = props;

  return (
    <div className={`egf-text-decoration-control ${className}`}>
      <SelectControl
        label={__('Text Decoration', 'easy-google-fonts')}
        value={textDecoration}
        options={[
          { value: '', label: __('Theme Default', 'easy-google-fonts') },
          { value: 'none', label: __('None', 'easy-google-fonts') },
          { value: 'underline', label: __('Underline', 'easy-google-fonts') },
          { value: 'line-through', label: __('Line-through', 'easy-google-fonts') },
          { value: 'overline', label: __('Overline', 'easy-google-fonts') }
        ]}
        onChange={setTextDecoration}
      />
    </div>
  );
};

export default TextDecorationControl;
