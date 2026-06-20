<?php

namespace App\Services;

use SimpleXMLElement;

class ParserKml
{
    /**
     * @return array{error?: string, coordinates?: array<int, array{lat: float, lng: float}>}
     */
    public function parseFile(string $path): array
    {
        if (! file_exists($path)) {
            return ['error' => 'File tidak ditemukan.'];
        }

        $content = file_get_contents($path);
        if ($content === false) {
            return ['error' => 'Tidak dapat membaca file.'];
        }

        try {
            $xml = new SimpleXMLElement($content);
        } catch (\Exception $e) {
            return ['error' => 'File KML tidak valid.'];
        }

        $xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');

        $placemarks = $xml->xpath('//kml:Placemark');

        if (empty($placemarks)) {
            $placemarks = $xml->xpath('//Placemark');
        }

        if (empty($placemarks)) {
            return ['error' => 'Tidak ada Placemark dalam file KML.'];
        }

        $coordinates = [];
        $placemark = $placemarks[0];

        $polygon = $placemark->xpath('.//kml:Polygon');
        if (empty($polygon)) {
            $polygon = $placemark->xpath('.//Polygon');
        }

        if (! empty($polygon)) {
            $coordNodes = $placemark->xpath('.//kml:coordinates');
            if (empty($coordNodes)) {
                $coordNodes = $placemark->xpath('.//coordinates');
            }

            if (! empty($coordNodes)) {
                $coordText = (string) $coordNodes[0];
                $coordText = trim($coordText);
                $points = preg_split('/\s+/', $coordText);

                foreach ($points as $point) {
                    $parts = explode(',', $point);
                    if (count($parts) >= 2) {
                        $coordinates[] = [
                            'lng' => (float) $parts[0],
                            'lat' => (float) $parts[1],
                        ];
                    }
                }
            }
        }

        if (empty($coordinates)) {
            return ['error' => 'Tidak dapat menemukan koordinat polygon dalam file KML.'];
        }

        return ['coordinates' => $coordinates];
    }
}
