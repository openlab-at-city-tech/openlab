// External dependencies.
import { Link, withRouter } from 'react-router-dom';
import { useToasts } from 'react-toast-notifications';

// WordPress dependencies.
import { __, _x, sprintf } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { CardDivider, CheckboxControl, Spinner } from '@wordpress/components';

// Internal dependencies.
import { STORE_KEY } from '../../store';
import getScreenLink from '../../utils/getScreenLink';

const FontControlRow = props => {
  const { appendDivider, fontControlId, selectors, fontControlTitle, forceStyles, minQuery, maxQuery } = props;
  const [isSaving, setIsSaving] = useState(false);

  const { addToast } = useToasts();
  const { deleteFontControl, updateFontControlForceStyles } = useDispatch(STORE_KEY);

  const isMediaQueryControl = minQuery || maxQuery;

  return (
    <div className="egf-manage-font-controls__font-control">
      <div className="row align-items-center">
        {/* Font control name/actions */}
        <div className="col-4">
          <h4 className="egf-manage-font-controls__font-control-name mt-0 mb-1">
            <Link to={`${getScreenLink('edit', { fontControl: fontControlId })}`}>{fontControlTitle}</Link>
          </h4>

          <div className="egf-manage-font-controls__font-control-actions">
            <Link to={`${getScreenLink('edit', { fontControl: fontControlId })}`}>
              {__('Edit', 'easy-google-fonts')}
            </Link>{' '}
            |{' '}
            <a
              href="#"
              onClick={async event => {
                event.preventDefault();

                const userConfirmedDeletion = confirm(
                  _x(
                    `Warning! You are about to permanently delete this font control. 'Cancel' to stop, 'OK' to delete.`,
                    'User confirmation message to delete a font control.',
                    'easy-google-fonts'
                  )
                );

                if (userConfirmedDeletion) {
                  setIsSaving(true);
                  await deleteFontControl(fontControlId);
                  addToast(sprintf(__('%s has been deleted.', 'easy-google-fonts'), fontControlTitle), {
                    appearance: 'info',
                    autoDismiss: true,
                    placement: 'bottom-right'
                  });
                }
              }}
              disabled={isSaving}
            >
              {__('Delete', 'easy-google-fonts')}
            </a>
          </div>
        </div>

        {/* CSS Selectors */}
        <div className="col">
          {selectors.map((selector, i) => {
            return <code key={i}>{selector}</code>;
          })}
        </div>

        {/* Media Query */}
        <div className="col">
          {isMediaQueryControl ? (
            <div className="m-0">
              <span className="dashicons dashicons-desktop mr-2"></span>
              {minQuery && (
                <code className="d-inline-block mr-1 mb-2" style={{ borderRadius: 2 }}>
                  {sprintf(__('Min: %s', 'easy-google-fonts'), minQuery)}
                </code>
              )}
              {maxQuery && (
                <code className="d-inline-block mr-1 mb-2" style={{ borderRadius: 2 }}>
                  {sprintf(__('Max: %s', 'easy-google-fonts'), maxQuery)}
                </code>
              )}
            </div>
          ) : (
            <div className="m-0">
              <span className="dashicons dashicons-desktop mr-2"></span>
              <code className="d-inline-block mr-1 mb-2" style={{ borderRadius: 2 }}>
                {__('All Screens')}
              </code>
            </div>
          )}
        </div>

        {/* Font control force styles */}
        <div className="col">
          <div className="row">
            <div className="col">
              <CheckboxControl
                className="egf-settings__force-style egf-font-control-property"
                checked={forceStyles}
                label={__('Force Styles', 'easy-google-fonts')}
                onChange={async forceStyles => {
                  setIsSaving(true);
                  await updateFontControlForceStyles({ id: fontControlId, forceStyles });
                  setIsSaving(false);
                  addToast(sprintf(__('%s has been updated.', 'easy-google-fonts'), fontControlTitle), {
                    appearance: 'success',
                    autoDismiss: true,
                    placement: 'bottom-right'
                  });
                }}
              />
            </div>

            {isSaving && (
              <div className="col-auto pl-0">
                <Spinner />
              </div>
            )}
          </div>
        </div>
      </div>
      {appendDivider ? <CardDivider className="my-3" /> : null}
    </div>
  );
};

export default withRouter(FontControlRow);
