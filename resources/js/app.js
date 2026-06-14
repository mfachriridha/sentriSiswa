import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

import { polygon as turfPolygon, point as turfPoint } from '@turf/helpers';
import turfBuffer from '@turf/buffer';

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

window.L = L;
window.turfPolygon = turfPolygon;
window.turfPoint = turfPoint;
window.turfBuffer = turfBuffer;

window.sentriLandingSlide = (targetId) => {
    document.getElementById(targetId)?.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest',
        inline: 'start',
    });
};
