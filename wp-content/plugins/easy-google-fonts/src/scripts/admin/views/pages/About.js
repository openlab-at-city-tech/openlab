/**
 * External dependancies
 */
import { NavLink, Redirect, Route } from 'react-router-dom';

/**
 * WordPress dependancies
 */
import { __, _x } from '@wordpress/i18n';
import { Button, TabPanel } from '@wordpress/components';

/**
 * Internal dependancies
 */
import { CreditsContent, WhatsNewContent } from '../components/about';

const AboutTabPanel = () => {
  const tabs = [
    {
      name: 'whats-new',
      title: __(`What's New`, 'easy-google-fonts'),
      className: 'egf-tab',
      content: <WhatsNewContent />
    },
    {
      name: 'credits',
      title: __('Credits', 'easy-google-fonts'),
      className: 'egf-tab',
      content: <CreditsContent />
    },
    {
      name: 'support',
      title: __('Support', 'easy-google-fonts'),
      className: 'egf-tab',
      content: (
        <Route
          path="/"
          render={() => {
            window.location = 'https://wordpress.org/support/plugin/easy-google-fonts/';
            return <h1>{__('Redirecting to plugin support...', 'easy-google-fonts')}</h1>;
          }}
        />
      )
    },
    {
      name: 'get-started',
      title: __('Get Started', 'easy-google-fonts'),
      className: 'egf-tab',
      content: <Redirect to="/themes.php?page=easy-google-fonts" />
    }
  ];

  return (
    <TabPanel className="egf-about__header-tab-panel" activeClass="egf-tab-active" tabs={tabs}>
      {tab => {
        return <div className="egf-about__header-tab-panel-content">{tab.content}</div>;
      }}
    </TabPanel>
  );
};

const About = () => {
  return (
    <>
      <div className="egf-about__container container mt-5">
        <div className="egf-about__header row align-items-center justify-content-center">
          <div className="col-12">
            <div className="egf-about__header-bg p-5">
              <div className="egf-about__header-title pt-3">
                <p>{__('Easy Google Fonts', 'easy-google-fonts')}</p>
              </div>
              <div className="egf-about__header-text pt-3">
                <p>
                  {__('A google fonts theme integration plugin built by ', 'easy-google-fonts')}
                  <a href="https://titaniumthemes.com" target="_blank">
                    {_x('Titanium Themes', 'Plugin author', 'easy-google-fonts')}
                  </a>
                  <span>{`${_x('v2.0.3', 'Plugin version', 'easy-google-fonts')}`}</span>
                </p>
              </div>
            </div>
          </div>
        </div>

        <div className="row">
          <div className="col">
            {/* Tabs */}
            <AboutTabPanel />
            <hr />
          </div>
        </div>

        {/* Get Started */}
        <div className="return-to-dashboard">
          <NavLink to="/options-general.php?page=easy-google-fonts">
            <Button isPrimary>{__(`Go to Settings → Easy Google Fonts`, 'easy-google-fonts')}</Button>
          </NavLink>
        </div>
      </div>
    </>
  );
};

export default About;
