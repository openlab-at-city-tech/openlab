export function setCustomizerSetting(settingKey, settingValue) {
	fetch('http://tests.local/wp-json/wp/v2/customizer/settings', {
	  method: 'POST',
	  headers: {
		'Content-Type': 'application/json',
	  },
	  body: JSON.stringify({
		setting_key: settingKey,
		setting_value: settingValue
	  })
	})
	.then(response => {
	  if (!response.ok) {
		throw new Error('Failed to set customizer setting value.');
	  }
	  return response.json();
	})
	.then(data => {
	})
	.catch(error => {
	  console.error(error);
	});
}