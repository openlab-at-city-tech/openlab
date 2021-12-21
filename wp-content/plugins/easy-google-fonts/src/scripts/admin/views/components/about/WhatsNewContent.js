/**
 * WordPress dependancies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

const WhatsNewContent = () => {
  return (
    <>
      {/* Theme Notice */}
      <div className="egf-about__section has-subtle-background-color is-feature pt-3 pb-5">
        <img
          style={{ maxWidth: 320 }}
          src={`${easy_google_fonts.image_url}/src/images/main-pic.png`}
          src="https://titaniumthemes.com/wp-content/plugins/titanium-coming-soon-template/img/main-pic.png"
        />

        <h1 style={{ fontSize: 30 }}>{__(`We're building something exciting.`, 'easy-google-fonts')}</h1>

        <p>
          {__(
            `A beautifully designed WordPress theme with a first class gutenberg editing experience.`,
            'easy-google-fonts'
          )}
        </p>

        <Button href="https://titaniumthemes.com" target="_blank" isPrimary className="mt-4">
          {__('Find out more', 'easy-google-fonts')}
        </Button>
      </div>

      <hr />

      {/* New Features */}
      <div className="about__section mb-0 pt-4 pb-0">
        <div className="row">
          <div className="col">
            <h2 className="px-4 mt-3" style={{ fontSize: 30 }}>
              {__(`What's new in this release.`, 'easy-google-fonts')}
            </h2>
          </div>
        </div>
      </div>

      <div className="about__section has-4-columns pt-3 pb-4">
        {/* Column */}
        <div className="column">
          <span className="material-icons mb-3" style={{ color: '#9C27B0', fontSize: 48 }}>
            source
          </span>
          <h2 className="is-smaller-heading">{__('Complete plugin rewrite', 'easy-google-fonts')}</h2>
          <p>{__('The codebase has been completely rewritten, tested and simplified.', 'easy-google-fonts')}</p>
        </div>

        {/* Column */}
        <div className="column">
          <span className="material-icons mb-3" style={{ color: '#3F51B5', fontSize: 48 }}>
            speed
          </span>
          <h2 className="is-smaller-heading">{__('Big Performance Increase', 'easy-google-fonts')}</h2>
          <p>{__('New fast and efficent css style and selector output in the frontend.', 'easy-google-fonts')}</p>
        </div>

        {/* Column */}
        <div className="column">
          <span className="material-icons mb-3" style={{ color: '#009688', fontSize: 48 }}>
            computer
          </span>
          <h2 className="is-smaller-heading">{__('New Admin Screen', 'easy-google-fonts')}</h2>
          <p>{__('React, javascript and WordPress REST API powered admin screen.', 'easy-google-fonts')}</p>
        </div>

        {/* Column */}
        <div className="column">
          <span className="material-icons mb-3" style={{ color: '#4CAF50', fontSize: 48 }}>
            mood
          </span>
          <h2 className="is-smaller-heading">{__('Strong Usability Testing', 'easy-google-fonts')}</h2>
          <p>
            {__('The new User Interface has been designed and tested with a UX first design.', 'easy-google-fonts')}
          </p>
        </div>
      </div>
    </>
  );
};

export default WhatsNewContent;
