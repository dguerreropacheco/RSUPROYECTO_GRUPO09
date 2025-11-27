<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class HorarioMantenimiento extends Model
{
    use HasFactory;

    protected $table = 'horarios_mantenimiento';

    protected $fillable = [
        'mantenimiento_id',
        'vehiculo_id',
        'responsable_id',
        'tipo_mantenimiento',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    
    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'mantenimiento_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function responsable()
    {
        return $this->belongsTo(Personal::class, 'responsable_id');
    }

    public function dias()
    {
        return $this->hasMany(DiaMantenimiento::class, 'horario_mantenimiento_id');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================
    
    public function getHoraInicioFormateadaAttribute()
    {
        return Carbon::parse($this->hora_inicio)->format('h:i A');
    }

    public function getHoraFinFormateadaAttribute()
    {
        return Carbon::parse($this->hora_fin)->format('h:i A');
    }

    public function getCantidadDiasAttribute()
    {
        return $this->dias()->count();
    }

    // ==========================================
    // MÉTODOS DE NEGOCIO
    // ==========================================
    
    /**
     * Generar automáticamente los días según el día de semana y el mes
     */
    public function generarDias()
    {
        $mantenimiento = $this->mantenimiento;
        $fechaInicio = $mantenimiento->fecha_inicio;
        $fechaFin = $mantenimiento->fecha_fin;
        
        // Mapeo de días en español a número (Carbon usa inglés)
        $diasSemana = [
            'Lunes' => Carbon::MONDAY,
            'Martes' => Carbon::TUESDAY,
            'Miércoles' => Carbon::WEDNESDAY,
            'Jueves' => Carbon::THURSDAY,
            'Viernes' => Carbon::FRIDAY,
            'Sábado' => Carbon::SATURDAY,
            'Domingo' => Carbon::SUNDAY,
        ];
        
        $diaNumero = $diasSemana[$this->dia_semana];
        
        // Generar todas las fechas del período
        $period = CarbonPeriod::create($fechaInicio, $fechaFin);
        
        $diasGenerados = [];
        foreach ($period as $date) {
            // Si el día de la semana coincide, crear el registro
            if ($date->dayOfWeek === $diaNumero) {
                $diasGenerados[] = [
                    'horario_mantenimiento_id' => $this->id,
                    'fecha' => $date->toDateString(),
                    'realizado' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Insertar en batch
        if (!empty($diasGenerados)) {
            DiaMantenimiento::insert($diasGenerados);
        }
        
        return count($diasGenerados);
    }

    /**
     * Verificar solapamiento de horarios
     */
    public static function validarSolapamiento(
        $mantenimientoId,
        $diaSemana,
        $vehiculoId,
        $horaInicio,
        $horaFin,
        $excludeId = null
    ) {
        $query = self::where('mantenimiento_id', $mantenimientoId)
                     ->where('dia_semana', $diaSemana)
                     ->where('vehiculo_id', $vehiculoId)
                     ->where(function ($q) use ($horaInicio, $horaFin) {
                         // Validar solapamiento de horas
                         $q->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                           ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                           ->orWhere(function ($q2) use ($horaInicio, $horaFin) {
                               $q2->where('hora_inicio', '<=', $horaInicio)
                                  ->where('hora_fin', '>=', $horaFin);
                           });
                     });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}



