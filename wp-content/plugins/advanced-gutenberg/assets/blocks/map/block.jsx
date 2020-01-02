(function ( wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents ) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const { __ } = wpI18n;
    const { Component, Fragment } = wpElement;
    const { registerBlockType } = wpBlocks;
    const { InspectorControls, MediaUpload } = wpBlockEditor;
    const { PanelBody, TextControl, TextareaControl, RangeControl, SelectControl, ToggleControl, BaseControl, Button, Placeholder, Spinner } = wpComponents;

    let mapWillUpdate = null;
    const mapBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22" className="dashicon">
            <path d="M20.5 3l-.16.03L15 5.1 9 3 3.36 4.9c-.21.07-.36.25-.36.48V20.5c0 .28.22.5.5.5l.16-.03L9 18.9l6 2.1 5.64-1.9c.21-.07.36-.25.36-.48V3.5c0-.28-.22-.5-.5-.5zM15 19l-6-2.11V5l6 2.11V19z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
        </svg>
    );
    const MAP_STYLES = {
        silver: [
            {
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#f5f5f5"
                    }
                ]
            },
            {
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#616161"
                    }
                ]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#f5f5f5"
                    }
                ]
            },
            {
                "featureType": "administrative.land_parcel",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#bdbdbd"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#eeeeee"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#757575"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#e5e5e5"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#ffffff"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#757575"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#dadada"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#616161"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            },
            {
                "featureType": "transit.line",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#e5e5e5"
                    }
                ]
            },
            {
                "featureType": "transit.station",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#eeeeee"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#c9c9c9"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            }
        ],
        retro: [
            {
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#ebe3cd"
                    }
                ]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#523735"
                    }
                ]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#f5f1e6"
                    }
                ]
            },
            {
                "featureType": "administrative",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#c9b2a6"
                    }
                ]
            },
            {
                "featureType": "administrative.land_parcel",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#dcd2be"
                    }
                ]
            },
            {
                "featureType": "administrative.land_parcel",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#ae9e90"
                    }
                ]
            },
            {
                "featureType": "landscape.natural",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#dfd2ae"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#dfd2ae"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#93817c"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#a5b076"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#447530"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#f5f1e6"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#fdfcf8"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#f8c967"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#e9bc62"
                    }
                ]
            },
            {
                "featureType": "road.highway.controlled_access",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#e98d58"
                    }
                ]
            },
            {
                "featureType": "road.highway.controlled_access",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#db8555"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#806b63"
                    }
                ]
            },
            {
                "featureType": "transit.line",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#dfd2ae"
                    }
                ]
            },
            {
                "featureType": "transit.line",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#8f7d77"
                    }
                ]
            },
            {
                "featureType": "transit.line",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#ebe3cd"
                    }
                ]
            },
            {
                "featureType": "transit.station",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#dfd2ae"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#b9d3c2"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#92998d"
                    }
                ]
            }
        ],
        dark: [
            {
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#212121"
                    }
                ]
            },
            {
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#757575"
                    }
                ]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#212121"
                    }
                ]
            },
            {
                "featureType": "administrative",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#757575"
                    }
                ]
            },
            {
                "featureType": "administrative.country",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            },
            {
                "featureType": "administrative.land_parcel",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "administrative.locality",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#bdbdbd"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#757575"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#181818"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#616161"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#1b1b1b"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#2c2c2c"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#8a8a8a"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#373737"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#3c3c3c"
                    }
                ]
            },
            {
                "featureType": "road.highway.controlled_access",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#4e4e4e"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#616161"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#757575"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#000000"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#3d3d3d"
                    }
                ]
            }
        ],
        night: [
            {
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#242f3e"
                    }
                ]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#746855"
                    }
                ]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#242f3e"
                    }
                ]
            },
            {
                "featureType": "administrative.locality",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#d59563"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#d59563"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#263c3f"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#6b9a76"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#38414e"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#212a37"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9ca5b3"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#746855"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#1f2835"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#f3d19c"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#2f3948"
                    }
                ]
            },
            {
                "featureType": "transit.station",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#d59563"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#17263c"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#515c6d"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#17263c"
                    }
                ]
            }
        ],
        aubergine: [
            {
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#1d2c4d"
                    }
                ]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#8ec3b9"
                    }
                ]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#1a3646"
                    }
                ]
            },
            {
                "featureType": "administrative.country",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#4b6878"
                    }
                ]
            },
            {
                "featureType": "administrative.land_parcel",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#64779e"
                    }
                ]
            },
            {
                "featureType": "administrative.province",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#4b6878"
                    }
                ]
            },
            {
                "featureType": "landscape.man_made",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#334e87"
                    }
                ]
            },
            {
                "featureType": "landscape.natural",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#023e58"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#283d6a"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#6f9ba5"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#1d2c4d"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#023e58"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#3C7680"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#304a7d"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#98a5be"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#1d2c4d"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#2c6675"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#255763"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#b0d5ce"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#023e58"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#98a5be"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#1d2c4d"
                    }
                ]
            },
            {
                "featureType": "transit.line",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#283d6a"
                    }
                ]
            },
            {
                "featureType": "transit.station",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#3a4762"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#0e1626"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#4e6d70"
                    }
                ]
            }
        ],
    };

    class AdvMap extends Component {
        constructor() {
            super( ...arguments );
            this.state = {
                currentAddress: '',
                currentMap: null,
                currentMarker: null,
                currentInfo: null,
                fetching: false,
                invalidStyle: false,
            };

            this.initMap = this.initMap.bind(this);
            this.fetchLocation = this.fetchLocation.bind(this);
        }

        componentWillMount() {
            const { attributes, setAttributes } = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-map'];

            // No override attributes of blocks inserted before
            if (attributes.changed !== true) {
                if (typeof currentBlockConfig === 'object' && currentBlockConfig !== null) {
                    Object.keys(currentBlockConfig).map((attribute) => {
                        if (typeof attributes[attribute] === 'boolean') {
                            attributes[attribute] = !!currentBlockConfig[attribute];
                        } else {
                            attributes[attribute] = currentBlockConfig[attribute];
                        }
                    });
                }

                // Finally set changed attribute to true, so we don't modify anything again
                setAttributes( { changed: true } );
            }
        }

        componentDidMount() {
            const { attributes, setAttributes, clientId } = this.props;

            if (!attributes.mapID) {
                setAttributes( { mapID: 'advgbmap-' + clientId } );
            }

            this.initMap();
        }

        componentDidUpdate( prevProps, prevState ) {
            const { address: prevAddr, useLatLng: prevUseLatLng } = prevProps.attributes;
            const { address, useLatLng } = this.props.attributes;

            if (prevAddr !== address || prevUseLatLng !== useLatLng || prevState !== this.state)
                return null;

            if (prevProps.attributes !== this.props.attributes) {
                clearTimeout(mapWillUpdate);
                mapWillUpdate = setTimeout(this.initMap, 1000);
            }
        }

        initMap() {
            if (typeof google === "undefined" || !this.props.attributes.mapID)
                return null;

            const DEFAULT_MARKER = 'https://maps.gstatic.com/mapfiles/api-3/images/spotlight-poi2.png';
            const { currentMap, currentMarker, currentInfo, invalidStyle } = this.state;
            const { mapID, lat, lng, zoom, markerTitle, markerIcon, markerDesc, mapStyle, mapStyleCustom, infoWindowDefaultShown } = this.props.attributes;
            const location = { lat: parseFloat(lat), lng: parseFloat(lng) };
            const that = this;
            const formattedDesc = markerDesc.replace(/\n/g, '<br/>');
            let map = currentMap;
            let marker = currentMarker;
            let infoWindow = currentInfo;
            let customStyleParsed = '';

            if (mapStyle === 'custom') {
                try {
                    customStyleParsed = JSON.parse(mapStyleCustom);
                    if (invalidStyle) that.setState( { invalidStyle: false } );
                } catch (e) {
                    that.setState( { invalidStyle: true } )
                }
            }

            if (!map) {
                map = new google.maps.Map(document.getElementById(mapID), {
                    zoom: zoom,
                    center: location,
                    gestureHandling: 'cooperative',
                });
                this.setState( { currentMap: map } );
            }

            map.setCenter( location );
            map.setZoom( zoom );
            map.setOptions( { styles: !!mapStyle ? mapStyle !== 'custom' ? MAP_STYLES[mapStyle] : customStyleParsed : undefined } );

            if (!infoWindow) {
                infoWindow = new google.maps.InfoWindow( {
                    content: `<div class="advgbmap-wrapper">
                    <h3 class="advgbmap-title">${markerTitle}</h3>
                    <p class="advgbmap-desc">${formattedDesc || ''}</p>
                </div>`,
                    maxWidth: 500,
                } );
                this.setState( { currentInfo: infoWindow } );
            }

            infoWindow.setContent(
                `<div class="advgbmap-wrapper">
                <h3 class="advgbmap-title">${markerTitle}</h3>
                <p class="advgbmap-desc">${formattedDesc || ''}</p>
            </div>`
            );

            if (!marker) {
                marker = new google.maps.Marker( {
                    position: location,
                    map: map,
                    title: markerTitle,
                    draggable: true,
                    animation: google.maps.Animation.DROP,
                    icon: {
                        url: markerIcon || DEFAULT_MARKER,
                        scaledSize: new google.maps.Size( 27, 43 ),
                    },
                } );
                this.setState( { currentMarker: marker } );
            }

            marker.setPosition( location );
            marker.setTitle( markerTitle );
            marker.setIcon( {
                url: markerIcon || DEFAULT_MARKER,
                scaledSize: new google.maps.Size( 27, 43 ),
            } );

            if (!!markerTitle || !!markerDesc) {
                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });

                if (infoWindowDefaultShown) {
                    infoWindow.open(map, marker);
                }
            } else {
                infoWindow.close();
            }

            marker.addListener( 'dragend', function() {
                const newLocation = marker.getPosition();
                const newLat = newLocation.lat();
                const newLng = newLocation.lng();

                that.props.setAttributes( { lat: newLat, lng: newLng } );
            } );
        }

        fetchLocation() {
            if (typeof google === "undefined")
                return null;

            const { attributes, setAttributes } = this.props;
            const { address } = attributes;
            const geoCoder = new google.maps.Geocoder();
            const { OK, ZERO_RESULTS } = google.maps.GeocoderStatus;
            const that = this;

            if (geoCoder) {
                that.setState( { fetching: true } );
                geoCoder.geocode( { address }, function ( res, stt ) {
                    if (stt === OK) {
                        const { location } = res[0].geometry;

                        setAttributes( {
                            lat: location.lat().toString(),
                            lng: location.lng().toString(),
                            currentAddress: res[0].formatted_address,
                        } );
                    } else if (stt === ZERO_RESULTS) {
                        setAttributes( { currentAddress: __( 'No matching address found!', 'advanced-gutenberg' ) } );
                    } else {
                        setAttributes( { currentAddress: stt } );
                    }

                    that.setState( { fetching: false } );
                } )
            }
        }

        render() {
            const { fetching, invalidStyle } = this.state;
            const { attributes, setAttributes } = this.props;
            const {
                mapID,
                useLatLng,
                address,
                currentAddress,
                lat,
                lng,
                zoom,
                height,
                markerIcon,
                markerIconID,
                markerTitle,
                markerDesc,
                mapStyle,
                mapStyleCustom,
                infoWindowDefaultShown,
            } = attributes;

            const listStyles = Object.keys(MAP_STYLES).map( (style) => {
                return {label: style[0].toUpperCase() + style.slice(1), value: style};
            } );

            return (
                <Fragment>
                    {typeof google !== 'undefined' &&
                    <InspectorControls>
                        <PanelBody title={ __( 'Map settings', 'advanced-gutenberg' ) }>
                            {!useLatLng &&
                            <Fragment>
                                <TextControl
                                    label={ [
                                        __( 'Address', 'advanced-gutenberg' ),
                                        <a key="switch-type"
                                           style={ { marginLeft: '10px' } }
                                           onClick={ () => setAttributes( { useLatLng: !useLatLng } ) }
                                        >
                                            { __( 'Use Lat/Lng', 'advanced-gutenberg' ) }
                                        </a>
                                    ] }
                                    value={ address }
                                    placeholder={ __( 'Enter address…', 'advanced-gutenberg' ) }
                                    onChange={ (value) => setAttributes( { address: value } ) }
                                />
                                <div>
                                    <Button className="button button-large" onClick={ this.fetchLocation }>
                                        { __( 'Fetch Location', 'advanced-gutenberg' ) }
                                    </Button>
                                    {fetching && <Spinner /> }
                                    <div style={ { margin: '10px auto' } }>
                                        <strong style={ { marginRight: '5px' } }>{ __( 'Current', 'advanced-gutenberg' ) }:</strong>
                                        <span>{ currentAddress }</span>
                                    </div>
                                </div>
                            </Fragment>
                            }
                            {!!useLatLng &&
                            <Fragment>
                                <TextControl
                                    label={ [
                                        __( 'Location', 'advanced-gutenberg' ),
                                        <a key="switch-type"
                                           style={ { marginLeft: '10px' } }
                                           onClick={ () => setAttributes( { useLatLng: !useLatLng } ) }
                                        >
                                            { __( 'Use Address', 'advanced-gutenberg' ) }
                                        </a>
                                    ] }
                                    value={ lat }
                                    placeholder={ __( 'Enter latitude…', 'advanced-gutenberg' ) }
                                    title={ __( 'Latitude', 'advanced-gutenberg' ) }
                                    onChange={ (value) => setAttributes( { lat: value } ) }
                                />
                                <TextControl
                                    value={ lng }
                                    placeholder={ __( 'Enter longitude…', 'advanced-gutenberg' ) }
                                    title={ __( 'Longitude', 'advanced-gutenberg' ) }
                                    onChange={ (value) => setAttributes( { lng: value } ) }
                                />
                            </Fragment>
                            }
                            <RangeControl
                                label={ __( 'Zoom level', 'advanced-gutenberg' ) }
                                value={ zoom }
                                min={ 0 }
                                max={ 25 }
                                onChange={ (value) => setAttributes( { zoom: value } ) }
                            />
                            <RangeControl
                                label={ __( 'Height', 'advanced-gutenberg' ) }
                                value={ height }
                                min={ 300 }
                                max={ 1000 }
                                onChange={ (value) => setAttributes( { height: value } ) }
                            />
                            <MediaUpload
                                allowedTypes={ ["image"] }
                                value={ markerIconID }
                                onSelect={ (image) => setAttributes( {
                                    markerIcon: image.sizes.thumbnail ? image.sizes.thumbnail.url : image.sizes.full.url,
                                    markerIconID: image.id
                                } ) }
                                render={ ( { open } ) => {
                                    return (
                                        <BaseControl label={ [
                                            __( 'Marker Icon (27x43 px)', 'advanced-gutenberg' ),
                                            markerIcon && (
                                                <a key="marker-icon-remove"
                                                   style={ { marginLeft: '10px', cursor: 'pointer' } }
                                                   onClick={ () => setAttributes( {
                                                       markerIcon: undefined,
                                                       markerIconID: undefined,
                                                   } ) }
                                                >
                                                    { __( 'Remove', 'advanced-gutenberg' ) }
                                                </a>
                                            )
                                        ] }
                                        >
                                            <Button className="button button-large"
                                                    onClick={ open }
                                            >
                                                { __( 'Choose icon', 'advanced-gutenberg' ) }
                                            </Button>
                                            {!!markerIcon &&
                                            <img style={ { maxHeight: '30px', marginLeft: '10px' } }
                                                 src={ markerIcon }
                                                 alt={ __( 'Marker icon', 'advanced-gutenberg' ) }/>
                                            }
                                        </BaseControl>
                                    )
                                } }
                            />
                            <TextControl
                                label={ __( 'Marker Title', 'advanced-gutenberg' ) }
                                value={ markerTitle }
                                placeholder={ __( 'Enter custom title…', 'advanced-gutenberg' ) }
                                onChange={ (value) => setAttributes( { markerTitle: value } ) }
                            />
                            <TextareaControl
                                label={ __( 'Marker description', 'advanced-gutenberg' ) }
                                value={ markerDesc }
                                placeholder={ __( 'Enter custom description…', 'advanced-gutenberg' ) }
                                onChange={ (value) => setAttributes( { markerDesc: value } ) }
                            />
                            <ToggleControl
                                label={ __( 'Open marker tooltip', 'advanced-gutenberg' ) }
                                checked={ infoWindowDefaultShown }
                                onChange={ () => setAttributes({infoWindowDefaultShown: !infoWindowDefaultShown}) }
                            />
                            <SelectControl
                                label={ __( 'Map styles', 'advanced-gutenberg' ) }
                                help={ __( 'Custom map style is recommended for experienced users only.', 'advanced-gutenberg' ) }
                                value={ mapStyle }
                                onChange={ (value) => setAttributes( { mapStyle: value } ) }
                                options={ [
                                    { label: __( 'Standard', 'advanced-gutenberg' ), value: '' },
                                    ...listStyles,
                                    { label: __( 'Custom', 'advanced-gutenberg' ), value: 'custom' },
                                ] }
                            />
                            {mapStyle === 'custom' && (
                                <TextareaControl
                                    label={ [
                                        __( 'Custom code', 'advanced-gutenberg' ),
                                        invalidStyle && (
                                            <span key="invalid-json"
                                                  style={ { fontWeight: 'bold', color: '#ff0000', marginLeft: 5 } }
                                            >
                                                { __( 'Invalid JSON', 'advanced-gutenberg' ) }
                                            </span>
                                        )
                                    ] }
                                    help={ [
                                        __( 'Paste your custom map styles in json format into the text field. You can create your own map styles by follow one of these links: ', 'advanced-gutenberg' ),
                                        <a href="https://mapstyle.withgoogle.com/" target="_blank" key="gg-map">Google Map</a>,
                                        ' - ',
                                        <a href="https://snazzymaps.com/" target="_blank" key="snazzy-map">Snazzy Map</a>
                                    ] }
                                    value={ mapStyleCustom }
                                    placeholder={ __( 'Enter your json code here…', 'advanced-gutenberg' ) }
                                    onChange={ (value) => setAttributes( { mapStyleCustom: value } ) }
                                />
                            ) }
                        </PanelBody>
                    </InspectorControls>
                    }
                    {typeof google !== 'undefined' ?
                        <div className="advgb-map-block">
                            <div className="advgb-map-content" id={ mapID } style={ { height: height } }/>
                        </div>
                        :
                        <Placeholder
                            icon={ mapBlockIcon }
                            label={ __( 'No API Key Provided!', 'advanced-gutenberg' ) }
                            instructions={ __( 'Opps! Look like you have not configured your Google API Key yet. ' +
                                'Add an API Key and refresh the page to start using Map Block. ' +
                                'This is a requirement enforced by Google.' ) }
                        >
                            <a target="_blank"
                               className="button button-large"
                               href={advgbBlocks.config_url + '#settings'}
                            >
                                { __( 'Add Google API Key', 'advanced-gutenberg' ) }
                            </a>
                        </Placeholder>
                    }
                </Fragment>
            )
        }
    }

    const mapBlockAttrs = {
        mapID: {
            type: 'string',
        },
        useLatLng: {
            type: 'boolean',
            default: false,
        },
        address: {
            type: 'string',
            default: '',
        },
        currentAddress: {
            type: 'string',
        },
        lat: {
            type: 'string',
            default: '48.858370',
        },
        lng: {
            type: 'string',
            default: '2.294471',
        },
        zoom: {
            type: 'number',
            default: 14,
        },
        height: {
            type: 'number',
            default: 350,
        },
        markerIcon: {
            type: 'string',
        },
        markerIconID: {
            type: 'number',
        },
        markerTitle: {
            type: 'string',
            default: __( 'Eiffel Tower', 'advanced-gutenberg' ),
        },
        markerDesc: {
            type: 'string',
            default: '',
        },
        changed: {
            type: 'boolean',
            default: false,
        },
    };

    registerBlockType( 'advgb/map', {
        title: __( 'Map', 'advanced-gutenberg' ),
        description: __( 'Block for inserting location map.', 'advanced-gutenberg' ),
        icon: {
            src: mapBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [ __( 'google map', 'advanced-gutenberg' ), __( 'location', 'advanced-gutenberg' ), __( 'address', 'advanced-gutenberg' ) ],
        attributes: {
            ...mapBlockAttrs,
            mapStyle: {
                type: 'string',
            },
            mapStyleCustom: {
                type: 'string',
            },
            infoWindowDefaultShown: {
                type: 'boolean',
                default: true,
            },
        },
        edit: AdvMap,
        save: function ( { attributes } ) {
            const {
                mapID,
                lat,
                lng,
                zoom,
                height,
                markerIcon,
                markerTitle,
                markerDesc,
                mapStyle,
                mapStyleCustom,
                infoWindowDefaultShown,
            } = attributes;

            const formattedDesc = markerDesc.replace( /\n/g, '<br/>' ).replace( /'/, '\\\'' );
            const formattedTitle = markerTitle.replace( /'/, '\\\'' );
            const DEFAULT_MARKER = 'https://maps.gstatic.com/mapfiles/api-3/images/spotlight-poi2.png';
            let mapStyleApply = MAP_STYLES[mapStyle];
            if (mapStyle === 'custom') {
                try {
                    mapStyleApply = JSON.parse(mapStyleCustom);
                } catch (e) {
                    mapStyleApply = '';
                }
            }
            if (mapStyleApply) {
                mapStyleApply = JSON.stringify(mapStyleApply);
            } else {
                mapStyleApply = '';
            }

            return (
                <div className="advgb-map-block" style={ { margin: '10px auto' } }>
                    <div className="advgb-map-content"
                         id={ mapID }
                         style={ { height: height } }
                         data-default={ DEFAULT_MARKER }
                         data-lat={ lat }
                         data-lng={ lng }
                         data-zoom={ zoom }
                         data-title={ formattedTitle }
                         data-desc={ formattedDesc }
                         data-icon={ markerIcon }
                         data-shown={ infoWindowDefaultShown }
                         data-style={ encodeURIComponent(mapStyleApply) }
                    />
                </div>
            );
        },
        deprecated: [
            {
                attributes: {
                    ...mapBlockAttrs,
                    mapStyle: {
                        type: 'string',
                    },
                    mapStyleCustom: {
                        type: 'string',
                    }
                },
                save: function ( { attributes } ) {
                    const {
                        mapID,
                        lat,
                        lng,
                        zoom,
                        height,
                        markerIcon,
                        markerTitle,
                        markerDesc,
                        mapStyle,
                        mapStyleCustom,
                    } = attributes;

                    const formattedDesc = markerDesc.replace( /\n/g, '<br/>' ).replace( /'/, '\\\'' );
                    const formattedTitle = markerTitle.replace( /'/, '\\\'' );
                    const DEFAULT_MARKER = 'https://maps.gstatic.com/mapfiles/api-3/images/spotlight-poi2.png';
                    let mapStyleApply = MAP_STYLES[mapStyle];
                    if (mapStyle === 'custom') {
                        try {
                            mapStyleApply = JSON.parse(mapStyleCustom);
                        } catch (e) {
                            mapStyleApply = '';
                        }
                    }
                    if (mapStyleApply) {
                        mapStyleApply = JSON.stringify(mapStyleApply);
                    } else {
                        mapStyleApply = '';
                    }

                    return (
                        <div className="advgb-map-block" style={ { margin: '10px auto' } }>
                            <div className="advgb-map-content"
                                 id={ mapID }
                                 style={ { height: height } }
                                 data-default={ DEFAULT_MARKER }
                                 data-lat={ lat }
                                 data-lng={ lng }
                                 data-zoom={ zoom }
                                 data-title={ formattedTitle }
                                 data-desc={ formattedDesc }
                                 data-icon={ markerIcon }
                                 data-style={ encodeURIComponent(mapStyleApply) }
                            />
                        </div>
                    );
                },
            },
        ]
    } );
})( wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components );