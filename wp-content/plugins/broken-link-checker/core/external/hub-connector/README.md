# Hub Connector #

Hub Connector module is used in our free plugins to connect the user websites with Hub.

## Requirements:

* PHP: 7.4+
* WordPress: 5.0+

# How to use it #

1. Insert this repository as **sub-module** into the existing project

2. Include the file `connector.php` in your plugin and initialize it by calling ``\WPMUDEV\Hub\Connector::get();``.

3. Set the plugin specific options (see below for more details) using a unique plugin identifier. Identifier can be any unique string.
  ``\WPMUDEV\Hub\Connector::get()->set_options( 'blc', $options );``

4. Call the action `wpmudev_hub_connector_ui` where you want the Hub connector UI to render.

5. Done!

### Options

There are a few plugin specific options you need to set in order for this module to work properly without conflicting with other WPMUDEV plugins.

These are the accepted options:

| Option       | Type  | Sample                                                                             | Description                                                                                                                  |
|--------------|-------|------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------|
| `screens`    | Array | `array( 'toplevel_page_blc' )`                                                     | Array of plugin admin screen IDs.                                                                                            |
| `extra_args` | Array | `array( 'auth' = array( 'ref' => 'blc' ), 'register' => array( 'ref' => 'blc' ) )` | Extra arguments to be added to the URLs of authentication with WPMUDEV.<br/>See more details about the expected items below. |

#### Extra Arguments

Extra custom arguments can be set to URLs used in Hub Connector auth page. Each sub items should contain array of custom arguments (key and value).

Expected items:

| Key               | Type  | Description                                   |
|-------------------|-------|-----------------------------------------------|
| `auth`            | Array | Custom for default login form action URL.     |
| `team_auth`       | Array | Arguments for team selection form action URL. |
| `google_auth`     | Array | Arguments for Google auth form action URL.    |
| `register`        | Array | Arguments for registration URL.               |
| `forgot_password` | Array | Arguments for forgot password URL.            |


## Sample Usage

Hub connector should be loaded unconditionally on every page load and admin screen should be set. Otherwise some hooks may not work.
```
<?php
// Load base file of Hub Connector.
if ( file_exists( \HUB_CONNECTOR_DIR . 'external/hub-connector/connector.php' ) ) {
    include_once \HUB_CONNECTOR_DIR . 'external/hub-connector/connector.php';
    // Initialize and set options.
    \WPMUDEV\Hub\Connector::get()->set_options( 'blc', $options );
}

// Now conditionally render Hub connector UI somewhere in your plugin.
if ( $my_condition === true ) {
    do_action( 'wpmudev_hub_connector_ui', 'blc' );
}
```



## Rendering Hub Connector ##

```
<?php
// Render Hub connector UI somewhere in BLC.
// Use a unique name to identify your plugin.
do_action( 'wpmudev_hub_connector_ui', 'blc' );
```

### Optional: Show the UI only when not connected ##

```
<?php
// Show UI only when not logged in.
if ( ! \WPMUDEV\Hub\Connector\API::get()->is_logged_in() ) {
    // Render Hub connector UI.
    do_action( 'wpmudev_hub_connector_ui', 'blc' );
}
```

## Available Helpers ##

```
<?php
// Check if website is connected with Hub.
$is_logged_in = \WPMUDEV\Hub\Connector\API::get()->is_logged_in();;
```

```
<?php
// Get the API key.
$api_key = \WPMUDEV\Hub\Connector\API::get()->get_api_key();
```

```
<?php
// Get the current membership type.
$membership_type = \WPMUDEV\Hub\Connector\Data::get()->membership_type();
```

```
<?php
// Get the current member profile data.
$profile = \WPMUDEV\Hub\Connector\Data::get()->profile_data();
```

## Modifying texts ##

You can modify texts in Hub Connector UI using `wpmudev_hub_connector_localize_text_vars` filter.

```
<?php
// Modifying login page texts.
add_filter( 'wpmudev_hub_connector_localize_text_vars', function ( $texts, $plugin ) {
    if ( 'blc' === $plugin ) {
        $texts['login_title'] = 'My custom login title';
    }
			
    return $texts;
});
```

## Action Hooks ##

There are a few action hooks which you can use in your plugins.

| Hook              | Description                                              |
|-------------------|----------------------------------------------------------|
| `wpmudev_hub_connector_sync_completed`   | Runs after every succesful hub sync.                     |
| `wpmudev_hub_connector_first_sync_completed` | Runs after first hub sync after connecting with Hub.     |

# Development

Do not commit anything directly to `master` branch. The `master` branch should always be production ready. All plugins will be using it as a submodule.

## Build Tasks (npm)

Everything should be handled by npm. Note that you don't need to interact with Gulp in a direct way.

| Command              | Action                                                 |
|----------------------|--------------------------------------------------------|
| `npm run watch`      | Compiles and watch for changes.                        |
| `npm run compile`    | Compile production ready assets.                       |
| `npm run build`  | Build production ready submodule inside `/build/` folder |

## Git Workflow

- Create a new branch from `dev` branch: `git checkout -b branch-name`. Try to give it a descriptive name. For example:
  -   `release/X.X.X` for next releases
  -   `new/some-feature` for new features
  -   `enhance/some-enhancement` for enhancements
  -   `fix/some-bug` for bug fixing
- Make your commits and push the new branch: `git push -u origin branch-name`
- File the new Pull Request against `dev` branch
- Assign somebody to review your code.
- Once the PR is approved and finished, merge it in `dev` branch.
- Checkout `dev` branch.
- Run `npm run build` and copy all files and folders from the `build` folder.
- Checkout `master` branch (preferably in a different folder) and replace all files and folders with copied content from the `build` folder.
- Commit and push the `master` branch changes.
- Inform all plugin devs to update the submodule to the latest commit.