function mp_field_type_geotag(settings)
{
	this.map  	= null;

	this.settings  	= settings;
	this.prefix  	= 'mp_' + this.settings.form + '_' + this.settings.field;
	this.suffix      = '_' + this.settings.form + '_' + this.settings.field;

	this.center_lng = jQuery('#' + this.prefix + '_center_lng');
	this.center_lat = jQuery('#' + this.prefix + '_center_lat');

	this.zoomlevel  = jQuery('#' + this.prefix + '_zoomlevel');
	this.maptype  	= jQuery('#' + this.prefix + '_maptype');

	this.container 	= this.prefix + '_map';

	this.lng 	= jQuery('#' + this.prefix + '_lng,#' + this.prefix + '_lng_d');
	this.lat 	= jQuery('#' + this.prefix + '_lat,#' + this.prefix + '_lat_d');
	this.rgeocode 	= jQuery('#' + this.prefix + '_geocode');

	this.popup = false;
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
			_this.setMarker();
			_this.setEvents();
	       		_this.setControls();
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

	this.setCenter = function() {
		var LoLa = this.getLoLa(this.marker.getLngLat());

		this.map.setCenter(this.getLatLng(LoLa));
		this.center_lat.val(LoLa.la);
		this.center_lng.val(LoLa.lo);
	};

	this.setMarker = function() {

		this.marker = new mapboxgl.Marker({draggable: true}).setLngLat(this.center).addTo(this.map);
	};

	this.moveMarker = function(LoLa) {
                this.hideMarkerInfo();
		this.marker.setLngLat(this.getLatLng(LoLa));
		this.lat.val(LoLa.la);
		this.lng.val(LoLa.lo);

		this.setCenter();
	};

	this.showMarkerInfo = function(LatLng, data) {
		this.popup = new mapboxgl.Popup({offset: 25}).setHTML(data);
		this.popup.setLngLat(this.marker.getLngLat()).addTo(this.map);
	};

	this.hideMarkerInfo = function() {
		if (this.popup) this.popup.remove();
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

		// marker
                this.marker.on('drag', function(){
			_this.hideMarkerInfo();
			var LoLa = _this.getLoLa(_this.marker.getLngLat());
			_this.lat.val(LoLa.la);
			_this.lng.val(LoLa.lo);
		});

                this.marker.on('dragend', function(){
			var LoLa = _this.getLoLa(_this.marker.getLngLat());
			_this.lat.val(LoLa.la);
			_this.lng.val(LoLa.lo);
		});

		// geocoder
		this.geocoder = new MapboxGeocoder({accessToken: mapboxgl.accessToken});
		this.geocoder.options.flyTo = false;
		this.geocoder.options.limit = 1;
		this.map.addControl(this.geocoder);
		jQuery('.mapboxgl-ctrl-geocoder').hide();


		jQuery('#' + this.prefix + '_geocode_button').click( function() {
			_this.geocoder.options.reverseGeocode = false;
			var address = jQuery('#' + _this.prefix + '_geocode').val();

			_this.geocoder.query(address);

			_this.geocoder.on( 'result', _this.geocoding);
			_this.geocoder.on( 'error' , _this.geocoding_ko);

		});
	};
	this.geocoding = function(e) {
		var LoLa = _this.getLoLa(e.result.center);
		_this.moveMarker(LoLa);
		_this.geocoder.off( 'result', _this.geocoding);
		_this.geocoder.off( 'error' , _this.geocoding_ko);
	};

	this.geocoding_ko = function(e) {
		alert("Geocoder failed");
		_this.geocoder.off( 'result', _this.geocoding);
		_this.geocoder.off( 'error' , _this.geocoding_ko);
	};
                
	this.setControls = function() {

		if ( 0 == this.settings.changemap+this.settings.center+this.settings.rgeocode ) return;

		this.map.mailpress = { changemap: this.settings.changemap, center: this.settings.center, rgeocode: this.settings.rgeocode, suffix: this.suffix };
       		this.ctrls = new MP_Mapbox_Controls();
       		this.map.addControl( this.ctrls );

		if ( 1 == this.settings.changemap ) {
	       		jQuery('#map_control'+this.suffix).click(function(){
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
		}

		if ( 1 == this.settings.center ) {
	       		jQuery('#map_center'+this.suffix).click(function(){
	       			_this.setCenter();
				return false;
	  		});
		}

		if ( 1 == this.settings.rgeocode ) {
			jQuery('#map_geocode'+this.suffix).click(function() {
				_this.geocoder.options.reverseGeocode = true;
				var location = _this.lat.val() + ', ' + _this.lng.val();

				_this.geocoder.query(location);

				_this.geocoder.on( 'result', _this.Rgeocoding);
				_this.geocoder.on( 'error' , _this.Rgeocoding_ko);

				return false;
			});
		}
	};

	this.Rgeocoding = function(e) {
		if (!e.result) return;
		var a = e.result.place_name;
		_this.setCenter();
		_this.showMarkerInfo(e.result.center, a);
		_this.geocoder.off( 'result', _this.Rgeocoding);
		_this.geocoder.off( 'error' , _this.Rgeocoding_ko);
	};

	this.Rgeocoding_ko = function(e) {
		alert("No result found");
		_this.geocoder.off( 'result', _this.Rgeocoding);
		_this.geocoder.off( 'error' , _this.Rgeocoding_ko);
	};

	var _this = this;

	this.init();
}

class MP_Mapbox_Controls {
	onAdd(map) {
		this.map = map;
		this.settings = this.map.mailpress;

		this.container = document.createElement('div');
		this.container.setAttribute('class', 'mapboxgl-ctrl mapboxgl-ctrl-group');

		if ( 1 == this.settings.changemap ) {
			var button = document.createElement('button');
 			button.setAttribute('type', 'button');
			button.setAttribute('id',   'map_control'+this.settings.suffix);
			button.setAttribute('class','mapboxgl-ctrl-icon map_control');
			button.setAttribute('alt',   mp_mapL10n.changemap);
			button.setAttribute('title', mp_mapL10n.changemap);
		 	this.container.appendChild(button);
		}

		if ( 1 == this.settings.center ) {
	    		var button = document.createElement('button');
			button.setAttribute('type', 'button');
			button.setAttribute('id',   'map_center'+this.settings.suffix);
			button.setAttribute('class','mapboxgl-ctrl-icon map_center');
			button.setAttribute('alt',   mp_mapL10n.center);
			button.setAttribute('title', mp_mapL10n.center);
		 	this.container.appendChild(button);
		}

		if ( 1 == this.settings.rgeocode ) {
			button = document.createElement('button');
			button.setAttribute('type', 'button');
			button.setAttribute('id',   'map_geocode'+this.settings.suffix);
			button.setAttribute('class','mapboxgl-ctrl-icon map_geocode');
			button.setAttribute('alt',   mp_mapL10n.rgeocode);
			button.setAttribute('title', mp_mapL10n.rgeocode);
		 	this.container.appendChild(button);
		}

		return this.container;
	}
	onRemove() {
		this.container.parentNode.removeChild(this.container);
		this.map = undefined;
	}
}