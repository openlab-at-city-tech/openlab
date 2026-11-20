/**
 * WordPress dependencies
 */
import { __, _x, sprintf } from '@wordpress/i18n';
import {
	useEffect,
	useRef,
	useState,
	createInterpolateElement,
} from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import {
	ToggleControl,
	Spinner,
	Modal,
	Button,
	Flex,
	Notice,
	Tooltip,
	ExternalLink,
} from '@wordpress/components';
import { useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import RepairChecklist from './fse-repair';
import navigate from './navigate';

/**
 * The query argument that carries a saved mode through the page reload.
 */
const SAVED_ARG = 'kadence-fse';

/**
 * Where alpha testers share feedback.
 */
const FEEDBACK_URL = 'https://www.facebook.com/groups/2651847825035423/';

/**
 * The Full Site Editing card on the Kadence dashboard, with the mode toggle and its confirm dialog.
 *
 * @since 1.6.0
 *
 * @return {Element} The card.
 */
export default function FseToggle() {
	const { path, repairPath, siteEditorUrl, updateCoreUrl, supported, minimumWp, disclosures = [] } = window.kadenceFseParams;
	const [ enabled, setEnabled ] = useState( window.kadenceFseParams.enabled );
	const [ saving, setSaving ] = useState( false );
	const [ pending, setPending ] = useState( null );
	const cancelRef = useRef();
	const { createSuccessNotice, createErrorNotice } = useDispatch( 'core/notices' );

	// Says what was saved before the reload, then drops the argument so a later reload doesn't repeat it.
	useEffect( () => {
		const url = new URL( window.location.href );
		const saved = url.searchParams.get( SAVED_ARG );

		if ( ! saved ) {
			return;
		}

		createSuccessNotice(
			'on' === saved
				? __(
						'Full Site Editing is on. See below for what to rebuild in the Site Editor.',
						'kadence'
				  )
				: __( 'Full Site Editing is off.', 'kadence' ),
			{ type: 'snackbar' }
		);

		url.searchParams.delete( SAVED_ARG );
		window.history.replaceState( window.history.state, '', url.toString() );
	}, [ createSuccessNotice ] );

	// The safe choice takes focus, not the info box's link, which comes first in the dialog. Without
	// preventScroll a dialog taller than the screen jumps to Cancel and hides its top; the buttons stay
	// pinned to the bottom, so Cancel is in view either way.
	useEffect( () => {
		if ( null !== pending ) {
			cancelRef.current?.focus( { preventScroll: true } );
		}
	}, [ pending ] );

	const save = ( next ) => {
		setSaving( true );
		apiFetch( { path, method: 'POST', data: { enabled: next } } )
			.then( ( response ) => {
				// The card remounts when the dashboard tab is switched back, so it reads the saved mode again.
				window.kadenceFseParams.enabled = response.enabled;
				setEnabled( response.enabled );

				// The admin menu and toolbar come from the server, so reload to show the mode's Editor or Design item.
				const url = new URL( window.location.href );
				url.searchParams.set(
					SAVED_ARG,
					response.enabled ? 'on' : 'off'
				);
				navigate( url.toString() );
			} )
			.catch( ( error ) => {
				createErrorNotice(
					error.message || __( 'The setting could not be saved. Please try again.', 'kadence' ),
					{ type: 'snackbar' }
				);
			} )
			.finally( () => setSaving( false ) );
	};

	const confirm = () => {
		save( pending );
		setPending( null );
	};

	const dialog = pending
		? {
			title: __( 'Turn on Full Site Editing?', 'kadence' ),
			action: __( 'Turn on Full Site Editing', 'kadence' ),
		}
		: {
			title: __( 'Turn off Full Site Editing?', 'kadence' ),
			action: __( 'Turn off Full Site Editing', 'kadence' ),
		};

	const toggle = (
		<ToggleControl
			label={ __( 'Use Full Site Editing', 'kadence' ) }
			checked={ enabled }
			onChange={ setPending }
			disabled={ saving || ! supported }
		/>
	);

	return (
		<div className="link-item kadence-fse-item">
			<div className="kadence-fse-head">
				<h4>{ __( 'Full Site Editing (Alpha)', 'kadence' ) }</h4>
				{ /* The heading already says it, so screen readers skip the badge. */ }
				<span className="kadence-fse-badge" aria-hidden="true">
					{ _x( 'Alpha', 'release stage badge', 'kadence' ) }
				</span>
			</div>
			<p>{ __( 'Keep the classic Kadence you know and love, or try the new Full Site Editing experience and build all your templates with blocks, using everything modern WordPress offers. Your Customizer settings stay saved, so you can switch back at any time.', 'kadence' ) }</p>
			{ ! supported && (
				// Silent, so screen readers don't hear it on every dashboard load.
				<Notice
					status="warning"
					isDismissible={ false }
					spokenMessage=""
					className="kadence-fse-requirement"
				>
					{ createInterpolateElement(
						sprintf(
							/* translators: %s: WordPress version number. */
							__( 'Full Site Editing needs WordPress %s or later. <a>Update WordPress to try it.</a>', 'kadence' ),
							minimumWp
						),
						{ a: updateCoreUrl ? <a href={ updateCoreUrl } /> : <span /> }
					) }
				</Notice>
			) }
			<div className="link-item-foot">
				{ enabled ? (
					<a href={ siteEditorUrl }>{ __( 'Open the Site Editor', 'kadence' ) }</a>
				) : (
					<span />
				) }
				{ saving && <Spinner /> }
				{ supported ? (
					toggle
				) : (
					// A disabled input gets no hover, so the wrapper carries the tooltip.
					<Tooltip
						text={ sprintf(
							/* translators: %s: WordPress version number. */
							__( 'Full Site Editing requires WordPress %s or later. Update WordPress to enable this option.', 'kadence' ),
							minimumWp
						) }
					>
						<div className="kadence-fse-toggle">{ toggle }</div>
					</Tooltip>
				) }
			</div>
			{ enabled && <RepairChecklist path={ repairPath } /> }
			{ null !== pending && (
				<Modal
					title={ dialog.title }
					className="kadence-fse-dialog"
					onRequestClose={ () => setPending( null ) }
					focusOnMount={ false }
				>
					{ pending ? (
						<>
							<Notice
								status="info"
								isDismissible={ false }
								className="kadence-fse-alpha-notice"
							>
								<p>
									<strong>{ __( 'Full Site Editing is in alpha', 'kadence' ) }</strong>
								</p>
								<p>
									{ createInterpolateElement(
										/* translators: <a> links to the Kadence community Facebook group. */
										__( 'Try it on a staging or test site first, not a live site. Share feedback through support or the <a>Kadence community</a>.', 'kadence' ),
										{ a: <ExternalLink href={ FEEDBACK_URL } /> }
									) }
								</p>
							</Notice>
							<p>{ __( 'Your header, footer and archive layouts switch to block templates that you rebuild in the Site Editor. Your Customizer settings stay saved, so you can switch back at any time.', 'kadence' ) }</p>
							{ disclosures.length > 0 && (
								<>
									<h2>{ __( 'What Full Site Editing mode doesn’t carry over', 'kadence' ) }</h2>
									<ul>
										{ disclosures.map( ( text ) => <li key={ text }>{ text }</li> ) }
									</ul>
									<p>{ __( 'After you turn it on, a checklist walks you through each of these.', 'kadence' ) }</p>
								</>
							) }
						</>
					) : (
						<>
							<p>{ __( 'Your site will go back to the classic header, footer and archive layouts from the Customizer.', 'kadence' ) }</p>
							<p>{ __( 'Nothing you built in the Site Editor is deleted, so you can turn Full Site Editing back on at any time.', 'kadence' ) }</p>
						</>
					) }
					<Flex justify="flex-end" className="kadence-fse-dialog-actions">
						<Button
							ref={ cancelRef }
							variant="secondary"
							onClick={ () => setPending( null ) }
						>
							{ __( 'Cancel', 'kadence' ) }
						</Button>
						<Button variant="primary" onClick={ confirm }>
							{ dialog.action }
						</Button>
					</Flex>
				</Modal>
			) }
		</div>
	);
}
