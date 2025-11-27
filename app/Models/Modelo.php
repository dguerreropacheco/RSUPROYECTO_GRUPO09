<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modelo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modelos';

    protected $fillable = [
        'marca_id',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorMarca($query, $marcaId)
    {
        return $query->where('marca_id', $marcaId);
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return "{$this->marca->nombre} {$this->nombre}";
    }
}
