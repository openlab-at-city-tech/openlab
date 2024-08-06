/* global H5P */
(() => {
	if ( 'undefined' === typeof H5P ) {
		return;
	}

	const getPostMessageData = ( event ) => {
		const { data } = event;

		if ( ! data ) {
			return null;
		}

		const { statement } = data;

		if ( ! statement ) {
			return null;
		}

		const { object } = statement;

		if ( ! object ) {
			return null;
		}

		const objectId = object.id;

		const verb = event.getVerb();

		const baseData = {
			objectId,
			source: 'h5p-postmessage',
			verb
		}

		switch ( verb ) {
			case 'attempted' :
				return baseData;

			case 'answered' :
				const isComplete = event.getScore() === event.getMaxScore() && event.getMaxScore() > 0

				if ( ! isComplete ) {
					return null;
				}

				return {
					...baseData,
					verb: 'completed'
				};
		}
	}

	H5P.externalDispatcher.on('xAPI', function (event) {
		const postMessageData = getPostMessageData( event );

		if ( ! postMessageData ) {
			return;
		}

		const { allowedDomains } = window.h5pPostMessageData;

		if ( ! allowedDomains.includes( window.parent.origin ) ) {
			return;
		}

		window.parent.postMessage( postMessageData, '*' );
	});
})();
