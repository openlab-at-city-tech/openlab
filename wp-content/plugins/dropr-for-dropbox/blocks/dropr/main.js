/**
 * BLOCK: dropr
 *
 * Registering block with Gutenberg.
 */

import DroprServerSideRender from './modules/dropr-server-side-render';
import DroprLinkInspector from './modules/Inspector/LinkInspector';
import DroprDocumentInspector from './modules/Inspector/DocumentInspector';
import DroprMediaInspector from './modules/Inspector/MediaInspector';
import DroprImageInspector from './modules/Inspector/ImageInspector';

import { droprFeaturedImgLink } from './modules/block-filters'; 
import icon from './modules/icon';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { BlockControls, BlockAlignmentToolbar, RichText } = wp.editor;
const { addFilter } = wp.hooks;
const { Placeholder, Button } = wp.components;

/**
 * Update attributes for the block based on user interactions
 */
const setDroprBlockProps = ( props ) => {
	props.activeDroprBlock = true;
	jQuery('#wpdrop-popup').on('click', '#wpdpx-drop-insert', (e) => {
		e.preventDefault();
		if( props !== null ) {
			if( props.activeDroprBlock === true ) {
				props.activeDroprBlock = false;
				let embedItem = jQuery("#wpdrop-popup input[name='insert-type']:checked").val();
				let itemWrapper = jQuery('#wpdpx-embed-' + embedItem);
				let attributes = { contentType: embedItem };
				let blockAttrs = itemWrapper.data('blockAttrs');
				if( embedItem === 'audio' || embedItem === 'video' ) {
					attributes.media = blockAttrs;
				} else {
					attributes[embedItem] = blockAttrs;
				}
				props.setAttributes( attributes );
			}
		}
	});
};

/**
 * Register Gutenberg Block.
 *
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'dropr-for-dropbox/dropr', {
	title: __( 'Dropr', 'dropr' ), // Block title.
	description: __( 'Add files from your Dropbox account.', 'dropr' ), // Block description
	icon: icon.main, // Block icon
	category: 'common', // Block category,
	keywords: [ __( 'Dropbox', 'dropr' ), __( 'Add from Dropbox', 'dropr' ) ], // Access the block easily with keyword aliases
	supports: {
		html: false, // Remove HTML mode support
		className: false // Remove default generated class
	},
	/**
	 * The edit function describes the structure of the block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 */
	edit: ( props ) => {
		const { attributes, setAttributes, className, isSelected } = props;
		const { contentType } = attributes;
		const setImageAttributes = ( attr, val ) => {
			let imageAttrs = { ...attributes.image };
			if( attr === 'class' ) {
				val = val ? 'align' + val : 'alignnone';
			}
			imageAttrs[attr] = val;
			setAttributes({
				image: imageAttrs
			});
		};

		if( typeof contentType !== 'undefined' ) {
			let controls = null;
			let inspector = null;
			let editorContent = null;
			if( contentType === 'link' || contentType === 'image' ) {
				let mainContent = null;
				let mainClass = "wp-dropr-block-wrapper";
				mainClass = className ? mainClass + " " + className : mainClass;
				if( contentType === 'link' ) {
					/**
					 * Editor: Link Handling
					*/
					inspector = <DroprLinkInspector { ...props } />;
					const linkAttrs = attributes.link;
					const linkTarget = linkAttrs.target === true ? '_blank' : null;
					const rel = linkAttrs.target === true ? 'noopener noreferrer' : null;

					mainContent = <a href={ linkAttrs.src } className={ linkAttrs.class } target={ linkTarget } rel={ rel }>{ linkAttrs.text }</a>;
				} else if( contentType === 'image' ) {
					/**
					 * Editor: Image Handling
					*/
					const imgAttrs = attributes.image;
					let alignClassName = typeof imgAttrs.class !== 'undefined' ? imgAttrs.class : '';
					alignClassName = alignClassName.length > 0 ? alignClassName : 'alignnone';
					const blockAlignmentVal = alignClassName !== 'undefined' ? alignClassName.replace( 'align', '' ) : '';

					controls = (
						<BlockControls>
							<BlockAlignmentToolbar value={ blockAlignmentVal } onChange={ (align) => setImageAttributes( 'class', align ) } controls={ [ 'left', 'center', 'right' ] } />
						</BlockControls>
					);
					inspector = <DroprImageInspector { ...props } />;

					const img = <img src={ imgAttrs.src } className={ alignClassName } alt={ imgAttrs.alt } title={ imgAttrs.title } width={ imgAttrs.width } height={ imgAttrs.height } />;

					const imgContent = imgAttrs.customURL ? <a href={ imgAttrs.customURL } className="dropr-img-block-link">{ img }</a> : img;

					let imgCaption = null;
					if( ! RichText.isEmpty( imgAttrs.caption ) || isSelected ) {
						imgCaption = <RichText tagName="figcaption" placeholder={ __( 'Write caption…' ) } value={ imgAttrs.caption } onChange={ (caption) => setImageAttributes( 'caption', caption ) } inlineToolbar />;
					}

					mainContent = (
						<figure className={ "dropr-img-" + alignClassName }>
							{ imgContent }
							{ imgCaption }
						</figure>
					);
				}
				editorContent = (
					<div className={ mainClass }>
						<div className={ `wp-dropr-block wp-dropr-${ contentType }-block` }>
							{ mainContent }
						</div>
					</div>
				);
			} else {
				/**
				 * Editor: Document and Video/Audio Handling
				*/
				if( contentType === 'audio' || contentType === 'video' ) {
					inspector = <DroprMediaInspector { ...props } />;
				} else if( contentType === 'document' ) {
					inspector = <DroprDocumentInspector { ...props } />;
				}
				editorContent = <DroprServerSideRender
				block="dropr-for-dropbox/dropr"
				attributes = { attributes } />;
			}
			return [
				controls,
				inspector,
				editorContent
			];
		} else {
			return (
				<Placeholder label={ __( 'Dropr', 'dropr' ) } instructions={ __( 'Add files from your Dropbox account.', 'dropr' ) } icon={ icon.button } className="dropr-block-wrapper">
					<Button className="awsm-dropr" onClick={ () => setDroprBlockProps( props ) } isSecondary isLarge>
						{ __( 'Add From Dropbox', 'dropr' ) }
					</Button>
				</Placeholder>
			);
		}
	},
	/**
	 * The save function defines the way in which the different attributes should be combined into the final markup, which is then serialized by Gutenberg into post_content.
	 */
	save: ( props ) => {
		const { attributes , className } = props;
		const { contentType } = attributes;
		let output = null;
		if( contentType && typeof attributes[contentType] !== undefined ) {
			let content = '';
			let mainClass = "wp-dropr-block-wrapper";
			mainClass = className ? mainClass + " " + className : mainClass;
			let attrType = ( contentType === 'audio' || contentType === 'video' ) ? 'media' : contentType;
			let atts = attributes[attrType];
			if( contentType === 'audio' || contentType === 'video' || contentType === 'document' ) {
				let shortcodeAtts = '';
				for( let attr in atts ) {
					let attrVal = atts[attr];
					shortcodeAtts += ` ${ attr }="${ attrVal }"`;
				}
				if( contentType === 'document' ) {
					content = `[docembed${ shortcodeAtts }]`;
				} else {
					content = `[${ contentType + shortcodeAtts }][/${ contentType }]`;
				}
			} else {
				if( contentType === 'link' ) {
					const linkTarget = atts.target === true ? '_blank' : null;
					const rel = atts.target === true ? 'noopener noreferrer' : null;
					
					content = <a href={ atts.src } className={ atts.class } target={ linkTarget } rel={ rel }>{ atts.text }</a>;
				} else if( contentType === 'image' ) {
					let alignClassName = typeof atts.class !== 'undefined' ? atts.class : '';
					alignClassName = alignClassName.length > 0 ? alignClassName : 'alignnone';

					const img = <img src={ atts.src } className={ alignClassName } alt={ atts.alt } title={ atts.title } width={ atts.width } height={ atts.height } />;

					const imgContent = atts.customURL ? <a href={ atts.customURL } className="dropr-img-block-link">{ img }</a> : img;

					let imgCaption = null;
					if( ! RichText.isEmpty( atts.caption ) ) {
						imgCaption = <RichText.Content tagName="figcaption" value={ atts.caption } />;
					}

					content = (
						<figure className={ "dropr-img-" + alignClassName }>
							{ imgContent }
							{ imgCaption }
						</figure>
					);
				}
			}
			output = (
				<div className={ mainClass }>
					<div className={ `wp-dropr-block wp-dropr-${ contentType }-block` }>
						{ content }
					</div>
				</div>
			);
		}
		return output;
	},
} );

/**
 *  Block Filters
 */
addFilter( 'editor.PostFeaturedImage', 'dropr-for-dropbox/dropr', droprFeaturedImgLink );