/**
 * JavaScript code for the "Advanced Access Rights" screen.
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
import Screen from './advanced-access-rights/screen';

initializeReactComponent(
	'tablepress-advanced-access-rights-screen',
	<Screen />
);
