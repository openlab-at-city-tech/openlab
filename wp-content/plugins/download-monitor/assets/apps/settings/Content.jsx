import { Panel, PanelBody } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import useStateContext from './context/useStateContext';
import { useTabsQuery } from './query/useTabsQuery';
import SettingsForm from './SettingsForm';
import SaveButton from './SaveButton';
import Skeleton from './Skeleton';
import ToggleSectionsPanel from './ToggleSectionsPanel';
import styles from './Content.module.scss';

const SELF_MANAGED_TYPES = [
	'callback',
	'download_paths_table',
	'api_keys_table',
	'templates_table',
	'page_addon_settings',
	'mailchimp_connection_app',
	'expiring_links_tokens_app',
];

const showSaveButton = ( section ) =>
	! section.locked && ! ( section.fields || [] ).every( ( field ) => SELF_MANAGED_TYPES.includes( field.type ) );

const isChecked = ( value ) => '1' === value || true === value;

const getFieldDefault = ( sections, fieldName ) => {
	for ( const section of sections ) {
		const field = ( section.fields || [] ).find( ( item ) => item.name === fieldName );

		if ( field ) {
			return field.default;
		}
	}

	return undefined;
};

function filterShopSections( sections, options, gateways ) {
	const shopEnabledValue = Object.prototype.hasOwnProperty.call( options, 'dlm_shop_enabled' )
		? options.dlm_shop_enabled
		: getFieldDefault( sections, 'dlm_shop_enabled' );
	const shopEnabled = isChecked( shopEnabledValue );

	const isGatewaySection = ( slug ) => ( gateways || [] ).some( ( gateway ) => gateway.id === slug );

	const isGatewayEnabled = ( slug ) => {
		const optionName = `dlm_gateway_${ slug }_enabled`;

		if ( Object.prototype.hasOwnProperty.call( options, optionName ) ) {
			return isChecked( options[ optionName ] );
		}

		return !! ( gateways || [] ).find( ( gateway ) => gateway.id === slug )?.enabled;
	};

	return sections.filter( ( section ) => {
		if ( ! shopEnabled ) {
			return 'general' === section.slug;
		}

		if ( isGatewaySection( section.slug ) ) {
			return isGatewayEnabled( section.slug );
		}

		return true;
	} );
}

function SectionPanel( { section } ) {
	return (
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
	);
}

export default function Content() {
	const { state } = useStateContext();
	const { data, isLoading } = useTabsQuery();
	const [ gateways, setGateways ] = useState( [] );

	useEffect( () => {
		if ( 'shop' === state.activeTab ) {
			apiFetch( { path: '/download-monitor/v1/settings-gateways' } ).then( setGateways );
		}
	}, [ state.activeTab ] );

	if ( isLoading ) {
		return <Skeleton />;
	}

	if ( ! data ) {
		return null;
	}

	const activeTab = data.find( ( tab ) => tab.slug === state.activeTab );

	if ( ! activeTab ) {
		return null;
	}

	if ( [ 'lead_generation', 'page_addon' ].includes( activeTab.slug ) ) {
		return <ToggleSectionsPanel sections={ activeTab.sections } />;
	}

	const sections = 'shop' === activeTab.slug
		? filterShopSections( activeTab.sections, state.options, gateways )
		: activeTab.sections;

	return (
		<div className={ styles.content }>
			{ sections.map( ( section ) => (
				<SectionPanel key={ section.slug } section={ section } />
			) ) }
		</div>
	);
}
