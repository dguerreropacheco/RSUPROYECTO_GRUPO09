<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoPersonal extends Model
{
    use HasFactory;

    protected $table = 'grupospersonal';

    protected $fillable = [
        'nombre',
        'zona_id',
        'turno_id',
        'vehiculo_id',
        'dias',
        'estado',
    ];

    protected $appends = [
        'conductor_id', 
        'ayudante1_id', 
        'ayudante2_id',
        'estado_label' 
    ];

    /**
     * Relación: Un grupo pertenece a una zona.
     */
    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }

    /**
     * Relación: Un grupo pertenece a un turno.
     */
    public function turno()
    {
        return $this->belongsTo(Turno::class, 'turno_id');
    }

    /**
     * Relación: Un grupo está asociado a un vehículo.
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }
    
    /**
     * Mutador para el estado.
     */
    public function getEstadoLabelAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }


public function personal()
{
    // Define la relación con la tabla 'personal' usando la tabla pivote 'confgrupos'
    return $this->belongsToMany(Personal::class, 'confgrupos', 'grupopersonal_id', 'personal_id')->orderBy('confgrupos.id'); ;
}


// 2. Accessor para Conductor (primer elemento)
public function getConductorIdAttribute()
{
    return $this->personal->get(0)->id ?? null;
}

public function getAyudante1IdAttribute()
{
    return $this->personal->get(1)->id ?? null;
}

public function getAyudante2IdAttribute()
{
    return $this->personal->get(2)->id ?? null;
}
}