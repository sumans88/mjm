// var map = L.map('maps', {
//     zoomControl: true,
//     attributionControl: false
//  }).setView([-1.714, 119.707], 13);
var map = L.map('maps', {
    zoomControl: false,
    attributionControl: false
 }).setView([-1.714, 119.707], 5);

var LeafIcon  = L.Icon.extend({
    iconSize:    [25, 25],
    shadowSize:  [25, 25],
    iconAnchor:  [12, 25],
    popupAnchor: [1, 1],
    tooltipAnchor: [16, -28]
});
var defaultIcon = new LeafIcon({
    iconUrl: "/pelitamaju/plugin/leaflet/images/marker-icon.png",
    iconSize:    [15, 25],
    shadowSize:  [15, 25],
    iconAnchor:  [7, 25],
    popupAnchor: [1, -7],
    tooltipAnchor: [16, -28]
})

L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
    maxZoom: 18,
    attribution: 'Pelitamaju',
    id: 'mapbox/light-v9',
    tileSize: 512,
    zoomOffset: -1
}).addTo(map);
function style(feature) {
    return {
        weight: 3,
        opacity: 1,
        color: '#dbd7d5',
        fillOpacity: 1,
        fillColor: '#dbd7d5'
    };
}
map.dragging.disable();
map.touchZoom.disable();
map.doubleClickZoom.disable();
map.scrollWheelZoom.disable();
$.getJSON("/pelitamaju/plugin/world.geo.json/IDN.geo.json").then(function(geoJSON) {
    var osm = new L.TileLayer.BoundaryCanvas("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        boundary: geoJSON,
    });
    map.addLayer(osm);
    var indoLayer = L.geoJSON(geoJSON, {
        style: style
    }).addTo(map);;
    map.fitBounds(indoLayer.getBounds());
});
var parameters = {};
parameters['draggable'] = false;
parameters['icon'] = defaultIcon;

var mapJakarta = L.marker([-6.28693, 106.79392], parameters).addTo(map);
mapJakarta.bindPopup(`<a href="project-city.html">Jakarta</a>`);

var mapBandung = L.marker([-6.9034443, 107.573117], parameters).addTo(map);
mapBandung.bindPopup(`<a href="project-city.html">Bandung</a>`);