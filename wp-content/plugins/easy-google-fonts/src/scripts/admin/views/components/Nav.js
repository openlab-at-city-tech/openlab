// External dependencies.
import { NavLink, withRouter } from 'react-router-dom';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { getQueryArg } from '@wordpress/url';

// Internal dependencies.
import getScreenLink from '../../utils/getScreenLink';

const isActive = (_, location, screen) => {
  return getQueryArg(location.search, 'screen') === screen;
};

const Nav = () => {
  return (
    <>
      <h1 className="wp-heading-inline">{__('Font Controls', 'easy-google-fonts')}</h1>

      <a
        className="page-title-action hide-if-no-customize ml-2"
        href={`${easy_google_fonts.admin_url}customize.php?autofocus%5Bpanel%5D=egf_typography_panel&return=%2Fwp-admin%2Foptions-general.php?page=easy-google-fonts`}
      >
        {__('Manage with Live Preview', 'easy-google-fonts')}
      </a>

      <hr className="wp-header-end" />

      <div className="components-tab-panel__tabs my-3">
        <NavLink
          to={getScreenLink('create')}
          isActive={(_, location) => isActive(_, location, 'create')}
          className="components-button components-tab-panel__tabs-item"
          activeClassName="is-active"
        >
          {__('Create', 'easy-google-fonts')}
        </NavLink>

        <NavLink
          to={getScreenLink('edit')}
          isActive={(_, location) => isActive(_, location, 'edit')}
          className="components-button components-tab-panel__tabs-item"
          activeClassName="is-active"
        >
          {__('Edit', 'easy-google-fonts')}
        </NavLink>

        <NavLink
          to={getScreenLink('manage')}
          isActive={(_, location) => isActive(_, location, 'manage')}
          className="components-button components-tab-panel__tabs-item"
          activeClassName="is-active"
        >
          {__('Manage', 'easy-google-fonts')}
        </NavLink>

        <NavLink
          to={getScreenLink('settings')}
          isActive={(_, location) => isActive(_, location, 'settings')}
          className="components-button components-tab-panel__tabs-item"
          activeClassName="is-active"
        >
          {__('Settings', 'easy-google-fonts')}
        </NavLink>
      </div>
    </>
  );
};

export default withRouter(Nav);
