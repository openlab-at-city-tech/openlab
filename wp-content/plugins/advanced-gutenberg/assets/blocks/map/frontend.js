window.addEventListener('load', function () {
    // Accessibility: name the map region and expose the marker details as text
    var mapL10n = typeof advgbMapL10n !== 'undefined' ? advgbMapL10n : {};
    var a11yMaps = document.querySelectorAll('.advgb-map-block .advgb-map-content');
    for (var j = 0; j < a11yMaps.length; j++) {
        var mapContent = a11yMaps[j];
        var mapBlock = mapContent.closest('.advgb-map-block');
        if (!mapBlock || mapBlock.querySelector('.advgb-map-a11y-info')) {
            continue;
        }

        var plainText = function (text) {
            var doc = new DOMParser().parseFromString((text || '').replace(/\\/g, '').replace(/<br\s*\/?>/gi, '\n'), 'text/html');
            return (doc.body.textContent || '').trim();
        };
        var markerTitle = plainText(mapContent.dataset.title);
        var markerDesc = plainText(mapContent.dataset.desc);

        mapBlock.setAttribute('role', 'region');
        mapBlock.setAttribute(
            'aria-label',
            markerTitle ? (mapL10n.map_named || 'Map: %s').replace('%s', markerTitle) : (mapL10n.map || 'Map')
        );

        var a11yInfo = document.createElement('div');
        a11yInfo.className = 'advgb-map-a11y-info';
        a11yInfo.setAttribute('style', 'position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;');
        if (markerTitle) {
            var titleElm = document.createElement('p');
            titleElm.textContent = markerTitle;
            a11yInfo.appendChild(titleElm);
        }
        if (markerDesc) {
            var descElm = document.createElement('p');
            descElm.textContent = markerDesc;
            a11yInfo.appendChild(descElm);
        }
        var mapLat = parseFloat(mapContent.dataset.lat),
            mapLng = parseFloat(mapContent.dataset.lng);
        if (!isNaN(mapLat) && !isNaN(mapLng)) {
            var linkElm = document.createElement('a');
            linkElm.href = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(mapLat + ',' + mapLng);
            linkElm.target = '_blank';
            linkElm.rel = 'noopener noreferrer';
            linkElm.textContent = (mapL10n.view_on_google_maps || 'View on Google Maps') + ' ' + (mapL10n.new_tab || '(opens in a new tab)');
            a11yInfo.appendChild(linkElm);
            // Show the details while the link has keyboard focus
            linkElm.addEventListener('focus', function () {
                this.parentNode.dataset.hiddenStyle = this.parentNode.getAttribute('style');
                this.parentNode.removeAttribute('style');
            });
            linkElm.addEventListener('blur', function () {
                this.parentNode.setAttribute('style', this.parentNode.dataset.hiddenStyle || '');
            });
        }
        mapBlock.appendChild(a11yInfo);
    }

    if (typeof google === "undefined") {
        return null;
    }

    const sanitizeMapText = (text, preserveLineBreaks = false) => {
        if (!text) return '';

        const div = document.createElement('div');
        div.textContent = text;
        let sanitized = div.innerHTML;

        if (preserveLineBreaks) {
            sanitized = sanitized.replace(/\n/g, '<br>');
        }

        return sanitized;
    };

    var mapElm = document.querySelectorAll('.advgb-map-block .advgb-map-content');
    for (var i = 0; i < mapElm.length; i++) {
        elm = mapElm[i];
        var lat = parseFloat(elm.dataset.lat),
            lng = parseFloat(elm.dataset.lng),
            zoom = parseFloat(elm.dataset.zoom),
            defaultMarker = elm.dataset.default,
            icon = elm.dataset.icon,
            title = sanitizeMapText(elm.dataset.title.replace(/\\/g, '')),
            desc = sanitizeMapText(elm.dataset.desc.replace(/\\/g, ''), true),
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