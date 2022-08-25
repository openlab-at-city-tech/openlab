import { __ } from '@wordpress/i18n';

/**
 * Help Tooltip strings.
 */
const help = {};

help.title = __( 'Title of the work you’re attributing. You can use something general like “this work” or “this image” if you can’t find a specific title.\n\nURL is the link to the original source. If you can’t find the URL, say where you got the work, e.g. “Brooklyn Bridge photograph from Wikimedia Commons.”', 'openlab-attributions' );

help.authorName = __( 'Name of the author(s) of the material you are attributing. If you can’t find the name, you can list the author’s screen or username. If available, enter the URL to the author’s website or user account page.\n\nIf an organization is listed but no individual author you can (1) use the organization name as the author, or (2) leave the author field blank and fill in the organization field below.', 'openlab-attributions' );

help.publisher = __( 'If available, add the name of the organization that sponsored or managed the work you are adopting; it’s always nice to acknowledge those entities for their contribution.', 'openlab-attributions' )

help.project = __( 'If available, add the name of the project associated with the work. For example, Open Learning Initiative is the name of a project managed by Carnegie Mellon University.\n\nIf available, add the project URL.', 'openlab-attributions' );

help.datePublished = __( 'Include the date that the work you’re attributing was published. Leave this blank if you can’t find the date or are unsure.', 'openlab-attributions' );

help.license = __( 'Choose the exact Creative Commons license given to the work you’re attributing.\n\nIf the work is in the public domain, you can choose whether it’s in the general public domain (‘General’) or if it’s officially released under the Creative Commons public domain (‘CC0’).', 'openlab-attributions' );

help.derivative = __( 'If you are attributing a work derived from other works, please add a link to the original.', 'openlab-attributions' );

export default help;
