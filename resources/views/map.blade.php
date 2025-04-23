
</html>
@extends('layouts.layout')

@section('title', 'Map Page')

@section('content')
<!-- Map Section -->

<main class="map-section">
    <div>
        <form method="GET" action="{{ url('/testmap') }}" >
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" value="{{ request('first_name', 'es') }}">

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" value="{{ request('last_name', 'es') }}">


            <button type="submit">Filter</button>
        </form>
    </div>
    <div class="map-container">
        {!! $map !!}

        <script>


            window.onload = function() {
                console.log(window.maps[0].shapes);
                console.log(typeof window.maps[0].shapes[0]);
                console.log(window.maps[0].shapes[0] instanceof google.maps.Polygon);
                window.maps[0].shapes.forEach(function (polygon, index) {
                    const polygonO = Object.values(polygon)[0];

                    polygonO.addListener('click', function (event) {
                        // Show the modal
                        const modal = document.getElementById('polygonModal');
                        const polygonInfo = document.getElementById('polygonInfo');

                        // Set some information about the polygon
                        polygonInfo.textContent = "This is a polygon with 5 vertices.";

                        // Display the modal
                        modal.classList.remove('hidden');
                        //alert("Polygon clicked at: " + event.latLng.toUrlValue());
                    });
                    // Add mouseover event
                    polygonO.addListener("mouseover", () => {
                        polygonO.setOptions({
                            fillOpacity: 1,
                            strokeOpacity: 1
                        });
                    });
                    polygonO.addListener("mouseout", () => {
                        polygonO.setOptions({
                            fillOpacity: 0.15,
                            strokeOpacity: 0.5
                        });
                    });
                });
                // Close modal when clicking the close button
                document.getElementById('closeModal').addEventListener('click', function() {
                    const modal = document.getElementById('polygonModal');
                    modal.classList.add('hidden');
                });
            };
        </script>
    </div>
</main>
<!-- Modal -->
<div id="polygonModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
        <h2 class="text-2xl font-semibold mb-4">Polygon Details</h2>
        <p id="polygonInfo" class="text-lg text-gray-700 mb-4">Some details about the polygon...</p>
        <button id="closeModal" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Close</button>
    </div>
</div>
@endsection
