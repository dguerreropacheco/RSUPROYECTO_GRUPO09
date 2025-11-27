<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motivo extends Model
{
    use HasFactory;

    protected $table = 'motivos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    
    public function cambios()
    {
        return $this->hasMany(Cambio::class, 'motivo_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================
    
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================
    
    public function getEstadoLabelAttribute()
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    public function getEstadoBadgeAttribute()
    {
        return $this->activo ? 'badge-success' : 'badge-secondary';
    }
}

