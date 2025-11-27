<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'funciones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'es_predefinida',
        'activo',
    ];

    protected $casts = [
        'es_predefinida' => 'boolean',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function personal()
    {
        return $this->hasMany(Personal::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePredefinidas($query)
    {
        return $query->where('es_predefinida', true);
    }

    public function scopeConductores($query)
    {
        return $query->where('nombre', 'Conductor');
    }

    public function scopeAyudantes($query)
    {
        return $query->where('nombre', 'Ayudante');
    }

    // Métodos auxiliares
    public function puedeEliminar()
    {
        return !$this->es_predefinida;
    }
}

