import $ from 'jquery';
import React from 'react';
import ReactDOM from 'react-dom';
import EGFFontControl from './controls/EGFFontControl';

const { settings } = egfCustomize;
const { customize } = wp;
const { control, Control, controlConstructor } = customize;

export const registerControls = () => {
  setupFontControl();
  customize.bind('ready', () => {
    registerFontControls();
  });
};

/**
 * Register Font Controls
 */
const registerFontControls = () => {
  const { config, setting_key } = settings;

  for (const id in config) {
    const { linked_control_id } = config[id].properties;
    const isNotLinkedControl = !linked_control_id;

    if (isNotLinkedControl) {
      switch (config[id].type) {
        case 'font':
          control.add(new customize.EGFFontControl(`${setting_key}[${id}]`, config[id]));
          registerLinkedFontControls(id);
          break;
        default:
          break;
      }
    }
  }
};

/**
 * Register Linked Font Controls
 * @param {*} parentControlId
 */
const registerLinkedFontControls = parentControlId => {
  const { config, setting_key } = settings;

  for (const id in config) {
    const { linked_control_id } = config[id].properties;
    const isLinkedControl = parentControlId === linked_control_id;

    if (isLinkedControl) {
      switch (config[id].type) {
        case 'font':
          control.add(new customize.EGFFontControl(`${setting_key}[${id}]`, config[id]));
          break;
        default:
          break;
      }
    }
  }
};

/**
 * Setup Font Control
 */
const setupFontControl = () => {
  customize.EGFFontControl = Control.extend({
    /**
     * Initialize.
     *
     * @param {string} id - Control ID.
     * @param {object} options - Setting config object
     */
    initialize(id, options) {
      const control = this;

      [
        'getSettingParamsFromConfig',
        'updateSetting',
        'updateSettingProps',
        'setNotificationContainer',
        'renderContent',
        'resetSetting'
      ].map(func => {
        control[func] = control[func].bind(control);
      });

      wp.customize.Control.prototype.initialize.call(control, id, control.getSettingParamsFromConfig(id, options));

      function onRemoved(controlToRemove) {
        if (control === controlToRemove) {
          control.destroy();
          control.container.remove();
          wp.customize.control.unbind('remove', onRemoved);
        }
      }

      wp.customize.control.bind('removed', onRemoved);
    },

    /**
     * Get Setting Props From Config
     *
     * @description Takes the json enqueued on the page
     *   for this control and returns the params obj
     *   used to construct a new control instance.
     *
     * @param {object} config Settings config object
     */
    getSettingParamsFromConfig(id, config) {
      const { section, title, description, type, properties, name } = config;

      return {
        name,
        type,
        label: title.replace('&amp;', '&'),
        description,
        section,
        capability: 'edit_theme_options',
        setting: customize(id),
        properties
      };
    },

    /**
     * Control Ready Event
     *
     * @description Fired after control has been first
     *   rendered, start re-rendering when setting
     *   changes. React is able to be used here instead
     *   of the wp.customize.Element abstraction.
     *
     * @return {void}
     */
    ready() {
      this.renderContent();
    },

    /**
     * Render Content
     *
     * @description Renders a React component in the
     *   controls DOM node using ReactDOM.render().
     *   The react component is now responsible for
     *   handling the ui state.
     *
     * @return {void}
     */
    renderContent() {
      const { container, resetSetting, updateSetting, updateSettingProps, setNotificationContainer } = this;
      const [domNode] = container;

      ReactDOM.render(
        <EGFFontControl
          control={this}
          resetSetting={resetSetting}
          updateSetting={updateSetting}
          updateSettingProps={updateSettingProps}
          setNotificationContainer={setNotificationContainer}
        />,
        domNode
      );
    },

    /**
     * Reset Setting
     *
     * @description Resets the control back to it's
     *   default setting.
     *
     * @return {void}
     */
    resetSetting({ renderAfterUpdate = false }) {
      const { setting, renderContent } = this;
      setting.set(setting.default);

      if (renderAfterUpdate) {
        renderContent();
      }
    },

    /**
     * Update Setting
     *
     * @description Update setting with new props. This
     *   will trigger the React component to update.
     *
     *
     * @param {object} props - New props to set in the setting (model).
     * @return {void}
     */
    updateSetting({ props, renderAfterUpdate = false }) {
      const { setting, renderContent } = this;
      setting.set(props);

      if (renderAfterUpdate) {
        renderContent();
      }
    },

    /**
     * Update Setting Prop
     *
     * @description Similar to update setting but used to
     *   update part of the setting. Useful for settings
     *   with multi-dimensional values.
     *
     *
     * @param {object} props - Object with a key value pair of the
     *                         setting prop that you want to update.
     * @return {void}
     */
    updateSettingProps({ props, renderAfterUpdate = false }) {
      const { setting, renderContent } = this;
      setting.set({ ...setting(), ...props });

      if (renderAfterUpdate) {
        renderContent();
      }
    },

    /**
     * Set notification container and render.
     *
     * @description This is called when the React component
     *   is mounted.
     *
     * @param {Element} element - Notification container.
     * @return {void}
     */
    setNotificationContainer(element) {
      this.notifications.container = $(element);
      this.notifications.render();
    },

    /**
     * Destroy Control
     *
     * @description Handle removal/de-registration of
     *   the control. This is essentially the inverse
     *   of the Control#embed() method.
     *
     * @link https://core.trac.wordpress.org/ticket/31334
     *
     * @return {void}
     */
    destroy() {
      const [domNode] = this.container;
      ReactDOM.unmountComponentAtNode(domNode);

      if (Control.prototype.destroy) {
        Control.prototype.destroy.call(this);
      }
    }
  });

  // Register control with WordPress.
  controlConstructor.egf_font_control = customize.EGFFontControl;
};
