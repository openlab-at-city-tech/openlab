const seleniumServer = require("selenium-server");
const chromedriver = require("chromedriver");

module.exports = {
	"src_folders": [
		"tests/e2e" // Change this to the directory where your test are
	],
	"output_folder": "./reports", // the directory for your rest reports
	"selenium": {
		"start_process": true,
		"server_path": seleniumServer.path,
		"host": "127.0.0.1",
		"port": 4444, // selenium usually runs on 4444
		"cli_args": {
			"webdriver.chrome.driver" : chromedriver.path // There are multiple drivers you can use, eg gecko or IE
		}
	},
	"test_settings": {
		"default": {
			"globals": {
				"waitForConditionTimeout": 5000
			},
			"desiredCapabilities": {
				"browserName": "chrome"
			}
		},
		"chrome": {
			"desiredCapabilities": {
				"browserName": "chrome",
				"javascriptEnabled": true
			}
		}
	}
};
