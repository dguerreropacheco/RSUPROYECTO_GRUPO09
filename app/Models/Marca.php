<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Marca extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'marcas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'logo',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function modelos()
    {
        return $this->hasMany(Modelo::class);
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
     public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return asset('storage/images/default.png');
    }

    // Evento para eliminar el logo al eliminar la marca
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($marca) {
            if ($marca->logo && Storage::disk('public')->exists($marca->logo)) {
                Storage::disk('public')->delete($marca->logo);
            }
        });
    }
}