A set of common postcss utilities.

## Installation
You probably will need to install this package in a custom paths, say `src/resources/postcss/utilities`; if that's the case then add this to the plugin/project `composer.json` file:

```json
  "repositories": [
    {
      "name": "moderntribe/tribe-common-styles",
      "type": "github",
      "url": "https://github.com/moderntribe/tribe-common-styles",
      "no-api": true
    }
  ],
  "extra": {
    "installer-paths": {
      "src/resources/postcss/utilities": [
        "moderntribe/tribe-common-styles"
      ]
    }
  }
```
To simply install the package in your project use:

```bash
composer require --dev moderntribe/tribe-common-styles
```

## Source installation
If you need to work on **this** package while working on your project you will want to clone the full Git repository, just follow the instructions above and, in place of the command above, use:

```bash
composer require --dev moderntribe/tribe-common-styles --prefer-source
```

Remember to ignore the package installation folder to avoid committing a dirty repository!
