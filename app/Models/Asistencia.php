<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';

    protected $fillable = [
        'personal_id',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_entrada' => 'datetime:H:i',
        'hora_salida' => 'datetime:H:i',
    ];

    // Relaciones
    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }

    // Scopes
    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    public function scopePorPersonal($query, $personalId)
    {
        return $query->where('personal_id', $personalId);
    }

    public function scopePresentes($query)
    {
        return $query->where('estado', 'presente');
    }

    public function scopeAusentes($query)
    {
        return $query->where('estado', 'ausente');
    }

    public function scopeHoy($query)
    {
        return $query->where('fecha', Carbon::today());
    }

    // Accessors
    public function getHorasTrabajadasAttribute()
    {
        if (!$this->hora_entrada || !$this->hora_salida) {
            return null;
        }
        
        $entrada = Carbon::parse($this->hora_entrada);
        $salida = Carbon::parse($this->hora_salida);
        
        return $entrada->diffInHours($salida, true);
    }

    // Métodos estáticos
    public static function marcarAsistencia($personalId, $dni, $clave)
    {
        $personal = Personal::where('dni', $dni)->where('activo', true)->first();
        
        if (!$personal || !$personal->verificarClave($clave)) {
            return [
                'success' => false,
                'message' => 'DNI o clave incorrectos'
            ];
        }
        
        if ($personal->id != $personalId) {
            return [
                'success' => false,
                'message' => 'El personal no coincide'
            ];
        }
        
        $hoy = Carbon::today();
        $ahora = Carbon::now();
        
        $asistencia = self::firstOrCreate(
            [
                'personal_id' => $personal->id,
                'fecha' => $hoy
            ],
            [
                'hora_entrada' => $ahora,
                'estado' => 'presente'
            ]
        );
        
        if (!$asistencia->wasRecentlyCreated && !$asistencia->hora_salida) {
            $asistencia->hora_salida = $ahora;
            $asistencia->save();
            
            return [
                'success' => true,
                'message' => 'Salida registrada correctamente',
                'tipo' => 'salida',
                'asistencia' => $asistencia
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Entrada registrada correctamente',
            'tipo' => 'entrada',
            'asistencia' => $asistencia
        ];
    }
}