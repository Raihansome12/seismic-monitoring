<div class="bg-gray-50 rounded-lg p-6 shadow">
    <div class="flex items-center space-x-1 mb-3">
        <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-blink"></span>
        <span class="font-semibold text-gray-900">Location: </span>
        <span class="text-gray-900"> {{ $city }} | <span id="time"></span> </span>
    </div> 
    <script src="{{ asset('js/time.js') }}"></script>

    <!-- Container Peta -->
    <div wire:ignore>
        <div id="map" class="w-full h-48 rounded-lg"></div>
    </div>

    {{-- <div>      
        <div class="bg-white rounded-lg shadow p-4">
            <p><strong>Latitude:</strong> {{ $location['latitude'] ?? '-' }}</p>
            <p><strong>Longitude:</strong> {{ $location['longitude'] ?? '-' }}</p>
            <p><strong>Waktu Lokal:</strong> {{ $currentTime ?? '-' }}</p>
        </div>
    </div> --}}
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
    
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    <script>
        let map = null;
        let marker = null;
        let lastLocation = null;
        
        function initializeMap() {
            if (map !== null) return; // Don't reinitialize if map exists

            var locationData = @json($location);
            // Default coordinates for Indonesia if location data is not available
            var defaultLat = -7.4541;
            var defaultLng = 110.1;
            
            // Initialize map with either actual location or default coordinates
            map = L.map('map').setView([
                locationData?.latitude || defaultLat,
                locationData?.longitude || defaultLng
            ], 9);
        
            // Use OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
        
            // Only add marker if we have valid coordinates
            if (locationData?.latitude && locationData?.longitude) {
                marker = L.marker([locationData.latitude, locationData.longitude]).addTo(map)
                    .bindPopup("IA.STMKG..SHZ")
                    .openPopup();
                lastLocation = {
                    latitude: locationData.latitude,
                    longitude: locationData.longitude
                };
            }

            // Force a map redraw after initialization
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }

        function haversineDistance(lat1, lon1, lat2, lon2) {
            // Convert latitude and longitude from degrees to radians
            const R = 6371e3; // Earth's radius in meters
            const φ1 = lat1 * Math.PI/180;
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2-lat1) * Math.PI/180;
            const Δλ = (lon2-lon1) * Math.PI/180;

            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                    Math.cos(φ1) * Math.cos(φ2) *
                    Math.sin(Δλ/2) * Math.sin(Δλ/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

            return R * c; // Distance in meters
        }

        function isSignificantMove(newLocation) {
            if (!lastLocation) return true;

            // Calculate distance between points
            const distance = haversineDistance(
                lastLocation.latitude, lastLocation.longitude,
                newLocation.latitude, newLocation.longitude
            );

            // Consider it significant if moved more than 10 km
            return distance > 10000;
        }

        function updateMarkerPosition(newLocation) {
            // console.log('Checking location update:', newLocation);
            
            // Handle array data structure
            if (Array.isArray(newLocation)) {
                newLocation = newLocation[0];
            }
            
            if (!map || !newLocation?.latitude || !newLocation?.longitude) {
                console.log('Invalid update data or map not initialized:', {
                    mapExists: !!map,
                    location: newLocation
                });
                return;
            }

            // Check if the location has actually changed
            if (!isSignificantMove(newLocation)) {
                // console.log('Location hasn\'t changed significantly, skipping update');
                return;
            }
            
            const newLatLng = [newLocation.latitude, newLocation.longitude];
            // console.log('Significant move detected, updating position to:', newLatLng);
            
            if (!marker) {
                // console.log('Creating new marker');
                marker = L.marker(newLatLng).addTo(map);
            }
            
            marker.setLatLng(newLatLng);
            marker.bindPopup("IA.STMKG..SHZ").openPopup();
            map.setView(newLatLng, 10);

            // Update last known location
            lastLocation = {
                latitude: newLocation.latitude,
                longitude: newLocation.longitude
            };
        }

        // Initialize map only once when component loads
        document.addEventListener('DOMContentLoaded', function() {
            // console.log('DOM Content Loaded - Initializing map');
            initializeMap();
        });

        // Listen for GPS location updates from both Livewire and WebSocket
        window.addEventListener('gps-location-updated', function(event) {
            // console.log('GPS Update Event Received:', event.detail);
            updateMarkerPosition(event.detail);
        });

        // Also listen for Echo events directly
        window.addEventListener('echo:gps-channel,NewGpsDataReceived', function(event) {
            // console.log('WebSocket Update Received:', event.detail);
            const newLocation = {
                latitude: event.detail.latitude,
                longitude: event.detail.longitude
            };
            updateMarkerPosition(newLocation);
        });

        // Handle Livewire refreshes
        document.addEventListener('livewire:initialized', function() {
            // console.log('Livewire Initialized');
            if (!map) {
                console.log('Map not found, initializing');
                initializeMap();
            }
        });

        document.addEventListener('livewire:update', function() {
            const locationData = @json($location);
            // console.log('Livewire Update - Location Data:', locationData);
            updateMarkerPosition(locationData);
            if (map) map.invalidateSize();
        });

        // Clean up on component disconnect
        document.addEventListener('livewire:disconnected', () => {
            // console.log('Component disconnected - cleaning up');
            if (map) {
                map.remove();
                map = null;
                marker = null;
                lastLocation = null;
            }
        });

        // Initial setup
        // console.log('Script loaded - checking map initialization');
        if (!map) initializeMap();
    </script>
</div>
