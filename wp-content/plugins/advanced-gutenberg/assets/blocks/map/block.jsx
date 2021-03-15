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

    const previewImageData = 'data:image/png;base64,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';

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
                isPreview,
            } = attributes;

            const listStyles = Object.keys(MAP_STYLES).map( (style) => {
                return {label: style[0].toUpperCase() + style.slice(1), value: style};
            } );

            return (
                isPreview ?
                    <img alt={__('Map', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
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
            default: 'Eiffel Tower',
        },
        markerDesc: {
            type: 'string',
            default: '',
        },
        changed: {
            type: 'boolean',
            default: false,
        },
        isPreview: {
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
        example: {
            attributes: {
                isPreview: true
            },
        },
        supports: {
            anchor: true
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