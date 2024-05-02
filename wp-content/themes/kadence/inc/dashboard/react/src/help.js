/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
const { Fragment } = wp.element;
const { withFilters } = wp.components;

export const HelpTab = () => {
	return (
		<div className="kadence-desk-help-inner">
			<h2>{ __( 'Welcome to Kadence!', 'kadence' ) }</h2>
			<p>{ __( 'You are going to love working with this theme! View the video below to get started with our video tutorials or click the view knowledge base button below to see all the documentation.', 'kadence' ) }</p>
			<div className="video-container">
				<a href="https://www.youtube.com/watch?v=GqEecMF7WtE"><img width="1280" height="720" src={ kadenceDashboardParams.videoImage } alt={ __( 'Kadence Theme Getting Started Tutorial - 10 Minute Quick Start Guide', 'kadence' ) } /></a>
			</div>
			<a href="https://kadence-theme.com/learn-kadence" className="kadence-desk-button" target="_blank">{ __( 'Video Tutorials', 'kadence' ) }</a><a href="https://kadence-theme.com/knowledge-base/" className="kadence-desk-button kadence-desk-button-second" target="_blank">{ __( 'View Knowledge Base', 'kadence' ) }</a>
		</div>
	);
};

export default withFilters( 'kadence_theme_help' )( HelpTab );