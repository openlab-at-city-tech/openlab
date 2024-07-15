# BP Two Factor

Integrates the [Two Factor](https://wordpress.org/plugins/two-factor/) plugin into BuddyPress.

## How to use?

1. Install and activate the [Two Factor](https://wordpress.org/plugins/two-factor/) plugin.
2. Install and activate this plugin.
3. Navigate to your BuddyPress profile's "Settings" page. You should see a **Two Factor Authentication** block.
4. Enable your preferred 2FA option and ensure one of them is marked as Primary.
5. Logout and login again to test two-factor authentication.

## Notes

This plugin also makes the following usability improvements:

- Pressing the "Enter" key can submit the TOTP authentication code now. (See [pull request](https://github.com/WordPress/two-factor/pull/390).)
- "Security Keys" block only shows up if it is enabled. (See [pull request](https://github.com/WordPress/two-factor/pull/386).)
- Deselecting a 2FA provider will remove it as the primary 2FA option if it was previously selected. Also, if another 2FA provider was enabled during unchecking, that provider will be selected as the new primary 2FA option. This was done to address [this problem](https://github.com/WordPress/two-factor/issues/342).
- Changed "FIDO U2F Security Keys" label to "Security Keys" for simplicity.
- Changed "Backup Verification Codes (Single Use)" label to "Recovery Codes". "Recovery Codes" seems to be more in use on other 2FA sites such as Github and Microsoft.
- Removed "You can find FIDO U2F Security Key devices for sale from here" link under the "Security Keys" section as that sounds like an ad. ([Source](https://github.com/WordPress/two-factor/blob/736473edf5ff6d2fed18ba2406c772f30950343c/providers/class-two-factor-fido-u2f-admin.php#L208).)
- Added more descriptive text to the "Security Keys" and "Recovery Codes" sections.
- Changed the BuddyPress "Settings > General" tab to "Settings > Security" to better reflect the content on the page.

These improvements **only** take place on the BuddyPress profile's "Settings" page, and not in the admin dashboard's profile page.

The following improvements have been merged into the main Two Factor plugin:
- Clicking on the 2FA provider name will select it under the "Enabled" checkbox. (See [pull request](https://github.com/WordPress/two-factor/pull/387).)