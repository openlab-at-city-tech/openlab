import { __ } from '@wordpress/i18n';
import TextField from './fields/TextField';
import PasswordField from './fields/PasswordField';
import ToggleField from './fields/ToggleField';
import SelectField from './fields/SelectField';
import TextareaField from './fields/TextareaField';
import RadioField from './fields/RadioField';
import EnhancedRadioField from './fields/EnhancedRadioField';
import LazySelectField from './fields/LazySelectField';
import WpEditorField from './fields/WpEditorField';
import HtaccessStatusField from './fields/HtaccessStatusField';
import BlacklistStatusField from './fields/BlacklistStatusField';
import DriveAuthButtonField from './fields/DriveAuthButtonField';
import ConsoleUriField from './fields/ConsoleUriField';
import CallbackField from './fields/CallbackField';
import DownloadPathsField from './fields/DownloadPathsField';
import ApiKeysField from './fields/ApiKeysField';
import TemplatesField from './fields/TemplatesField';
import PageAddonSettingsField from './fields/PageAddonSettingsField';
import MailchimpConnectionField from './fields/MailchimpConnectionField';
import ExpiringLinksTokensField from './fields/ExpiringLinksTokensField';

export default function FieldRenderer( { field, value, onChange, disabled = false } ) {
	switch ( field.type ) {
		case 'text':
			return <TextField field={ field } value={ value } onChange={ onChange } disabled={ disabled } />;

		case 'password':
			return <PasswordField field={ field } value={ value } onChange={ onChange } disabled={ disabled } />;

		case 'checkbox':
			return <ToggleField field={ field } value={ value } onChange={ onChange } disabled={ disabled } />;

		case 'select':
			return <SelectField field={ field } value={ value } onChange={ onChange } disabled={ disabled } />;

		case 'textarea':
			return <TextareaField field={ field } value={ value } onChange={ onChange } disabled={ disabled } />;

		case 'editor':
		case 'el_code_editor':
			return <WpEditorField field={ field } value={ value } onChange={ onChange } disabled={ disabled } />;

		case 'radio':
			return <RadioField field={ field } value={ value } onChange={ onChange } disabled={ disabled } />;

		case 'enhanced_radio':
			return <EnhancedRadioField field={ field } value={ value } onChange={ onChange } disabled={ disabled } />;

		case 'lazy_select':
			return <LazySelectField field={ field } value={ value } onChange={ onChange } disabled={ disabled } />;

		case 'htaccess_status':
			return <HtaccessStatusField field={ field } />;

		case 'blacklist_status':
			return <BlacklistStatusField field={ field } />;

		case 'drive_auth_button':
			return <DriveAuthButtonField field={ field } />;

		case 'console_uri_field':
			return <ConsoleUriField field={ field } />;

		case 'callback':
			return <CallbackField field={ field } />;

		case 'download_paths_table':
			return <DownloadPathsField />;

		case 'api_keys_table':
			return <ApiKeysField />;

		case 'templates_table':
			return <TemplatesField />;

		case 'page_addon_settings':
			return <PageAddonSettingsField field={ field } />;

		case 'mailchimp_connection_app':
			return <MailchimpConnectionField />;

		case 'expiring_links_tokens_app':
			return <ExpiringLinksTokensField />;

		default:
			return (
				<p>
					{ __( 'This field isn’t available in the new Settings page yet.', 'download-monitor' ) }
					{ ' ' }({ field.type })
				</p>
			);
	}
}
