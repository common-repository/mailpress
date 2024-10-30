function mp_field_type_geotag(settings)
{
	this.map 	= null;

	this.settings 	= settings;
	this.prefix 	= 'mp_' + this.settings.form + '_' + this.settings.field;

	this.center_lat = jQuery('#' + this.prefix + '_center_lat');
	this.center_lng = jQuery('#' + this.prefix + '_center_lng');
        
	this.zoomlevel  = jQuery('#' + this.prefix + '_zoomlevel');
	this.maptype  	= jQuery('#' + this.prefix + '_maptype');

	this.container 	= document.getElementById(this.prefix + '_map');

	this.lat 	= jQuery('#' + this.prefix + '_lat,#' + this.prefix + '_lat_d');
	this.lng 	= jQuery('#' + this.prefix + '_lng,#' + this.prefix + '_lng_d');
	this.rgeocode 	= jQuery('#' + this.prefix + '_geocode');
        
	this.bubble = false;


	this.init = function() {

		// Initialize communication with the platform
		var ptOptions = {
			'app_id'  : mp_mapL10n.app_id,
			'app_code': mp_mapL10n.app_code
		}
		if (location.protocol == 'https:') ptOptions.useHTTPS = true;

		this.platform = new H.service.Platform(ptOptions);

		// Obtain the default map types from the platform object
		this.Layers = this.platform.createDefaultLayers();

		this.center = this.getLatLng(this.setLoLa(this.settings.center_lng, this.settings.center_lat));

		var myOptions = {
			center: 	this.center,
			zoom: 		parseInt(this.zoomlevel.val()),
		};

		// Initialize a map
		this.map = new H.Map(	this.container,
					this.get_maptype(this.maptype.val()),
					myOptions
		);

		// Make the map interactive
		// MapEvents enables the event system
		// Behavior implements default interactions for pan/zoom
		this.behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(this.map));

		// Create the UI (controls)
		this.ui = new H.ui.UI(this.map);

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
		return { lo: this.sanitize(LatLng.lng), la: this.sanitize(LatLng.lat) };
	};

	this.getLatLng = function(LoLa) {
		return { lat: LoLa.la, lng: LoLa.lo };
	};

	this.setCenter = function() {
		var LoLa = this.getLoLa(this.marker.getGeometry());

		this.map.setCenter(this.getLatLng(LoLa));
		this.center_lat.val(LoLa.la);
		this.center_lng.val(LoLa.lo);
	};

	this.setMarker = function() {

		// Create a group that can hold map objects:
		this.group = new H.map.Group();

		// Add the group to the map object:
		this.map.addObject(this.group);

		this.marker = new H.map.Marker(this.center);
		this.marker.draggable = true;

		this.group.addObject(this.marker);
	};

	this.moveMarker = function(LoLa) {
                this.hideMarkerInfo();
		this.marker.setGeometry(this.getLatLng(LoLa));
		this.lat.val(LoLa.la);
		this.lng.val(LoLa.lo);

		this.setCenter();
	};

	this.showMarkerInfo = function(LatLng, data) {
		data = '<table><tr><td style="text-align:center;padding-left:5px;min-width: 120px;">'+data+'</td></tr></table>';
		this.bubble =  new H.ui.InfoBubble(LatLng, {content: data});
		this.ui.addBubble(this.bubble);
	};

	this.hideMarkerInfo = function() {
		if (this.bubble) this.ui.removeBubble(this.bubble);
	};

	this.get_maptype = function(maptype) {
                var s = v = null;
		switch(maptype)
		{
			case 'SATELLITE': s = this.Layers.satellite.xbase; break;
			case 'HYBRID'	: s = this.Layers.satellite.map;   break;
			case 'TERRAIN'	: s = this.Layers.terrain.map;     break;
			default	 	: s = this.Layers.normal.map;	   break;
		}
                return s;
	};

	this.setEvents = function() {
		// map
                this.map.addEventListener('dragend', function(e){
			var LoLa = _this.getLoLa(_this.map.getCenter());
			_this.center_lat.val(LoLa.la);
			_this.center_lng.val(LoLa.lo);
                }); 

                 this.map.addEventListener('mapviewchangeend', function(){
			_this.zoomlevel.val(parseInt(_this.map.getZoom()));
                });

                 this.map.addEventListener('baselayerchange', function(){
			switch(_this.map.getBaseLayer())
			{
				case _this.Layers.satellite.xbase: s = 'SATELLITE'; break;
				case _this.Layers.satellite.map	 : s = 'HYBRID';    break;
				case _this.Layers.terrain.map	 : s = 'TERRAIN';   break;
				default	 			 : s = 'ROADMAP';   break;
			}
			_this.maptype.val(s);
                });

		// marker
		// disable the default draggability of the underlying map
		// when starting to drag a marker object:
		this.map.addEventListener('dragstart', function(ev) {
			var target = ev.target;
			if (target instanceof H.map.Marker) {
				_this.behavior.disable();
				_this.hideMarkerInfo();
			}
		}, false);

		// Listen to the drag event and move the position of the marker
		// as necessary
		this.map.addEventListener('drag', function(ev) {
			var target = ev.target,
			pointer = ev.currentPointer;
			if (target instanceof mapsjs.map.Marker) { target.setPosition(_this.map.screenToGeo(pointer.viewportX, pointer.viewportY));
				var LoLa = _this.getLoLa(target.getPosition());
				_this.lat.val(LoLa.la);
				_this.lng.val(LoLa.lo);
			}
		}, false);

		// re-enable the default draggability of the underlying map
		// when dragging has completed
		this.map.addEventListener('dragend', function(ev) {
			var target = ev.target;
			if (target instanceof mapsjs.map.Marker) { _this.behavior.enable();
				var LoLa = _this.getLoLa(target.getPosition());
				_this.lat.val(LoLa.la);
				_this.lng.val(LoLa.lo);
			}
		}, false);

		// geocoder
		this.geocoder = _this.platform.getGeocodingService();

		jQuery('#' + this.prefix + '_geocode_button').click( function() {
			var address = jQuery('#' + _this.prefix + '_geocode').val();

			// Create the parameters for the geocoding request:
			var geocodingParams = { searchText: address, maxresults: 1 };

			// Call the geocode method with the geocoding parameters,
			// the callback and an error callback function (called if a
			// communication error occurs):
			_this.geocoder.geocode(geocodingParams, _this.geocoding, function(e) {
				alert("(Geocoder failed)");
			});
			return false;
		});
	};

	this.setControls = function() {

		if ( 0 == this.settings.changemap+this.settings.center+this.settings.rgeocode ) return;

		var container = new H.ui.Control();
                container.addClass('here-ctrl here-ctrl-group');

		if ( 1 == this.settings.changemap ) {
			var button = new H.ui.base.Element('button', 'here-ctrl-icon map_control');
		  	container.addChild(button);
		}


		if ( 1 == this.settings.center ) {
			var button = new H.ui.base.Element('button', 'here-ctrl-icon map_center');
		  	container.addChild(button);
		}

		if ( 1 == this.settings.rgeocode ) {
			var button = new H.ui.base.Element('button', 'here-ctrl-icon map_geocode');
		  	container.addChild(button);
		}

		container.setAlignment('top-right');

		this.ui.addControl('mailpress', container );
		this.ui.addControl('ScaleBar', new H.ui.ScaleBar() );

		if ( 1 == this.settings.changemap ) {
			jQuery('#' + _this.prefix + '_map .map_control').attr({alt:mp_mapL10n.changemap,title:mp_mapL10n.changemap}).click(function(){
	       			switch (_this.maptype.val())
	       			{
	       				case 'ROADMAP' : _this.maptype.val('SATELLITE'); break;
	       				case 'SATELLITE': _this.maptype.val('HYBRID');  break;
	       				case 'HYBRID' :  _this.maptype.val('TERRAIN');    break;
	       				default: 	 _this.maptype.val('ROADMAP');    break;
	                        }
				_this.map.setBaseLayer(_this.get_maptype(_this.maptype.val()));
				return false;
			});
		}

		if ( 1 == this.settings.center ) {
			jQuery('#' + _this.prefix + '_map .map_center').attr({alt:mp_mapL10n.center,title:mp_mapL10n.center}).click(function(){
				_this.setCenter();
				return false;
			});
		}

		if ( 1 == this.settings.rgeocode ) {
			jQuery('#' + _this.prefix + '_map .map_geocode').attr({alt:mp_mapL10n.rgeocode,title:mp_mapL10n.rgeocode}).click(function(){

				// Create the parameters for the reverse geocoding request:
				var reverseGeocodingParameters = { prox: _this.lat.val()+','+_this.lng.val(), maxresults: 1, mode: 'retrieveAddresses' };

				// Call the geocode method with the geocoding parameters,
				// the callback and an error callback function (called if a
				// communication error occurs):
				_this.geocoder.reverseGeocode(reverseGeocodingParameters, _this.rgeocoding, function(e) { 
					alert("No result found");
				});
				return false;
			});

		}

	};

	this.geocoding = function(data) {
            if (typeof(data.Response.View[0].Result[0].Location) == null) 
            {
                alert("Geocoder failed");
                return;
            }
            r = data.Response.View[0].Result[0].Location;
            var a   =  ( typeof(r.Address.Label ) != "undefined")              ? r.Address.Label             : '';
            var lat =  ( typeof(r.DisplayPosition.Latitude  ) != "undefined" ) ? r.DisplayPosition.Latitude  : 0;
            var lng =  ( typeof(r.DisplayPosition.Longitude ) != "undefined")  ? r.DisplayPosition.Longitude : 0;
    
            _this.moveMarker(_this.setLoLa(lng, lat));
	};

	this.rgeocoding = function(data) {
            if (typeof(data.Response.View[0].Result[0].Location) == null) 
            {
                alert("No result found!");
                return;
            }
            r = data.Response.View[0].Result[0].Location;
            var a   =  ( typeof(r.Address.Label ) != "undefined")              ? r.Address.Label             : '?';
            _this.setCenter();
            _this.showMarkerInfo(_this.getLatLng(_this.setLoLa(_this.lng.val(), _this.lat.val())), a)
	};

	var _this = this;

	this.init();
}