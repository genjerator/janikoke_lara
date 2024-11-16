<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="googleMapHandler()"
        x-init="init()"
        wire:ignore
    >
        <!-- Textarea to display the polygon coordinates -->
        <textarea
            x-ref="polygonTextarea"
            wire:model="{{ $getStatePath() }}"
            class="w-full p-2 border rounded-md"
            placeholder='[{"lat": "12.345", "lng": "67.890"}]'
            rows="5"
        ></textarea>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('google.maps.key') }}"></script>

        <!-- Google Maps container -->
        <div id="map" style="width: 100%; height: 400px;" class="mt-4"></div>
    </div>
    @php

    $area = $field->getRecord();
    $polygonCoordinates = $area->getJsonPolygonAttribute();
    @endphp
    <script>
        function googleMapHandler() {
            return {
                map: null,
                polygon: null,
                init() {
                    // Reference to the textarea for synchronization
                    const polygonTextarea = this.$refs.polygonTextarea;

                    // Initial map configuration
                    const initialPosition = { lat: 45.2671, lng: 19.8335 };

                    // Initialize the Google Map
                    this.map = new google.maps.Map(document.getElementById('map'), {
                        center: initialPosition,
                        zoom: 10,
                    });

                    var triangleCoords1 = JSON.parse('{!! $polygonCoordinates !!}');
                    console.log(triangleCoords1);
                    // Define an editable polygon
                    this.polygon = new google.maps.Polygon({
                        paths: triangleCoords1,
                        editable: true,
                        draggable: true,
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#FF0000',
                        fillOpacity: 0.35,
                    });

                    // Add polygon to the map
                    this.polygon.setMap(this.map);

                    // Update the polygon from the textarea on load
                    const initialCoordinates = JSON.parse(polygonTextarea.value || '[]');
                    if (initialCoordinates.length > 0) {
                        const path = initialCoordinates.map(coord => new google.maps.LatLng(coord.lat, coord.lng));
                        this.polygon.setPath(path);
                        this.map.setCenter(path[0]);
                    }

                    // Event listeners to update the textarea when the polygon is edited
                    google.maps.event.addListener(this.polygon.getPath(), 'set_at', () => this.updateTextarea());
                    google.maps.event.addListener(this.polygon.getPath(), 'insert_at', () => this.updateTextarea());
                    google.maps.event.addListener(this.polygon.getPath(), 'remove_at', () => this.updateTextarea());

                    // Sync Livewire state when the polygon is updated
                    this.updateTextarea();
                },
                updateTextarea() {
                    const coordinates = this.polygon.getPath().getArray().map((point) => ({
                        lat: parseFloat(point.lat().toFixed(7)),
                        lng: parseFloat(point.lng().toFixed(7)),
                    }));
                    const jsonString = JSON.stringify(coordinates);
                    this.$refs.polygonTextarea.value = jsonString;

                    // Update Livewire state
                @this.set('{{ $getStatePath() }}', jsonString);
                },
            };
        }
    </script>
</x-dynamic-component>
