function mp_field_type_geotag(settings)
{
	this.map	= null;

	this.settings 	= settings;
	this.prefix 	= 'mp_' + this.settings.form + '_' + this.settings.field;
	this.suffix      = '_' + this.settings.form + '_' + this.settings.field;

	this.center_lat	= jQuery('#' + this.prefix + '_center_lat');
	this.center_lng	= jQuery('#' + this.prefix + '_center_lng');

	this.zoomlevel 	= jQuery('#' + this.prefix + '_zoomlevel');
	this.maptype  	= jQuery('#' + this.prefix + '_maptype');

	this.container 	= document.getElementById(this.prefix + '_map');

	this.lat 	= jQuery('#' + this.prefix + '_lat,#' + this.prefix + '_lat_d');
	this.lng 	= jQuery('#' + this.prefix + '_lng,#' + this.prefix + '_lng_d');
	this.rgeocode 	= jQuery('#' + this.prefix + '_geocode');

	this.infowindow = false;


	this.init = function() {

		this.center = this.getLatLng(this.setLoLa(this.settings.center_lng, this.settings.center_lat));

		var myOptions = {
			center:		this.center,
			zoom: 		parseInt(this.zoomlevel.val()),
			mapTypeId: 	this.get_maptype(this.maptype.val()),

			gestureHandling: 	'greedy',
			draggable:		true,

			disableDefaultUI: 	true,
			zoomControl:	(	this.settings.zoom == 1),
			zoomControlOptions: 	{style:'SMALL'},
			mapTypeControl:		false,
			panControl:		false,
			streetViewControl:	false,
		};

		this.map = new google.maps.Map(this.container, myOptions);

		this.setMarker();
		this.setEvents();
       		this.setControls();
	};

	this.sanitize = function(x) {
		x = parseFloat(x);
		return x.toFixed(8);
	};

	this.setLoLa = function(lng, lat) {
		return { lo: this.sanitize(lng), la: this.sanitize(lat) };
	};

	this.getLoLa = function(LatLng) {
		return { lo: this.sanitize(LatLng.lng()), la: this.sanitize(LatLng.lat()) };
	};

	this.getLatLng = function(LoLa) {
		return new google.maps.LatLng(LoLa.la, LoLa.lo);
	};

	this.setCenter = function() {
		var LoLa = this.getLoLa(this.marker.getPosition());

		this.map.setCenter(this.getLatLng(LoLa));
		this.center_lat.val(LoLa.la);
		this.center_lng.val(LoLa.lo);
	};

	this.setMarker = function() {

		var mkOptions = { position: this.center, map: this.map, draggable: true };
		this.marker = new google.maps.Marker(mkOptions);
	};

	this.moveMarker = function(LoLa) {
                this.hideMarkerInfo();
		this.marker.setPosition(this.getLatLng(LoLa));
		this.lat.val(LoLa.la);
		this.lng.val(LoLa.lo);

		this.setCenter();
	};

	this.showMarkerInfo = function(LatLng, data) {
		this.infowindow = new google.maps.InfoWindow({content:data});
		this.infowindow.open(this.map, this.marker);
	};

	this.hideMarkerInfo = function() {
		if (this.infowindow) this.infowindow.close();
	};

	this.get_maptype = function(maptype) {
                var s = v = null;
		switch(maptype)
		{
			case 'SATELLITE': s = google.maps.MapTypeId.SATELLITE;	break;
			case 'HYBRID' 	: s = google.maps.MapTypeId.HYBRID;	break;
			case 'TERRAIN'	: s = google.maps.MapTypeId.TERRAIN;	break;
			default	 	: s = google.maps.MapTypeId.ROADMAP;	break;
		}
		return s;
	};

	this.setEvents = function() {
		// map
		google.maps.event.addListener(_this.map, 'click', function() {
			-this.hideMarkerInfo();
		});

		google.maps.event.addListener(_this.map, 'dragend', function() {
			var LoLa = _this.getLoLa(_this.map.getCenter());
			_this.center_lat.val(LoLa.la);
			_this.center_lng.val(LoLa.lo);
		});

		google.maps.event.addListener(this.map, 'zoom_changed', function() {
			_this.zoomlevel.val(parseInt(_this.map.getZoom()));
		});

		// marker
		google.maps.event.addListener(this.marker, 'drag', function() {
			_this.hideMarkerInfo();
			var LoLa = _this.getLoLa(this.getPosition());
			_this.lat.val(LoLa.la);
			_this.lng.val(LoLa.lo);
		});

		google.maps.event.addListener(this.marker, 'dragend', function() {
			var LoLa = _this.getLoLa(this.getPosition());
			_this.lat.val(LoLa.la);
			_this.lng.val(LoLa.lo);
		});

		// geocoder
		this.geocoder = new google.maps.Geocoder();

		jQuery('#' + this.prefix + '_geocode_button').click( function() {
			var address = jQuery('#' + _this.prefix + '_geocode').val();

			_this.geocoder.geocode( {'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var LoLa = _this.getLoLa(results[0].geometry.location);
					_this.moveMarker(LoLa);
				} else {
					alert("Geocoder failed due to: " + status);
				}
			});
		});

	};

	this.setControls = function() {

		if ( 0 == this.settings.changemap+this.settings.center+this.settings.rgeocode ) return;

		var container = document.createElement('div');
		container.setAttribute('class', 'google-ctrl google-ctrl-group');

		if ( 1 == this.settings.changemap ) {
			var button = document.createElement('button');
 			button.setAttribute('type', 'button');
			button.setAttribute('id',   'map_control'+this.settings.suffix);
			button.setAttribute('class','google-ctrl-icon map_control');
			button.setAttribute('alt',   mp_mapL10n.changemap);
			button.setAttribute('title', mp_mapL10n.changemap);
		  	container.appendChild(button);

		  	google.maps.event.addDomListener(button, 'click', function(){
	       			switch (_this.maptype.val())
	       			{
	       				case 'ROADMAP' : _this.maptype.val('SATELLITE'); break;
	       				case 'SATELLITE' : _this.maptype.val('HYBRID');  break;
	       				case 'HYBRID' : _this.maptype.val('TERRAIN');    break;
	       				default: 	_this.maptype.val('ROADMAP');    break;
	                        }
	                        var s = _this.get_maptype(_this.maptype.val());
				_this.map.setMapTypeId(s);
				return false;
			});
		}

		if ( 1 == this.settings.center ) {
			button = document.createElement('button');
			button.setAttribute('type', 'button');
			button.setAttribute('id',   'map_center'+this.settings.suffix);
			button.setAttribute('class','google-ctrl-icon map_center');
			button.setAttribute('alt',   mp_mapL10n.center);
			button.setAttribute('title', mp_mapL10n.center);
		 	container.appendChild(button);

		  	google.maps.event.addDomListener(button, 'click', function(){
				_this.setCenter();
				return false;
			});
		}


		if ( 1 == this.settings.rgeocode ) {
			button = document.createElement('button');
			button.setAttribute('type', 'button');
			button.setAttribute('id',   'map_geocode'+this.settings.suffix);
			button.setAttribute('class', 'google-ctrl-icon map_geocode');
			button.setAttribute('alt',   mp_mapL10n.rgeocode);
			button.setAttribute('title', mp_mapL10n.rgeocode);
		 	container.appendChild(button);

		  	google.maps.event.addDomListener(button, 'click', function(){
				_this.geocoder.geocode( {'location': _this.marker.getPosition()}, function(results, status) {
					if (status === 'OK') {
						if (results[0]) {
							_this.setCenter();
							_this.showMarkerInfo(_this.marker.getPosition(), results[0].formatted_address);
						} else {
							alert("No result found");
						}
					} else {
						alert("Geocoder failed due to: " + status);
					}
					return false;
				});
			});
		}

		this.map.controls[google.maps.ControlPosition.TOP_RIGHT].push(container);
	};

	var _this = this;

	this.init();
}