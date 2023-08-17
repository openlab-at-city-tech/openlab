(function(){

	const jetsloth_matchHeights_setHeight = (el, val) => {
		if (typeof val === "function") {
			val = val();
		}
		if (typeof val === "string") {
			el.style.height = val;
		}
		else {
			el.style.height = val + "px";
		}
	}


	const jetsloth_matchHeights = elemsOrSelector => {
		let currentTallest = 0
		let currentRowStart = 0
		let rowDivs = []
		let currentDiv
		let topPosition = 0

		const collection = ( typeof elemsOrSelector === "string" ) ? document.querySelectorAll(elemsOrSelector) : elemsOrSelector

		collection.forEach((el,i) => {
			if ( window.outerWidth < 680 ) {
				// el.style.height = ''
				// return
			}
			el.style.height = ""
			topPosition = el.offsetTop
			if ( currentRowStart !== topPosition ){
				for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
					jetsloth_matchHeights_setHeight( rowDivs[currentDiv], currentTallest )
				}
				rowDivs.length = 0
				currentRowStart = topPosition
				currentTallest = parseFloat(window.getComputedStyle(el, null).height.replace("px", ""))
				rowDivs.push(el)
			}
			else {
				rowDivs.push(el)
				currentTallest = (currentTallest < parseFloat(window.getComputedStyle(el, null).height.replace("px", ""))) ? (parseFloat(window.getComputedStyle(el, null).height.replace("px", ""))) : (currentTallest)
			}
			for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
				jetsloth_matchHeights_setHeight(rowDivs[currentDiv], currentTallest)
			}
		})
	}

	const _jetsloth_matchHeights_doMatchHeights = () => {
		const mhGroups = {}
		document.querySelectorAll('[data-jmh]').forEach(el => {
			let group = el.dataset.jmh
			if ( !group || group === "" ) {
				group = "all"
			}
			if ( !mhGroups.hasOwnProperty(group) ) {
				mhGroups[group] = []
			}
			mhGroups[group].push(el)
		})

		document.querySelectorAll('.jmh').forEach(el => {
			if ( el.dataset.hasOwnProperty('jmh') ) {
				return
			}
			const group = "all"
			if ( !mhGroups.hasOwnProperty(group) ) {
				mhGroups[group] = []
			}
			mhGroups[group].push(el)
		})

		Object.keys(mhGroups).forEach(key => {
			jetsloth_matchHeights( mhGroups[key] )
		})
	}

	let _jetsloth_matchHeight_listenersAdded = false;
	const _jetsloth_matchHeights_addListeners = () => {
		if ( _jetsloth_matchHeight_listenersAdded ) {
			return;
		}

		window.addEventListener('resize', () => {
			setTimeout(function(){
				_jetsloth_matchHeights_doMatchHeights()
			}, 10)
		})

		document.addEventListener('jetsloth-lazy-loaded', () => {
			setTimeout(function(){
				_jetsloth_matchHeights_doMatchHeights()
			}, 10)
		})

		_jetsloth_matchHeight_listenersAdded = true;
	}

	window.jetslothMatchHeights = () => {
		_jetsloth_matchHeights_addListeners();
		_jetsloth_matchHeights_doMatchHeights()
	}

})()
