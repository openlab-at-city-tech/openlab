// External dependencies.
import { Prompt, withRouter } from 'react-router-dom';
import { useToasts } from 'react-toast-notifications';
import { useBeforeunload } from 'react-beforeunload';

// WordPress dependencies.
import { __, _x } from '@wordpress/i18n';
import { Button, Card, CardBody, CardHeader, TextControl, Notice } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

// Internal dependencies.
import { STORE_KEY } from '../../store';
import getScreenLink from '../../utils/getScreenLink';
import PluginSettingsLoader from '../components/loaders/PluginSettingsLoader';

const PluginSettings = () => {
  const { addToast } = useToasts();
  const [changesMade, setChangesMade] = useState(false);
  const [isSaving, setIsSaving] = useState(false);
  const [apiKey, setApiKey] = useState('');
  const [isValid, setIsValid] = useState(false);

  const dataLoaded = useSelect(select => {
    select(STORE_KEY).getApiKey();
    return [select(STORE_KEY).hasFinishedResolution('getApiKey')].every(loaded => loaded);
  });

  const savedApiKey = useSelect(select => select(STORE_KEY).getApiKey());

  // Sync state.
  useEffect(() => {
    if (savedApiKey) {
      setApiKey(savedApiKey);
    }
  }, [dataLoaded]);

  useEffect(() => {
    if (!apiKey) {
      return;
    }

    const url = `https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key=${apiKey}`;
    fetch(url).then(res => {
      setIsValid(res.status === 200);

      if (changesMade && res.status === 200) {
        saveApiKey();
      }
    });
  }, [apiKey]);

  /**
   * Update API Key
   */
  const { updateApiKey } = useDispatch(STORE_KEY);
  const saveApiKey = async () => {
    if (isSaving) {
      return;
    }

    try {
      setIsSaving(true);
      await updateApiKey(apiKey);
      addToast(__('Your API key has been saved.', 'easy-google-fonts'), {
        appearance: 'success',
        autoDismiss: true,
        placement: 'bottom-right'
      });
      setChangesMade(false);
      setIsSaving(false);

      if (!apiKey) {
        setIsValid(false);
      }
    } catch (error) {
      addToast(__('Unable to save your API key. Please try again.', 'easy-google-fonts'), {
        appearance: 'error',
        autoDismiss: true,
        placement: 'bottom-right'
      });
      setIsSaving(false);
    }
  };

  /**
   * Prompt for unsaved changes.
   */
  useBeforeunload(() => {
    if (changesMade) {
      return __(
        'You have made changes to the settings that are not saved. Are you sure you want to leave this page?',
        'easy-google-fonts'
      );
    }
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
                  {__('Manage your plugin settings here or', 'easy-google-fonts')}
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

      {/* Notices */}
      <div className="row">
        <div className="col-12 mb-3">
          {!isValid && (
            <Notice className="m-0" status="info" isDismissible={false}>
              {__('Please enter a valid google API key.', 'easy-google-fonts')}
            </Notice>
          )}

          {isValid && (
            <Notice className="m-0" status="success" isDismissible={false}>
              {__(
                'Your API key is valid and your site will automatically fetch the latest fonts from google.',
                'easy-google-fonts'
              )}
            </Notice>
          )}
        </div>
      </div>

      {/* API Key */}
      <Card>
        <CardHeader className="d-block">
          <div className="row justify-content-between align-items-center">
            <div className="col-6">
              <TextControl
                className="egf-settings__api-key"
                label={__('Google API Key', 'easy-google-fonts')}
                help={__(
                  'Your site will fetch the latest fonts from google automatically once you enter a valid api key.',
                  'easy-google-fonts'
                )}
                value={apiKey}
                onChange={apiKey => {
                  setApiKey(apiKey.trim());
                  setChangesMade(true);
                }}
              />
            </div>
            <div className="col-auto px-0">
              <Button isBusy={isSaving} isPrimary onClick={saveApiKey}>
                {__('Save API Key', 'easy-google-fonts')}
              </Button>
            </div>
          </div>
        </CardHeader>
      </Card>

      <Prompt
        when={changesMade}
        message={__(
          'You have made changes to the settings that are not saved. Are you sure you want to leave this page?',
          'easy-google-fonts'
        )}
        beforeUnload={true}
      />
    </div>
  ) : (
    <PluginSettingsLoader />
  );
};

export default withRouter(PluginSettings);
