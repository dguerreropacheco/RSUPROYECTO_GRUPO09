<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacaciones extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vacaciones';

    protected $fillable = [
        'personal_id',
        'anio',
        'dias_totales',  
        'dias_programados',
        'dias_pendientes',
    ];

    protected $casts = [
        'anio' => 'integer',
        'dias_programados' => 'integer',
        'dias_pendientes' => 'integer',
    ];

    // Constantes
    const DIAS_MAXIMOS_ANIO = 30;

    // Relaciones
    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }

    public function periodos()
    {
        return $this->hasMany(VacacionesPeriodo::class, 'vacaciones_id');
    }

    // Scopes
    public function scopePorAnio($query, $anio)
    {
        return $query->where('anio', $anio);
    }

    public function scopeConDiasPendientes($query)
    {
        return $query->where('dias_pendientes', '>', 0);
    }

    public function scopePorPersonal($query, $personalId)
    {
        return $query->where('personal_id', $personalId);
    }

    // Accessors
    
    /**
     * Accessor para dias_totales (siempre retorna 30)
     * Permite usar $vacacion->dias_totales en vistas y lógica
     */
    public function getDiasTotalesAttribute()
    {
        return self::DIAS_MAXIMOS_ANIO;
    }

    /**
     * Días ya utilizados (calculado)
     */
    public function getDiasUtilizadosAttribute()
    {
        return self::DIAS_MAXIMOS_ANIO - $this->dias_pendientes;
    }

    /**
     * Verifica si puede tomar vacaciones
     */
    public function getPuedeTomarVacacionesAttribute()
    {
        return $this->dias_pendientes > 0;
    }

    /**
     * Nombre completo del empleado (útil para vistas)
     */
    public function getNombrePersonalAttribute()
    {
        return $this->personal ? $this->personal->nombre_completo : 'N/A';
    }

    /**
     * Porcentaje de vacaciones utilizadas
     */
    public function getPorcentajeUtilizadoAttribute()
    {
        return ($this->dias_utilizados / self::DIAS_MAXIMOS_ANIO) * 100;
    }

    // Métodos auxiliares
    
    /**
     * Actualiza los días programados y pendientes basándose en los períodos
     */
    public function actualizarDias()
{
    $diasUsados = $this->periodos()
                       ->whereIn('estado', ['programado', 'en_curso', 'finalizado'])
                       ->sum('dias_utilizados');
    
    $this->dias_programados = $diasUsados;
    $this->dias_pendientes = $this->dias_totales - $diasUsados;  // Usar dias_totales en lugar de constante
    $this->save();
}

    /**
     * Verifica si puede programar vacaciones para una cantidad de días específica
     * 
     * @param int $dias Cantidad de días a programar
     * @return bool
     */
    public function puedeProgramar($dias)
    {
        return $this->dias_pendientes >= $dias;
    }

    /**
     * Calcula cuántos días puede tomar el empleado
     * 
     * @return int
     */
    public function diasDisponibles()
    {
        return $this->dias_pendientes;
    }


    /**
     * Verifica si tiene períodos activos (pendiente,aprobado)
     * 
     * @return bool
     */
    public function tienePeriodosActivos()
    {
        return $this->periodos()
                    ->whereIn('estado', ['pendiente', 'aprobado','rechazado','cancelado','completado'])
                    ->exists();
    }

    /**
     * Obtiene los períodos activos
     */
    public function periodosActivos()
    {
        return $this->periodos()
                    ->whereIn('estado', ['programado', 'en_curso'])
                    ->orderBy('fecha_inicio')
                    ->get();
    }

    /**
     * Verifica si está de vacaciones en una fecha específica
     * 
     * @param string $fecha Fecha en formato Y-m-d
     * @return bool
     */
    public function estaDeVacacionesEn($fecha)
    {
        return $this->periodos()
                    ->where('estado', 'en_curso')
                    ->where('fecha_inicio', '<=', $fecha)
                    ->where('fecha_fin', '>=', $fecha)
                    ->exists();
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        // Al crear un nuevo registro de vacaciones, inicializar con valores por defecto
        static::creating(function ($vacaciones) {
            if (is_null($vacaciones->dias_programados)) {
                $vacaciones->dias_programados = 0;
            }
            if (is_null($vacaciones->dias_pendientes)) {
                $vacaciones->dias_pendientes = self::DIAS_MAXIMOS_ANIO;
            }
        });
    }
}