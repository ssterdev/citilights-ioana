function Noo_Bing_Map() {

    var noo_bing_map = jQuery('.noo_property_bing_map');

    if (noo_bing_map.length > 0) {

        noo_bing_map.each(function(index, el) {

            var $$ = jQuery(this),
                id = $$.data('id'),
                zoom = parseInt(nooBingMap.zoom),
                lat = parseFloat(nooBingMap.latitude),
                lng = parseFloat(nooBingMap.longitude);
            var lat_current = jQuery('#latitude').val(),
                lng_current = jQuery('#longitude').val();
            if (typeof lat_current !== 'undefined' && lat_current !== '') {
                lat = parseFloat(lat_current);
            }

            if (typeof lng_current !== 'undefined' && lng_current !== '') {
                lng = parseFloat(lng_current);
            }

            var latitude = jQuery('#_noo_property_bmap_latitude').val(),
                longitude = jQuery('#_noo_property_bmap_longitude').val();

            if (typeof latitude !== 'undefined' && latitude !== '') {
                lat = parseFloat(latitude);
            }

            if (typeof longitude !== 'undefined' && longitude !== '') {
                lng = parseFloat(longitude);
            }
            var map = new Microsoft.Maps.Map(document.getElementById(id), {
                /* No need to set credentials if already passed in URL */
                center: new Microsoft.Maps.Location(lat, lng),
                zoom: zoom,
                disableScrollWheelZoom: true,
            });
            var center = map.getCenter();
            var Events = Microsoft.Maps.Events;
            var Location = Microsoft.Maps.Location;
            var Pushpin = Microsoft.Maps.Pushpin;
            var pins = [
                new Pushpin(new Location(center.latitude, center.longitude), {
                    icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png',
                    draggable: true
                }),
            ];

            // var pushpin = new Microsoft.Maps.Pushpin(map.getCenter(), { icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png',draggable:true});
            // map.Layer.add(pins);
            Events.addHandler(pins[0], 'dragend', function() {
                displayPinCoordinates(pins);
            });

            Microsoft.Maps.loadModule('Microsoft.Maps.AutoSuggest', function() {
                var options = {
                    maxResults: 4,
                    map: map
                };
                var manager = new Microsoft.Maps.AutosuggestManager(options);
                manager.attachAutosuggest('#noo_property_bing_map_search_input', '.noo_property_bing_map_search', selectedSuggestion);
            });

            function displayPinCoordinates(pins) {
                var pin_location = pins[0].getLocation();
                document.getElementById('_noo_property_bmap_latitude').value = pin_location.latitude;
                document.getElementById('_noo_property_bmap_longitude').value = pin_location.longitude;
            }


            function selectedSuggestion(suggestionResult) {
                var map = new Microsoft.Maps.Map(document.getElementById(id), {
                    /* No need to set credentials if already passed in URL */
                    center: new Microsoft.Maps.Location(suggestionResult.location.latitude, suggestionResult.location.latitude),
                    zoom: zoom,
                });
                map.entities.clear();
                map.setView({
                    bounds: suggestionResult.bestView
                });
                var center = map.getCenter();
                var pushpin = [
                    new Pushpin(new Location(suggestionResult.location.latitude, suggestionResult.location.longitude), {
                        icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png',
                        draggable: true
                    })
                    // new Pushpin(suggestionResult.location,{ icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png',draggable:true})
                ];
                map.entities.push(pushpin);
                Events.addHandler(pushpin[0], 'dragend', function() {
                    displayPinCoordinates(pushpin);
                });
                document.getElementById('_noo_property_bmap_latitude').value = suggestionResult.location.latitude;
                document.getElementById('_noo_property_bmap_longitude').value = suggestionResult.location.longitude;
            }
        });

    }
    var noo_location = jQuery('.bmap');

    if (noo_location.length > 0) {

        // noo_location.each(function(index, el) {

            var $$ = jQuery('.noo-map-search'),
                id = $$.find('.bmap').data('id'),
                source = $$.find('.bmap').data('source'),
                lat = parseFloat(nooGmapL10n.latitude),
                lng = parseFloat(nooGmapL10n.longitude),
                markers = [],
                zoom = 0;
            var map = new Microsoft.Maps.Map(document.getElementById(id), {
                /* No need to set credentials if already passed in URL */
                center: new Microsoft.Maps.Location(lat, lng),
                zoom: zoom,
                disableScrollWheelZoom: true,
            });
            $$.find('.gmap-loading').hide();
            $$.find('.gmap-loader').hide();

            if(source == 'IDX'){
                var searchParams = getSearchParams();
                if ( ('object' === typeof(dsidx)) && !$.isEmptyObject(dsidx.dataSets) ) {

                    var idxCode = null;
                    $.each(dsidx.dataSets, function ( i, e ) {
                        if(i){
                            idxCode = i;
                            return false;
                        }
                        
                    });
                    for ( var i = 0; i < dsidx.dataSets[ idxCode ].length; i++ ) {

                        var marker = dsidx.dataSets[ idxCode ][ i ];

                        var markerPlace = new google.maps.LatLng(marker.Latitude, marker.Longitude);

                        if(typeof marker.ShortDescription !== 'undefined'){
                            var info_address = marker.ShortDescription.split(',');
                            marker.city = info_address[ 1 ];
                            marker.title = info_address[ 0 ] + ', ' + info_address[ 1 ];
                        }else{
                            marker.title = marker.Address;
                            marker.city = marker.City;
                        }
                        marker.latitude = marker.Latitude;
                        marker.longitude = marker.Longitude;
                        marker.bathrooms = parseInt(( marker.BedsShortString ).charAt(0));
                        marker.bedrooms = parseInt(( marker.BathsShortString ).charAt(0));
                        marker.url = nooGmapL10n.url_idx + marker.PrettyUriForUrl;
                        marker.image = '<img src="' + marker.PhotoUriBase + marker.PhotoFileName + '" alt="'+ marker.title +'">';
                        marker.price = parseFloat((marker.Price).replace(/[^\d.]/g, ''));
                        marker.price_html = marker.Price;
                        marker.area = marker.ImprovedSqFt + ' ' + nooGmapL10n.area_unit;
                        marker.icon = nooGmapL10n.theme_uri + '/assets/images/marker-icon.png';
                        
                        markers.push(marker);
                    }
                }
            }else{
                var markers = JSON.parse(nooGmapL10n.markers);
            }
             infobox = new Microsoft.Maps.Infobox(map.getCenter(), {
                visible: false
            });

            //Assign the infobox to a map instance.
            infobox.setMap(map);

            request_data_form();

            get_list_markers(map, markers);

            function get_list_markers(map, markers) {
                Microsoft.Maps.loadModule("Microsoft.Maps.Clustering", function(e) {
                    var pins = [];
                    var searchParams = getSearchParams();
                    for (var j = 0; j < markers.length; j++) {
                        if ((markers[j]['latitude'] == '') && (markers[j]['longitude'] == '')) {
                            continue;
                        };
                        var icon_markers = markers[j].icon;
                        var type_markers = markers[j].category;
                        if (type_markers == '') {
                            icon_markers = nooGmapL10n.theme_uri + '/assets/images/marker-icon.png';
                        }
                        var pin = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(markers[j].latitude, markers[j].longitude), {
                            icon: icon_markers
                        });
                        pin.Title = '<h6><a href="' + markers[j].url + '">' + markers[j].title + '</a></h6>';
                        pin.Description = '<div class="bmap-infobox">\
                                <div class="info-img"><a href="' + markers[j].url + '">' + markers[j].image + '</a></div> \
                                <div class="info-summary"> \
                                    <h5 class="info-title"><a href="' + markers[j].url + '">' + markers[j].title + '</a></h5>';
                        pin.Description += '<div class="info-detail">' +
                            '<div class="bedrooms">' +
                            '<span class="property-meta-icon" style="background-image: url(' + NooPropertyBingMap.icon_bedrooms + ');"></span>' +
                            '<span class="property-meta">' + markers[j].bedrooms + '</span>' +
                            '</div>' +
                            '<div class="bathrooms">' +
                            '<span class="property-meta-icon" style="background-image: url(' + NooPropertyBingMap.icon_bathrooms + ');"></span>' +
                            '<span class="property-meta">' + markers[j].bathrooms + '</span>' +
                            '</div>' +
                            '<div class="area">' +
                            '<span class="property-meta-icon" style="background-image: url(' + NooPropertyBingMap.icon_area + ');"></span>' +
                            '<span class="property-meta">' + markers[j].area + '</span>' +
                            '</div>' +
                            '</div>';
                        pin.Description += '<div class="info-more"> \
                                <div class="info-price">' + markers[j].price_html + '</div> \
                                <div class="info-action"><a href="' + markers[j].url + '"><i class="fa fa-plus"></i></a></div>';
                        pin.Description += '</div>\
                            </div>';

                        Microsoft.Maps.Events.addHandler(pin, 'click', displayInfobox);

                        Microsoft.Maps.Events.addHandler(pin, 'click', 

                            function (args) {
                                return map.setView({
                                    center: args.target.getLocation(),
                                    zoom: map.getZoom() + 1
                                });
                            }
                        );
                        // dataLayer.push(pin);
                        pin.metadata = markers[j];
                        if(setMarkerVisible(pin.metadata,searchParams)){
                            pins.push(pin);
                        }
                    }
                        clusterLayer = new Microsoft.Maps.ClusterLayer(pins, {
                        clusteredPinCallback: customizeClusteredPin
                    });
                    map.layers.insert(clusterLayer);
                });
            }
            var infobox;

            function close() {
                document.close();
            }

            function displayInfobox(e) {
                if (e.targetType == 'pushpin') {
                    infobox.setLocation(e.target.getLocation());
                    infobox.setOptions({
                        visible: true,
                        description: e.target.Description,
                        maxWidth: 480,
                        maxHeight: 300
                    });
                }
            }

            function customizeClusteredPin(cluster) {
                var minRadius = 30;

                var url = nooGmapL10n.theme_uri + '/assets/images/cloud.png';
                var clusterSize = cluster.containedPushpins.length;
                var radius = Math.log(clusterSize) / Math.log(10) * 5 + minRadius;
                cluster.setOptions({
                    icon: url,
                    textOffset: new Microsoft.Maps.Point(0, radius - 5)
                });
                // Add click event to clustered pushpin
                Microsoft.Maps.Events.addHandler(cluster, 'click', pushpinClicked);
            }

            function pushpinClicked(e) {
                //Show an infobox when a pushpin is clicked.

                if (e.target.containedPushpins) {
                    var locs = [];
                    for (var i = 0, len = e.target.containedPushpins.length; i < len; i++) {
                        //Get the location of each pushpin.
                        locs.push(e.target.containedPushpins[i].getLocation());
                    }
                    //Create a bounding box for the pushpins.
                    var bounds = Microsoft.Maps.LocationRect.fromLocations(locs);
                    //Zoom into the bounding box of the cluster. 
                    //Add a padding to compensate for the pixel area of the pushpins.
                    map.setOptions({
                        maxZoom: 20
                    });
                    map.setView({
                        bounds: bounds,
                        padding: 100
                    });
                    if (bounds.width == 0) {
                        showInfobox(e.target);
                    }
                }
            }

            function showInfobox(pin) {
                var description = [];

                // Check to see if the pushpin is a cluster.
                if (pin.containedPushpins) {
                    //Create a list of all pushpins that are in the cluster.
                    description.push('<div style="overflow-y:auto;"><ul class="bmap-listCluster" style="list-style:none;"');
                    for (var i = 0; i < pin.containedPushpins.length; i++) {
                        description.push('<li><h6><a href="' + pin.containedPushpins[i].metadata.url + '">', pin.containedPushpins[i].metadata.title, '</a></h6><div><a href="' + pin.containedPushpins[i].metadata.url + '">' + pin.containedPushpins[i].metadata.image + '</div></li>');
                    }
                    description.push('</ul></div>');
                }

                //Display an infobox for the pushpin.
                infobox.setOptions({
                    title: pin.getTitle(),
                    location: pin.getLocation(),
                    description: description.join(''),
                    visible: true,
                    maxWidth: 500,
                    maxHeight: 280
                });
            }
            function getSearchParams() {
                return $('.gsearchform').serializeArray();
            }
            /* Form Ajax serch request map */
            function request_data_form() {
                if(source == 'IDX'){
                    jQuery('.gsearch').find('.dropdown-menu > li > a').on('click',function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        var dropdown = jQuery(this).closest('.dropdown'),
                            val = jQuery(this).data('value');
                        dropdown.children('input').val(val);
                        dropdown.children('input').trigger('change');
                        dropdown.children('[data-toggle="dropdown"]').text($(this).text());

                        // dropdown.children('[data-toggle="dropdown"]').dropdown('toggle');
                    });
                    if(jQuery('.noo-map').length){
                        jQuery('.noo-map').each(function(){
                            if(!jQuery(this).hasClass('no-gmap')){
                                jQuery(this).find('.gsearch').find('.gsearch-field').find(':input').on('change',function() {
                                    getRequestNewMarker();
                                });
                            }
                        });

                    }
                }else{
                    jQuery('.gsearchform').find('.gsearch-field').find(':input').on('change',function() {
                        // initialization new markers
                        getRequestNewMarker();
                        
                    });
                }                

            }
            function getRequestNewMarker(){
                var new_markers = []; new_markers.length = 0;
                // Clear old clusterLayer to update new clusterLayer
                clusterLayer.clear();
                if ( markers.length > 0 ) {

                    var searchParams = getSearchParams();
                    var total_property = 0;
                    for ( var i = 0; i < markers.length; i++ ) {

                        var marker = markers[ i ];
                        if ( setMarkerVisible(marker, searchParams) ) {                     
                            new_markers.push(marker);
                            total_property++;
                        }

                    }
                    if(Array.isArray(new_markers) && new_markers.length !== 0){
                        if ( (new_markers.length >= total_property )) {
                            var bounds = Microsoft.Maps.LocationRect.fromLocations(new_markers);
                            map.setView({
                                bounds: bounds,
                            });
                            map.setOptions({
                                maxZoom: 2
                            });
                        }
                        get_list_markers(map,new_markers);

                    }
                }
            }
            function setMarkerVisible( bmarkers, searchParams ) {
                if ( bmarkers == null || typeof bmarkers === "undefined" ) {
                    return false;
                }
                if ( searchParams == null || typeof searchParams === "undefined" ) {
                    return false;
                }
                var end_point = false;
                $.each(searchParams, function ( name, value ) {

                    if ( searchParams[ name ].name == null || typeof searchParams[ name ].name === "undefined" ) {
                        return false;
                    }

                    if ( searchParams[ name ].value == null || typeof searchParams[ name ].value === "undefined" ) {
                        return false;
                    }

                    var name_field = (searchParams[ name ].name).toLocaleLowerCase();
                    var value_field = (searchParams[ name ].value).toLocaleLowerCase();

                    if(name_field.indexOf('[]') !== -1) name_field = name_field.replace('[]','');

                    if ( source === 'IDX' ) {
                        if ( name_field === 'idx-q-locations' && (bmarkers.title).toLocaleLowerCase().indexOf( value_field ) === -1) {
                            
                            end_point = true;
                            return false;
                        }
                        if ( name_field === 'idx-q-pricemin' && parseInt(bmarkers.price) < parseInt(value_field) ) {
                            
                            end_point = true;
                            return false;
                        }
                        if ( name_field === 'idx-q-pricemax' && parseInt(bmarkers.price) > parseInt(value_field) ) {
                            
                            end_point = true;
                            return false;
                        }

                        if ( name_field === 'idx-q-bathsmin' && parseInt(bmarkers.bathrooms) < parseInt(value_field) ) {
                            
                            end_point = true;
                            return false;
                        }

                        if ( name_field === 'idx-q-bedsmin' && parseInt(bmarkers.bedrooms) < parseInt(value_field) ) {
                            
                            end_point = true;
                            return false;
                        }

                    } else if ( source === 'property' ) {
                        if ( value_field !== '' && name_field === 'keyword' ) {
                            if((bmarkers.title).toLocaleLowerCase().indexOf( value_field ) == -1){
                                
                                end_point = true;
                                return false;
                            }
                        }
                        if ( name_field === 'location' ) {
                            // name_field = 'location';
                            value_field = value_field.replace(/ /g, '-');
                        }
                        if ( name_field === 'city' ) {
                            value_field = value_field.replace(/ /g, '-');
                        }

                        if ( value_field !== '' && name_field === 'keyword' ) {
                            if((bmarkers.title).toLocaleLowerCase().indexOf( value_field ) === -1){
                                
                                end_point = true;
                                return false;
                            }
                        }

                        if ( name_field === 'min_price' && value_field !== '' ) {
                            if(parseInt(bmarkers.price) < parseInt(value_field)){
                                end_point = true;
                                return false;
                            }
                            
                        } else if ( name_field === 'max_price' && value_field !== '' ) {
                            if(parseInt(bmarkers.price) > parseInt(value_field)){
                                end_point = true;
                                return false;
                            }
                            
                        }else if ( name_field === 'min_area' && value_field !== '' ) {
                            if(parseInt(bmarkers.area) < parseInt(value_field)){
                                end_point = true;
                                return false;
                            }
                        }else if ( name_field === 'max_area' && value_field !== '' ) {
                            if(parseInt(bmarkers.area) > parseInt(value_field)){
                                end_point = true;
                                return false;
                            }
                        }else if ( name_field === 'bathrooms' && value_field !== '' ) {

                            if(parseInt(bmarkers.bathrooms) < parseInt(value_field)){
                                end_point = true;
                                return false;
                            }
                        }else if( name_field === 'bedrooms' && value_field !== '' ) {

                            if(parseInt(bmarkers.bedrooms) < parseInt(value_field)){
                                end_point = true;
                                return false;
                            }
                            
                        }else if ( value_field !== '' && value_field !== null && typeof bmarkers[ name_field ] !== 'undefined' ) {
                            var value_marker;
                            /**
                             * Check field is array
                             */
                            if ( $.isArray(bmarkers[ name_field ]) ) {
                                /**
                                 * Check field status
                                 */
                                if ( name_field === 'category' ) {
                                    if((bmarkers[ 'category' ]).indexOf( value_field ) === -1){
                                        end_point = true;
                                        return false;
                                    }
                                }else if ( name_field === 'location') {
                                    if((bmarkers[ 'location' ]).indexOf( value_field ) === -1){
                                        end_point = true;
                                        return false;
                                    }
                                }else if( $.inArray(value_field,bmarkers[name_field]) === -1 ) {
                                    
                                    end_point = true;
                                    return false;
                                }
                            } 
                            else{

                                if ( ( name_field === 'location') ) {
                                    value_marker = convert_string((bmarkers.location).toString().toLocaleLowerCase().replace(/ /g, '-'));
                                    if ( value_marker !== value_field ) {
                                        
                                        end_point = true;
                                        return false;
                                    }
                                } else if (value_field !== bmarkers[name_field]) {
                                    end_point = true;
                                    return false;
                                }
                            }

                        }

                    }

                });

                if ( end_point ) {
                    
                    return false;
                }

                return true;

            }
            function convert_string(str) {
                str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
                str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
                str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
                str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
                str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
                str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
                str = str.replace(/đ/g, "d");
                str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
                str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
                str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
                str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
                str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
                str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
                str = str.replace(/Đ/g, "D");
                return str;
            }

        // });
    }
}