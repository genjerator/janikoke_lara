import {APIProvider, Map, useMap} from '@vis.gl/react-google-maps';
import {useEffect, useState, useRef} from 'react';

function PolygonOverlay({ combined = [] }) {
    const map = useMap();
    const polygonObjectsRef = useRef([]);

    useEffect(() => {
        if (!map || !window.google) return;
        // Remove old polygons
        polygonObjectsRef.current.forEach(polygon => polygon.setMap(null));
        polygonObjectsRef.current = [];
        // Draw new polygons

        combined.forEach((entity, index)  => {

            const path = entity.polygon.map(point => ({
                lat: point.latitude,
                lng: point.longitude
            }));
            console.log('path', path);

            const polygon = new window.google.maps.Polygon({
                paths: path,
                map: map,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.35,
            });

            polygon.addListener('click', () => {
                alert(
                    `Name: ${entity.person.first_name} ${entity.person.last_name}\n` +
                    `Description: ${entity.person.description}\n` +
                    `Date of Birth: ${entity.person.date_of_birth}\n` +
                    `Date of Death: ${entity.person.date_of_die || 'N/A'}`
                );
            });
            polygonObjectsRef.current.push(polygon);
        });
        // Cleanup on unmount
        return () => {
            polygonObjectsRef.current.forEach(polygon => polygon.setMap(null));
            polygonObjectsRef.current = [];
        };
    }, [map, combined]);

    return null;
}

const Mapx = (props) => {
    const [filter, setFilter] = useState('');
    console.log(props);
    // Filter people by first or last name
    const filteredPeople = Object.values(props.people).filter(person =>
        person.first_name.toLowerCase().includes(filter.toLowerCase()) ||
        person.last_name.toLowerCase().includes(filter.toLowerCase())
    );

    // Get IDs of filtered people
    const filteredIds = filteredPeople.map(person => person.id.toString());

    // Filter polygons by people IDs
    const filteredPolygons = Object.fromEntries(
        Object.entries(props.polygons).filter(([id]) => filteredIds.includes(id))
    );
    console.log(filteredIds);
    console.log(filteredPolygons);
    console.log(filteredPeople);
    const combined = filteredPeople.map(person => ({
        person: person,
        polygon: filteredPolygons[person.id] || null
    }));
    console.log(combined);
    return (
        <>
            <div>sdsdsd</div>
            <div>sdsdsd</div>
            <input
                type="text"
                placeholder="Filter"
                value={filter}
                onChange={e => setFilter(e.target.value)}
                style={{marginBottom: '10px'}}
            />
            <div style={{width: '100%', height: '90vh'}}>
                <APIProvider apiKey="AIzaSyC56Te0QXLZVHcF76LVO3MNBOFnoSdVP98">
                    <Map
                        defaultZoom={18}
                        mapTypeId="satellite"
                        defaultCenter={{lat: 45.5675, lng: 19.434804832919067}}
                    >
                        <PolygonOverlay combined={combined}/>
                    </Map>
                </APIProvider>
            </div>
        </>
    );
};

export default Mapx;
