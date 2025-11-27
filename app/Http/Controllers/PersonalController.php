<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\Funcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // DESPUÉS (agregar esta línea):
$personal = Personal::with(['funcion', 'contratoActivo'])
                   ->whereNull('deleted_at')  // ← AGREGAR
                   ->orderBy('nombres')
                   ->get();
        
        $funciones = Funcion::activos()->orderBy('nombre')->get();
        
        return view('personal.personal.index', compact('personal', 'funciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|size:8|unique:personal,dni|regex:/^[0-9]{8}$/',
            'nombres' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150|unique:personal,email',
            'direccion' => 'nullable|string',
            'licencia_conducir' => 'nullable|string|max:20',
            'fecha_vencimiento_licencia' => 'nullable|date|after:today',
            'funcion_id' => 'required|exists:funciones,id',
            'clave' => 'required|string|min:4|max:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'activo' => 'boolean'
        ], [
            'dni.required' => 'El DNI es obligatorio',
            'dni.size' => 'El DNI debe tener exactamente 8 dígitos',
            'dni.unique' => 'Este DNI ya está registrado',
            'dni.regex' => 'El DNI debe contener solo números',
            'nombres.required' => 'Los nombres son obligatorios',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio',
            'apellido_materno.required' => 'El apellido materno es obligatorio',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'fecha_vencimiento_licencia.after' => 'La fecha de vencimiento debe ser posterior a hoy',
            'funcion_id.required' => 'La función es obligatoria',
            'funcion_id.exists' => 'La función seleccionada no existe',
            'clave.required' => 'La clave es obligatoria',
            'clave.min' => 'La clave debe tener mínimo 4 caracteres',
            'clave.max' => 'La clave debe tener máximo 6 caracteres',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'La foto debe ser de tipo: jpeg, png, jpg',
            'foto.max' => 'La foto no debe superar los 2MB'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'dni' => $request->dni,
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'direccion' => $request->direccion,
                'licencia_conducir' => $request->licencia_conducir,
                'fecha_vencimiento_licencia' => $request->fecha_vencimiento_licencia,
                'funcion_id' => $request->funcion_id,
                'clave' => $request->clave, // El mutator lo encripta automáticamente
                'activo' => $request->activo ?? true
            ];

            // Procesar foto si existe
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $nombreArchivo = 'personal_' . $request->dni . '_' . time() . '.' . $foto->getClientOriginalExtension();
                $path = $foto->storeAs('personal', $nombreArchivo, 'public');
                $data['foto'] = $path;
            }

            $personal = Personal::create($data);
            $personal->load('funcion');

            return response()->json([
                'success' => true,
                'message' => 'Personal registrado correctamente',
                'data' => $personal
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el personal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Personal $personal)
    {
        $personal->load('funcion');
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $personal->id,
                'dni' => $personal->dni,
                'nombres' => $personal->nombres,
                'apellido_paterno' => $personal->apellido_paterno,
                'apellido_materno' => $personal->apellido_materno,
                'fecha_nacimiento' => $personal->fecha_nacimiento->format('Y-m-d'),
                'telefono' => $personal->telefono,
                'email' => $personal->email,
                'direccion' => $personal->direccion,
                'licencia_conducir' => $personal->licencia_conducir,
                'fecha_vencimiento_licencia' => $personal->fecha_vencimiento_licencia ? $personal->fecha_vencimiento_licencia->format('Y-m-d') : null,
                'funcion_id' => $personal->funcion_id,
                'activo' => $personal->activo,
                'foto' => $personal->foto,
                'foto_url' => $personal->foto_url,
                'funcion' => $personal->funcion ? $personal->funcion->nombre : null
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Personal $personal)
    {
        $validator = Validator::make($request->all(), [
            'dni' => [
                'required',
                'string',
                'size:8',
                Rule::unique('personal', 'dni')->ignore($personal->id),
                'regex:/^[0-9]{8}$/'
            ],
            'nombres' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'telefono' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('personal', 'email')->ignore($personal->id)
            ],
            'direccion' => 'nullable|string',
            'licencia_conducir' => 'nullable|string|max:20',
            'fecha_vencimiento_licencia' => 'nullable|date|after:today',
            'funcion_id' => 'required|exists:funciones,id',
            'clave' => 'nullable|string|min:4|max:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'activo' => 'boolean'
        ], [
            'dni.required' => 'El DNI es obligatorio',
            'dni.size' => 'El DNI debe tener exactamente 8 dígitos',
            'dni.unique' => 'Este DNI ya está registrado',
            'dni.regex' => 'El DNI debe contener solo números',
            'nombres.required' => 'Los nombres son obligatorios',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio',
            'apellido_materno.required' => 'El apellido materno es obligatorio',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'fecha_vencimiento_licencia.after' => 'La fecha de vencimiento debe ser posterior a hoy',
            'funcion_id.required' => 'La función es obligatoria',
            'clave.min' => 'La clave debe tener mínimo 4 caracteres',
            'clave.max' => 'La clave debe tener máximo 6 caracteres',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'La foto debe ser de tipo: jpeg, png, jpg',
            'foto.max' => 'La foto no debe superar los 2MB'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'dni' => $request->dni,
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'direccion' => $request->direccion,
                'licencia_conducir' => $request->licencia_conducir,
                'fecha_vencimiento_licencia' => $request->fecha_vencimiento_licencia,
                'funcion_id' => $request->funcion_id,
                'activo' => $request->activo ?? $personal->activo
            ];

            // Actualizar clave solo si se proporciona
            if ($request->filled('clave')) {
                $data['clave'] = $request->clave;
            }

            // Procesar foto si existe
            if ($request->hasFile('foto')) {
                // Eliminar foto anterior si existe
                if ($personal->foto && Storage::disk('public')->exists($personal->foto)) {
                    Storage::disk('public')->delete($personal->foto);
                }

                $foto = $request->file('foto');
                $nombreArchivo = 'personal_' . $request->dni . '_' . time() . '.' . $foto->getClientOriginalExtension();
                $path = $foto->storeAs('personal', $nombreArchivo, 'public');
                $data['foto'] = $path;
            }

            $personal->update($data);
            $personal->load('funcion');

            return response()->json([
                'success' => true,
                'message' => 'Personal actualizado correctamente',
                'data' => $personal
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el personal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Personal $personal)
{
    try {
        // Log para debugging
        \Log::info('Intentando eliminar personal', [
            'id' => $personal->id,
            'nombre' => $personal->nombre_completo,
            'tiene_contratos' => $personal->contratos()->count(),
            'tiene_asistencias' => $personal->asistencias()->count()
        ]);

        // Verificar si tiene contratos asociados
        $contratoCount = $personal->contratos()->count();
        if ($contratoCount > 0) {
            \Log::warning('No se puede eliminar: tiene contratos', [
                'personal_id' => $personal->id,
                'contratos' => $contratoCount
            ]);
            
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar el personal porque tiene {$contratoCount} contrato(s) asociado(s)"
            ], 409);
        }

        // Verificar si tiene asistencias asociadas
        
        $asistenciaCount = $personal->asistencias()->count();
        if ($asistenciaCount > 0) {
            \Log::warning('No se puede eliminar: tiene asistencias', [
                'personal_id' => $personal->id,
                'asistencias' => $asistenciaCount
            ]);
            
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar el personal porque tiene {$asistenciaCount} asistencia(s) registrada(s)"
            ], 409);
        }

        // Verificar si tiene programaciones
        /*
        if (method_exists($personal, 'programaciones')) {
            $programacionCount = $personal->programaciones()->count();
            if ($programacionCount > 0) {
                \Log::warning('No se puede eliminar: tiene programaciones', [
                    'personal_id' => $personal->id,
                    'programaciones' => $programacionCount
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar el personal porque tiene {$programacionCount} programación(ones) asociada(s)"
                ], 409);
            }
        }*/


        // Guardar info de la foto antes de eliminar
        $fotoPath = $personal->foto;
        $nombreCompleto = $personal->nombre_completo;

        // Eliminar foto del storage si existe
        if ($fotoPath && \Storage::disk('public')->exists($fotoPath)) {
            \Storage::disk('public')->delete($fotoPath);
            \Log::info('Foto eliminada', ['path' => $fotoPath]);
        }

        // Eliminar el registro (soft delete si está configurado)
        $personal->delete();

        \Log::info('Personal eliminado exitosamente', [
            'id' => $personal->id,
            'nombre' => $nombreCompleto
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Personal eliminado correctamente'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error al eliminar personal', [
            'personal_id' => $personal->id ?? 'desconocido',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar el personal: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Toggle active status
     */
    public function toggleActivo(Personal $personal)
    {
        try {
            $personal->update(['activo' => !$personal->activo]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'activo' => $personal->activo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete photo
     */
    public function deleteFoto(Personal $personal)
    {
        try {
            if ($personal->foto && Storage::disk('public')->exists($personal->foto)) {
                Storage::disk('public')->delete($personal->foto);
                $personal->update(['foto' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto eliminada correctamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'El personal no tiene foto'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get personal by function (for selects)
     */
    public function getPorFuncion($funcionId)
    {
        $personal = Personal::activos()
                           ->contratados()
                           ->where('funcion_id', $funcionId)
                           ->select('id', 'nombres', 'apellido_paterno', 'apellido_materno', 'dni')
                           ->orderBy('nombres')
                           ->get()
                           ->map(function ($p) {
                               return [
                                   'id' => $p->id,
                                   'nombre_completo' => $p->nombre_completo,
                                   'dni' => $p->dni
                               ];
                           });

        return response()->json([
            'success' => true,
            'data' => $personal
        ]);
    }

    /**
     * Get conductores activos
     */
    public function getConductores()
    {
        $conductores = Personal::activos()
                              ->contratados()
                              ->conductores()
                              ->select('id', 'nombres', 'apellido_paterno', 'apellido_materno', 'dni', 'licencia_conducir')
                              ->orderBy('nombres')
                              ->get()
                              ->map(function ($p) {
                                  return [
                                      'id' => $p->id,
                                      'nombre_completo' => $p->nombre_completo,
                                      'dni' => $p->dni,
                                      'licencia' => $p->licencia_conducir
                                  ];
                              });

        return response()->json([
            'success' => true,
            'data' => $conductores
        ]);
    }

    /**
     * Get ayudantes activos
     */
    public function getAyudantes()
    {
        $ayudantes = Personal::activos()
                            ->contratados()
                            ->ayudantes()
                            ->select('id', 'nombres', 'apellido_paterno', 'apellido_materno', 'dni')
                            ->orderBy('nombres')
                            ->get()
                            ->map(function ($p) {
                                return [
                                    'id' => $p->id,
                                    'nombre_completo' => $p->nombre_completo,
                                    'dni' => $p->dni
                                ];
                            });

        return response()->json([
            'success' => true,
            'data' => $ayudantes
        ]);
    }


    
}