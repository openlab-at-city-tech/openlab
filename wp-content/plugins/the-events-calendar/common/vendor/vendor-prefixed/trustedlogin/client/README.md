![TrustedLogin_TrustedLogin Logo Horizontal](https://user-images.githubusercontent.com/870979/234620734-0dbe5771-7b45-45e2-a68f-428dd92f9a3a.svg)

# TrustedLogin SDK

**[Download the Latest Version](https://github.com/trustedlogin/client/archive/refs/heads/main.zip)**

Easily and securely log in to your customers sites when providing support.

### [🔍 See the Changelog](https://github.com/trustedlogin/client/blob/main/CHANGELOG.md)

### [📖 Read the Documentation](https://trustedlogin.github.io/docs/Client/intro)

### Requirements:

- PHP 5.3.0 or greater
- WordPress 5.2 or greater

### Local Development And Testing

Make sure to install [wp-env](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) globally first by running `npm -g i @wordpress/env`.

- Start local dev
	- `wp-env start`
- Stop local dev
	- `wp-env stop`
- Run the PHP tests in container
	- `npm run test:php`

### Running linting and tests

- `composer lint` Checks for any linting errors
- `composer format` Fixes any code that is able to be automatically fixed
- `composer test` Runs PHPUnit tests

#### WordPress 4.1+ support

By default, TrustedLogin supports WordPress 5.2 or newer; this is the first version that includes the Sodium cryptography library. To support earlier versions of WordPress (version 4.1 or greater), add the following libraries to your Composer `require` definitions:

```json
"paragonie/random_compat": "<9.99",
"paragonie/sodium_compat": "^1.12"
```
