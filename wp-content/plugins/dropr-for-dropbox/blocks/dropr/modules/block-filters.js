const { __ } = wp.i18n;
const { Fragment } = wp.element;

/**
 * Adds 'Add From Dropbox' Link on Featured Image panel in Gutenberg
 */
const droprFeaturedImgLink = ( OriginalComponent ) => {
	return ( props ) => {
		const styleHidden = {
			display: 'none'
		};
		const linkStyle = {
			float: 'left',
			marginRight: '5px'
		};
		const pluginImgBaseUrl = dropr_options.plugin_url + 'images/';
		const linkContent = (
			<div className="wp-dropr-block-featured-img-wrapper">
				<p className="hide-if-no-js">
					<a href="#" id="droper-featured">
						<img src={ pluginImgBaseUrl + "dropr-icon-xs-b.png" } style={ linkStyle } />{ __( 'Add From Dropbox', 'dropr' ) }
					</a>
					<img src={ pluginImgBaseUrl + "loading-bubbles.svg" } style={ styleHidden } className="droprLoader" alt={ __( 'Loading icon', 'dropr' ) } />
				</p>
				<p id="dropr-holder"></p>
			</div>
		);
		return (
			<Fragment>
				<OriginalComponent { ...props } />
				{ linkContent }
			</Fragment>
		);
	};
}

export { droprFeaturedImgLink };