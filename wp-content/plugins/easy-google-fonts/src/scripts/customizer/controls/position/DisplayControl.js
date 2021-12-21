import { __ } from '@wordpress/i18n';
import { Panel, PanelBody, PanelRow, SelectControl } from '@wordpress/components';

const DisplayControl = props => {
  const { control, className = '', display, setDisplay } = props;

  return (
    <Panel className={`egf-display-control ${className}`}>
      <PanelBody title={__('Display', 'easy-google-fonts')} initialOpen={false}>
        <SelectControl
          label={__('Display', 'easy-google-fonts')}
          value={display}
          onChange={setDisplay}
          options={[
            { value: '', label: __('Theme Default', 'easy-google-fonts') },
            { value: 'block', label: __('Block', 'easy-google-fonts') },
            { value: 'inline-block', label: __('Inline Block', 'easy-google-fonts') }
          ]}
        />
      </PanelBody>
    </Panel>
  );
};

export default DisplayControl;
