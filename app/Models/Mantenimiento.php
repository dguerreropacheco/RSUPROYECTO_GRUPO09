<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'mantenimientos';

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    
    public function horarios()
    {
        return $this->hasMany(HorarioMantenimiento::class, 'mantenimiento_id');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================
    
    public function getFechaInicioFormateadaAttribute()
    {
        return $this->fecha_inicio->format('d/m/Y');
    }

    public function getFechaFinFormateadaAttribute()
    {
        return $this->fecha_fin->format('d/m/Y');
    }

    public function getDuracionDiasAttribute()
    {
        return $this->fecha_inicio->diffInDays($this->fecha_fin) + 1;
    }

    public function getCantidadHorariosAttribute()
    {
        return $this->horarios()->count();
    }

    // ==========================================
    // MÉTODOS DE VALIDACIÓN
    // ==========================================
    
    /**
     * Verificar si las fechas se solapan con otro mantenimiento
     */
    public static function validarSolapamiento($fechaInicio, $fechaFin, $excludeId = null)
    {
        $query = self::where(function ($q) use ($fechaInicio, $fechaFin) {
            $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
              ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
              ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                  $q2->where('fecha_inicio', '<=', $fechaInicio)
                     ->where('fecha_fin', '>=', $fechaFin);
              });
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Obtener el mes y año del mantenimiento
     */
    public function getMesAnioAttribute()
    {
        return $this->fecha_inicio->locale('es')->isoFormat('MMMM YYYY');
    }
}

