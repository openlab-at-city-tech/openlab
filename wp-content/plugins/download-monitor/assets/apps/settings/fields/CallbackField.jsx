/**
 * Mounts a PHP-rendered fragment as-is — used for the "callback" field type,
 * where the fragment is just a container div for an existing standalone
 * React app (Expiring Links tokens table, Mailchimp connection, Page-Addon
 * files-listing settings). Those apps' own scripts render into the div;
 * this component doesn't reimplement any of them.
 */
export default function CallbackField( { field } ) {
	// eslint-disable-next-line react/no-danger
	return <div dangerouslySetInnerHTML={ { __html: field.html || '' } } />;
}
