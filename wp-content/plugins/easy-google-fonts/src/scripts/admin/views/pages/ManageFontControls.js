// External dependencies.
import { Link, withRouter } from 'react-router-dom';
import { useToasts } from 'react-toast-notifications';

// WordPress dependencies.
import { __, _x } from '@wordpress/i18n';
import { Button, Card, CardBody, CardHeader, Spinner } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';

// Internal dependencies.
import { STORE_KEY } from '../../store';
import getScreenLink from '../../utils/getScreenLink';
import ManageFontControlsLoader from '../components/loaders/ManageFontControlsLoader';
import FontControlRow from '../components/FontControlRow';

const sortFontControlsByTitle = fontControls => {
  return Object.keys(fontControls).sort((a, b) => {
    const firstTitle = fontControls[a].title.rendered.toUpperCase();
    const secondTitle = fontControls[b].title.rendered.toUpperCase();

    if (firstTitle < secondTitle) {
      return -1;
    }

    if (firstTitle > secondTitle) {
      return 1;
    }

    return 0;
  });
};

/**
 * Manage Font Controls Component
 */
const ManageFontControls = () => {
  const [isDeleting, setIsDeleting] = useState(false);
  const fontControls = useSelect(select => select(STORE_KEY).getFontControls());
  const dataLoaded = useSelect(select => select(STORE_KEY).hasFinishedResolution('getFontControls'));
  const { addToast } = useToasts();
  const { deleteAllFontControls } = useDispatch(STORE_KEY);

  const fontControlList = sortFontControlsByTitle(fontControls).map((id, index, arr) => {
    const isLastItem = index === arr.length - 1;

    const {
      control_selectors,
      force_styles,
      min_screen_amount,
      min_screen_unit,
      max_screen_amount,
      max_screen_unit
    } = fontControls[id].meta;

    return (
      <FontControlRow
        key={id}
        selectors={control_selectors}
        fontControlId={id}
        fontControlTitle={fontControls[id].title.rendered}
        forceStyles={force_styles}
        minQuery={min_screen_amount ? `${min_screen_amount}${min_screen_unit}` : ''}
        maxQuery={max_screen_amount ? `${max_screen_amount}${max_screen_unit}` : ''}
        appendDivider={!isLastItem}
      />
    );
  });

  return dataLoaded ? (
    <div>
      {/* Admin UI Headers */}
      <Card className="mb-3">
        <CardBody className="d-block py-0 px-3">
          <div className="row">
            <div className="col">
              <p>
                <span className="d-inline-block mr-2">
                  {__('Manage your customizer font controls here or', 'easy-google-fonts')}
                </span>
                <Button isPrimary onClick={() => props.history.push(getScreenLink('create'))}>
                  {_x(
                    'Create a new Font Control',
                    'Create font control button text on manage font controls screen.',
                    'easy-google-fonts'
                  )}
                </Button>
              </p>
            </div>
          </div>
        </CardBody>
      </Card>

      {/* List of current font controls. */}
      <Card className="egf-manage-font controls">
        <CardHeader className="d-block px-3">
          <div className="row">
            <div className="col-4">{__('Font Control', 'easy-google-fonts')}</div>
            <div className="col">{__('CSS Selectors', 'easy-google-fonts')}</div>
            <div className="col">{__('Media Query Settings', 'easy-google-fonts')}</div>
            <div className="col">{__('Force Styles (Optional)', 'easy-google-fonts')}</div>
          </div>
        </CardHeader>
        <CardBody className="px-3">
          {fontControlList.length === 0 && (
            <p>
              {__('No font controls exist.', 'easy-google-fonts')}{' '}
              <Link to={`${getScreenLink('create')}`}>{__('Create your first font control', 'easy-google-fonts')}</Link>
            </p>
          )}
          {fontControlList}
        </CardBody>
      </Card>

      {Object.keys(fontControls).length > 0 && (
        <div className="egf-manage-font-controls d-flex align-items-center mt-3">
          <Button
            isDestructive
            className="egf-manage-font-controls-delete"
            onClick={async () => {
              const userConfirmedDeletion = confirm(
                _x(
                  `Warning! You are about to permanently delete all font controls. 'Cancel' to stop, 'OK' to delete.`,
                  'User confirmation message to delete all font controls.',
                  'easy-google-fonts'
                )
              );

              if (userConfirmedDeletion) {
                setIsDeleting(true);

                try {
                  await deleteAllFontControls();
                  setIsDeleting(false);
                  addToast(__('All font controls have been deleted.', 'easy-google-fonts'), {
                    appearance: 'info',
                    autoDismiss: true,
                    placement: 'bottom-right'
                  });
                } catch (error) {
                  setIsDeleting(false);
                }
              }
            }}
            disabled={isDeleting}
          >
            {_x('Delete All Font Controls', 'Button text to delete all font controls.', 'easy-google-fonts')}
          </Button>
          {isDeleting && <Spinner />}
        </div>
      )}
    </div>
  ) : (
    <ManageFontControlsLoader />
  );
};

export default withRouter(ManageFontControls);
