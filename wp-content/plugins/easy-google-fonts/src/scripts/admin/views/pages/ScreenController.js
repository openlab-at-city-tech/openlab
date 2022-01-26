// External dependencies.
import { Redirect } from 'react-router-dom';

// Internal dependencies.
import getQueryFromUrl from '../../utils/getQueryFromUrl';
import getScreenLink from '../../utils/getScreenLink';
import Nav from '../components/Nav';
import About from './About';
import CreateFontControl from './CreateFontControl';
import EditFontControl from './EditFontControl';
import ManageFontControl from './ManageFontControls';
import PluginSettings from './PluginSettings';

function ScreenController() {
  switch (getQueryFromUrl('screen')) {
    case 'about':
      return (
        <div>
          <About />
        </div>
      );
      break;

    case 'create':
      return (
        <div>
          <Nav />
          <CreateFontControl />
        </div>
      );
      break;

    case 'edit':
      return (
        <div>
          <Nav />
          <EditFontControl />
        </div>
      );
      break;

    case 'manage':
      return (
        <div>
          <Nav />
          <ManageFontControl />
        </div>
      );
      break;

    case 'settings':
      return (
        <div>
          <Nav />
          <PluginSettings />
        </div>
      );
      break;

    default:
      let defaultRedirect = easy_google_fonts.num_font_controls > 0 ? 'edit' : 'create';
      return (
        <div>
          <Redirect to={`${getScreenLink(defaultRedirect)}`} />
        </div>
      );
      break;
  }
}

export default ScreenController;
