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

	this.container 	= document.getElementById(this.prefix + '_map');
        
	this.count = parseInt(this.settings.count);
	this.max   = 10;



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

		this.map = new H.Map(	this.container,
					this.get_maptype(this.maptype.val()),
					myOptions
		);

		// Make the map interactive
		// MapEvents enables the event system
		// Behavior implements default interactions for pan/zoom
		var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(this.map));

		// Create the UI (bubbles, controls)
		this.ui = new H.ui.UI(this.map);

		if ( this.count )
		{
			if ( this.count < this.max ) {
				// Create a group that can hold map objects:
				this.group = new H.map.Group();

				// Add the group to the map object:
				this.map.addObject(this.group);

				// add 'tap' event listener, that opens info bubble, to the group
				this.group.addEventListener('tap', function (evt) {
					if(typeof(_this.bubble) != "undefined") _this.ui.removeBubble(_this.bubble);		//_this.bubble.close();
					// event target is the marker itself, group is a parent event target
					// for all objects that it contains
					_this.bubble =  new H.ui.InfoBubble(evt.target.getPosition(), {
						// read custom data
						content: evt.target.getData()
					});
					// show info bubble
					_this.ui.addBubble(_this.bubble);
					_this.bubble.getElement().addEventListener('click', function(e) {
						_this.ui.removeBubble(_this.bubble);						//_this.bubble.close();
					});
				}, false);
			
				for (var i in this.data.markers) this.setMarker(this.data.markers[i]);
			}
			else {
				this.setCluster();
			}
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
		return { lat: LoLa.la, lng: LoLa.lo };
	};

	this.setMarker = function(data) {

		var coords = this.getLatLng(this.setLoLa(data['lng'], data['lat']));

		var marker = new H.map.Marker(coords);

		if(typeof(data['info']) != "undefined")
		{
			marker.setData(data['info']);
		}

		this.group.addObject(marker);

		return marker;
	};

	this.setCluster = function() {
		var data = this.data.markers;

		// First we need to create an array of DataPoint objects,
		// for the ClusterProvider
		var dataPoints = new Array();
		for (var i in this.data.markers) {
			var item = this.data.markers[i];
			dataPoints[i] = (typeof(item.info) != "undefined" )
                                     ? new H.clustering.DataPoint(item.lat, item.lng, 1, item.info)
                                     : new H.clustering.DataPoint(item.lat, item.lng);
		}

		// Create a clustering provider with custom options for clusterizing the input
		var clusteredDataProvider = new H.clustering.Provider(dataPoints, {
			clusteringOptions: {
			// Maximum radius of the neighbourhood
			eps: 32,
			// minimum weight of points required to form a cluster
			minWeight: 2
			}
		});

		// Create a layer tha will consume objects from our clustering provider
		var clusteringLayer = new H.map.layer.ObjectLayer(clusteredDataProvider);

		// To make objects from clustering provider visible,
		// we need to add our layer to the map
		this.map.addLayer(clusteringLayer);
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
	};

	this.setControls = function() {

		var container = new H.ui.Control();
                container.addClass('here-ctrl here-ctrl-group');

		var button = new H.ui.base.Element('button', 'here-ctrl-icon map_control');
	  	container.addChild(button);

		var button = new H.ui.base.Element('button', 'here-ctrl-icon map_center');
	  	container.addChild(button);

		container.setAlignment('top-right');

		this.ui.addControl('mailpress', container );
		this.ui.addControl('ScaleBar', new H.ui.ScaleBar() );

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

		jQuery('#' + _this.prefix + '_map .map_center').attr({alt:mp_mapL10n.center,title:mp_mapL10n.center}).click(function(){
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