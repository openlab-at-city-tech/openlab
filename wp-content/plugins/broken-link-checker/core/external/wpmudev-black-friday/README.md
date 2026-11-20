# WPMUDEV Black Friday Banner

This is a submodule that can be used in our free plugins.

Waiting for task.

Waiting for UTM list.

# How to Use It

1. Add this repository as a **submodule** to the project. Make sure you point to the **production** branch. Example for Smush:

   `git submodule add -b production git@bitbucket.org:incsub/wpmudev-blackfriday.git core/external/wpmudev-black-friday`

   *Note the destination folder (in the example we used `wpmudev-black-friday`), as it will be used when including the PHP file.*

2. Include the file `wpmudev-black-friday/campaign.php` in your plugin.

3. Create a new instance of `\WPMUDEV\Modules\BlackFriday\Campaign()`. No parameters are required.

## IMPORTANT

Do NOT include this submodule in Pro plugins. These notices are only for wp.org versions.

## Code Example (from Smush)

```
if ( ! class_exists( '\WPMUDEV\Modules\BlackFriday\Campaign' ) ) {
	$black_friday_path = WP_SMUSH_DIR . 'core/external/wpmudev-black-friday/campaign.php';

	if ( file_exists( $black_friday_path ) ) {
		require_once $black_friday_path;
		new \WPMUDEV\Modules\BlackFriday\Campaign();
	}
}
```

> IMPORTANT: Make sure to initialize this on a hook that also runs during admin-ajax requests. Test with `init` or `plugins_loaded`.

## Filters

### Banner values

Use `wpmudev_blackfriday_banner_data` to change any value passed to the banner script, for example the texts in `translation_texts` (`subtext`, `button_text`, `black_friday_special`, `dismiss_button`) or the `campaign_url`:

```
<?php
add_filter(
	'wpmudev_blackfriday_banner_data',
	function( $data ) {
		$data['translation_texts']['subtext']     = '$79/y forever for unlimited sites';
		$data['translation_texts']['button_text'] = 'Grab the deal';

		return $data;
	}
);
```

If you only need to change the price and keep the translated wording (`%s forever for unlimited sites`), use `wpmudev_blackfriday_deal_price`:

```
<?php
add_filter(
	'wpmudev_blackfriday_deal_price',
	function() {
		return '$79/y';
	}
);
```

### Upsell widget values

Use `wpmudev_blackfriday_widget_data` to change the values passed to the upsell widget script, for example the texts in `translation_texts` (`button_text`, `black_friday_special`) or each widget's `campaign_url`:

```
<?php
add_filter(
	'wpmudev_blackfriday_widget_data',
	function( $data ) {
		$data['translation_texts']['button_text'] = 'Grab the deal';

		return $data;
	}
);
```

> Campaign URLs are sanitized with `esc_url_raw()` after these filters run.

## Testing

To display the banners, admin menus, and action links before the scheduled dates, you can override the current date using this filter:

```
<?php
// Set current date to 22nd Nov.
add_filter(
	'wpmudev_blackfriday_current_date',
	function() {
		return '22-11-2026';
	}
);
```

# Development

In previous versions we kept the `master` branch production-ready and pushed manually. From version 2.0.0 onward, `master` can be used normally for development. A Bitbucket pipeline will run `pnpm build` and copy the generated files into the `production` branch.

The `production` branch is orphaned to avoid history conflicts.

## Build Tasks (npm)

Suggested to use pnpm, if you prefer you can keep using npm.

| Command            | Action                                                    |
| ------------------ | --------------------------------------------------------- |
| `pnpm watch`   | Compiles and watches for changes.                         |
| `pnpm compile` | Compiles production-ready assets.                         |
| `pnpm build`   | Builds the production-ready submodule in the main folder. |

## Git Workflow

* Create a new branch from the `dev` branch:
  `git checkout -b branch-name`
  Use a descriptive name, for example:

  * `release/X.X.X` for release versions
  * `new/some-feature` for new features
  * `enhance/some-enhancement` for enhancements
  * `fix/some-bug` for bug fixes
* Commit and push your branch:
  `git push -u origin branch-name`
* Open a Pull Request against the `dev` branch.
* Assign someone to review your code.
* Once approved, merge it into the `dev` branch.
* Checkout the `dev` branch.
* If you want to test in multiple plugins, run `pnpm run build` and copy all files from `wpmudev-blackfriday/wpmudev-blackfriday` into the submodule folder inside each plugin.
* When ready to update the `production` branch, go to Bitbucket Pipelines, choose your branch (`release/X.X.X`, `master`, etc.) in **Run Pipeline**, and select the **custom: deploy production** pipeline. Run it and wait for completion.
  After that, you can use the updated **production** branch.
