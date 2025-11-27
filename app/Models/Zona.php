<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zona extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'zonas';

    protected $fillable = [
        'codigo',
        'nombre',
        'distrito_id',
        'descripcion',
        'perimetro',
        'area',
        'poblacion_estimada',
        'activo'
    ];

    protected $casts = [
        'perimetro' => 'json',
        'area' => 'decimal:2',
        'activo' => 'boolean'
    ];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    /**
     * Verifica si un polígono se interseca con zonas existentes
     * 
     * @param array $newPolygon Array de coordenadas del nuevo polígono [lng, lat]
     * @param int|null $excludeId ID de zona a excluir de la verificación (para edición)
     * @return array ['overlap' => bool, 'zones' => array de zonas con las que se interseca]
     */
    public static function checkPolygonOverlap($newPolygon, $excludeId = null)
    {
        // Obtener todas las zonas activas, excluyendo la zona actual si se proporciona
        $query = self::whereNotNull('perimetro');
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        $zonas = $query->get();
        
        $overlappingZones = [];
        
        foreach ($zonas as $zona) {
            if (!$zona->perimetro) {
                continue;
            }
            
            try {
                $existingPolygon = json_decode($zona->perimetro, true);
                
                if (!isset($existingPolygon['geometry']['coordinates'][0])) {
                    continue;
                }
                
                $existingCoords = $existingPolygon['geometry']['coordinates'][0];
                
                // Verificar si los polígonos se intersectan
                if (self::polygonsIntersect($newPolygon, $existingCoords)) {
                    $overlappingZones[] = [
                        'id' => $zona->id,
                        'nombre' => $zona->nombre,
                        'codigo' => $zona->codigo
                    ];
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return [
            'overlap' => count($overlappingZones) > 0,
            'zones' => $overlappingZones
        ];
    }

    /**
     * Verifica si dos polígonos se intersectan usando el algoritmo de intersección de segmentos
     * 
     * @param array $polygon1 Array de coordenadas [lng, lat]
     * @param array $polygon2 Array de coordenadas [lng, lat]
     * @return bool
     */
    private static function polygonsIntersect($polygon1, $polygon2)
    {
        // Verificar si algún vértice de polygon1 está dentro de polygon2
        foreach ($polygon1 as $point) {
            if (self::pointInPolygon($point, $polygon2)) {
                return true;
            }
        }
        
        // Verificar si algún vértice de polygon2 está dentro de polygon1
        foreach ($polygon2 as $point) {
            if (self::pointInPolygon($point, $polygon1)) {
                return true;
            }
        }
        
        // Verificar si algún borde de polygon1 interseca con algún borde de polygon2
        for ($i = 0; $i < count($polygon1) - 1; $i++) {
            for ($j = 0; $j < count($polygon2) - 1; $j++) {
                if (self::segmentsIntersect(
                    $polygon1[$i], $polygon1[$i + 1],
                    $polygon2[$j], $polygon2[$j + 1]
                )) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Verifica si un punto está dentro de un polígono usando el algoritmo Ray Casting
     * 
     * @param array $point Coordenadas del punto [lng, lat]
     * @param array $polygon Array de coordenadas del polígono
     * @return bool
     */
    private static function pointInPolygon($point, $polygon)
    {
        $x = $point[0];
        $y = $point[1];
        $inside = false;
        
        $count = count($polygon);
        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];
            
            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            
            if ($intersect) {
                $inside = !$inside;
            }
        }
        
        return $inside;
    }

    /**
     * Verifica si dos segmentos de línea se intersectan
     * 
     * @param array $p1 Punto inicial del segmento 1
     * @param array $p2 Punto final del segmento 1
     * @param array $p3 Punto inicial del segmento 2
     * @param array $p4 Punto final del segmento 2
     * @return bool
     */
    private static function segmentsIntersect($p1, $p2, $p3, $p4)
    {
        $d1 = self::direction($p3, $p4, $p1);
        $d2 = self::direction($p3, $p4, $p2);
        $d3 = self::direction($p1, $p2, $p3);
        $d4 = self::direction($p1, $p2, $p4);
        
        if ((($d1 > 0 && $d2 < 0) || ($d1 < 0 && $d2 > 0)) &&
            (($d3 > 0 && $d4 < 0) || ($d3 < 0 && $d4 > 0))) {
            return true;
        }
        
        if ($d1 == 0 && self::onSegment($p3, $p4, $p1)) return true;
        if ($d2 == 0 && self::onSegment($p3, $p4, $p2)) return true;
        if ($d3 == 0 && self::onSegment($p1, $p2, $p3)) return true;
        if ($d4 == 0 && self::onSegment($p1, $p2, $p4)) return true;
        
        return false;
    }

    /**
     * Calcula la dirección del giro de tres puntos
     */
    private static function direction($pi, $pj, $pk)
    {
        return ($pk[0] - $pi[0]) * ($pj[1] - $pi[1]) - ($pj[0] - $pi[0]) * ($pk[1] - $pi[1]);
    }

    /**
     * Verifica si un punto está en un segmento de línea
     */
    private static function onSegment($pi, $pj, $pk)
    {
        return min($pi[0], $pj[0]) <= $pk[0] && $pk[0] <= max($pi[0], $pj[0]) &&
               min($pi[1], $pj[1]) <= $pk[1] && $pk[1] <= max($pi[1], $pj[1]);
    }
}