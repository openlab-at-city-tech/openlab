# Feedback Package

Shared feedback collection library for Smash Balloon plugins. Currently provides a deactivation survey modal that collects structured feedback when users deactivate a plugin.

## Usage

In your plugin's main file or bootstrap:

```php
use Smashballoon\Framework\Packages\Feedback\FeedbackManager;

FeedbackManager::init([
    'plugin_slug'    => 'instagram-feed',
    'plugin_name'    => 'Smash Balloon Instagram Feed',
    'plugin_version' => SBIF_VERSION,
    'plugin_file'    => __FILE__,
]);
```

### Configuration Options

| Parameter | Required | Default | Description |
|-----------|----------|---------|-------------|
| `plugin_slug` | Yes | — | Unique plugin identifier (e.g. `instagram-feed`) |
| `plugin_name` | Yes | — | Display name shown in the modal |
| `plugin_version` | No | `''` | Current plugin version (sent with feedback) |
| `plugin_file` | Yes | — | Main plugin file path (`__FILE__`) |
| `support_url` | No | `https://smashballoon.com/support/` | "Get Support" button link |
| `api_endpoint` | No | Auto-detected | Feedback API endpoint URL (see below) |

### API Endpoints

Feedback is automatically sent to the correct endpoint based on the plugin slug:

| Plugin Slugs | Production Endpoint | Staging Endpoint |
|--------------|---------------------|------------------|
| `wpchat`, `wpchat-pro` | `https://wpchat.com/wp-json/sb/v1/feedback` | `https://staging.wpchat.com/wp-json/sb/v1/feedback` |
| All other SB plugins | `https://smashballoon.com/wp-json/sb/v1/feedback` | `https://staging.smashballoon.com/wp-json/sb/v1/feedback` |

You can override this by setting `api_endpoint` in the config:

```php
FeedbackManager::init([
    'plugin_slug'    => 'instagram-feed',
    'plugin_name'    => 'Smash Balloon Instagram Feed',
    'plugin_version' => SBIF_VERSION,
    'plugin_file'    => __FILE__,
    'api_endpoint'   => 'https://custom-endpoint.example.com/wp-json/sb/v1/feedback',
]);
```

### Development/Staging Environment

The library automatically detects development environments and routes feedback to staging endpoints. This is useful for testing the feedback flow without polluting production data.

**Automatic detection** uses [`wp_get_environment_type()`](https://developer.wordpress.org/reference/functions/wp_get_environment_type/) (WP 5.5+). If the environment is `local`, `development`, or `staging`, staging endpoints are used.

To set your environment type, add to `wp-config.php`:

```php
define( 'WP_ENVIRONMENT_TYPE', 'development' );
```

**Manual override** — force staging endpoints regardless of environment:

```php
define( 'SB_FEEDBACK_USE_STAGING', true );
```

### API Request Format

The library sends a POST request with JSON body:

```json
{
    "plugin_slug": "instagram-feed",
    "plugin_name": "Smash Balloon Instagram Feed",
    "plugin_version": "6.4.0",
    "reason": "no-longer-needed",
    "comment": "Optional user feedback text",
    "wp_version": "6.4.2",
    "php_version": "8.2.0",
    "site_url": "https://example.com"
}
```

**Reason values:** `no-longer-needed`, `did-not-work`, `caused-errors`, `switching-plugin`, `too-complicated`, `other`

### Customizing Support URL

```php
FeedbackManager::init([
    'plugin_slug'    => 'wpchat',
    'plugin_name'    => 'WPChat',
    'plugin_version' => WPCHAT_VERSION,
    'plugin_file'    => __FILE__,
    'support_url'    => 'https://wpchat.com/support/',
]);
```

## How It Works

1. Plugin calls `FeedbackManager::init()` during bootstrap
2. On the WordPress plugins page, the library hooks into the "Deactivate" link
3. When clicked, a modal shows asking for a cancellation reason
4. Each reason provides context-specific messaging and an optional textarea
5. On submit, feedback is sent via AJAX to the backend
6. Backend forwards to the REST API endpoint (fire-and-forget)
7. Plugin deactivates regardless of API response

## CSS Isolation (Shadow DOM)

The modal renders inside a [Shadow DOM](https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_shadow_DOM) for complete CSS isolation from WordPress admin styles. This means:

- **No style bleed-through** — WP admin CSS cannot affect the modal
- **No `!important` hacks** — clean, maintainable selectors
- **No leaking out** — modal styles don't interfere with the admin page

The CSS file (`assets/deactivation-modal.css`) is inlined into a `<template>` element by the PHP renderer, then cloned into the shadow root by the JS. Browser support covers all modern browsers (Chrome 53+, Firefox 63+, Safari 10+, Edge 79+).

## Multisite

On multisite installs, the survey only triggers on the plugins admin screen where the user has `activate_plugins` capability.

## Data Collected

- Plugin slug, name, and version
- Selected deactivation reason
- Optional free-text comment
- WordPress version, PHP version
- Site URL (home_url)

**No user-identifying information is collected.**

## Hooks

### `sb_feedback_deactivation_submitted`

Fires after feedback is collected. Useful for logging or custom integrations.

```php
add_action( 'sb_feedback_deactivation_submitted', function( $data, $config ) {
    // Custom handling
    error_log( 'Feedback: ' . $data['plugin_slug'] . ' - ' . $data['reason'] );
}, 10, 2 );
```

The `$data` array includes all API fields plus additional metadata:
- `locale` — WordPress locale
- `multisite` — `'yes'` or `'no'`
- `timestamp` — MySQL datetime (UTC)
