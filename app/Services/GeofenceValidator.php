<?php

namespace App\Services;

class GeofenceValidator
{
    public function isInsidePolygon(float $lat, float $lng, array $polygon): bool
    {
        $n = count($polygon);
        $inside = false;

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];

            if ((($yi > $lng) !== ($yj > $lng)) && ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi)) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }

    public function distanceToPolygonEdge(float $lat, float $lng, array $polygon): float
    {
        $minDistance = INF;
        $n = count($polygon);

        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $dist = $this->pointToSegmentDistance(
                $lat, $lng,
                $polygon[$i]['lat'], $polygon[$i]['lng'],
                $polygon[$j]['lat'], $polygon[$j]['lng']
            );

            if ($dist < $minDistance) {
                $minDistance = $dist;
            }
        }

        return $minDistance;
    }

    private function pointToSegmentDistance(
        float $px, float $py,
        float $x1, float $y1,
        float $x2, float $y2
    ): float {
        $dx = $x2 - $x1;
        $dy = $y2 - $y1;

        if ($dx === 0.0 && $dy === 0.0) {
            return $this->haversine($px, $py, $x1, $y1);
        }

        $t = max(0, min(1, (($px - $x1) * $dx + ($py - $y1) * $dy) / ($dx * $dx + $dy * $dy)));

        $nearestLat = $x1 + $t * $dx;
        $nearestLng = $y1 + $t * $dy;

        return $this->haversine($px, $py, $nearestLat, $nearestLng);
    }

    public function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
