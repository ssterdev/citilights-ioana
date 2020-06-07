//global infoBox
var infoBox;
//global map
var map;
// global list properties
var gmarkers = [];
// global cureent properties index
var gmarker_index = 1;
// global map search box
var mapSearchBox;
// global MarkerClusterer
var mcluster;
function noo_number_format(number, decimals, dec_point, thousands_sep) {
	number = (number + '')
		.replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
	    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	    s = '',
	    toFixedFix = function(n, prec) {
		    var k = Math.pow(10, prec);
		    return '' + (Math.round(n * k) / k)
			    .toFixed(prec);
	    };
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
		.split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '')
			.length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1)
			.join('0');
	}
	return s.join(dec);
}
;(function($){
	"use strict";
	// $.fn.nooLoadmore = function(options,callback){
	// 	var defaults = {
	// 		agentID:0,
	// 		contentSelector: null,
	// 		contentWrapper:null,
	// 		nextSelector: "div.navigation a:first",
	// 		navSelector: "div.navigation",
	// 		itemSelector: "div.post",
	// 		dataType: 'html',
	// 		finishedMsg: "<em>Congratulations, you've reached the end of the internet.</em>",
	// 		loading:{
	// 			speed:'fast',
	// 			start: undefined
	// 		},
	// 		state: {
	// 			isDuringAjax: false,
	// 			isInvalidPage: false,
	// 			isDestroyed: false,
	// 			isDone: false, // For when it goes all the way through the archive.
	// 			isPaused: false,
	// 			isBeyondMaxPage: false,
	// 			currPage: 1
	// 		}
	// 	};
	// 	var options = $.extend(defaults, options);

	// 	return this.each(function(){
	// 		var self = this;
	// 		var $this = $(this),
	// 		    wrapper = $this.find('.loadmore-wrap'),
	// 		    action = $this.find('.loadmore-action'),
	// 		    btn = action.find(".btn-loadmore"),
	// 		    loading = action.find('.loadmore-loading');

	// 		options.contentWrapper = options.contentWrapper || wrapper;



	// 		var _determinepath = function(path){
	// 			if (path.match(/^(.*?)\b2\b(.*?$)/)) {
	// 				path = path.match(/^(.*?)\b2\b(.*?$)/).slice(1);
	// 			} else if (path.match(/^(.*?)2(.*?$)/)) {
	// 				if (path.match(/^(.*?page=)2(\/.*|$)/)) {
	// 					path = path.match(/^(.*?page=)2(\/.*|$)/).slice(1);
	// 					return path;
	// 				}
	// 				path = path.match(/^(.*?)2(.*?$)/).slice(1);

	// 			} else {
	// 				if (path.match(/^(.*?page=)1(\/.*|$)/)) {
	// 					path = path.match(/^(.*?page=)1(\/.*|$)/).slice(1);
	// 					return path;
	// 				} else {
	// 					options.state.isInvalidPage = true;
	// 				}
	// 			}
	// 			return path;
	// 		}
	// 		var path = $(options.nextSelector).attr('href');
	// 		path = _determinepath(path);

	// 		// callback loading
	// 		options.callback = function(data, url) {
	// 			if (callback) {
	// 				callback.call($(options.contentSelector)[0], data, options, url);
	// 			}
	// 		};

	// 		options.loading.start = options.loading.start || function() {
	// 			btn.hide();
	// 			$(options.navSelector).hide();
	// 			loading.show(options.loading.speed, $.proxy(function() {
	// 				loadAjax(options);
	// 			}, self));
	// 		};

	// 		var loadAjax = function(options){
	// 			var callback=options.callback,
	// 			    desturl,frag,box,children,data;

	// 			options.state.currPage++;
	// 			// Manually control maximum page
	// 			if ( options.maxPage !== undefined && options.state.currPage > options.maxPage ){
	// 				options.state.isBeyondMaxPage = true;
	// 				return;
	// 			}
	// 			desturl = path.join(options.state.currPage);
	// 			$.post(nooGmapL10n.ajax_url,{
	// 				action:'noo_agent_ajax_property',
	// 				page:options.state.currPage,
	// 				agent_id:options.agentID
	// 			},function(res){

	// 				if(res.content != '' && res.content !=null && res.content != undefined){
	// 					$(options.contentWrapper).append(res.content);
	// 					loading.hide();
	// 					btn.show(options.loading.speed);
	// 				}else{
	// 					btn.hide();
	// 					action.append('<div style="margin-top:5px;">' + options.finishedMsg + '</div>').animate({ opacity: 1 }, 2000, function () {
	// 						action.fadeOut(options.loading.speed);
	// 					});
	// 					return ;
	// 				}
	// 			},'json');
	// 		}


	// 		btn.on('click',function(e){
	// 			e.stopPropagation();
	// 			e.preventDefault();
	// 			options.loading.start.call($(options.contentWrapper)[0],options);
	// 		});
	// 	});
	// };

	function search_initialize(){
		mapSearchBox = $('.noo-map');
		var mapBox = mapSearchBox.find('#gmap'),
		    latitude = nooGmapL10n.latitude,
		    longitude = nooGmapL10n.longitude;
		if(mapSearchBox.length && mapBox.length){
			var myPlace    = new google.maps.LatLng(latitude,longitude);
			var myOptions = {
				flat:false,
				noClear:false,
				zoom: parseInt(nooGmapL10n.zoom),
				scrollwheel: false,
				streetViewControl:false,
				disableDefaultUI: true,
				draggable: (Modernizr.touch ? false  : nooGmapL10n.draggable),
				center: myPlace,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			map = new google.maps.Map(mapBox.get(0),myOptions );
			google.maps.visualRefresh = true;

			google.maps.event.addListener(map, 'tilesloaded', function() {
				mapSearchBox.find('.gmap-loading').hide();
			});

			var input = document.getElementById('gmap_search_input');
			map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
			var searchBox = new google.maps.places.SearchBox(input);
			google.maps.event.addListener(searchBox, 'places_changed', function() {
				var places = searchBox.getPlaces();

				if (places.length == 0) {
					return;
				}
				var bounds = new google.maps.LatLngBounds();
				for (var i = 0, place; place = places[i]; i++) {
					// Create a marker for each place.
					var _marker = new google.maps.Marker({
						map: map,
						zoom:parseInt(nooGmapL10n.zoom),
						title: place.name,
						position: place.geometry.location
					});
					bounds.extend(place.geometry.location);
				}
				map.fitBounds(bounds);
				map.setZoom(parseInt(nooGmapL10n.zoom));
			});

			var infoboxOptions = {
				content: document.createElement("div"),
				disableAutoPan: true,
				maxWidth: 500,
				boxClass:"myinfobox",
				zIndex: null,
				closeBoxMargin: "-13px 0px 0px 0px",
				closeBoxURL: "",
				infoBoxClearance: new google.maps.Size(1, 1),
				isHidden: false,
				pane: "floatPane",
				enableEventPropagation: false
			};
			infoBox = new InfoBox(infoboxOptions);

			var clickMarkerListener = function(maker) {
				var infoContent = '<div class="gmap-infobox"><a class="info-close" onclick="return infoBox.close();" href="javascript:void(0)">x</a>\
					 <div class="info-img"><a href="' + maker.url + '">' + maker.image + '</a></div> \
						 <div class="info-summary"> \
						 	<h5 class="info-title"><a href="' + maker.url + '">' + maker.title + '</a></h5>';
				// if( maker.info_summary && maker.info_summary.length ) {
				// 	infoContent += '<div class="info-detail">';
				// 	$.each( maker.info_summary, function( i, v ) {
				// 		infoContent += '<div class="' + v.class + '"><span class="property-meta-icon" style="background-image: url(' + v.icon + ')"></span><span class="property-meta">' + v.value + '</span></div>';
				// 	} );

				// 	infoContent += "<div/>";
				// }

				if( maker.info_summary != '' ) {
					infoContent += maker.info_summary;
				}

				infoContent += '<div class="info-more"> \
					 			<div class="info-price">' + maker.price_html + '</div> \
					 			<div class="info-action"><a href="' + maker.url + '"><i class="fa fa-plus"></i></a></div> \
					 		</div> \
					 	</div> \
				 	</div>';
				infoBox.setContent(infoContent);
				infoBox.open(map,maker);
				map.setCenter(maker.position);
				map.panBy(50,-120);
			}

			//create markers
			if( $( mapBox ).data( 'source' ) == 'IDX' ){
				if( (typeof(dsidx) == 'object') && !$.isEmptyObject( dsidx.dataSets ) ){
					var idxCode = null;
					var bounds = new google.maps.LatLngBounds();
					var searchParams = getSearchParams();
					$.each(dsidx.dataSets,function(i,e){
						idxCode = i;
					});
					for (var i = 0; i < dsidx.dataSets[idxCode].length; i++) {
						var marker = dsidx.dataSets[idxCode][i];
						if (marker.ShortDescription !== undefined){
							var title_arr = marker.ShortDescription.split(",");
							var title = title_arr[0] +', '+ title_arr[1];
						}
						else{
							var title = marker.Address + ", " + marker.City;
						}
						var num_bed   = parseInt( (marker.BedsShortString).charAt(0) ),
						    num_bath  = parseInt( (marker.BathsShortString).charAt(0) ),
						    idx_price = parseFloat( (marker.Price).replace( /[^\d.]/g, '' ) );

						var info_summary = '<div class="info-detail">' +
							'<div class="bedrooms">' +
							'<span class="property-meta-icon" style="background-image: url(' + NooPropertyMap.icon_bedrooms + ');"></span>' +
							'<span class="property-meta">' + num_bed + '</span>' +
							'</div>' +
							'<div class="bathrooms">' +
							'<span class="property-meta-icon" style="background-image: url(' + NooPropertyMap.icon_bathrooms + ');"></span>' +
							'<span class="property-meta">' + num_bath + '</span>' +
							'</div>' +
							'<div class="area">' +
							'<span class="property-meta-icon" style="background-image: url(' + NooPropertyMap.icon_area + ');"></span>' +
							'<span class="property-meta">' + marker.ImprovedSqFt+' '+nooGmapL10n.area_unit + '</span>' +
							'</div>' +
							'</div>';

						var markerPlace = new google.maps.LatLng(marker.Latitude,marker.Longitude);
						var gmarker = new google.maps.Marker({
							position: markerPlace,
							map: map,
							area: marker.ImprovedSqFt+' '+nooGmapL10n.area_unit,
							image: '<img src="' + marker.PhotoUriBase + marker.PhotoFileName + '" />',
							title: title,
							bedrooms: num_bed,
							bathrooms: num_bath,
							info_summary: info_summary,
							price: idx_price,
							price_html:marker.Price,
							url: nooGmapL10n.home_url + '/idx/' + marker.PrettyUriForUrl
						});
						gmarker.setIcon(nooGmapL10n.theme_uri + '/assets/images/marker-icon.png');
						gmarkers.push(gmarker);
						if( setMarkerVisible( gmarker, searchParams ) ) {
							bounds.extend( gmarker.getPosition() );
							if( nooGmapL10n.fitbounds ) map.fitBounds(bounds);
						}
						google.maps.event.addListener(gmarker, 'click', function(e) {
							clickMarkerListener(this);
						});
					}
					// console.log('IDX old');
				} else {
					// console.log('IDX new');
					for (var i = 0; i < Noo_Source_IDX.length; i++) {
						var marker = Noo_Source_IDX[i];
						var num_bed = marker.noo_property_bedrooms;
						var num_bath = marker.noo_property_bathrooms;
						var idx_price = parseFloat( (marker.Price).replace( /[^\d.]/g, '' ) );

						var markerPlace = new google.maps.LatLng(marker.Latitude,marker.Longitude);
						var gmarker = new google.maps.Marker({
							position: markerPlace,
							map: map,
							area: marker.noo_property_area,
							image:	marker.image,
							title: marker.title,
							bedrooms: num_bed,
							bathrooms: num_bath,
							info_summary: marker.info_summary,
							price: idx_price,
							price_html:marker.Price,
							url: marker.url
						});
						gmarker.setIcon(nooGmapL10n.theme_uri + '/assets/images/marker-icon.png');
						gmarkers.push(gmarker);
						if( setMarkerVisible( gmarker, searchParams ) ) {
							bounds.extend( gmarker.getPosition() );
							if( nooGmapL10n.fitbounds ) map.fitBounds(bounds);
						}
						google.maps.event.addListener(gmarker, 'click', function(e) {
							clickMarkerListener(this);
						});
					}

				}

			}else{
				var markers = $.parseJSON(nooGmapL10n.markers);
				if(markers.length){
					var bounds = new google.maps.LatLngBounds();
					var searchParams = getSearchParams();

					for(var i = 0; i < markers.length ; i ++){
						var marker = markers[i];

						var markerPlace = new google.maps.LatLng(marker.latitude,marker.longitude);
						var points_map = {
							position: new google.maps.LatLng(marker.latitude, marker.longitude),
							map: map
						}

						$.extend( points_map, marker );

						// var gmarker = new google.maps.Marker(points_map);
						// var gmarker = new google.maps.Marker({
						// 	position: new google.maps.LatLng(marker.latitude, marker.longitude),
						// 	// position: {lat: marker.latitude, lng: marker.longitude},
						// 	map     : map
						// });
						// ==== Old
						var gmarker = new google.maps.Marker({
							position: markerPlace,
							map: map,
							image:	marker.image,
							title: marker.title,
							area:	marker.area,
							bedrooms: marker.bedrooms,
							agent_search: marker.agent_search,
							bathrooms: marker.bathrooms,
							price: marker.price,
							price_html:marker.price_html,
							info_summary: marker.info_summary,
							url: marker.url,
							category:marker.category,
							status:marker.status,
							sub_location:marker.sub_location,
							location:marker.location,
							year_built:marker.year_built
						});

						if(marker.icon != ''){
							gmarker.setIcon(marker.icon);
						}
						gmarkers.push(gmarker);
						// console.log(setMarkerVisible( gmarker, searchParams ));
						if( setMarkerVisible( gmarker, searchParams ) ) {
							bounds.extend( gmarker.getPosition() );
							if( nooGmapL10n.fitbounds ) map.fitBounds(bounds);
						}
						google.maps.event.addListener(gmarker, 'click', function(e) {
							clickMarkerListener(this);
						});
					}
				}
			}

			//MarkerClustererPlus
			var clusterStyles = [{
				textColor: '#ffffff',
				opt_textColor: '#ffffff',
				url: nooGmapL10n.theme_uri+'/assets/images/cloud.png',
				height: 72,
				width: 72,
				textSize:15
			}
			];
			mcluster = new MarkerClusterer(map, gmarkers,{
				gridSize: 50,
				ignoreHidden:true,
				styles: clusterStyles,
				maxZoom: 20
			});
			// mcluster.setIgnoreHidden(true);
			google.maps.event.addListener(mcluster, 'clusterclick', function(cluster){
			    map.setCenter(cluster.getCenter());
			    map.setZoom(map.getZoom() + 3);
			});


			//zoom in action
			if(mapSearchBox.find('.zoom-in').length){
				google.maps.event.addDomListener(mapSearchBox.find('.zoom-in').get(0), 'click', function (e) {
					e.stopPropagation();
					e.preventDefault();
					var current= parseInt( map.getZoom(),10);
					current++;
					if(current>20){
						current=20;
					}
					map.setZoom(current);
				});

			}

			// zoom out action
			if(mapSearchBox.find('.zoom-out').length){
				google.maps.event.addDomListener(mapSearchBox.find('.zoom-out').get(0), 'click', function (e) {
					e.stopPropagation();
					e.preventDefault();
					var current= parseInt( map.getZoom(),10);
					current--;
					if(current<0){
						current=0;
					}
					map.setZoom(current);
				});

			}

			if($('.gsearch').length){
				$('.gsearch').find('.dropdown-menu > li > a').on('click',function(e){
					e.stopPropagation();
					e.preventDefault();
					var dropdown = $(this).closest('.dropdown'),
					    val = $(this).data('value');
					dropdown.children('input').val(val);
					dropdown.children('input').trigger('change');
					dropdown.children('[data-toggle="dropdown"]').text($(this).text());

					dropdown.children('[data-toggle="dropdown"]').dropdown('toggle');
				});
				if($('.noo-map').length){
					$('.noo-map').each(function(){
						if(!$(this).hasClass('no-gmap')){
							$(this).find('.gsearch').find('.gsearch-field').find(':input').on('change',function() {
								search_filter();
							});
						}
					});

				}
			}
		}
	}

	google.maps.event.addDomListener(window, 'load', search_initialize);

	function mylocationCallback(pos){
		var shape = {
			coord: [1, 1, 1, 38, 38, 59, 59 , 1],
			type: 'poly'
		};
		if( map.getZoom() != 15 ){
			map.setZoom(15);
		}
		var myLocation =  new google.maps.LatLng( pos.coords.latitude, pos.coords.longitude);
		map.setCenter(myLocation);
		var marker = new google.maps.Marker({
			position: myLocation,
			map: map,
			icon: nooGmapL10n.theme_uri+'/assets/images/my-marker.png',
			shape: shape,
			zIndex: 9999,
			infoWindowIndex : 9999,
			radius: 1000
		});

		var circleOptions = {
			strokeColor: '#75b08a',
			strokeOpacity: 0.6,
			strokeWeight: 1,
			fillColor: '#75b08a',
			fillOpacity: 0.2,
			map: map,
			center: myLocation,
			radius: 1000
		};
		var cityCircle = new google.maps.Circle(circleOptions);

	}
	function mylocationError(){
		alert(nooGmapL10n.no_geolocation_pos);
	}

	$(document).on('click','.gmap-mylocation',function(e){
		e.stopPropagation();
		e.preventDefault();
		if(navigator.geolocation){
			navigator.geolocation.getCurrentPosition(mylocationCallback,mylocationError,{timeout:10000});
		}
		else{
			alert(nooGmapL10n.no_geolocation_msg);
		}
	});

	$(document).on('click','.gmap-full',function(e){
		e.stopPropagation();
		e.preventDefault();
		if($(this).closest('.noo-map').hasClass('fullscreen')){
			$(this).closest('.noo-map').removeClass('fullscreen');
			$(this).empty().html('<i class="fa fa-expand"></i> '+nooGmapL10n.fullscreen_label);
			if ( Modernizr.touch ) {
				map.setOptions({draggable: false});
			}
		}else{
			$(this).closest('.noo-map').addClass('fullscreen');
			$(this).empty().html('<i class="fa fa-compress"></i> '+nooGmapL10n.default_label);
			if ( Modernizr.touch ) {
				map.setOptions({draggable: true});
			}
		}
		google.maps.event.trigger(map, "resize");
	});

	$(document).on('click','.gmap-prev',function(e){
		e.stopPropagation();
		e.preventDefault();
		gmarker_index -- ;
		if (gmarker_index < 1){
			gmarker_index = gmarkers.length;
		}
		while(gmarkers[gmarker_index - 1].visible === false){
			gmarker_index--;
			if (gmarker_index > gmarkers.length){
				gmarker_index = 1;
			}
		}

		if( map.getZoom() <15 ){
			map.setZoom(15);
		}
		google.maps.event.trigger(gmarkers[gmarker_index - 1], 'click');
	});

	$(document).on('click','.gmap-next',function(e){
		e.stopPropagation();
		e.preventDefault();
		gmarker_index ++;
		if (gmarker_index > gmarkers.length){
			gmarker_index = 1;
		}
		while(gmarkers[gmarker_index - 1].visible === false){
			gmarker_index++;
			if (gmarker_index > gmarkers.length){
				gmarker_index = 1;
			}
		}

		if( map.getZoom() < 15 ){
			map.setZoom(15);
		}
		google.maps.event.trigger(gmarkers[gmarker_index - 1], 'click');
	});

	function single_initialize(){
		var mapBox = $('.property-map-box'),
		    searchMarker,
		    zoom = mapBox.data('zoom'),
		    latitude = mapBox.data('latitude'),
		    longitude = mapBox.data('longitude'),
		    marker = mapBox.data('marker');
		if(mapBox.length){
			var myPlace    = new google.maps.LatLng(latitude,longitude);
			var map = new google.maps.Map(mapBox.get(0), {
				flat:false,
				noClear:false,
				zoom: zoom,
				scrollwheel: false,
				draggable: (Modernizr.touch ? false  : nooGmapL10n.draggable),
				center: myPlace,
				streetViewControl:false,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			});
			if(marker == ''){
				marker = nooGmapL10n.theme_uri + '/assets/images/marker-icon.png'
			}
			var marker = new google.maps.Marker({
				icon: marker,
				position: myPlace,
				map: map
			});
			var input = /** @type {HTMLInputElement} */(
				document.getElementById('property_map_search_input'));
			map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

			var searchBox = new google.maps.places.SearchBox(
				/** @type {HTMLInputElement} */(input));

			google.maps.event.addListener(searchBox, 'places_changed', function() {
				if(searchMarker != null)
					searchMarker.setMap(null);

				var geocoder = new google.maps.Geocoder();
				var getAddress = function(Latlng) {
					geocoder.geocode({'latLng': Latlng}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							if (results[0]) {
								return results[0].formatted_address;
							}
						}
					});
				}
				var myAddress = getAddress(myPlace);
				geocoder.geocode( {
					'address': input.value
				}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						map.setCenter(results[0].geometry.location);
						searchMarker = new google.maps.Marker({
							position: results[0].geometry.location,
							map:map,
							draggable: false,
							animation: google.maps.Animation.DROP
						});
						var start = input.value;
						var end = myPlace;
						var directionsService = new google.maps.DirectionsService();
						var directionsDisplay = new google.maps.DirectionsRenderer();
						directionsDisplay.setMap(map);
						var request = {
							origin:start,
							destination:end,
							travelMode: google.maps.TravelMode.DRIVING
						};

						directionsService.route(request, function(response, status) {
							if (status == google.maps.DirectionsStatus.OK) {
								directionsDisplay.setDirections(response);
							}
						});
					} else {
						alert("Geocode was not successful for the following reason: " + status);
					}
				});


			});
		}
	}
	google.maps.event.addDomListener(window, 'load', single_initialize);

	function search_filter(){

		$('.noo_advanced_search_property').each(function(){
			var $this = $(this);
			if($this.find('#gmap').length){
				var searchParams = getSearchParams();
				if(  typeof infoBox!=='undefined' && infoBox !== null ){
					infoBox.close();
				}
				var bounds = new google.maps.LatLngBounds();
				if(typeof mcluster !== 'undefined')
					mcluster.setIgnoreHidden(true);

				var count_property = 0;
				if(gmarkers.length){
					for (var i=0; i < gmarkers.length; i++) {
						var gmarker = gmarkers[i];
						if( setMarkerVisible( gmarker, searchParams ) ) {
							bounds.extend( gmarker.getPosition() );
							count_property++;
						}
					}
					if(typeof mcluster !== 'undefined')
						mcluster.repaint();

					if ( count_property > 0 ) {

						$('.show-filter-property').html( (NooPropertyMap.results_search).replace( '%d', count_property ) ).css('display', 'inline-block').removeClass('not_found');

					} else {

						$('.show-filter-property').html( NooPropertyMap.no_results_search ).css('display', 'inline-block').addClass('not_found');

					}
				}
				map.setZoom(10);
				if( !bounds.isEmpty() ){
					map.fitBounds(bounds);
				}
			};
		});
	}

	function getFormData($form){
		var unindexed_array = $form.serializeArray();
		var indexed_array = {};

		$.map(unindexed_array, function(n, i){
			indexed_array[n['name']] = n['value'];
		});

		return indexed_array;
	}

	function getSearchParams() {
		var $IDXForm      = $('.noo_advanced_search_property .idx'),
		    $PROPERTYForm = $('.noo_advanced_search_property .property'),
		    Custom_Fields = nooGmapL10n.custom_fields;

		if ( $IDXForm.length == '1' ) {
			var searchParams = {
				form_map: '',
				city: '',
				bedrooms: NaN,
				bathrooms: NaN,
				min_price: NaN,
				max_price: NaN,
				sqft: NaN
			}
		}
		if ( $PROPERTYForm.length == '1' ) {
			var searchParams = {
				form_map: '',
				location: '',
				sub_location: '',
				status: '',
				category: '',
				bedrooms: NaN,
				agent_search: NaN,
				bathrooms: NaN,
				min_price: NaN,
				max_price: NaN,
				min_area: NaN,
				max_area: NaN,
			}
			$.extend( searchParams, nooGmapL10n.custom_fields );
		}

		if( !$('#gmap').length ) return searchParams;
		var $searchForm = $('.noo_advanced_search_property .gsearch');
		if ( $IDXForm.length == '1' ) {
			searchParams.form_map = 'idx';
			//searchParams.location = $IDXForm.find('input.dsidx-search-widget-propertyTypes').length > 0 ? $IDXForm.find('input.dsidx-search-widget-propertyTypes').val() : '';
			searchParams.city = $IDXForm.find('input#idx-q-Cities').length > 0 ? $IDXForm.find('input#idx-q-Cities').val() : '';
			searchParams.bedrooms = $IDXForm.find('input#idx-q-BedsMin').length > 0 ? parseInt( $IDXForm.find('input#idx-q-BedsMin').val() ) : NaN;
			searchParams.bathrooms = $IDXForm.find('input#idx-q-BathsMin').length > 0 ? parseInt( $IDXForm.find('input#idx-q-BathsMin').val() ) : NaN;
			searchParams.min_price = $IDXForm.find('input#idx-q-PriceMin').length > 0 ? parseInt( $IDXForm.find('input#idx-q-PriceMin').val() ) : NaN;
			searchParams.max_price = $IDXForm.find('input#idx-q-PriceMax').length > 0 ? parseInt( $IDXForm.find('input#idx-q-PriceMax').val() ) : NaN;
			searchParams.sqft = $IDXForm.find('input#idx-q-ImprovedSqFtMin').length > 0 ? parseInt( $IDXForm.find('input#idx-q-ImprovedSqFtMin').val() ) : NaN;
			//alert(searchParams.bathrooms);
		}
		if ( $PROPERTYForm.length == '1' ){
			if($searchForm.length){
				searchParams.form_map 		= 'property';
				searchParams.location 		= $searchForm.find('[name="location"]').length > 0 ? $searchForm.find('[name="location"]').val() : '';
				searchParams.sub_location 	= $searchForm.find('[name="sub_location"]').length > 0 ? $searchForm.find('[name="sub_location"]').val() : '';
				searchParams.status 		= $searchForm.find('[name="status"]').length > 0 ? $searchForm.find('[name="status"]').val() : '';
				searchParams.category 		= $searchForm.find('[name="category"]').length > 0 ? $searchForm.find('[name="category"]').val() : '';
				searchParams.bedrooms 		= $searchForm.find('[name="bedrooms"]').length > 0 ? parseInt( $searchForm.find('[name="bedrooms"]').val() ) : NaN;
				searchParams.agent_search 	= $searchForm.find('[name="agent_search"]').length > 0 ? parseInt($searchForm.find('[name="agent_search"]').val()) : NaN;
				searchParams.bathrooms 		= $searchForm.find('[name="bathrooms"]').length > 0 ? parseInt( $searchForm.find('[name="bathrooms"]').val() ) : NaN;
				searchParams.min_price 		= $searchForm.find('[name="min_price"]').length > 0 ? parseFloat( $searchForm.find('[name="min_price"]').val() ) : NaN;
				searchParams.max_price 		= $searchForm.find('[name="max_price"]').length > 0 ? parseFloat( $searchForm.find('[name="max_price"]').val() ) : NaN;
				searchParams.min_area 		= $searchForm.find('[name="min_area"]').length > 0 ? parseInt( $searchForm.find('[name="min_area"]').val() ) : NaN;
				searchParams.max_area 		= $searchForm.find('[name="max_area"]').length > 0 ? parseInt( $searchForm.find('[name="max_area"]').val() ) : NaN;

				/**
				 * Get all custom field in form
				 */
				$.each( Custom_Fields, function(key, value) {
					var key_field = '_noo_property_field_' + key;
					if ( $searchForm.find('[name="' + key_field + '"]').length > 0 ) {
						var field_value = $searchForm.find('[name="' + key_field + '"]').val();
						if ( $searchForm.find('input[name="' + key_field + '"]:checked').length > 0 ) {
							field_value = $searchForm.find('[name="' + key_field + '"]:checked').val();
						}
						searchParams[key] = field_value;
					}

					if ( $searchForm.find('input[name="' + key_field + '[]"]:checkbox:checked').length > 0 ) {
						var field_value = $searchForm.find('input[name="' + key_field + '[]"]:checkbox:checked').map(function(){
							return $(this).val();
						}).get();
						searchParams[key] = field_value;
					}

					if ( $searchForm.find('[name="' + key_field + '[]"] :selected').length > 0 ) {
						var field_value = [];
						$searchForm.find('[name="' + key_field + '[]"] :selected').each(function(i, selected){
							field_value[i] = $(selected).val();
						});
						console.log('Checkbox: ' + field_value);
						searchParams[key] = field_value;
					}
				});

			}
		}

		return searchParams;
	}

	function setMarkerVisible( gmarker, searchParams ) {
		if( gmarker == null || typeof gmarker === "undefined" ) return false;
		if( searchParams == null || typeof searchParams === "undefined" ) return false;
		if ( searchParams.form_map == 'idx') {
			//alert( searchParams.max_price + ' - ' + gmarker.price + ' - ' + searchParams.min_price );
			if( !isNaN( searchParams.bedrooms ) && gmarker.bedrooms !==  parseInt(searchParams.bedrooms) ){

				gmarker.setVisible(false);
				return false;
			}
			if( !isNaN( searchParams.bathrooms ) && gmarker.bathrooms !==  parseInt(searchParams.bathrooms) ){
				//alert( searchParams.bathrooms + ' - ' + gmarker.bathrooms )
				gmarker.setVisible(false);
				return false;
			}
			if( !isNaN( searchParams.min_price ) && parseFloat(gmarker.price) < parseFloat(searchParams.min_price) ){
				gmarker.setVisible(false);
				return false;
			}

			if( !isNaN( searchParams.max_price ) && parseFloat(gmarker.price) > parseFloat(searchParams.max_price) ){
				gmarker.setVisible(false);
				return false;
			}
		}
		if ( searchParams.form_map == 'property') {
			if ( searchParams.location !='' && gmarker.location.indexOf(searchParams.location) == -1 ) {
				gmarker.setVisible(false);
				return false;
			}
			if( searchParams.sub_location !='' && gmarker.sub_location.indexOf(searchParams.sub_location) == -1 ){
				gmarker.setVisible(false);
				return false;
			}

			if( searchParams.status !='' && gmarker.status.indexOf(searchParams.status) == -1 ){
				gmarker.setVisible(false);
				return false;
			}

			if( searchParams.category !='' && gmarker.category.indexOf(searchParams.category) == -1 ){
				gmarker.setVisible(false);
				return false;
			}

			/*if(gmarker.location !== searchParams.location && searchParams.location !='' ){
				gmarker.setVisible(false);
				return false;
			}

			if( gmarker.sub_location !== searchParams.sub_location && searchParams.sub_location !='' ){
				gmarker.setVisible(false);
				return false;
			}

			if( gmarker.status !== searchParams.status && searchParams.status !='' ){
				gmarker.setVisible(false);
				return false;
			}

			if( gmarker.category !== searchParams.category && searchParams.category !='' ){
				gmarker.setVisible(false);
				return false;
			}*/

			if( searchParams.bedrooms !='' && !isNaN( searchParams.bedrooms ) && gmarker.bedrooms !== searchParams.bedrooms ){
				gmarker.setVisible(false);
				return false;
			}
			if( searchParams.agent_search !='' && !isNaN( searchParams.agent_search ) && gmarker.agent_search !== searchParams.agent_search ){
				gmarker.setVisible(false);
				return false;
			}

			if( searchParams.bathrooms !='' && !isNaN( searchParams.bathrooms ) && gmarker.bathrooms !== searchParams.bathrooms ){
				gmarker.setVisible(false);
				return false;
			}

			if( searchParams.min_price !='' && !isNaN( searchParams.min_price ) && parseFloat(gmarker.price) < searchParams.min_price ){
				gmarker.setVisible(false);
				return false;
			}

			if( searchParams.max_price !='' && !isNaN( searchParams.max_price ) && parseFloat(gmarker.price) > searchParams.max_price ){
				gmarker.setVisible(false);
				return false;
			}

			if( searchParams.min_area !='' && !isNaN( searchParams.min_area ) && parseInt(gmarker.area) < searchParams.min_area ){
				gmarker.setVisible(false);
				return false;
			}

			if( searchParams.max_area !='' && !isNaN( searchParams.max_area ) && parseInt(gmarker.area) > searchParams.max_area ){
				gmarker.setVisible(false);
				return false;
			}

			var end_point = false;
			$.each(searchParams, function(name, value) {
				if ( searchParams[name] !== '' && $.type(gmarker[name]) !== 'undefined' ) {

					if ( !isNaN( searchParams[name] ) ) {

						if ( searchParams[name] !== gmarker[name] && searchParams[name] != '' && searchParams[name] != 0 ) {
							gmarker.setVisible(false);
							end_point = true;
						}

					} else if ( $.type(searchParams[name]) === 'string' && $.type(gmarker[name]) !== 'array' ) {

						console.log( name + ':' + searchParams[name] + ' - ' + gmarker[name]  + ' --- ' + $.type(searchParams[name]) + ' --- ' + $.type(gmarker[name]) );
						if ( searchParams[name] !== gmarker[name] ) {
							gmarker.setVisible(false);
							end_point = true;
						}

					}

					if ( $.isArray(searchParams[name]) ) {

						var list_value = searchParams[name];
						$.each(list_value, function(index, val) {
							if ( $.inArray(list_value[index], gmarker[name]) == -1 ) {

								console.log( name + ': ' + searchParams[name] + ' --- ' + gmarker[name] + ' --- ' + $.type(searchParams[name]) + ' --- ' + $.type(gmarker[name]) );
								gmarker.setVisible(false);
								end_point = true;

							}

						});

					}

				}
			});
			if ( end_point ) {
				gmarker.setVisible(false);
				return false;
			}
		}

		gmarker.setVisible(true);
		return true;
	}

	/**
	 * Process event when select option form map
	 */
	jQuery(document).ready(function($) {

		if ( $('.noo-map').length > 0 && $('.show-filter-property').length > 0 && $('.noo-map').find('#gmap').length > 0 && $('.results-property-map').length == 0 ) {

			// var i_filter_map = 0;

			// if ( $('.gsearchform').length > 0 ) {

			// 	$('.gsearchform').each(function(index, el) {

			// 		var gsearchform = $(this);

			// 		gsearchform.find('.gsearch-field').find(':input').on('change',function() {
			// 			/**
			// 			 * VAR
			// 			 * @type {[type]}
			// 			 */
			// 			var searchParams          = getFormData(gsearchform);
			// 				searchParams.action   = 'filter_property_map';
			// 				searchParams.security = NooPropertyMap.security;
			// 				searchParams.results  = 'count';
			// 				// console.log(searchParams);
			// 			/**
			// 			 * Process ajax
			// 			 */
			// 				$.ajax({
			// 					url: NooPropertyMap.ajax_url,
			// 					type: 'POST',
			// 					dataType: 'html',
			// 					cache: false,
			// 					data: searchParams,
			// 					beforeSend: function() {
			// 						// $('.show-filter-property').show();
			// 					}
			// 				})
			// 				.done(function( response ) {

			// 					var data = $.parseJSON(response);

			// 					if ( data.status == 'ok' ) {

			// 						$('.show-filter-property').html( data.msg ).css('display', 'inline-block').removeClass('not_found');

			// 					} else if ( data.status == 'not_found' ) {

			// 						$('.show-filter-property').html( data.msg ).css('display', 'inline-block').addClass('not_found');

			// 					}

			// 					/**
			// 					 * Check if page load then hide text count property
			// 					 */
			// 					if(i_filter_map < 2) {

			// 						$('.show-filter-property').html( '' ).css('display', 'none');

			// 					}

			// 					i_filter_map++;

			// 				})
			// 				.fail(function() {
			// 					console.log("error");
			// 				})

			// 		});

			// 	});

			// }

			// if ( $('.show-results-property').length > 0 ) {

			// 	$('.show-results-property').prepend( '<div class="noo-main"><div class="noo-loading-property"><img alt="" src="' + NooPropertyMap.loading + '" /></div><div class="results-property"></div></div>' );

			// } else {

			// $('.noo-main').html( '<div class="noo-loading-property"><img alt="" src="' + NooPropertyMap.loading + '" /></div><div class="results-property"></div>' );

			// }

			$('.show-filter-property').click(function(event) {

				if ( $(this).hasClass('not_found') ) return;

				if ( $('.container-wrap > .noo_advanced_search_property').length ) {

					event.preventDefault();

					$('.noo-main').html( '<div class="noo-loading-property"><img alt="" src="' + NooPropertyMap.loading + '" /></div><div class="results-property"></div>' );

					/**
					 * VAR
					 * @type {[type]}
					 */
					var searchParams = getFormData($('.gsearchform').first());
					searchParams.action = 'filter_property_map';
					searchParams.security = NooPropertyMap.security;

					/**
					 * Process ajax
					 */
					$.ajax({
						url: NooPropertyMap.ajax_url,
						type: 'POST',
						dataType: 'html',
						cache: false,
						data: searchParams,
						beforeSend: function( xhr ) {
							$('.noo-loading-property').show();
							$('.results-property').html( '' );

							/**
							 * Check if menu is fixed
							 */
							var remove_height = 95;
							if ( $('.navbar-fixed-top.navbar').length > 0 ) {
								var remove_height = remove_height + $('.navbar-fixed-top.navbar').height();
							}

							/**
							 * Check if user login
							 */
							if ( $('#wpadminbar').length > 0 ) {
								var remove_height = remove_height + $('#wpadminbar').height();
							}

							/**
							 * Go to position loading
							 */
							$('html, body').animate({ scrollTop: ( $('.noo-loading-property').offset().top - remove_height ) }, 'slow');

						},
						complete: function(){
							$('.noo-loading-property').hide();
						}
					})
						.done(function( response ) {

							$('.results-property').html( response );

						})
						.fail(function() {
							console.log("error");
						})

				}

			});

		}


		/**
		 * Event process in page map property
		 */
		if ( $('.results-property-map').length > 0 ) {

			$('.results-property-map').prepend( '<div class="noo-loading-property"><img alt="" src="' + NooPropertyMap.loading + '" /></div><div class="results-map-property"></div>' );

			$('.gsearchform').find('.gsearch-field').find(':input').on('change',function() {

				/**
				 * VAR
				 * @type {[type]}
				 */
				var searchParams                 = getFormData($('.gsearchform'));
				searchParams.action          = 'filter_property_map';
				searchParams.security        = NooPropertyMap.security;
				searchParams.hide_head 		 = true;
				searchParams.hide_orderby    = true;

				/**
				 * Process ajax
				 */
				$.ajax({
					url: NooPropertyMap.ajax_url,
					type: 'POST',
					dataType: 'html',
					cache: false,
					data: searchParams,
					beforeSend: function( xhr ) {

						$('.noo-loading-property').show();

					},
					complete: function(){

						$('.noo-loading-property').hide();

					}
				})
					.done(function( response ) {

						$('.results-map-property').html( response );
						$('.results-map-property').next().remove();

					})
					.fail(function() {
						console.log("error");
					})
			});
		}

		/**
		 * Event process when click button loadmore
		 */
		$('.noo-main').on('click', '.btn-loadmore', function(e){
			e.stopPropagation();
			e.preventDefault();
			/**
			 * VAR
			 * @type {[type]}
			 */
			var searchParams              = getFormData($('.gsearchform').first());
			searchParams.action       = 'filter_property_map';
			searchParams.security     = NooPropertyMap.security;
			searchParams.results      = 'load_more';
			searchParams.current_page = $(this).data('current-page');

			/**
			 * Remove class loadmore old and add image loading
			 */
			var class_wrap = $(this).data('class-wrap');
			$('.' + class_wrap ).html('<div class="noo-loading-property"><img alt="" src="' + NooPropertyMap.loading + '" /></div>');

			/**
			 * Process ajax
			 */
			$.ajax({
				url: NooPropertyMap.ajax_url,
				type: 'POST',
				dataType: 'html',
				cache: false,
				data: searchParams,
				beforeSend: function() {
					// $('.show-filter-property').show();
				}
			})
				.done(function( response ) {

					$('.' + class_wrap ).remove();
					$('.loadmore-wrap').append( response );

				})
				.fail(function() {
					console.log("error");
				})
		});

	});

})(jQuery);