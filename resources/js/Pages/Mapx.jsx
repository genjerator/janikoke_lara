import {APIProvider, Map, useMap} from '@vis.gl/react-google-maps';
import {useEffect} from 'react';

function PolygonOverlay() {
    const map = useMap();

    useEffect(() => {
        if (!map || !window.google) return;

        const polygon = new window.google.maps.Polygon({
            paths: [
                {lat: -33.870, lng: 151.200}, // NW
                {lat: -33.870, lng: 151.220}, // NE
                {lat: -33.850, lng: 151.220}, // SE
                {lat: -33.850, lng: 151.200}, // SW
            ],
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35,
        });

        polygon.setMap(map);

        // Clean up on unmount
        return () => {
            polygon.setMap(null);
        };
    }, [map]);

    return null;
}

const Mapx = (props) => {
    console.log(props.map);
    return (
        <>
            <div>sdsdsd</div>
            <div>sdsdsd</div>
            <div style={{width: '100%', height: '500px'}}>
                <Map
                    defaultZoom={13}
                    defaultCenter={{lat: -33.860664, lng: 151.208138}}
                >
                    <PolygonOverlay/>
                </Map>
            </div>
        </>
    );
};

export default Mapx;
