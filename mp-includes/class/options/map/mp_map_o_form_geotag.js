function mp_field_type_geotag(settings)
{
	this.map	= null;

	this.settings 	= settings;
	this.prefix 	= 'mp_' + this.settings.form + '_' + this.settings.field;
	this.suffix      = '_' + this.settings.form + '_' + this.settings.field;

	this.center_lat	= jQuery('#' + this.prefix + '_center_lat');
	this.center_lng	= jQuery('#' + this.prefix + '_center_lng');

	this.zoomlevel  = jQuery('#' + this.prefix + '_zoomlevel');
	this.maptype  	= jQuery('#' + this.prefix + '_maptype');

	this.container 	= this.prefix + '_map';

	this.lat 	= jQuery('#' + this.prefix + '_lat,#' + this.prefix + '_lat_d');
	this.lng	= jQuery('#' + this.prefix + '_lng,#' + this.prefix + '_lng_d');
	this.rgeocode 	= jQuery('#' + this.prefix + '_geocode');

	this.popup      = false;
        this.tileLayer  = null;

	this.init = function() {

		this.center = this.getLatLng(this.setLoLa(this.settings.center_lng, this.settings.center_lat));

		var myOptions = {
			center: 	this.center,
			zoom: 		parseInt(this.zoomlevel.val()),

			zoomControl:	( this.settings.zoom == '1' )
		};

		this.map = L.map(this.container, myOptions);

		var layer = this.get_maptype(this.maptype.val());
		this.tileLayer = L.tileLayer(layer.tile, layer.opts);
		this.tileLayer.addTo(this.map);

		this.setMarker();
		this.setEvents();
       		this.setControls();
	}

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
		return L.latLng( LoLa.la, LoLa.lo );
	};

	this.setCenter = function() {
		var LoLa = this.getLoLa(this.marker.getLatLng());

		this.map.setView(this.getLatLng(LoLa));
		this.center_lat.val(LoLa.la);
		this.center_lng.val(LoLa.lo);
	};

	this.setMarker = function() {

		this.marker = L.marker(this.center, {draggable: true, autoPan: true});
		this.map.addLayer(this.marker);
	};

	this.moveMarker = function(LoLa) {
                this.hideMarkerInfo();
		this.marker.setLatLng(this.getLatLng(LoLa));
		this.lat.val(LoLa.la);
		this.lng.val(LoLa.lo);

		this.setCenter();
	};

	this.showMarkerInfo = function(LatLng, data) {
		this.popup = true;
		this.marker.bindPopup(data).addTo(this.map);
		this.marker.openPopup();
	};

	this.hideMarkerInfo = function() {
		if (!this.popup) return;
		this.popup = false;
		this.marker.closePopup();
		this.marker.unbindPopup();

	};

	this.get_maptype = function(maptype) {
                var layer = {};
		switch(maptype)
		{
			case 'SATELLITE': 
				layer.tile = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
				layer.opts = {attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'};
			break;
			case 'HYBRID'	:
				layer.tile = 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
				layer.opts = {maxZoom: 17, attribution: 'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a ref="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'};
			break;
			case 'TERRAIN'	:
				layer.tile = 'https://stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}{r}.{ext}';
				layer.opts = {	subdomains: 'abcd', minZoom: 0, maxZoom: 18, ext: 'png', attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'};
			break;
			default	 	:
				layer.tile = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
				layer.opts = {attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'};
			break;
		}
                return layer;
	};

	this.setEvents = function() {
		// map
                this.map.on('dragend', function(){
			var LoLa = _this.getLoLa(_this.map.getCenter());
			_this.center_lat.val(LoLa.la);
			_this.center_lng.val(LoLa.lo);
                }); 

                this.map.on('zoom', function(){
			_this.zoomlevel.val(parseInt(_this.map.getZoom()));
                }); 

		// marker
                this.marker.on('drag', function(e){ var marker = e.target;
			_this.hideMarkerInfo();
			var LatLng = marker.getLatLng();
			var LoLa = _this.getLoLa(LatLng);
			_this.lat.val(LoLa.la);
			_this.lng.val(LoLa.lo);
			marker.setLatLng(LatLng,{draggable: true, autoPan: true});
		});

                this.marker.on('dragend', function(e){ var marker = e.target;
			var LatLng = marker.getLatLng();
			var LoLa = _this.getLoLa(LatLng);
			_this.lat.val(LoLa.la);
			_this.lng.val(LoLa.lo);
			marker.setLatLng(LatLng,{draggable: true, autoPan: true});
		});

		// geocoder
		jQuery('#' + this.prefix + '_geocode_button').click( function() {
			var address = jQuery('#' + _this.prefix + '_geocode').val();

			var data = {};
			data['action']		= 'mp_ajax';
			data['mp_action'] 	= 'geocoding';
			data['map_provider'] 	= 'o';
			data['addr']		= address;

			jQuery.ajax({
				data: data,
				beforeSend: null,
				type: "POST",
				url: mp_mapL10n.url,
				success: _this.geocoding
			});
		});

	};

	this.setControls = function() {

		if ( 0 == this.settings.changemap+this.settings.center+this.settings.rgeocode ) return;

		L.Control.Ctrls = L.Control.extend({
			onAdd: function(map) {
				var suffix = map.mailpress.suffix;
				var div = L.DomUtil.create('div', 'leaflet-ctrl leaflet-ctrl-group');
				div.innerHTML  = '';
				if ( 1 == map.mailpress.changemap ) div.innerHTML += '<button type="button" id="map_control'+suffix+'" class="leaflet-ctrl-icon map_control" alt="'+mp_mapL10n.changemap+'" title="'+mp_mapL10n.changemap+'"></button>';
				if ( 1 == map.mailpress.center    ) div.innerHTML += '<button type="button" id="map_center' +suffix+'" class="leaflet-ctrl-icon map_center"  alt="'+mp_mapL10n.center   +'" title="'+mp_mapL10n.center+'"   ></button>';
				if ( 1 == map.mailpress.rgeocode  ) div.innerHTML += '<button type="button" id="map_geocode'+suffix+'" class="leaflet-ctrl-icon map_geocode" alt="'+mp_mapL10n.rgeocode +'" title="'+mp_mapL10n.rgeocode+'" ></button>';
				return div;
			},
			onRemove: function(map) {
			}
		});

		L.control.ctrls = function(opts) {
			return new L.Control.Ctrls(opts);
		}
		this.map.mailpress = {changemap : this.settings.changemap , center : this.settings.center , rgeocode : this.settings.rgeocode,  suffix: this.suffix};
		L.control.ctrls({ position: 'topright' }).addTo(this.map);

		if ( 1 == this.settings.changemap ) {
			jQuery('#map_control'+this.suffix).click(function(){
				switch (_this.maptype.val())
				{
					case 'ROADMAP' : _this.maptype.val('SATELLITE'); break;
					case 'SATELLITE' : _this.maptype.val('HYBRID');  break;
					case 'HYBRID' : _this.maptype.val('TERRAIN');    break;
					default: 	_this.maptype.val('ROADMAP');    break;
				}
				_this.tileLayer.remove(_this.map);
				var layer = _this.get_maptype(_this.maptype.val());
				_this.tileLayer = L.tileLayer(layer.tile, layer.opts);
				_this.tileLayer.addTo(_this.map);
				return false;
			});
		}

		if ( 1 == this.settings.center ) {
			jQuery('#map_center'+this.suffix).click(function(){
				_this.setCenter();
				return false;
			});
		}


		if ( 1 == this.settings.rgeocode ) {
			jQuery('#map_geocode'+this.suffix).click(function(){
				var data = {};
				data['action']		= 'mp_ajax';
				data['mp_action'] 	= 'rgeocoding';
				data['map_provider'] 	= 'o';
				data['lng']		= _this.lng.val();
				data['lat']		= _this.lat.val();

				jQuery.ajax({
					data: data,
					beforeSend: null,
					type: "POST",
					url: mp_mapL10n.url,
					success: _this.rgeocoding
				});

				return false;
			});
		}
	};

	this.geocoding = function(data) {
		if (data.success) {
			data = data.data;
			var lng = data.lng;
			var lat = data.lat;
			_this.moveMarker(_this.setLoLa(lng, lat));
		}
		else {
			alert("Geocoder failed");
		}
	};

	this.rgeocoding= function(data) {
		if (data.success) {
			data = data.data;
			_this.showMarkerInfo(_this.getLatLng(_this.setLoLa(_this.lng.val(), _this.lat.val())), data);
			_this.setCenter();

		}
		else {
			alert("No result found");
		}
	};

	var _this = this;

	this.init();
}