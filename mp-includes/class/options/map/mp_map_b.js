function mp_map_bing(data)
{
	this.map 	= null;

	this.data	= data;
	this.settings 	= data.settings;
	this.prefix 	= this.settings.prefix;

	this.center_lat = jQuery('#' + this.prefix + '_center_lat');
	this.center_lng = jQuery('#' + this.prefix + '_center_lng');

	this.zoomlevel  = jQuery('#' + this.prefix + '_zoomlevel');
	this.maptype  	= jQuery('#' + this.prefix + '_maptype');

	this.container 	= '#' + this.prefix + '_map';

	this.count = parseInt(this.settings.count);
	this.max   = 10;

	this.infowindow = false;
        
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

		if ( this.count )
		{
		        //Create an infobox at the center of the map but don't show it.
			this.infobox = new Microsoft.Maps.Infobox(this.center, {visible: false});
		        //Assign the infobox to a map instance.
			this.infobox.setMap(this.map);

			if ( this.count >= this.max ) 
				this.setCluster();
			else
				for (var i in this.data.markers) this.setMarker(this.data.markers[i]);
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
		return { lo: this.sanitize(LatLng.longitude), la: this.sanitize(LatLng.latitude) };
	};

	this.getLatLng = function(LoLa) {
		return new Microsoft.Maps.Location(LoLa.la, LoLa.lo);
	};

	this.setMarker = function(data) {
		this.map.entities.push(this.getMarker(data));
	};

	this.getMarker = function(data) {

		var pos = this.getLatLng(this.setLoLa(data['lng'], data['lat']));
		var ppOptions = { title: data['ip'] };

		var pin= new Microsoft.Maps.Pushpin(pos, ppOptions);

		if(typeof(data['info']) != "undefined")
		{
			//Store some metadata with the pushpin.
			pin.metadata = { description: data['info'], title: data['ip'] };

			//Add a click event handler to the pushpin.
			Microsoft.Maps.Events.addHandler(pin, 'click', function(e){
				_this.infobox.setOptions({
					location: e.target.getLocation(),
					title: e.target.metadata.title,
					description: e.target.metadata.description,
					visible: true
				});
			});
		}
		return pin;
	};

	this.setCluster = function() {
	        //Load the Clustering module.
	        Microsoft.Maps.loadModule("Microsoft.Maps.Clustering", function () {

			// Get pins
			var pins = new Array();
			for (var i in _this.data.markers) pins[i] = _this.getMarker(_this.data.markers[i]);


			clusterLayer = new Microsoft.Maps.ClusterLayer(pins, { clusteredPinCallback: _this.setCustomCluster, gridSize: 80 });
			//clusterLayer = new Microsoft.Maps.ClusterLayer(pins);
			_this.map.layers.insert(clusterLayer);
	        });		
	};

	this.setCustomCluster = function(cluster) {
	        //Define variables for minimum cluster radius, and how wide the outline area of the circle should be.
	        var minRadius = 12;
	        var outlineWidth = 7;

	        //Get the number of pushpins in the cluster
	        var clusterSize = cluster.containedPushpins.length;

	        //Calculate the radius of the cluster based on the number of pushpins in the cluster, using a logarithmic scale.
	        var radius = Math.log(clusterSize) / Math.log(10) * 5 + minRadius;

	        //Default cluster color is red.
	        var fillColor = 'rgba(255, 40, 40, 0.5)';

	        if (clusterSize < 5) {
	            //Make the cluster green if there are less than 10 pushpins in it.
	            fillColor = 'rgba(20, 180, 20, 0.5)';            
	        } else if (clusterSize < 20) {
	            //Make the cluster yellow if there are 10 to 99 pushpins in it.
	            fillColor = 'rgba(255, 210, 40, 0.5)';
	        }

	        //Create an SVG string of two circles, one on top of the other, with the specified radius and color.
	        var svg = ['<svg xmlns="http://www.w3.org/2000/svg" width="', (radius * 2), '" height="', (radius * 2), '">',
	            '<circle cx="', radius, '" cy="', radius, '" r="', radius, '" fill="', fillColor, '"/>',
	            '<circle cx="', radius, '" cy="', radius, '" r="', radius - outlineWidth, '" fill="', fillColor, '"/>',
	            '</svg>'];

	        //Customize the clustered pushpin using the generated SVG and anchor on its center.
	        cluster.setOptions({
	            icon: svg.join(''),
	            anchor: new Microsoft.Maps.Point(radius, radius),
	            textOffset: new Microsoft.Maps.Point(0, radius - 8) //Subtract 8 to compensate for height of text.
		});
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
	};

	this.setControls = function() {

		MyControls.prototype = new Microsoft.Maps.CustomOverlay({ beneathLabels : false });

		function MyControls() {

			_this.div = document.createElement('div');
			_this.div.setAttribute('class', 'bing-ctrl bing-ctrl-group');
                        
			var button = document.createElement('button');
			button.setAttribute('type', 'button');
			button.setAttribute('alt', mp_mapL10n.changemap);
			button.setAttribute('title', mp_mapL10n.changemap);
			button.setAttribute('class', 'bing-ctrl-icon map_control');
		  	_this.div.appendChild(button);

			button.onclick = function() 
			{
	       			switch (_this.maptype.val())
	       			{
	       				case 'ROADMAP' : _this.maptype.val('HYBRID'); break;
//	       				case 'SATELLITE' : _this.maptype.val('HYBRID');  break;
	       				case 'HYBRID' : _this.maptype.val('TERRAIN');    break;
	       				default: 	_this.maptype.val('ROADMAP');    break;
	                        }
	                        var s = _this.get_maptype(_this.maptype.val());
				_this.map.setMapType(s);
				return false;
			};

			button = document.createElement('button');
			button.setAttribute('type', 'button');
			button.setAttribute('alt', mp_mapL10n.center);
			button.setAttribute('title', mp_mapL10n.center);
			button.setAttribute('class', 'bing-ctrl-icon map_center');
		 	_this.div.appendChild(button);

			button.onclick = function() 
			{
				var LoLa = _this.getLoLa(_this.center);
				_this.map.setView({center:_this.center});
				_this.center_lat.val(LoLa.la);
				_this.center_lng.val(LoLa.lo);
				return false;
			};
		};

		MyControls.prototype.onAdd = function () {
			this.setHtmlElement(_this.div);
		};

	        //Implement the new custom overlay class.
	        var controls = new MyControls();

	        //Add the custom overlay to the map.
	        this.map.layers.insert(controls);
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

var MAILPRESS_data = new Array();

function mp_map(d){
	MAILPRESS_data.push(d);
}