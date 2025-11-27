<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class VacacionesPeriodo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vacaciones_periodos';

    protected $fillable = [
        'vacaciones_id',
        'fecha_inicio',
        'fecha_fin',
        'dias_utilizados',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'dias_utilizados' => 'integer',
    ];

    // Relaciones
    public function vacaciones()
    {
        return $this->belongsTo(Vacaciones::class);
    }

    // Scopes
    public function scopeProgramados($query)
    {
        return $query->where('estado', 'programado');
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('estado', 'finalizado');
    }

    public function scopeVigentes($query)
    {
        $hoy = Carbon::today();
        return $query->where('fecha_inicio', '<=', $hoy)
                     ->where('fecha_fin', '>=', $hoy)
                     ->whereIn('estado', ['programado', 'en_curso']);
    }

    // Accessors
    public function getEstaVigenteAttribute()
    {
        $hoy = Carbon::today();
        return Carbon::parse($this->fecha_inicio)->lte($hoy) 
            && Carbon::parse($this->fecha_fin)->gte($hoy)
            && in_array($this->estado, ['programado', 'en_curso']);
    }

    // Eventos
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($periodo) {
            $periodo->vacaciones->actualizarDias();
        });

        static::deleted(function ($periodo) {
            $periodo->vacaciones->actualizarDias();
        });
    }
}
