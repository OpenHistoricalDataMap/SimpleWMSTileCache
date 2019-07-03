
var creationMarker = false;
var format = 'image/png';

var addLayers = [
	'cache'
];
var layers = [];
addLayers.forEach(function(layer){
	layers.push(
		new ol.layer.Tile({
			source: new ol.source.TileWMS({
				url: 'https://ohdmcache.f4.htw-berlin.de',
				params: {
					'FORMAT': format, 
					'VERSION': '1.1.1',
					tiled: true,
					LAYERS: layer,
					STYLES: '',
					date: '2018-01-01'
				}
			})
		})
	);
});

var map = new ol.Map({
	layers: layers,
	target: 'map',
	view: new ol.View({
		center: ol.proj.fromLonLat([13.526506, 52.457630]),
		zoom: 19
	})
});

$('#mapYear').on('change', function() {
	var newDate = this.value;
	layers.forEach(function(layer){
		layers[0].getSource().updateParams({date:newDate});
	});
});