import { __, sprintf } from '@wordpress/i18n';
import { useState, useMemo, useEffect } from '@wordpress/element';
import { Button } from '@wordpress/components';
import styles from './extension-license-header.module.scss';
import { useLicensingQuery } from '../query/use-licensing-query';
import { useLicensingMutation } from '../query/use-licensing-mutation';

export default function ExtensionLicenseHeader() {
	const [ showLicenseField, setShowLicenseField ] = useState( false );
	const [ inputValue, setInputValue ] = useState( '' );
	const [ error, setError ] = useState( null );

	const { data: license } = useLicensingQuery();

	const licenseKey = useMemo( () => {
		return license?.license_key || inputValue;
	}, [ license?.license_key, inputValue ] );

	let displayLicenseKey = inputValue || license?.license_key || '';

	const activateMutation = useLicensingMutation();
	const deactivateMutation = useLicensingMutation();

	const isLicenseActive = license?.status === 'active';

	const isProcessing = activateMutation.isPending || deactivateMutation.isPending;

	const getErrorMessage = ( response ) => {
		if ( ! response ) {
			return null;
		}

		if ( response.code ) {
			switch ( response.code ) {
				case 'license_not_found':
					return __( 'License not found. Please check your license key and try again.', 'download-monitor' );
				case 'no_license_key':
					return __( 'Please enter a license key.', 'download-monitor' );
				default:
					return response.message || __( 'Unable to activate license. Please try again.', 'download-monitor' );
			}
		}

		if ( response.status === 'error' ) {
			return response.message || __( 'Unable to activate license. Please try again.', 'download-monitor' );
		}

		if ( response.message && ! response.status ) {
			return response.message;
		}

		return null;
	};

	const isSuccessfulActivation = ( response ) => {
		if ( ! response ) {
			return false;
		}

		if ( response.code || response.status === 'error' ) {
			return false;
		}

		if ( response.status === 'active' ) {
			return true;
		}

		if ( response.license_key && ! response.code ) {
			return true;
		}

		return false;
	};

	useEffect( () => {
		if ( isLicenseActive && error ) {
			setError( null );
		}
	}, [ isLicenseActive, error ] );

	const handleActivate = async () => {
		if ( ! displayLicenseKey.trim() ) {
			setError( __( 'Please enter a license key.', 'download-monitor' ) );
			return;
		}

		setError( null );

		activateMutation.mutate(
			{ licenseKey: displayLicenseKey, action: 'activate' },
			{
				onSuccess: ( response ) => {
					const errorMessage = getErrorMessage( response );
					if ( errorMessage ) {
						setError( errorMessage );
					} else if ( isSuccessfulActivation( response ) ) {
						setError( null );
						setInputValue( '' );
					}
				},
				onError: ( err ) => {
					setError( err?.message || __( 'Unable to activate license. Please try again.', 'download-monitor' ) );
				},
			}
		);
	};

	const handleDeactivate = async () => {
		if ( ! licenseKey.trim() ) {
			return;
		}
		setError( null );
		deactivateMutation.mutate(
			{ licenseKey: displayLicenseKey, action: 'deactivate' },
			{
				onSuccess: () => {
					setInputValue( '' );
					setError( null );
				},
				onError: ( err ) => {
					setError( err?.message || __( 'Unable to deactivate license. Please try again.', 'download-monitor' ) );
				},
			}
		);
	};

	const handleInputChange = ( e ) => {
		const newValue = e.target.value;
		setInputValue( newValue );
		if ( error ) {
			setError( null );
		}
	};

	const licenseText = useMemo( () => {
		if ( license?.is_lifetime ) {
			return sprintf(
				/* translators: 1: Product name */
				__( 'Hello, your license (%1$s) is active for lifetime', 'download-monitor' ),
				license?.product_name || ''
			);
		}

		return sprintf(
			/* translators: 1: Product name, 2: Expiration date */
			__( 'Hello, your license (%1$s) is active until %2$s', 'download-monitor' ),
			license?.product_name || '',
			license?.expiration ? new Date( license.expiration * 1000 ).toLocaleDateString() : ''
		);
	}, [ license ] );

	const activationInfoText = useMemo( () => {
		if ( ! isLicenseActive || ! license ) {
			return null;
		}

		const activationsLeft = license?.activations_left;
		const activationLimit = license?.activation_limit;

		if ( ( activationsLeft === undefined || activationsLeft === null ) && ( activationLimit === undefined || activationLimit === null ) ) {
			return null;
		}

		if ( activationLimit === 0 || activationLimit === null ) {
			return null;
		}

		if ( activationsLeft !== undefined && activationsLeft !== null ) {
			if ( activationsLeft === 0 ) {
				return sprintf( __( 'No activations left out of %1$d', 'download-monitor' ), activationLimit );
			}

			return sprintf( __( '%1$d activations left out of %2$d', 'download-monitor' ), activationsLeft, activationLimit );
		}

		if ( license?.activation_count !== undefined ) {
			return sprintf( __( '%1$d of %2$d activations used', 'download-monitor' ), license.activation_count, activationLimit );
		}

		return null;
	}, [ license, isLicenseActive ] );

	const licenseKeyText = useMemo( () => {
		if ( isLicenseActive ) {
			return __( 'Change license key', 'download-monitor' );
		}

		if ( showLicenseField ) {
			return __( 'Hide License Key', 'download-monitor' );
		}

		return __( 'Enter License Key', 'download-monitor' );
	}, [ showLicenseField, isLicenseActive ] );

	return (
		<div className={ styles.licenseHeader }>
			<div className={ styles.licenseContent }>
				{ isLicenseActive ? (
					<div className={ styles.licenseActive }>
						<div className={ styles.licenseTextWrapper }>
							<p className={ styles.greeting }>{ licenseText }</p>
							{ activationInfoText && <span className={ styles.activationInfo }>{ activationInfoText }</span> }
						</div>
						<Button
							variant="link"
							onClick={ () => {
								setShowLicenseField( ! showLicenseField );
								if ( error ) {
									setError( null );
								}
							} }
							className={ styles.toggleButton }
						>
							{ licenseKeyText }
						</Button>
					</div>
				) : (
					<div className={ styles.licenseInactive }>
						<div className={ styles.licenseTextWrapper }>
							<p className={ styles.greeting }>
								{ __( 'Hello, please enter your license key to activate extensions', 'download-monitor' ) }
							</p>
						</div>
						<Button
							variant="link"
							onClick={ () => {
								setShowLicenseField( ! showLicenseField );
								if ( error ) {
									setError( null );
								}
							} }
							className={ styles.toggleButton }
						>
							{ licenseKeyText }
						</Button>
					</div>
				) }
			</div>

			{ showLicenseField && (
				<div className={ styles.licenseKeySection }>
					<div className={ styles.inputGroup }>
						<div className={ styles.inputWrapper }>
							<input
								type="text"
								value={ displayLicenseKey }
								onChange={ handleInputChange }
								placeholder={ __( 'Enter your license key', 'download-monitor' ) }
								className={ `${ styles.licenseInput } ${ error ? styles.error : '' }` }
								disabled={ isProcessing }
								aria-invalid={ error ? 'true' : 'false' }
								aria-describedby={ error ? 'license-error-message' : undefined }
							/>
							{ error && (
								<div id="license-error-message" className={ styles.errorMessage } role="alert">
									{ error }
								</div>
							) }
						</div>
						<div className={ styles.buttonGroup }>
							<Button
								variant="primary"
								onClick={ handleActivate }
								disabled={ ! displayLicenseKey.trim() || activateMutation.isPending || isLicenseActive }
								isBusy={ activateMutation.isPending }
								className={ styles.actionButton }
							>
								{ __( 'Activate', 'download-monitor' ) }
							</Button>
							<Button
								variant="secondary"
								onClick={ handleDeactivate }
								disabled={ ! licenseKey.trim() || deactivateMutation.isPending || ! isLicenseActive }
								isBusy={ deactivateMutation.isPending }
								className={ styles.actionButton }
							>
								{ __( 'Deactivate and clear license', 'download-monitor' ) }
							</Button>
						</div>
					</div>
				</div>
			) }
		</div>
	);
}
