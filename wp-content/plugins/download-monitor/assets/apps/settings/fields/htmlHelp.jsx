export default function htmlHelp( html ) {
	return html ? <span dangerouslySetInnerHTML={ { __html: html } } /> : null;
}
