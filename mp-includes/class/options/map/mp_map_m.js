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

	mapboxgl.accessToken = mp_mapL10n.mapboxtoken;

	this.init = function() {

		this.center = this.getLatLng(this.setLoLa(this.settings.center_lng, this.settings.center_lat));

		var myOptions = {
			container: 	this.container,

			center: 	this.center,
			zoom: 		parseInt(this.zoomlevel.val()),
			style: 		this.get_maptype(this.maptype.val()),

			dragRotate:	false
		};

		this.map = new mapboxgl.Map(myOptions);

                this.map.on('load', function(){

			if ( _this.count )
			{
				if ( _this.count < _this.max )
					for (var i in _this.data.markers) _this.setMarker(_this.data.markers[i]);
				else
					_this.setCluster();
			}

			_this.setEvents();
        		_this.setControls();
			_this.scheduler();						
                });                
	};

	this.sanitize = function(x) {
		x = parseFloat(x);
		return x.toFixed(8);
	};

	this.setLoLa = function(lng, lat) {
		return { lo: this.sanitize(lng), la: this.sanitize(lat) };
	};

	this.getLoLa = function(LatLng) {
		return (Array.isArray(LatLng))
			? { lo: this.sanitize(LatLng[0]),  la: this.sanitize(LatLng[1])  }
			: { lo: this.sanitize(LatLng.lng), la: this.sanitize(LatLng.lat) };
	};

	this.getLatLng = function(LoLa) {
		return { lon: LoLa.lo, lat: LoLa.la };
	};

	this.setMarker = function(data) {

		var coords = this.getLatLng(this.setLoLa(data['lng'], data['lat']));

		var el = document.createElement('div');
		el.className = 'marker';

		var marker = new mapboxgl.Marker(el);
		marker.setLngLat( coords );

		if(typeof(data['info']) != "undefined")
		{
			marker.setPopup(new mapboxgl.Popup({offset: 25}).setHTML(data['info'])).addTo(this.map);
		}
	};

	this.setCluster = function() {
		var features = new Array();
		
		for (var i in this.data.markers)
		{
			if (typeof(this.data.markers[i].info) != "undefined" )
				features.push( { type: "Feature", geometry: { type: "Point", coordinates: [this.data.markers[i].lng, this.data.markers[i].lat] }, properties: { html: this.data.markers[i].info } });
			else
				features.push( { type: "Feature", geometry: { type: "Point", coordinates: [this.data.markers[i].lng, this.data.markers[i].lat] } });
		}

		var geojson = { type: "FeatureCollection", features: features };
		this.map.addSource('tracks', { type: 'geojson', data: geojson, cluster: true, clusterMaxZoom: 14, clusterRadius: 50 });

		this.map.addLayer({
			id: "clusters",
			type: "circle",
			source: 'tracks',
			filter: ["has", "point_count"],
			paint: {
// Use step expressions (https://docs.mapbox.com/mapbox-gl-js/style-spec/#expressions-step)
// with three steps to implement three types of circles:
//   * Blue, 20px circles when point count is less than 100
//   * Yellow, 30px circles when point count is between 100 and 750
//   * Pink, 40px circles when point count is greater than or equal to 750
				"circle-color": [
					"step",
					["get", "point_count"],
					"#51bbd6",
					10,
					"#f1f075",
					30,
					"#f28cb1"
				],
				"circle-radius": [
					"step",
					["get", "point_count"],
					10,
					10,
					15,
					30,
					20
				]
			}
		});

		this.map.addLayer({
			id: "cluster-count",
			type: "symbol",
			source: 'tracks',
			filter: ["has", "point_count"],
			layout: {
				"text-field": "{point_count_abbreviated}",
				"text-font": ["DIN Offc Pro Medium", "Arial Unicode MS Bold"],
				"text-size": 12
			}
		});

 		this.map.addLayer({
			id: "unclustered-point",
			type: "circle",
			source: 'tracks',
			filter: ["!", ["has", "point_count"]],
			paint: {
				"circle-color": "#11b4da",
				"circle-radius": 4,
				"circle-stroke-width": 1,
				"circle-stroke-color": "#fff"
			}
		});

		// inspect a cluster on click
		this.map.on('click', 'clusters', function (e) {
			var features = _this.map.queryRenderedFeatures(e.point, { layers: ['clusters'] });
			var clusterId = features[0].properties.cluster_id;
			_this.map.getSource('tracks').getClusterExpansionZoom(clusterId, function (err, zoom) {
				if (err)
					return;
 
				_this.map.easeTo({
					center: features[0].geometry.coordinates,
					zoom: zoom
				});
			});
		});
 
		this.map.on('mouseenter', 'clusters', function () {
			_this.map.getCanvas().style.cursor = 'pointer';
		});
		this.map.on('mouseleave', 'clusters', function () {
			_this.map.getCanvas().style.cursor = '';
		});
	};

	this.get_maptype = function(maptype) {
                var s = v = null;
		switch(maptype)
		{
			case 'SATELLITE': s = 'satellite'; v = '9';	break;
			case 'HYBRID'	: s = 'satellite-streets'; v = '11'; break;
			case 'TERRAIN'	: s = 'outdoors'; v = '11';	break;
			default	 	: s = 'streets'; v = '11';	break;
		}
                return 'mapbox://styles/mapbox/' + s + '-v' + v;
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

       		var c = new MP_Mapbox_Controls();
       		this.map.addControl( c );

       		jQuery('#' + _this.prefix + '_map .map_control').click(function(){
       			switch (_this.maptype.val())
       			{
       				case 'ROADMAP' : _this.maptype.val('SATELLITE'); break;
       				case 'SATELLITE' : _this.maptype.val('HYBRID');  break;
       				case 'HYBRID' : _this.maptype.val('TERRAIN');    break;
       				default: 	_this.maptype.val('ROADMAP');    break;
                        }
                        var s = _this.get_maptype(_this.maptype.val());
                        _this.map.setStyle(s);
			return false;
       		});

       		jQuery('#' + _this.prefix + '_map .map_center').click(function(){
			var LoLa = _this.getLoLa(_this.center);
			_this.map.setCenter(_this.center);
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

class MP_Mapbox_Controls {
	onAdd(map) {
		this.map = map;
		this.container = document.createElement('div');
		this.container.setAttribute('class', 'mapboxgl-ctrl mapboxgl-ctrl-group');
		var button = document.createElement('button');
                
		button.setAttribute('class', 'mapboxgl-ctrl-icon map_control');
		button.setAttribute('type', 'button');
		button.setAttribute('alt', mp_mapL10n.changemap);
		button.setAttribute('title', mp_mapL10n.changemap);
	 	this.container.appendChild(button);
                
    		var button = document.createElement('button');
		button.setAttribute('class', 'mapboxgl-ctrl-icon map_center');
		button.setAttribute('type', 'button');
		button.setAttribute('alt', mp_mapL10n.center);
		button.setAttribute('title', mp_mapL10n.center);
	 	this.container.appendChild(button);
                
		return this.container;
	}
	onRemove() {
		this.container.parentNode.removeChild(this.container);
		this.map = undefined;
	}
}