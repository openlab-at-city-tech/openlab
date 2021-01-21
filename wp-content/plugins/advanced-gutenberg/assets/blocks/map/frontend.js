window.addEventListener('load', function () {
    if (typeof google === "undefined") {
        return null;
    }

    var mapElm = document.querySelectorAll('.advgb-map-block .advgb-map-content');
    for (var i = 0; i < mapElm.length; i++) {
        elm = mapElm[i];
        var lat = parseFloat(elm.dataset.lat),
            lng = parseFloat(elm.dataset.lng),
            zoom = parseFloat(elm.dataset.zoom),
            defaultMarker = elm.dataset.default,
            icon = elm.dataset.icon,
            title = elm.dataset.title.replace(/\\/g, ''),
            desc = elm.dataset.desc.replace(/\\/g, ''),
            infoShown = elm.dataset.shown === 'true',
            info = '',
            mapStyle = decodeURIComponent(elm.dataset.style);

        if (!elm.dataset.info && (desc || title) ) {
            info = '<div class="advgbmap-wrapper">';
            if (title) info += '<h3 class="advgbmap-title">' + title + '</h3>';
            if (desc) info += '<p class="advgbmap-desc">'+ desc +'</p>';
            info += '</div>';
        } else if (elm.dataset.info) {
            info = decodeURIComponent(elm.dataset.info);
        }

        var location = {
            lat: lat,
            lng: lng
        };

        var map = new google.maps.Map(elm, {
            zoom: zoom,
            center: location,
            styles: mapStyle !== '' ? JSON.parse(mapStyle) : {},
            gestureHandling: 'cooperative'
        });
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            title: title,
            animation: google.maps.Animation.DROP,
            icon: {
                url: icon || defaultMarker,
                scaledSize: new google.maps.Size(27, 43)
            }
        });

        if (info) {
            var infoWindow = new google.maps.InfoWindow({
                content: info
            });

            marker.addListener('click', function () {
                infoWindow.open(map, marker);
            });

            if (infoShown) {
                infoWindow.open(map, marker);
            }

        }
    }
});