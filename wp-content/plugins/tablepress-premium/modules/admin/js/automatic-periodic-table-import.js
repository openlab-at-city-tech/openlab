/**
 * JavaScript code for the "Automatic Periodic Table Import" screen.
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 2.0.0
 */

/**
 * Internal dependencies.
 */
import { initializeReactComponent } from '../../../admin/js/common/react-loader';
import Screen from './automatic-periodic-table-import/screen';

initializeReactComponent(
	'tablepress-automatic-periodic-table-import-screen',
	<Screen />
);
