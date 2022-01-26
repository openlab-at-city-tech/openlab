// External dependencies.
import { Link, withRouter } from 'react-router-dom';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';
import { Button, Card, CardBody, SelectControl } from '@wordpress/components';

// Internal dependencies.
import { STORE_KEY } from '../../store';
import getScreenLink from '../../utils/getScreenLink';

const sortFontControlsByTitle = fontControls => {
  return Object.keys(fontControls)
    .sort((a, b) => {
      const firstTitle = fontControls[a].title.rendered.toUpperCase();
      const secondTitle = fontControls[b].title.rendered.toUpperCase();

      if (firstTitle < secondTitle) {
        return -1;
      }

      if (firstTitle > secondTitle) {
        return 1;
      }

      return 0;
    })
    .map(id => ({
      label: fontControls[id].title.rendered,
      value: id
    }));
};

const FontControlSelector = props => {
  const { selectedFontControlId } = props;
  const [switchToFontControl, setSwitchToFontControl] = useState(selectedFontControlId);
  const [allFontControls, setAllFontControls] = useState([]);

  const fontControls = useSelect(select => select(STORE_KEY).getFontControls());

  useEffect(() => setAllFontControls(sortFontControlsByTitle(fontControls)), [fontControls]);

  return (
    <Card className="egf-font-control-selector">
      <CardBody className="row px-3 align-items-center">
        <div className="col-12 col-sm-auto mb-2 mb-sm-0 pr-sm-2">
          <span>{__('Select a font control to edit:', 'easy-google-fonts')}</span>
        </div>

        <div className="col-12 col-sm-auto mb-2 mb-sm-0 px-sm-0">
          <div className="d-flex no-wrap align-items-center">
            <div className="col pl-0 pr-2">
              <SelectControl
                options={allFontControls}
                value={switchToFontControl}
                onChange={fontControlId => {
                  setSwitchToFontControl(fontControlId);
                }}
                style={{ minWidth: 160, width: '100%', maxWidth: '100%' }}
              />
            </div>

            <div className="col-auto px-0">
              <Button
                isSecondary
                onClick={() => {
                  props.history.push(getScreenLink('edit', { fontControl: switchToFontControl }));
                }}
              >
                {__('Select', 'easy-google-fonts')}
              </Button>
            </div>
          </div>
        </div>

        <div className="col-12 col-sm-auto pl-sm-2">
          <span>
            <Link to={`${getScreenLink('create')}`}>{__('or create a new font control', 'easy-google-fonts')}</Link>
          </span>
        </div>
      </CardBody>
    </Card>
  );
};

export default withRouter(FontControlSelector);
