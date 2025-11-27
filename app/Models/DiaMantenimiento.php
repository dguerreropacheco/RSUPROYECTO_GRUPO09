<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaMantenimiento extends Model
{
    use HasFactory;

    protected $table = 'dias_mantenimiento';

    protected $fillable = [
        'horario_mantenimiento_id',
        'fecha',
        'observacion',
        'imagen',
        'realizado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'realizado' => 'boolean',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    
    public function horario()
    {
        return $this->belongsTo(HorarioMantenimiento::class, 'horario_mantenimiento_id');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================
    
    public function getFechaFormateadaAttribute()
    {
        return $this->fecha->format('d/m/Y');
    }

    public function getEstadoLabelAttribute()
    {
        return $this->realizado ? 'Realizado' : 'Pendiente';
    }

    public function getEstadoBadgeAttribute()
    {
        return $this->realizado ? 'badge-success' : 'badge-danger';
    }

    public function getImagenUrlAttribute()
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }

    // ==========================================
    // MUTATORS
    // ==========================================
    
    public function setRealizadoAttribute($value)
    {
        $this->attributes['realizado'] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}








