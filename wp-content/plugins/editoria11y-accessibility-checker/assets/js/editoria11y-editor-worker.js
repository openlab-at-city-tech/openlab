let allPorts = [];
let options = false;
let results = false;
let action = false;

// eslint-disable-next-line no-undef
onconnect = function (event) {
	const port = event.ports[0];
	allPorts.push(port);
	port.onmessage = function (e) {
		if (e.data[0]) {
			options = e.data[0];
		}
		if (e.data[1]) {
			results = e.data[1];
		}
		if (e.data[2]) {
			action = e.data[2];
		}
		allPorts.forEach(port => {
			port.postMessage([options, results, action]);
		});
		action = false;
	};
};