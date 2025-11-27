<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VehiculoImagen extends Model
{
    use HasFactory;

    protected $table = 'vehiculo_imagenes';

    protected $fillable = [
        'vehiculo_id',
        'ruta_imagen',
        'es_perfil',
        'orden',
    ];

    protected $casts = [
        'es_perfil' => 'boolean',
        'orden' => 'integer',
    ];

    // Relaciones
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    // Accessors
    public function getUrlImagenAttribute()
    {
        return asset('storage/' . $this->ruta_imagen);
    }

    // Mutators
    public function setEsPerfilAttribute($value)
    {
        if ($value) {
            // Si se establece como perfil, quitar el perfil de las demás imágenes del mismo vehículo
            VehiculoImagen::where('vehiculo_id', $this->vehiculo_id)
                ->where('id', '!=', $this->id)
                ->update(['es_perfil' => false]);
        }
        $this->attributes['es_perfil'] = $value;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($imagen) {
            // Eliminar el archivo físico al borrar el registro
            if (Storage::disk('public')->exists($imagen->ruta_imagen)) {
                Storage::disk('public')->delete($imagen->ruta_imagen);
            }
        });
    }
}