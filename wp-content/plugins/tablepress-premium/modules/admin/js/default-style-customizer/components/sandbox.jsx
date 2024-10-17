/**
 * JavaScript code for the SandBox component.
 *
 * @package TablePress
 * @subpackage Default Style Customizer Screen
 * @author Tobias Bäthge
 * @since 2.2.0
 */

/**
 * WordPress dependencies.
 */
import {
	SandBox as WpSandBox,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */
import { useScreenSettings } from '../context/screen-settings';
import { sandboxCss, tableHtml } from '../data/sandbox-data';
import { generateCssCode } from '../functions/css';

/**
 * Returns the SandBox component's JSX markup.
 *
 * @return {Object} SandBox component.
 */
const SandBox = () => {
	const { currentStyle } = useScreenSettings();
	const currentStyleCss = generateCssCode( currentStyle );
	const styles = [ sandboxCss, currentStyleCss ];

	// Append the default CSS code to a hidden container, so that the SandBox content is different and triggers a re-render.
	const sandboxHtml = `
${ tableHtml }
<pre style="display:none">${ currentStyleCss }</pre>
`;

	return (
		<WpSandBox
			title="TablePress"
			html={ sandboxHtml }
			styles={ styles }
		/>
	);
};

export default SandBox;
