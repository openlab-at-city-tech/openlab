import {
	Panel,
	PanelBody,
	ToggleGroupControl as StableToggleGroupControl,
	ToggleGroupControlOption as StableToggleGroupControlOption,
	__experimentalToggleGroupControl,
	__experimentalToggleGroupControlOption,
} from '@wordpress/components';
import { useCallback, useState } from '@wordpress/element';
import SettingsForm from './SettingsForm';
import SaveButton from './SaveButton';
import styles from './Content.module.scss';

const ToggleGroupControl = StableToggleGroupControl || __experimentalToggleGroupControl;
const ToggleGroupControlOption = StableToggleGroupControlOption || __experimentalToggleGroupControlOption;

const SELF_MANAGED_TYPES = [ 'callback', 'mailchimp_connection_app', 'expiring_links_tokens_app', 'page_addon_settings' ];

const showSaveButton = ( section ) =>
	! section.locked && ! ( section.fields || [] ).every( ( field ) => SELF_MANAGED_TYPES.includes( field.type ) );

const getInitialSection = ( sections ) => {
	const url = new URL( window.location );
	const fromUrl = url.searchParams.get( 'section' );

	if ( fromUrl && sections.some( ( section ) => section.slug === fromUrl ) ) {
		return fromUrl;
	}

	return sections[ 0 ]?.slug;
};

export default function ToggleSectionsPanel( { sections } ) {
	const [ active, setActive ] = useState( () => getInitialSection( sections ) );

	const handleSelect = useCallback( ( slug ) => {
		setActive( slug );

		const url = new URL( window.location );
		url.searchParams.set( 'section', slug );
		window.history.replaceState( {}, '', url );
	}, [] );

	const section = sections.find( ( item ) => item.slug === active ) || sections[ 0 ];

	return (
		<div className={ styles.content }>
			<Panel className={ styles.panel }>
				<PanelBody initialOpen>
					<div className={ styles.sectionsNav }>
						<ToggleGroupControl value={ section.slug } onChange={ handleSelect } isBlock={ false }>
							{ sections.map( ( item ) => (
								<ToggleGroupControlOption key={ item.slug } value={ item.slug } label={ item.label } />
							) ) }
						</ToggleGroupControl>
					</div>
				</PanelBody>
			</Panel>

			<Panel
				className={ styles.panel }
				header={
					<span className={ styles.panelTitle }>
						<span>{ section.label }</span>
						{ section.badge && <span className={ styles.badge }>{ section.badge }</span> }
					</span>
				}
			>
				<PanelBody initialOpen>
					<SettingsForm section={ section } />
					{ showSaveButton( section ) && <SaveButton /> }
				</PanelBody>
			</Panel>
		</div>
	);
}
