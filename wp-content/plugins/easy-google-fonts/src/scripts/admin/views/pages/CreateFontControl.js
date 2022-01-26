// External dependencies.
import { withRouter } from 'react-router-dom';
import { useBeforeunload } from 'react-beforeunload';
import { useToasts } from 'react-toast-notifications';

// WordPress dependencies.
import { __, _x, sprintf } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import {
  Button,
  Card,
  CardBody,
  CardDivider,
  CardHeader,
  CardFooter,
  FormTokenField,
  TextControl,
  TextareaControl,
  CheckboxControl,
  Notice,
  Panel,
  PanelBody,
  PanelRow,
  RangeControl,
  __experimentalUnitControl as UnitControl
} from '@wordpress/components';

// Internal dependencies.
import { STORE_KEY } from '../../store';
import { parseUnit, MIN_SCREEN_UNITS, MAX_SCREEN_UNITS } from '../../utils/units';
import getScreenLink from '../../utils/getScreenLink';
import CreateFontControlLoader from '../components/loaders/CreateFontControlLoader';

const CreateFontControl = props => {
  const { addToast } = useToasts();
  const [isSaving, setIsSaving] = useState(false);
  const [fontControlName, setFontControlName] = useState('');
  const [fontControlNameError, setFontControlNameError] = useState(false);
  const [description, setDescription] = useState('');
  const [forceStyles, setForceStyles] = useState(false);
  const [minAmount, setMinAmount] = useState(0);
  const [minUnit, setMinUnit] = useState('px');
  const [maxAmount, setMaxAmount] = useState(0);
  const [maxUnit, setMaxUnit] = useState('px');
  const [selectors, setSelectors] = useState([]);
  const hasSelectors = selectors.length > 0;

  const dataLoaded = useSelect(select => {
    select(STORE_KEY).getFontControls();
    return [select(STORE_KEY).hasFinishedResolution('getFontControls')].every(loaded => loaded);
  });

  useEffect(() => {
    if (fontControlName) {
      setFontControlNameError(false);
    }
  }, [fontControlName]);

  // Create font control.
  const { createFontControl } = useDispatch(STORE_KEY);
  const createFontControlAndRedirect = async () => {
    if (isSaving) {
      return;
    }

    if (!fontControlName) {
      setFontControlNameError(true);
      return;
    }

    try {
      setIsSaving(true);
      const newFontControl = await createFontControl({
        name: fontControlName,
        description,
        selectors,
        forceStyles,
        minAmount,
        minUnit,
        maxAmount,
        maxUnit
      });
      resetFontControl();
      props.history.push(`${getScreenLink('edit', { fontControl: newFontControl.payload.fontControl.id })}`);
      addToast(
        sprintf(__('%s has been created.', 'easy-google-fonts'), newFontControl.payload.fontControl.title.rendered),
        {
          appearance: 'success',
          autoDismiss: true,
          placement: 'bottom-right'
        }
      );
    } catch (error) {
      addToast(sprintf(__('Unable to create %s. Please try again.', 'easy-google-fonts'), fontControlName), {
        appearance: 'error',
        autoDismiss: true,
        placement: 'bottom-right'
      });
      setIsSaving(false);
    }
  };

  const resetFontControl = () => {
    setFontControlName('');
    setSelectors([]);
    setForceStyles(false);
    setDescription('');
    setMinAmount(0);
    setMinUnit('px');
    setMaxAmount(0);
    setMaxUnit('px');
  };

  const changesMade = () => {
    const changesMade = [
      fontControlName !== '',
      description !== '',
      forceStyles !== false,
      minAmount !== '',
      maxAmount !== '',
      selectors.length !== 0
    ];
    return changesMade.some(hasChanged => hasChanged);
  };

  useBeforeunload(() => {
    if (changesMade()) {
      return __(
        'You have made changes to this font control that are not saved. Are you sure you want to leave this page?',
        'easy-google-fonts'
      );
    }
  });

  let minUnitSelected = minUnit || 'px';

  if (minUnit === '%') {
    minUnitSelected = 'percent';
  }

  let maxUnitSelected = maxUnit || 'px';

  if (maxUnit === '%') {
    maxUnitSelected = 'percent';
  }

  return dataLoaded ? (
    <div>
      <div className="container-fluid p-0">
        <div className="row">
          {/* Notice */}
          <div className="col-12 mb-3">
            <Notice className="m-0" status="info" isDismissible={false}>
              {__(
                'Create a new customizer font control for your theme below and click the create font control button to save your changes.',
                'easy-google-fonts'
              )}
            </Notice>

            {fontControlNameError && (
              <Notice className="m-0 mt-2" status="error" isDismissible={false}>
                {__('Please enter a valid name for your font control.', 'easy-google-fonts')}
              </Notice>
            )}
          </div>

          {/* Font Control Settings. */}
          <div className="col">
            <Card className="egf-settings">
              {/* Font control name and save action. */}
              <CardHeader className="d-block">
                <div className="row justify-content-between align-items-center">
                  <div className="col-6">
                    <TextControl
                      className="egf-settings__font-control-name"
                      label={__('Font Control Name', 'easy-google-fonts')}
                      value={fontControlName}
                      onChange={value => setFontControlName(value)}
                    />
                  </div>
                  <div className="col-auto px-0">
                    <Button isBusy={isSaving} isPrimary onClick={createFontControlAndRedirect}>
                      {__('Create Font Control', 'easy-google-fonts')}
                    </Button>
                  </div>
                </div>
              </CardHeader>

              {/* Font Control Settings */}
              <CardBody>
                <h3>{__('Font Control CSS Selectors', 'easy-google-fonts')}</h3>
                <FormTokenField
                  label={__('Add your CSS selectors below', 'easy-google-fonts')}
                  // label="this is the label"
                  value={selectors}
                  placeholder="Enter CSS selector"
                  tokenizeOnSpace={false}
                  onChange={selectors => setSelectors(selectors)}
                />

                {hasSelectors && (
                  <Button isDestructive onClick={() => setSelectors([])}>
                    {__('Remove all selectors', 'easy-google-fonts')}
                  </Button>
                )}

                <CardDivider className="my-4" />

                <div className="row">
                  <div className="col-md-6">
                    <h3>{__('Font Control Properties', 'easy-google-fonts')}</h3>

                    <CheckboxControl
                      className="egf-settings__force-style egf-font-control-property"
                      checked={forceStyles}
                      label={__('Force Styles (Optional)', 'easy-google-fonts')}
                      help={__(
                        'This will enable the important rule for any of the CSS styles generated for the selectors defined above. It is used to add more importance to a property/value than normal.',
                        'easy-google-fonts'
                      )}
                      onChange={forceStyles => setForceStyles(forceStyles)}
                    />

                    <TextareaControl
                      label={__('Customizer Description', 'easy-google-fonts')}
                      className="egf-settings__description egf-font-control-property"
                      help={__(
                        'Description of the font control, displayed in the customizer interface.',
                        'easy-google-fonts'
                      )}
                      value={description}
                      onChange={description => setDescription(description)}
                    />
                  </div>

                  <div className="col-md-6">
                    <h3>{__('Media Query Settings', 'easy-google-fonts')}</h3>
                    <p>
                      {__(
                        'By default, any styles created by this font control will apply to all screen sizes for your theme. If you only want to apply styles to certain screen sizes you can adjust the settings below.',
                        'easy-google-fonts'
                      )}
                    </p>

                    <Panel className="my-4">
                      <PanelBody title={__('Min Screen', 'easy-google-fonts')} initialOpen={false}>
                        <p className="description">
                          {__('The minimum width of the browser window.', 'easy-google-fonts')}
                        </p>
                        <RangeControl
                          value={minAmount}
                          onChange={setMinAmount}
                          initialPosition={minAmount}
                          min={MIN_SCREEN_UNITS[minUnitSelected].min}
                          max={MIN_SCREEN_UNITS[minUnitSelected].max}
                          step={MIN_SCREEN_UNITS[minUnitSelected].step}
                          renderTooltipContent={() => `${minAmount}${minUnit}`}
                          withInputField={false}
                        />
                        <PanelRow className="mt-2">
                          <UnitControl
                            min={MIN_SCREEN_UNITS[minUnitSelected].min}
                            max={MIN_SCREEN_UNITS[minUnitSelected].max}
                            step={MIN_SCREEN_UNITS[minUnitSelected].step}
                            onChange={value => {
                              const [amount, unit] = parseUnit(value, Object.values(MIN_SCREEN_UNITS));
                              if (amount > 0) {
                                setMinAmount(amount);
                                setMinUnit(unit);
                              }
                            }}
                            onUnitChange={(unit = 'px') => {
                              if (unit === '%') {
                                unit = 'percent';
                              }

                              setMinAmount(MIN_SCREEN_UNITS[minUnitSelected].initial);
                              setMinUnit(unit);
                            }}
                            value={`${minAmount}${minUnit}`}
                            units={Object.values(MIN_SCREEN_UNITS)}
                          />
                          <Button
                            isSecondary
                            onClick={() => {
                              setMinAmount('');
                              setMinUnit('px');
                            }}
                          >
                            {__('Reset', 'easy-google-fonts')}
                          </Button>
                        </PanelRow>
                      </PanelBody>
                      <PanelBody title={__('Max Screen', 'easy-google-fonts')} initialOpen={false}>
                        <p className="description">
                          {__('The maximum width of the browser window.', 'easy-google-fonts')}
                        </p>
                        <RangeControl
                          value={maxAmount}
                          onChange={setMaxAmount}
                          initialPosition={maxAmount}
                          min={MAX_SCREEN_UNITS[maxUnitSelected].min}
                          max={MAX_SCREEN_UNITS[maxUnitSelected].max}
                          step={MAX_SCREEN_UNITS[maxUnitSelected].step}
                          renderTooltipContent={() => `${maxAmount}${maxUnit}`}
                          withInputField={false}
                        />
                        <PanelRow className="mt-2">
                          <UnitControl
                            min={MAX_SCREEN_UNITS[maxUnitSelected].min}
                            max={MAX_SCREEN_UNITS[maxUnitSelected].max}
                            step={MAX_SCREEN_UNITS[maxUnitSelected].step}
                            onChange={value => {
                              const [amount, unit] = parseUnit(value, Object.values(MAX_SCREEN_UNITS));
                              if (amount > 0) {
                                setMaxAmount(amount);
                                setMaxUnit(unit);
                              }
                            }}
                            onUnitChange={(unit = 'px') => {
                              if (unit === '%') {
                                unit = 'percent';
                              }

                              setMaxAmount(MAX_SCREEN_UNITS[maxUnitSelected].initial);
                              setMaxUnit(unit);
                            }}
                            value={`${maxAmount}${maxUnit}`}
                            units={Object.values(MAX_SCREEN_UNITS)}
                          />
                          <Button
                            isSecondary
                            onClick={() => {
                              setMaxAmount('');
                              setMaxUnit('px');
                            }}
                          >
                            {__('Reset', 'easy-google-fonts')}
                          </Button>
                        </PanelRow>
                      </PanelBody>
                    </Panel>
                  </div>
                </div>
              </CardBody>

              {/* Footer Actions */}
              <CardFooter className="d-block">
                <div className="row justify-content-between">
                  <div className="col-auto">
                    <Button
                      isDestructive
                      onClick={() => {
                        const confirmDelete = confirm(
                          _x(
                            `Warning! You are about to permanently delete this font control. 'Cancel' to stop, 'OK' to delete.`,
                            'User confirmation message to delete a font control.',
                            'easy-google-fonts'
                          )
                        );

                        if (confirmDelete) {
                          resetFontControl();
                          addToast(__('Your font control has been deleted.', 'easy-google-fonts'), {
                            appearance: 'info',
                            autoDismiss: true,
                            placement: 'bottom-right'
                          });
                        }
                      }}
                    >
                      {__('Delete Font Control', 'easy-google-fonts')}
                    </Button>
                  </div>
                  <div className="col-auto px-0">
                    <Button isBusy={isSaving} isPrimary onClick={createFontControlAndRedirect}>
                      {__('Create Font Control', 'easy-google-fonts')}
                    </Button>
                  </div>
                </div>
              </CardFooter>
            </Card>
          </div>
        </div>
      </div>
    </div>
  ) : (
    <CreateFontControlLoader />
  );
};

export default withRouter(CreateFontControl);
