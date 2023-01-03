=== DCO Comment Attachment ===
Contributors: denisco
Tags: comment, comment attachment, attachment, image, video
Requires at least: 4.6
Tested up to: 6.0
Requires PHP: 5.6
Stable tag: 2.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.donationalerts.com/r/denisco

Allows your visitors to attach files with their comments

== Description ==
DCO Comment Attachment allows your visitors to attach images, videos, audios, documents and other files with their comments. They will also be able to automatically embed links from Youtube, Facebook, Twitter and other services in the comment text.

With plugin settings you can:

* Limit the maximum file upload size.
* Make an attachment required.
* Specify whether the attachment will be embedded or displayed as a link.
* Enable/Disable autoembed links (like Youtube, Facebook, Twitter, etc.) in the comment text.
* Select an attachment image size from thumbnails available in your WordPress install.
* Link a thumbnail to a full-size image with lightbox plugins support (see [FAQ](#faq) for details).
* Open a full-size image in a new tab or link thumbnail to the attachment page.
* Enable/Disable multiple upload.
* Combine images to gallery.
* Select an attachment image size for the images gallery.
* Restrict attachment file types.
* Decide who will be able to upload attachments: all users or only logged users.
* Manually moderate comments with attachments.

You can also:

* Add, replace or delete an attachment from a comment on the Edit Comment screen.
* Attach an unlimited number of attachments to the comment in the admin panel.
* Delete an attachment from a specific comment or bulk delete attachments from comments on the Comments screen.
* Display attachments attached to comments to the current post (or a specific post) with the `[dco_ca]` shortcode. You can also filter by type. See [FAQ](#faq) for details.

Attachments are uploaded to the WordPress Media Library and deleted along with the comment (if this is set in the settings).

REST API is supported.

DCO Comment Attachment is also available on [GitHub](https://github.com/yadenis/DCO-Comment-Attachment).

== Installation ==
1. Upload `dco-comment-attachment` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What lightbox plugins are supported? =
 
DCO Comment Attachment tested with:
* Simple Lightbox
* Easy FancyBox
* Responsive Lightbox & Gallery
* FooBox Image Lightbox
* FancyBox for WordPress

Feel free to create [a new topic](https://wordpress.org/support/plugin/dco-comment-attachment/) on support forum if you need integration with another plugin.

= How to use the [dco_ca] shortcode? =

Without attributes specified, the `[dco_ca]` shortcode will display all attachments attached to current post.

You can filter attachments using the `type` attribute. By default it is `all`. Also supported: `image`, `video`, `audio` and `misc`.
You can specify one value `[dco_ca type="image"]` or multiple values, separated by commas `[dco_ca type="video,audio"]`.

You can also display attachments from the comments of another post using the `post_id` attribute.
For example, `[dco_ca post_id="45"]`, where `45` is the ID of the specific post.

You can also combine these attributes. For example, `[dco_ca post_id="45" type="image"]` will display all images attached to comments to the post with ID 45.

== Changelog ==

= 2.4.0 =
* Added bulk delete attachments action on the Comments screen.
* Added the `[dco_ca]` shortcode for display attachments attached to comments (see FAQ for details).
* Fixed bug: now if there is no attachment, the empty array is not saved to the database.

= 2.3.1 =
* Fixed a bug with the accept attribute of the attachment upload field.
* Added compatibility with Loco Translate plugin.

= 2.3.0 =
* Added allowed file types to the file input dialog box. (thank you [@pranciskus](https://profiles.wordpress.org/pranciskus/))
* Added REST API support (thank you [@daohoangson](https://profiles.wordpress.org/daohoangson/))
* Added `dco_ca_force_download_misc_attachments` filter for force download files instead of open.
* Improved check/uncheck allowed file types on Settings page.
* Fixed "Trying to get property ‘comment_ID’ of non-object" bug. (thank you [@pranciskus](https://profiles.wordpress.org/pranciskus/))

= 2.2.0 =
* Added drag and drop support for attachment input field.
* Improved "Link thumbnail?" option. Link to a new tab and an attachment page (thank you [@nourijp](https://profiles.wordpress.org/nourijp/)) is now supported.
* Fixed bug with "Manually moderate comments with attachments" option. (thank you [@thompro](https://profiles.wordpress.org/thompro/))
* Removed "Attach to commented post?" option from the Settings page. You can use the `dco_ca_attach_to_post` filter instead.

= 2.1.1 =
* Added new filters for the attachment markup customization: `dco_ca_get_attachment_preview` and `dco_ca_get_attachment_preview_image`.

= 2.1.0 =
* Added links to attached attachments to the new comment notification email.
* Added the feature to force moderation comments with attachments.
* Added compatible with SVG Support plugin.

= 2.0.0 =
* Added the feature to upload multiple files.
* Added support for some lightbox plugins (see FAQ for details).
* Added additional markup to the form elements (thank you [@matthewmcvickar](https://profiles.wordpress.org/matthewmcvickar/))
* Added error handling for JavaScript on the frontend (thank you [@mrbalkon](https://profiles.wordpress.org/mrbalkon/))
* Improved Settings page
* Filter `dco_ca_form_element_autoembed_links_notification` is deprecated. Use `dco_ca_form_element_autoembed_links` instead.
* Type `autoembed-links-notification` for form_element function is deprecated. Use `autoembed-links` instead.

= 1.3.1 =
* Fixed image embed bug when attachment url has get parameters (thank you [@deepreef](https://profiles.wordpress.org/deepreef/))

= 1.3.0 =
* Added the feature to link a thumbnail to a full-size image.
* Added the feature for restrict uploading attachments only to logged users.
* Added notification about automatically embedded links, when it's enabled.
* Fixed bug with incorrect display of attachment types that do not support embedding. (thank you [@nazzareno](https://profiles.wordpress.org/nazzareno/))
* Removed jQuery dependency on the frontend.

= 1.2.1 =
* Fixed Quick Edit Comment function bug (thank you [@bbceg](https://profiles.wordpress.org/bbceg/))

= 1.2.0 =
* Added the feature for autoembed links in comment text. You can disable it in Settings -> DCO Comment Attachment.
* Introduced `dco_ca_disable_display_attachment` hook. Now you can display attachment in custom place with `add_filter('dco_ca_disable_display_attachment', '__return_true');` filter and `dco_ca()->display_attachment()` function.

= 1.1.2 =
* Fixed display of empty allowed types if the website administrator has forbidden the upload of all extensions of this type. (thank you [@nunofrsilva](https://profiles.wordpress.org/nunofrsilva/))

= 1.1.1 =
* Added filters for the attachment field customization

= 1.1.0 =
* Now you can select and deselect Allowed File Types by the type in one click.
* Added `dco_ca_disable_attachment_field` hook for disable the upload attachment field.
* Reduced the effect of mime types filtering. Now it applies only for comment attachment upload.
* Added the feature to attach an attachment to a commented post.

= 1.0.0 =
* Initial Release

== Screenshots ==
 
1. Examples of attachment types.
2. A commenting form with an attachment field.
3. List of comments in the admin panel.
4. Screen for editing a comment in the admin panel.
5. Plugin settings page.
6. An example of a lightbox with the Simple Lightbox plugin.
7. An example of a drag and drop support.
8. An example of the new comment notification email.
9. An example of REST API support.