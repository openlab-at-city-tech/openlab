/**
 * WordPress dependancies
 */
import { __ } from '@wordpress/i18n';

const CreditsContent = () => {
  return (
    <>
      <div className="about__section">
        <div className="column has-subtle-background-color">
          <h2 className="wp-people-group-title mb-5">
            Plugin developed by <a href="https://titaniumthemes.com">titanium themes</a>.
          </h2>
          <ul className="wp-people-group">
            <li className="wp-person" id="wp-person-sunny">
              <a href="https://profiles.wordpress.org/sunny_johal/" className="web">
                <img
                  src="https://secure.gravatar.com/avatar/c07075163051ae1230672f76222c5ed5?s=80&amp;d=mm&amp;r=g"
                  srcSet="https://secure.gravatar.com/avatar/c07075163051ae1230672f76222c5ed5?s=160&amp;d=mm&amp;r=g 2x"
                  className="gravatar"
                  alt="Sunny Johal"
                />
                Sunny Johal
              </a>
              <span className="title">{__('Release Lead', 'easy-google-fonts')}</span>
            </li>

            <li className="wp-person" id="wp-person-amit">
              <a href="https://profiles.wordpress.org/amit_kayasth/" className="web">
                <img
                  src="https://secure.gravatar.com/avatar/a605686ad5c0fc2dc3b22fafc71a457c?s=80&amp;d=mm&amp;r=g"
                  srcSet="https://secure.gravatar.com/avatar/a605686ad5c0fc2dc3b22fafc71a457c?s=160&amp;d=mm&amp;r=g 2x"
                  className="gravatar"
                  alt="Amit Kayasth"
                />
                Amit Kayasth
              </a>
              <span className="title">{__('Release Lead', 'easy-google-fonts')}</span>
            </li>
          </ul>
        </div>
      </div>
    </>
  );
};

export default CreditsContent;
