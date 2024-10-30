function mp_map(data)
{
	this.map 	= null;

	this.data	= data;
	this.settings 	= data.settings;
	this.prefix 	= this.settings.prefix;

	this.center_lat = jQuery('#' + this.prefix + '_center_lat');
	this.center_lng = jQuery('#' + this.prefix + '_center_lng');

	this.zoomlevel  = jQuery('#' + this.prefix + '_zoomlevel');
	this.maptype  	= jQuery('#' + this.prefix + '_maptype');

	this.container 	= document.getElementById(this.prefix + '_map');

	this.count = parseInt(this.settings.count);
	this.max   = 10;

	this.infowindow = false;
        
	this.init = function() {

		this.center = this.getLatLng(this.setLoLa(this.settings.center_lng, this.settings.center_lat));

		var myOptions = {
			center: 	this.center,
			zoom: 		parseInt(this.zoomlevel.val()),
			mapTypeId: 	this.get_maptype(this.maptype.val()),

			gestureHandling: 	'greedy',
			draggable:		true,

			disableDefaultUI: 	true,
			mapTypeControl:		false,
			panControl:		false,
			zoomControlOptions: 	{style:'SMALL'}
		};

		this.map = new google.maps.Map(this.container, myOptions);

		if ( this.count )
		{
			var markers = new Array();
			for (var i in this.data.markers) markers.push(this.setMarker(this.data.markers[i]));
			if ( this.count >= this.max ) this.setCluster( markers );
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
		return { lo: this.sanitize(LatLng.lng()), la: this.sanitize(LatLng.lat()) };
	};

	this.getLatLng = function(LoLa) {
		return new google.maps.LatLng(LoLa.la, LoLa.lo);
	};

	this.setMarker = function(data) {

		var mkOptions = {
			position: this.getLatLng(this.setLoLa(data['lng'], data['lat'])),
			map: this.map,
			title: data['ip']
		};

		if(typeof(data['icon']) != "undefined")
			mkOptions['icon'] = new google.maps.MarkerImage(data['icon']);

		var marker = new google.maps.Marker(mkOptions);

		if(typeof(data['info']) != "undefined")
		{
			google.maps.event.addListener(marker, 'click', function() {
				if (_this.infowindow) _this.infowindow.close();
				_this.infowindow = new google.maps.InfoWindow({content:data['info']});
				_this.infowindow.open(_this.map, marker);
			});
		}
		return marker;
	};

	this.setCluster = function(markers) {
		new MarkerClusterer(this.map, markers, {imagePath: mp_mapL10n.url+'m'});
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
			if (_this.infowindow) _this.infowindow.close();
		});
                
		google.maps.event.addListener(_this.map, 'dragend', function() {
			var LoLa = _this.getLoLa(_this.map.getCenter());
			_this.center_lat.val(LoLa.la);
			_this.center_lng.val(LoLa.lo);
		});

		google.maps.event.addListener(this.map, 'zoom_changed', function() {
			_this.zoomlevel.val(parseInt(_this.map.getZoom()));
		});
	};

	this.setControls = function() {

		var container = document.createElement('div');
		container.setAttribute('class', 'google-ctrl google-ctrl-group');

		var button = document.createElement('button');
		button.setAttribute('type', 'button');
		button.setAttribute('alt', mp_mapL10n.changemap);
		button.setAttribute('title', mp_mapL10n.changemap);
		button.setAttribute('class', 'google-ctrl-icon map_control');
	  	container.appendChild(button);

	  	google.maps.event.addDomListener(button, 'click', function() 
		{
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

		button = document.createElement('button');
		button.setAttribute('type', 'button');
		button.setAttribute('alt', mp_mapL10n.center);
		button.setAttribute('title', mp_mapL10n.center);
		button.setAttribute('class', 'google-ctrl-icon map_center');
	 	container.appendChild(button);

	  	google.maps.event.addDomListener(button, 'click', function() {
			var LoLa = _this.getLoLa(_this.center);
			_this.map.setCenter(_this.center);
			_this.center_lat.val(LoLa.la);
			_this.center_lng.val(LoLa.lo);
			return false;
		});

		this.map.controls[google.maps.ControlPosition.TOP_RIGHT].push(container);
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