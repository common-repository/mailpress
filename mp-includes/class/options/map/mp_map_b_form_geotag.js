function mp_map_bing(settings)
{
	this.map	= null;

	this.settings 	= settings;
	this.prefix 	= 'mp_' + this.settings.form + '_' + this.settings.field;
	this.suffix      = '_' + this.settings.form + '_' + this.settings.field;

	this.center_lat = jQuery('#' + this.prefix + '_center_lat');
	this.center_lng = jQuery('#' + this.prefix + '_center_lng');
        
	this.zoomlevel  = jQuery('#' + this.prefix + '_zoomlevel');
	this.maptype  	= jQuery('#' + this.prefix + '_maptype');

	this.container 	= '#' + this.prefix + '_map';

	this.lat 	= jQuery('#' + this.prefix + '_lat,#' + this.prefix + '_lat_d');
	this.lng 	= jQuery('#' + this.prefix + '_lng,#' + this.prefix + '_lng_d');
	this.rgeocode 	= jQuery('#' + this.prefix + '_geocode');

	this.infobox = false;


	this.init = function() {

		this.center = this.getLatLng(this.setLoLa(this.settings.center_lng, this.settings.center_lat));

		var myOptions = {
			center: 	this.center,
			zoom: 		parseInt(this.zoomlevel.val()),
			mapTypeId: 	this.get_maptype(this.maptype.val()),

			showDashboard:	false,
			showMapTypeSelector: false,
			showZoomButtons: false,

			credentials: 	mp_mapL10n.bmapkey
		};

		this.map = new Microsoft.Maps.Map(this.container, myOptions);

	        //Create an infobox at the center of the map but don't show it.
		this.infobox = new Microsoft.Maps.Infobox(this.center, {visible: false});
	        //Assign the infobox to a map instance.
		this.infobox.setMap(this.map);

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
		return { lo: this.sanitize(LatLng.longitude), la: this.sanitize(LatLng.latitude) };
	};

	this.getLatLng = function(LoLa) {
		return new Microsoft.Maps.Location(LoLa.la, LoLa.lo);
	};

	this.setCenter = function() {
		var LoLa = this.getLoLa(this.pin.getLocation());
		
		this.map.setView({center: this.getLatLng(LoLa)});
		this.center_lat.val(LoLa.la);
		this.center_lng.val(LoLa.lo);
	};

	this.setMarker = function() {

		this.pin = new Microsoft.Maps.Pushpin(this.center, { draggable: true });

		this.map.entities.push(this.pin);
	};

	this.moveMarker = function(LoLa) {
                this.hideMarkerInfo();
		this.pin.setLocation(this.getLatLng(LoLa));
		this.lat.val(LoLa.la);
		this.lng.val(LoLa.lo);

		this.setCenter();
	};

	this.showMarkerInfo = function(LatLng, data) {
		this.infobox.setOptions({
			location: LatLng,
			title: '',
			description: data,
			visible: true
		});
	};

	this.hideMarkerInfo = function() {
		this.infobox.setOptions({title: '',description: '', visible: false});
	};

	this.get_maptype = function(maptype) {
                var s = v = null;
		switch(maptype)
		{
			case 'SATELLITE': s = Microsoft.Maps.MapTypeId.aerial;	break;
			case 'HYBRID' 	: s = Microsoft.Maps.MapTypeId.aerial; break;
			case 'TERRAIN'	: s = Microsoft.Maps.MapTypeId.canvasLight; break;
			default	 	: s = Microsoft.Maps.MapTypeId.road;	break;
		}
		return s;
	};

	this.setEvents = function() {
		// map
		Microsoft.Maps.Events.addHandler(_this.map, 'viewchangeend', function (e) { if (e.targetType != 'map') return;
			var LoLa = _this.getLoLa(_this.map.getCenter());
			_this.center_lat.val(LoLa.la);
			_this.center_lng.val(LoLa.lo);

			_this.zoomlevel.val(parseInt(_this.map.getZoom()));
		});

		// pin
		Microsoft.Maps.Events.addHandler(_this.pin, 'drag', function (e) { if (e.targetType != 'pushpin') return;
			_this.hideMarkerInfo();
			var LoLa = _this.getLoLa(_this.pin.getLocation());
			_this.lat.val(LoLa.la);
			_this.lng.val(LoLa.lo);
		});

		Microsoft.Maps.Events.addHandler(_this.pin, 'dragend', function (e) { if (e.targetType != 'pushpin') return;
			var LoLa = _this.getLoLa(_this.pin.getLocation());
			_this.lat.val(LoLa.la);
			_this.lng.val(LoLa.lo);
		});


		// geocoder
		this.geocoder = null;
		Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
			_this.geocoder = new Microsoft.Maps.Search.SearchManager(_this.map);
		});

		jQuery('#' + this.prefix + '_geocode_button').click( function() {
			var address = jQuery('#' + _this.prefix + '_geocode').val();

			var request = {
				where: address,
				callback: function(r) {
					if (r && r.results && r.results.length > 0) {
						var LoLa = _this.getLoLa(r.results[0].location);
						_this.moveMarker(LoLa);
					}
					else
					{
						alert("Geocoder failed!");
					}
				},
				errorCallback: function (e) {
					alert("Geocoder failed");
				}
			};
			_this.geocoder.geocode(request);
		});
	};

	this.setControls = function() {

		if ( 0 == this.settings.changemap+this.settings.center+this.settings.rgeocode ) return;

		MyControls.prototype = new Microsoft.Maps.CustomOverlay({ beneathLabels : false });

		function MyControls() {

			_this.div = document.createElement('div');
			_this.div.setAttribute('class', 'bing-ctrl bing-ctrl-group');
                        
			if ( 1 == _this.settings.changemap ) {
				var button = document.createElement('button');
	 			button.setAttribute('type', 'button');
				button.setAttribute('class', 'bing-ctrl-icon map_control');
				button.setAttribute('alt',   mp_mapL10n.changemap);
				button.setAttribute('title', mp_mapL10n.changemap);
			  	_this.div.appendChild(button);

				button.onclick = function(){
		       			switch (_this.maptype.val())
		       			{
		       				case 'ROADMAP' : _this.maptype.val('HYBRID'); break;
//		       				case 'SATELLITE' : _this.maptype.val('HYBRID');  break;
		       				case 'HYBRID' : _this.maptype.val('TERRAIN');    break;
		       				default: 	_this.maptype.val('ROADMAP');    break;
		                        }
		                        var s = _this.get_maptype(_this.maptype.val());
					_this.map.setMapType(s);
					return false;
				};
			}

			if ( 1 == _this.settings.center ) {
				button = document.createElement('button');
				button.setAttribute('type', 'button');
				button.setAttribute('class', 'bing-ctrl-icon map_center');
				button.setAttribute('alt',   mp_mapL10n.center);
				button.setAttribute('title', mp_mapL10n.center);
			 	_this.div.appendChild(button);

				button.onclick = function(){
					_this.setCenter();
					return false;
				};
			};


			if ( 1 == _this.settings.rgeocode ) {
				button = document.createElement('button');
				button.setAttribute('type', 'button');
				button.setAttribute('class', 'bing-ctrl-icon map_geocode');
				button.setAttribute('alt',   mp_mapL10n.rgeocode);
				button.setAttribute('title', mp_mapL10n.rgeocode);
			 	_this.div.appendChild(button);

				button.onclick = function(){
					var request = {
						location: _this.pin.getLocation(),
						callback: function(r) {
							_this.setCenter();
							_this.showMarkerInfo(_this.pin.getLocation(), r.name)
						},
						errorCallback: function (e) {
							alert("No result found");
						}
					};
					_this.geocoder.reverseGeocode(request);
					return false;
				};
			}
		};

		MyControls.prototype.onAdd = function () {
			this.setHtmlElement(_this.div);
		};

	        //Implement the new custom overlay class.
	        var controls = new MyControls();

	        //Add the custom overlay to the map.
	        this.map.layers.insert(controls);
	};

	var _this = this;

	this.init();
}

var MAILPRESS_data = new Array();

function mp_field_type_geotag(s){
	MAILPRESS_data.push(s);
}