function mp_map(data)
{
	this.map 	= null;

	this.data	= data;
	this.settings 	= data.settings;
	this.prefix 	= this.settings.prefix;

	this.center_lng = jQuery('#' + this.prefix + '_center_lng');
	this.center_lat = jQuery('#' + this.prefix + '_center_lat');

	this.zoomlevel  = jQuery('#' + this.prefix + '_zoomlevel');
	this.maptype  	= jQuery('#' + this.prefix + '_maptype');

	this.container 	= this.prefix + '_map';

	this.count = parseInt(this.settings.count);
	this.max   = 10;

        this.tileLayer  = null;
        
	this.init = function() {
	
		this.center = this.getLatLng(this.setLoLa(this.settings.center_lng, this.settings.center_lat));

		var myOptions = {
			center: 	this.center,
			zoom: 		parseInt(this.zoomlevel.val()),

			zoomControl:	false
		};

		this.map = L.map(this.container, myOptions);

		var layer = this.get_maptype(this.maptype.val());
		this.tileLayer = L.tileLayer(layer.tile, layer.opts);
		this.tileLayer.addTo(this.map);

		if ( this.count )
		{
			if ( this.count < this.max )
				for (var i in this.data.markers) this.setMarker(this.data.markers[i]);
			else
				this.setCluster( this.data.markers );
		}
		
		this.setEvents();
        	this.setControls();
       		this.scheduler();						
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
		return L.latLng( LoLa.la, LoLa.lo );
//		return { lat: LoLa.la, lng: LoLa.lo };
	};

	this.setMarker = function(data) {

		var coords = this.getLatLng(this.setLoLa(data['lng'], data['lat']));

		var m = L.marker(coords).addTo(this.map);

		if(typeof(data['info']) != "undefined")
		{
			m.bindPopup(data['info']);
		}

		return m;
	};

	this.setCluster = function() {
		var markers = L.markerClusterGroup({ spiderfyOnMaxZoom: false, showCoverageOnHover: false, zoomToBoundsOnClick: false });

		markers.on('clusterclick', function (a) {
			a.layer.spiderfy();
		});

		for (var i in this.data.markers)
		{
			var data = this.data.markers[i];

			var coords = this.getLatLng(this.setLoLa(data['lng'], data['lat']));
			var m = L.marker(coords);

			if(typeof(data['info']) != "undefined")
			{
				m.bindPopup(data['info']);
			}

			markers.addLayer(m);
		}

		this.map.addLayer(markers);
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
	};

	this.setControls = function() {

		L.Control.Ctrls = L.Control.extend({
			onAdd: function(map) {
				var div = L.DomUtil.create('div', 'leaflet-ctrl leaflet-ctrl-group');
				div.innerHTML  = '<button class="leaflet-ctrl-icon map_control" type="button" title="'+mp_mapL10n.changemap+'" alt="'+mp_mapL10n.changemap+'"></button>'
					       + '<button class="leaflet-ctrl-icon map_center"  type="button" title="'+mp_mapL10n.center+'"    alt="'+mp_mapL10n.center   +'"></button>';
				return div;
			},
			onRemove: function(map) {
			}
		});

		L.control.ctrls = function(opts) {
			return new L.Control.Ctrls(opts);
		}

		L.control.ctrls({ position: 'topright' }).addTo(this.map);

		jQuery('#' + _this.prefix + '_map .map_control').click(function(){
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

		jQuery('#' + _this.prefix + '_map .map_center').click(function(){
			var LoLa = _this.getLoLa(_this.center);
			_this.map.setView(_this.center);
			_this.center_lat.val(LoLa.la);
			_this.center_lng.val(LoLa.lo);
			return false;
		});
	};

	this.scheduler = function() {
		jQuery.schedule({	id: _this.prefix + '_schedule',
					time: 60000, 
					func: function() { _this.update_settings(); }, 
					repeat: true, 
					protect: true
		});
	};

	this.update_settings = function() {
		var data = {};
		data['action']		= 'mp_ajax';
		data['mp_action'] 	= 'map_settings';
		data['id']		= mp_mapL10n.id;
		data['type']		= mp_mapL10n.type;
		data['prefix']		= this.prefix;
		data['settings[center_lat]'] = this.center_lat.val();
		data['settings[center_lng]'] = this.center_lng.val();
		data['settings[zoomlevel]']  = this.zoomlevel.val();
		data['settings[maptype]']    = this.maptype.val();

		jQuery.ajax({
			data: data,
			beforeSend: null,
			type: "POST",
			url: ajaxurl,
			success: null
		});
	};

	var _this = this;

	this.init();
}