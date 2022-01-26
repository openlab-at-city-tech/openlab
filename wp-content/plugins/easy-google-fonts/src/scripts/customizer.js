import { fetchGoogleFonts } from './customizer/utils/fetchGoogleFonts';
import { registerPanels } from './customizer/panels';
import { registerSections } from './customizer/sections';
import { registerSettings } from './customizer/settings';
import { registerControls } from './customizer/controls';

fetchGoogleFonts();
registerPanels();
registerSections();
registerSettings();
registerControls();
