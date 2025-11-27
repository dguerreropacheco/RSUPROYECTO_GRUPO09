<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class Personal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'personal';

    protected $fillable = [
        'dni',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'telefono',
        'email',
        'direccion',
        'licencia_conducir',
        'fecha_vencimiento_licencia',
        'foto',
        'funcion_id',
        'clave',
        'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_vencimiento_licencia' => 'date',
        'deleted_at' => 'datetime',  
        'activo' => 'boolean',
    ];

    protected $hidden = [
        'clave',
    ];
     protected $appends = [
        'nombre_completo',
    ];


    // Relaciones
    public function funcion()
    {
        return $this->belongsTo(Funcion::class);
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }

    public function contratoActivo()
    {
        return $this->hasOne(Contrato::class)->where('activo', true);
    }

    public function vacaciones()
    {
        return $this->hasMany(Vacaciones::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
    /*

    public function programaciones()
    {
        return $this->hasMany(Programacion::class);
    }
        */

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeContratados($query)
    {
        return $query->whereHas('contratoActivo');
    }

    public function scopeConductores($query)
    {
        return $query->whereHas('funcion', function ($q) {
            $q->where('nombre', 'Conductor');
        });
    }

    public function scopeAyudantes($query)
    {
        return $query->whereHas('funcion', function ($q) {
            $q->where('nombre', 'Ayudante');
        });
    }

    public function scopePorFuncion($query, $funcionId)
    {
        return $query->where('funcion_id', $funcionId);
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}";
    }

    public function getNombreCortoAttribute()
    {
        return "{$this->nombres} {$this->apellido_paterno}";
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : asset('storage/images/personal.jpg');
    }


    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento ? Carbon::parse($this->fecha_nacimiento)->age : null;
    }

    public function getLicenciaVigente()
    {
        if (!$this->fecha_vencimiento_licencia) {
            return null;
        }
        return Carbon::parse($this->fecha_vencimiento_licencia)->isFuture();
    }

    // Mutators
    /*public function setClaveAttribute($value)
    {
        $this->attributes['clave'] = Hash::make($value);
    }*/
          // Mutator para email - Convierte string vacío a null
      // Mutator para email - Convierte string vacío a null
    public function setEmailAttribute($value) {
        $this->attributes['email'] = empty($value) ? null : $value;
    }
      public function setClaveAttribute($value) {
        if (!empty($value)) {
            $this->attributes['clave'] = Hash::make($value);
        }
    }

    // Métodos auxiliares
    public function tieneContratoVigente()
    {
        return $this->contratoActivo()->exists();
    }

    public function esConductor()
    {
        return $this->funcion && $this->funcion->nombre === 'Conductor';
    }

    public function esAyudante()
    {
        return $this->funcion && $this->funcion->nombre === 'Ayudante';
    }

    public function verificarClave($clave)
    {
        return Hash::check($clave, $this->clave);
    }

    public function tieneVacacionesEnFecha($fecha)
    {
        return VacacionesPeriodo::where('vacaciones_id', function ($query) {
            $query->select('id')
                  ->from('vacaciones')
                  ->where('personal_id', $this->id);
        })
        ->where('fecha_inicio', '<=', $fecha)
        ->where('fecha_fin', '>=', $fecha)
        ->whereIn('estado', ['programado', 'en_curso'])
        ->exists();
    }

    public function registroAsistenciaHoy()
    {
        return $this->asistencias()
                    ->where('fecha', Carbon::today())
                    ->first();
    }

    public function asistiohoy()
    {
        return $this->asistencias()
                    ->where('fecha', Carbon::today())
                    ->where('estado', 'presente')
                    ->exists();
    }
     public function scopeConDerechoAVacaciones($query)
    {
    return $query->whereHas('contratoActivo', function ($q) {
        $q->whereIn('tipo_contrato', ['nombrado', 'permanente']);
    });
    } 

    public function getDiasDisponiblesAttribute()
{
    $anio = Carbon::now()->year;

    $vacacion = $this->vacaciones()
        ->where('anio', $anio)
        ->with('periodos')
        ->first();

    if (!$vacacion) {
        return 30; 
    }

    $diasUsados = $vacacion->periodos
        ->whereNotIn('estado', ['rechazado', 'cancelado'])
        ->sum('dias_utilizados');

    $diasDisponibles = 30 - $diasUsados;

    return max($diasDisponibles, 0);
}


    public function getNombreConDiasAttribute()
    {
        return "{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno} ({$this->dias_disponibles} días disponibles)";
    }
}
