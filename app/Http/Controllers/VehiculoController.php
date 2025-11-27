<?php
namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\VehiculoImagen;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\TipoVehiculo;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VehiculoController extends Controller
{
    public function index()
    {
        // Si es una petición AJAX (para DataTables)
        if (request()->ajax()) {
            try {
                $vehiculos = Vehiculo::with(['marca', 'modelo', 'tipoVehiculo', 'color', 'imagenPerfil'])
                    ->orderBy('codigo')
                    ->get()
                    ->map(function ($vehiculo) {
                        return [
                            'id' => $vehiculo->id,
                            'codigo' => $vehiculo->codigo,
                            'nombre' => $vehiculo->nombre,
                            'placa' => $vehiculo->placa,
                            'marca' => optional($vehiculo->marca)->nombre ?? 'N/A',
                            'modelo' => optional($vehiculo->modelo)->nombre ?? 'N/A',
                            'tipo' => optional($vehiculo->tipoVehiculo)->nombre ?? 'N/A',
                            'anio' => $vehiculo->anio,
                            'activo' => $vehiculo->activo,
                            'disponible' => $vehiculo->disponible,
                            'imagen_perfil' => $vehiculo->imagenPerfil 
                                ? asset('storage/' . $vehiculo->imagenPerfil->ruta_imagen) 
                                : asset('storage/images/default.png'),
                        ];
                    });

                return response()->json(['data' => $vehiculos]);
            } catch (\Exception $e) {
                Log::error('Error al cargar vehículos: ' . $e->getMessage());
                return response()->json([
                    'data' => [],
                    'error' => 'Error al cargar los vehículos'
                ], 500);
            }
        }

        // Si es una petición normal, cargar la vista
        $marcas = Marca::where('activo', true)->orderBy('nombre')->get();
        $tipos = TipoVehiculo::where('activo', true)->orderBy('nombre')->get();
        $colores = Color::where('activo', true)->orderBy('nombre')->get();
        
        return view('vehiculos.vehiculos.index', compact('marcas', 'tipos', 'colores'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:50|unique:vehiculos,codigo',
            'placa' => [
                'required',
                'string',
                'max:20',
                'unique:vehiculos,placa',
                'regex:/^([A-Z0-9]{6}|[A-Z0-9]{2}-[A-Z0-9]{4}|[A-Z0-9]{3}-[A-Z0-9]{3})$/'
            ],
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'tipo_vehiculo_id' => 'required|exists:tipos_vehiculo,id',
            'color_id' => 'required|exists:colores,id',
            'anio' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'numero_motor' => 'nullable|string|max:100',
            'nombre' => 'nullable|string|max:100',
            'capacidad_carga' => 'nullable|numeric|min:0',
            'capacidad_ocupacion' => 'nullable|integer|min:1',
            'capacidad_compactacion' => 'nullable|numeric|min:0',      // ← AGREGAR
    'capacidad_combustible' => 'nullable|numeric|min:0', 
            'observaciones' => 'nullable|string',
            'disponible' => 'boolean',
            'activo' => 'boolean',
        ], [
            'codigo.required' => 'El código es obligatorio',
            'codigo.unique' => 'El código ya está registrado',
            'placa.required' => 'La placa es obligatoria',
            'placa.unique' => 'La placa ya está registrada',
            'placa.regex' => 'El formato de placa no es válido (XXXXXX, XX-XXXX o XXX-XXX)',
            'marca_id.required' => 'La marca es obligatoria',
            'modelo_id.required' => 'El modelo es obligatorio',
            'tipo_vehiculo_id.required' => 'El tipo de vehículo es obligatorio',
            'color_id.required' => 'El color es obligatorio',
            'anio.required' => 'El año es obligatorio',
            'anio.min' => 'El año debe ser mayor o igual a 1990',
            'anio.max' => 'El año no puede ser mayor a ' . (date('Y') + 1),
            'capacidad_carga.numeric' => 'La capacidad de carga debe ser un número',
            'capacidad_ocupacion.integer' => 'La capacidad de ocupación debe ser un número entero',  // ← AGREGAR
    'capacidad_ocupacion.min' => 'La capacidad de ocupación debe ser al menos 1',  // ← AGREGAR
        'capacidad_compactacion.numeric' => 'La capacidad de compactación debe ser un número',    // ← AGREGAR
    'capacidad_compactacion.min' => 'La capacidad de compactación debe ser mayor o igual a 0',  // ← AGREGAR
    'capacidad_combustible.numeric' => 'La capacidad de combustible debe ser un número',        // ← AGREGAR
    'capacidad_combustible.min' => 'La capacidad de combustible debe ser mayor o igual a 0',  
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehiculo = Vehiculo::create([
                'codigo' => strtoupper($request->codigo),
                'placa' => strtoupper($request->placa),
                'marca_id' => $request->marca_id,
                'modelo_id' => $request->modelo_id,
                'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
                'color_id' => $request->color_id,
                'anio' => $request->anio,
                'numero_motor' => $request->numero_motor,
                'nombre' => $request->nombre,
                'capacidad_carga' => $request->capacidad_carga,
                'capacidad_ocupacion' => $request->capacidad_ocupacion,
                 'capacidad_compactacion' => $request->capacidad_compactacion,     // ← AGREGAR
    'capacidad_combustible' => $request->capacidad_combustible, 
                'observaciones' => $request->observaciones,
                'disponible' => $request->disponible ?? true,
                'activo' => $request->activo ?? true,
            ]);

            $vehiculo->load(['marca', 'modelo', 'tipoVehiculo', 'color', 'imagenPerfil']);

            return response()->json([
                'success' => true,
                'message' => 'Vehículo creado exitosamente',
                'data' => $vehiculo
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear vehículo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el vehículo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Vehiculo $vehiculo)
    {
        try {
            $vehiculo->load(['marca', 'modelo', 'tipoVehiculo', 'color', 'imagenes']);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $vehiculo->id,
                    'codigo' => $vehiculo->codigo,
                    'placa' => $vehiculo->placa,
                    'marca_id' => $vehiculo->marca_id,
                    'modelo_id' => $vehiculo->modelo_id,
                    'tipo_vehiculo_id' => $vehiculo->tipo_vehiculo_id,
                    'color_id' => $vehiculo->color_id,
                    'anio' => $vehiculo->anio,
                    'capacidad_carga' => $vehiculo->capacidad_carga,
                    'capacidad_ocupacion' => $vehiculo->capacidad_ocupacion,
                     'capacidad_compactacion' => $vehiculo->capacidad_compactacion,     // ← AGREGAR
        'capacidad_combustible' => $vehiculo->capacidad_combustible,
                    'numero_motor' => $vehiculo->numero_motor,
                    'nombre' => $vehiculo->nombre,
                    'observaciones' => $vehiculo->observaciones,
                    'disponible' => $vehiculo->disponible,
                    'activo' => $vehiculo->activo,
                    'marca' => optional($vehiculo->marca)->nombre,
                    'modelo' => optional($vehiculo->modelo)->nombre,
                    'tipo' => optional($vehiculo->tipoVehiculo)->nombre,
                    'color' => optional($vehiculo->color)->nombre,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener vehículo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los datos del vehículo'
            ], 500);
        }
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:50|unique:vehiculos,codigo,' . $vehiculo->id,
            'placa' => [
                'required',
                'string',
                'max:20',
                'unique:vehiculos,placa,' . $vehiculo->id,
                'regex:/^([A-Z0-9]{6}|[A-Z0-9]{2}-[A-Z0-9]{4}|[A-Z0-9]{3}-[A-Z0-9]{3})$/'
            ],
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'tipo_vehiculo_id' => 'required|exists:tipos_vehiculo,id',
            'color_id' => 'required|exists:colores,id',
            'anio' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'numero_motor' => 'nullable|string|max:100',
            'nombre' => 'nullable|string|max:100',
            'capacidad_carga' => 'nullable|numeric|min:0',
            'capacidad_ocupacion' => 'nullable|integer|min:1',
            'capacidad_compactacion' => 'nullable|numeric|min:0',      // ← AGREGAR
    'capacidad_combustible' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'disponible' => 'boolean',
            'activo' => 'boolean',
        ], [
            'codigo.required' => 'El código es obligatorio',
            'codigo.unique' => 'El código ya está registrado',
            'placa.required' => 'La placa es obligatoria',
            'placa.unique' => 'La placa ya está registrada',
            'placa.regex' => 'El formato de placa no es válido (XXXXXX, XX-XXXX o XXX-XXX)',
            'marca_id.required' => 'La marca es obligatoria',
            'modelo_id.required' => 'El modelo es obligatorio',
            'tipo_vehiculo_id.required' => 'El tipo de vehículo es obligatorio',
            'color_id.required' => 'El color es obligatorio',
            'anio.required' => 'El año es obligatorio',
            'anio.min' => 'El año debe ser mayor o igual a 1990',
            'anio.max' => 'El año no puede ser mayor a ' . (date('Y') + 1),
            'capacidad_carga.numeric' => 'La capacidad de carga debe ser un número',
            'capacidad_ocupacion.integer' => 'La capacidad de ocupación debe ser un número entero',  // ← AGREGAR
    'capacidad_ocupacion.min' => 'La capacidad de ocupación debe ser al menos 1',  // ← AGREGAR
    'capacidad_compactacion.numeric' => 'La capacidad de compactación debe ser un número',    // ← AGREGAR
    'capacidad_compactacion.min' => 'La capacidad de compactación debe ser mayor o igual a 0',  // ← AGREGAR
    'capacidad_combustible.numeric' => 'La capacidad de combustible debe ser un número',        // ← AGREGAR
    'capacidad_combustible.min' => 'La capacidad de combustible debe ser mayor o igual a 0',  
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vehiculo->update([
                'codigo' => strtoupper($request->codigo),
                'placa' => strtoupper($request->placa),
                'marca_id' => $request->marca_id,
                'modelo_id' => $request->modelo_id,
                'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
                'color_id' => $request->color_id,
                'anio' => $request->anio,
                'numero_motor' => $request->numero_motor,
                'nombre' => $request->nombre,
                'capacidad_carga' => $request->capacidad_carga,
                'capacidad_ocupacion' => $request->capacidad_ocupacion, 
                'capacidad_compactacion' => $request->capacidad_compactacion,     // ← AGREGAR
    'capacidad_combustible' => $request->capacidad_combustible,
                'observaciones' => $request->observaciones,
                'disponible' => $request->disponible ?? $vehiculo->disponible,
                'activo' => $request->activo ?? $vehiculo->activo,
            ]);

            $vehiculo->load(['marca', 'modelo', 'tipoVehiculo', 'color', 'imagenPerfil']);

            return response()->json([
                'success' => true,
                'message' => 'Vehículo actualizado exitosamente',
                'data' => $vehiculo
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar vehículo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el vehículo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Vehiculo $vehiculo)
    {
        try {
            // Verificar si tiene programaciones asociadas
            /*
            if ($vehiculo->programaciones()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el vehículo porque tiene programaciones asociadas'
                ], 409);
            }*/

            // Eliminar imágenes asociadas (el evento del modelo se encarga de eliminar archivos)
            foreach ($vehiculo->imagenes as $imagen) {
                $imagen->delete();
            }

            $vehiculo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vehículo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar vehículo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el vehículo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleActivo(Vehiculo $vehiculo)
    {
        try {
            $vehiculo->update(['activo' => !$vehiculo->activo]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'activo' => $vehiculo->activo
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleDisponible(Vehiculo $vehiculo)
    {
        try {
            $vehiculo->update(['disponible' => !$vehiculo->disponible]);

            return response()->json([
                'success' => true,
                'message' => 'Disponibilidad actualizada exitosamente',
                'disponible' => $vehiculo->disponible
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cambiar disponibilidad: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la disponibilidad: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload images
     */
    public function uploadImagen(Request $request, Vehiculo $vehiculo)
    {
        $validator = Validator::make($request->all(), [
            'imagen' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'es_perfil' => 'boolean',
        ], [
            'imagen.required' => 'Debe seleccionar una imagen',
            'imagen.image' => 'El archivo debe ser una imagen',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg',
            'imagen.max' => 'La imagen no debe superar los 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $imagen = $request->file('imagen');
            $path = $imagen->store('vehiculos', 'public');

            $esPerfil = $request->boolean('es_perfil', false);
            
            // Si es perfil, quitar el perfil de las demás
            if ($esPerfil) {
                VehiculoImagen::where('vehiculo_id', $vehiculo->id)
                    ->update(['es_perfil' => false]);
            }

            // Si es la primera imagen, hacerla perfil por defecto
            if ($vehiculo->imagenes()->count() === 0) {
                $esPerfil = true;
            }

            $vehiculoImagen = VehiculoImagen::create([
                'vehiculo_id' => $vehiculo->id,
                'ruta_imagen' => $path,
                'es_perfil' => $esPerfil,
                'orden' => $vehiculo->imagenes()->max('orden') + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen cargada exitosamente',
                'data' => [
                    'id' => $vehiculoImagen->id,
                    'url' => asset('storage/' . $vehiculoImagen->ruta_imagen),
                    'es_perfil' => $vehiculoImagen->es_perfil
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al cargar imagen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set profile image
     */
    public function setImagenPerfil(Vehiculo $vehiculo, VehiculoImagen $imagen)
    {
        try {
            if ($imagen->vehiculo_id !== $vehiculo->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'La imagen no pertenece a este vehículo'
                ], 403);
            }

            // Quitar perfil de todas las imágenes
            VehiculoImagen::where('vehiculo_id', $vehiculo->id)
                ->update(['es_perfil' => false]);

            // Establecer como perfil
            $imagen->update(['es_perfil' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen de perfil actualizada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al establecer imagen de perfil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al establecer imagen de perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete image
     */
    public function deleteImagen(Vehiculo $vehiculo, VehiculoImagen $imagen)
    {
        try {
            if ($imagen->vehiculo_id !== $vehiculo->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'La imagen no pertenece a este vehículo'
                ], 403);
            }

            $esPerfil = $imagen->es_perfil;
            $imagen->delete(); // El evento del modelo se encarga de eliminar el archivo

            // Si era perfil, establecer otra como perfil
            if ($esPerfil) {
                $nuevoPerfil = $vehiculo->imagenes()->first();
                if ($nuevoPerfil) {
                    $nuevoPerfil->update(['es_perfil' => true]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar imagen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get images
     */
    public function getImagenes(Vehiculo $vehiculo)
    {
        try {
            $imagenes = $vehiculo->imagenes()
                ->orderBy('es_perfil', 'desc')
                ->orderBy('orden')
                ->get()
                ->map(function ($imagen) {
                    return [
                        'id' => $imagen->id,
                        'url' => asset('storage/' . $imagen->ruta_imagen),
                        'es_perfil' => $imagen->es_perfil
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $imagenes
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener imágenes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las imágenes'
            ], 500);
        }
    }
}