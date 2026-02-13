jQuery(document).ready(function($) {
    // Basic settings (These should ideally be localized from PHP)
    var pricePerKm = parseFloat(mytours_vars.price_per_km) || 0;
    var basePrice = parseFloat(mytours_vars.base_price) || 0;
    
    // Initialize Map
    // Italy Coords: 41.8719, 12.5674
    var map = L.map('mytours-map').setView([41.8719, 12.5674], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var pickupMarker = null;
    var dropoffMarker = null;

    // Custom Icons
    // You can replace these with your own images
    var iconOptions = {
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    };
    
    // Simple way to handle clicks: toggle between pickup and dropoff
    var setPickup = true; // State to decide next click action

    map.on('click', function(e) {
        if (setPickup) {
            if (pickupMarker) map.removeLayer(pickupMarker);
            pickupMarker = L.marker(e.latlng).addTo(map).bindPopup("Pickup").openPopup();
            $('#mytours_pickup_coords').val(e.latlng.lat + ',' + e.latlng.lng);
            setPickup = false;
            $('#mytours-instruction').text('Select Drop-off point on the map.');
        } else {
            if (dropoffMarker) map.removeLayer(dropoffMarker);
            dropoffMarker = L.marker(e.latlng).addTo(map).bindPopup("Drop-off").openPopup();
            $('#mytours_dropoff_coords').val(e.latlng.lat + ',' + e.latlng.lng);
            setPickup = true;
            $('#mytours-instruction').text('Calculation in progress...');
            
            calculateRoute();
        }
    });

    function calculateRoute() {
        if (!pickupMarker || !dropoffMarker) return;

        var start = pickupMarker.getLatLng();
        var end = dropoffMarker.getLatLng();

        // Using OSRM public API (Demo server - not for heavy production use)
        var url = 'https://router.project-osrm.org/route/v1/driving/' + 
                  start.lng + ',' + start.lat + ';' + end.lng + ',' + end.lat + 
                  '?overview=false';

        $.get(url, function(data) {
            if (data.routes && data.routes.length > 0) {
                var distanceMeters = data.routes[0].distance;
                var distanceKm = (distanceMeters / 1000).toFixed(2);
                
                var totalPrice = ((distanceKm * pricePerKm) + basePrice).toFixed(2);

                $('#mytours-distance-display').text(distanceKm + ' km');
                $('#mytours-price-display').text('€ ' + totalPrice);
                $('#mytours_total_price').val(totalPrice);
                $('#mytours_distance').val(distanceKm);
                
                $('#mytours-instruction').text('Route calculated! You can adjust points or Book Now.');
            } else {
                $('#mytours-instruction').text('Could not calculate route. Try closer points.');
            }
        }).fail(function() {
            $('#mytours-instruction').text('Error connecting to routing service.');
        });
    }
});
