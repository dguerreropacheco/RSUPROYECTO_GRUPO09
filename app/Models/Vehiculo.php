<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehiculos';

    protected $fillable = [
        'codigo',
        'placa',
        'marca_id',
        'modelo_id',
        'tipo_vehiculo_id',
        'color_id',
        'anio',
        'numero_motor',
        'nombre',
        'capacidad_carga',
        'capacidad_ocupacion',
        'capacidad_compactacion',     // ← AGREGAR
        'capacidad_combustible', 
        'observaciones',
        'disponible',
        'activo',
    ];

    protected $casts = [
        'anio' => 'integer',
        'capacidad_carga' => 'decimal:2',
        'disponible' => 'boolean',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function imagenes()
    {
        return $this->hasMany(VehiculoImagen::class);
    }

    public function imagenPerfil()
    {
        return $this->hasOne(VehiculoImagen::class)->where('es_perfil', true);
    }

    public function programaciones()
    {
        return $this->hasMany(Programacion::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true)->where('activo', true);
    }

    public function scopePorTipo($query, $tipoId)
    {
        return $query->where('tipo_vehiculo_id', $tipoId);
    }

    // Accessors
    public function getDescripcionCompletaAttribute()
    {
        return "{$this->marca->nombre} {$this->modelo->nombre} - {$this->placa}";
    }

    public function getImagenPerfilUrlAttribute()
    {
        $imagenPerfil = $this->imagenPerfil;
        return $imagenPerfil ? asset('storage/' . $imagenPerfil->ruta_imagen) : asset('storage/images/default.png');
    }
}