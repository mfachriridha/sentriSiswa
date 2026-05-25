<?php

namespace App\Services;

use SimpleXMLElement;

class KmlParser
{
    public function parseFile(string $path): array
    {
        return $this->parseKml(file_get_contents($path));
    }

    public function parseKml(string $xmlContent): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent, SimpleXMLElement::class, LIBXML_NOCDATA);
        libxml_clear_errors();

        if ($xml === false) {
            return ['error' => 'File KML tidak valid.'];
        }

        $xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');

        $allCoordinates = [];
        $placemarks = $xml->xpath('//kml:Placemark') ?: $xml->xpath('//Placemark');

        foreach ($placemarks as $placemark) {
            $polygonCoords = $this->extractPolygonCoordinates($placemark);
            if ($polygonCoords !== []) {
                $allCoordinates = array_merge($allCoordinates, $polygonCoords);
            }
        }

        if ($allCoordinates === []) {
            return ['error' => 'Tidak ditemukan polygon dalam file KML. Pastikan area digambar menggunakan polygon di Google My Maps.'];
        }

        $allCoordinates = $this->deduplicateCoordinates($allCoordinates);

        if (count($allCoordinates) < 3) {
            return ['error' => 'Polygon harus memiliki minimal 3 titik koordinat unik.'];
        }

        return ['coordinates' => $allCoordinates];
    }

    private function extractPolygonCoordinates(SimpleXMLElement $placemark): array
    {
        $placemark->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');

        $polygons = $placemark->xpath('.//kml:Polygon') ?: $placemark->xpath('.//Polygon');

        $coordinates = [];

        foreach ($polygons as $polygon) {
            $polygon->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');

            $outerBoundary = $polygon->xpath('.//kml:outerBoundaryIs/kml:LinearRing/kml:coordinates')
                ?: $polygon->xpath('.//outerBoundaryIs/LinearRing/coordinates');

            foreach ($outerBoundary as $coordElement) {
                $rawCoords = trim((string) $coordElement);
                $points = $this->parseKmlCoordinateString($rawCoords);
                $coordinates = array_merge($coordinates, $points);
            }
        }

        return $coordinates;
    }

    private function parseKmlCoordinateString(string $rawCoords): array
    {
        $points = [];
        $pairs = preg_split('/\s+/', trim($rawCoords));

        foreach ($pairs as $pair) {
            $parts = explode(',', trim($pair));
            if (count($parts) >= 2) {
                $lng = (float) $parts[0];
                $lat = (float) $parts[1];

                if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                    $points[] = [
                        'lat' => round($lat, 7),
                        'lng' => round($lng, 7),
                    ];
                }
            }
        }

        return $points;
    }

    private function deduplicateCoordinates(array $coordinates): array
    {
        $seen = [];
        $unique = [];

        foreach ($coordinates as $coord) {
            $key = $coord['lat'].','.$coord['lng'];
            if (! isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $coord;
            }
        }

        return $unique;
    }
}
