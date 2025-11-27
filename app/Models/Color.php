<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'colores';

    protected $fillable = [
        'nombre',
        'codigo_rgb',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Accessors
    public function getCodigoRgbFormateadoAttribute()
    {
        return strtoupper($this->codigo_rgb);
    }
}
