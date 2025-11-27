<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cambio extends Model
{
    use HasFactory;

    protected $table = 'cambios';

    protected $fillable = [
        'programacion_id',
        'tipo_cambio',
        'valor_anterior',
        'valor_anterior_nombre',
        'valor_nuevo',
        'valor_nuevo_nombre',
        'motivo_id',
        'notas',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    
    public function programacion()
    {
        return $this->belongsTo(Programaciones::class, 'programacion_id');
    }

    public function motivo()
    {
        return $this->belongsTo(Motivo::class, 'motivo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ==========================================
    // ACCESSORS
    // ==========================================
    
    public function getTipoCambioFormateadoAttribute()
    {
        $tipos = [
            'turno' => 'Cambio de Turno',
            'vehiculo' => 'Cambio de Vehículo',
            'personal' => 'Cambio de Personal',
        ];

        return $tipos[$this->tipo_cambio] ?? ucfirst($this->tipo_cambio);
    }

    public function getDescripcionCambioAttribute()
    {
        return "{$this->valor_anterior_nombre} → {$this->valor_nuevo_nombre}";
    }

    public function getFechaFormateadaAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    // ==========================================
    // SCOPES
    // ==========================================
    
    public function scopePorProgramacion($query, $programacionId)
    {
        return $query->where('programacion_id', $programacionId);
    }

    public function scopeRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}


















