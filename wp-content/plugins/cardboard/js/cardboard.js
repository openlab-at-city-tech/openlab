( function( $ ) {
	$( '.cardboard' ).each( function() {
		var _self = this;
		var width = $( _self ).width(),
				height = $( _self ).width() / 16 * 9;

		// scene
		var scene = new THREE.Scene();

		// mesh
		var geometry = new THREE.SphereGeometry( 5, 32, 24 );
		geometry.scale( -1, 1, 1 );
		var texloader = new THREE.TextureLoader();
		var material = new THREE.MeshBasicMaterial( {
			 map: texloader.load( $( _self ).data( 'image' ) )
		} );
		var sphere = new THREE.Mesh( geometry, material );
		scene.add( sphere );

		// camera
		var camera = new THREE.PerspectiveCamera( 75, width / height, 1, 100 );
		camera.position.set( 0, 0, 0.1 );
		camera.lookAt( sphere.position );

		// render
		var renderer = new THREE.WebGLRenderer();
		renderer.setSize( width, height );
		renderer.setClearColor( { color: 0x000000 } );
		$( _self ).append( renderer.domElement );
		renderer.render( scene, camera );

		// control
		var controls = new THREE.OrbitControls( camera, renderer.domElement );
		controls.minDistance = 0;
		controls.maxDistance = 5;

		var render = function() {
			requestAnimationFrame( render );
			sphere.rotation.y -= 0.05 * Math.PI / 180;
			renderer.render( scene, camera );
			controls.update();
		}
		render();

		window.addEventListener( 'resize', function() {
			var width = $( _self ).width(),
					height = $( _self ).width() / 16 * 9;
			camera.aspect = width / height;
			camera.updateProjectionMatrix();

			renderer.setSize( width, height );
		}, false );
	} );
} )( jQuery );
