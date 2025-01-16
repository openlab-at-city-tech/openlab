const allPorts = [];
onconnect = (e) => {
	const port = e.ports[0];
	allPorts.push(port);
	port.addEventListener("message", (e) => {
		allPorts.forEach(port => {
			port.postMessage(e.data);
		});
	});

	port.start(); // Required when using addEventListener.
};
