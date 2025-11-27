<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'turnos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'hour_in',
        'hour_out',
    ];

    // Las columnas 'id', 'created_at', 'updated_at' son manejadas automáticamente por Eloquent.
}