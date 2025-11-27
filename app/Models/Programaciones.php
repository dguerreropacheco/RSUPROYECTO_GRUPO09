<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programaciones extends Model
{    
    use HasFactory;

    protected $table = 'programaciones'; 

    protected $fillable = [
        'grupo_id',
        'turno_id',
        'zona_id',
        'vehiculo_id',
        'fecha_inicio',
        'fecha_fin',
        'status',
        'notes',
    ];

    protected $dates = ['fecha_inicio', 'fecha_fin'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'status' => 'integer', // ✅ AGREGADO: Cast a entero
    ];

    // ✅ CRÍTICO: Incluir accessors en la serialización JSON
    protected $appends = [
        'status_label',
        'status_badge',
        'fecha_dia',
        'cantidad_cambios',
        'conductor_asignado',
        'ayudantes_asignados'
    ];

    // ==========================================
    // RELACIONES BÁSICAS
    // ==========================================
    
    public function grupo()
    {
        return $this->belongsTo(GrupoPersonal::class, 'grupo_id')->with('personal');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    // ==========================================
    // RELACIONES PARA PERSONAL ASIGNADO
    // ==========================================
    
    public function personalAsignado()
    {
        return $this->belongsToMany(
            Personal::class,
            'programacion_personal',
            'programacion_id',
            'personal_id'
        )
        ->withPivot('fecha_dia')
        ->with('funcion') // ✅ Cargar la función del personal
        ->withTimestamps();
    }

    // ✅ NUEVAS RELACIONES: Acceso directo a conductor y ayudantes
    public function conductor()
    {
        return $this->personalAsignado()
                    ->whereHas('funcion', function($q) {
                        $q->where('nombre', 'Conductor');
                    });
    }

    public function ayudantes()
    {
        return $this->personalAsignado()
                    ->whereHas('funcion', function($q) {
                        $q->where('nombre', 'Ayudante');
                    });
    }

    public function cambios()
    {
        return $this->hasMany(Cambio::class, 'programacion_id')
                    ->orderBy('created_at', 'desc');
    }

    // ==========================================
    // ACCESSORS - CORREGIDOS
    // ==========================================
    
    public function getStatusLabelAttribute()
    {
        $estados = [
            0 => 'Cancelada',
            1 => 'Programada',
            2 => 'Iniciada',
            3 => 'Completada',
            4 => 'Reprogramada',
        ];
        
        // ✅ Asegurar que status sea integer
        $status = (int) $this->status;
        return $estados[$status] ?? 'Desconocido';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            0 => 'badge-danger',
            1 => 'badge-success',
            2 => 'badge-info',
            3 => 'badge-primary',
            4 => 'badge-warning',
        ];
        
        // ✅ Asegurar que status sea integer
        $status = (int) $this->status;
        return $badges[$status] ?? 'badge-secondary';
    }

    public function getFechaDiaAttribute()
    {
        return $this->fecha_inicio;
    }

    public function getCantidadCambiosAttribute()
    {
        return $this->cambios()->count();
    }

    // ✅ NUEVO: Obtener conductor como objeto único
    public function getConductorAsignadoAttribute()
    {
        return $this->personalAsignado
                    ->first(function($personal) {
                        return $personal->funcion && $personal->funcion->nombre === 'Conductor';
                    });
    }

    // ✅ NUEVO: Obtener ayudantes como colección
    public function getAyudantesAsignadosAttribute()
    {
        return $this->personalAsignado
                    ->filter(function($personal) {
                        return $personal->funcion && $personal->funcion->nombre === 'Ayudante';
                    });
    }
    //Agregué este método

    public function personal()
{
    return $this->belongsToMany(Personal::class, 'programacion_personal', 'programacion_id', 'personal_id')
                ->withPivot('fecha_dia');
}
}