<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Asistencia::with(['personal.funcion']);

        if ($request->filled('fecha_inicio')) {
            $query->where('fecha', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->where('fecha', '<=', $request->fecha_fin);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('personal', function($q) use ($buscar) {
                $q->where('dni', 'like', "%{$buscar}%")
                  ->orWhere('nombres', 'like', "%{$buscar}%")
                  ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                  ->orWhere('apellido_materno', 'like', "%{$buscar}%");
            });
        }

        $asistencias = $query->orderBy('fecha', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();

        $personal = Personal::where('activo', true)
                           ->with('funcion')
                           ->orderBy('nombres')
                           ->get();

        return view('asistencias.index', compact('asistencias', 'personal'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'personal_id' => 'required|exists:personal,id',
            'fecha' => 'required|date',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i|after:hora_entrada',
            'estado' => 'required|in:presente,ausente,tardanza,permiso',
            'observaciones' => 'nullable|string|max:500'
        ], [
            'personal_id.required' => 'Debe seleccionar un empleado',
            'personal_id.exists' => 'El empleado seleccionado no existe',
            'fecha.required' => 'La fecha es obligatoria',
            'fecha.date' => 'La fecha no es válida',
            'hora_entrada.date_format' => 'El formato de hora de entrada debe ser HH:MM',
            'hora_salida.date_format' => 'El formato de hora de salida debe ser HH:MM',
            'hora_salida.after' => 'La hora de salida debe ser posterior a la hora de entrada',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado seleccionado no es válido',
            'observaciones.max' => 'Las observaciones no pueden superar los 500 caracteres'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $personal = Personal::with('funcion')->find($request->personal_id);
            $es_transportista = ($personal->funcion->nombre === 'Conductor' || $personal->funcion->nombre === 'Ayudante');

            Log::debug("Registro Store - Personal ID: {$personal->id}, Fecha: {$request->fecha}, Es Transportista: " . ($es_transportista ? 'Sí' : 'No'));

            // ----------------------------------------------------
            // LÓGICA DE VALIDACIÓN DE REGISTROS DUPLICADOS/ADMINISTRATIVOS (Permiso/Ausente)
            // ----------------------------------------------------
            
            $es_nuevo_registro_administrativo = in_array($request->estado, ['permiso', 'ausente']);

            // 1. Contar registros existentes (Entrada/Salida, Permiso, Ausente)
            $registro_existente = Asistencia::where('personal_id', $personal->id)
                ->whereDate('fecha', $request->fecha);
            
            $registro_administrativo_existente = (clone $registro_existente)->whereIn('estado', ['permiso', 'ausente'])->exists();
            $registro_con_entrada_existente = (clone $registro_existente)->whereNotNull('hora_entrada')->exists();


            if ($registro_administrativo_existente) {
                 // Bloquear si ya existe un registro administrativo
                 $msg = 'Ya existe un registro administrativo (Permiso o Ausente) para este empleado en la fecha ' . Carbon::parse($request->fecha)->format('d/m/Y') . '. No se puede registrar otra asistencia en el mismo día.';
                 Log::error("Registro Store - Rechazado: Ya existe un registro administrativo.");
                 return response()->json(['success' => false, 'message' => $msg], 403);
            }
            
            if ($es_nuevo_registro_administrativo && $registro_con_entrada_existente) {
                 // Bloquear si se intenta guardar un Permiso/Ausente cuando ya hay una entrada
                 $msg = 'Ya existe un registro de entrada/salida para este empleado en la fecha ' . Carbon::parse($request->fecha)->format('d/m/Y') . '. No se puede registrar un estado administrativo (Permiso/Ausente).';
                 Log::error("Registro Store - Rechazado: Intento de registro administrativo con entrada existente.");
                 return response()->json(['success' => false, 'message' => $msg], 403);
            }
            
            // Si el registro es administrativo (Permiso/Ausente), no necesitamos más validaciones de entrada/salida
            if ($es_nuevo_registro_administrativo) {
                 // Se crea el registro administrativo
                 $asistencia = Asistencia::create([
                    'personal_id' => $request->personal_id,
                    'fecha' => $request->fecha,
                    'hora_entrada' => null, // Asegurar que sea null
                    'hora_salida' => null, // Asegurar que sea null
                    'estado' => $request->estado,
                    'observaciones' => $request->observaciones
                ]);

                $asistencia->load('personal.funcion');

                return response()->json([
                    'success' => true,
                    'message' => 'Asistencia administrativa registrada correctamente',
                    'asistencia' => $asistencia
                ]);
            }

            // ----------------------------------------------------
            // LÓGICA DE VALIDACIÓN PARA REGISTROS DE ENTRADA/SALIDA
            // ----------------------------------------------------
            
            // Contar solo las entradas para la lógica de límites (solo si hora_entrada está lleno en la solicitud)
            $entradas_registradas = (clone $registro_existente)->whereNotNull('hora_entrada')->count();
            
            $total_programaciones_hoy = 1;

            if ($es_transportista) {
                $programaciones_hoy = DB::table('programacion_personal')
                    ->join('programaciones', 'programacion_personal.programacion_id', '=', 'programaciones.id')
                    ->join('turnos', 'programaciones.turno_id', '=', 'turnos.id')
                    ->select('turnos.hour_in')
                    ->where('programacion_personal.personal_id', $personal->id)
                    ->whereDate('programacion_personal.fecha_dia', $request->fecha)
                    ->count(); 
                
                $total_programaciones_hoy = $programaciones_hoy;

                if ($total_programaciones_hoy === 0) {
                    Log::error("Registro Store - Rechazado: Conductor/Ayudante sin programación.");
                    return response()->json([
                        'success' => false,
                        'message' => 'El empleado Conductor/Ayudante no tiene una programación de transporte asignada para esa fecha (' . Carbon::parse($request->fecha)->format('d/m/Y') . ').'
                    ], 403);
                }
            }
            
            Log::debug("Registro Store - Entradas Registradas: {$entradas_registradas}, Límite Programado: {$total_programaciones_hoy}");

            if ($request->filled('hora_entrada')) {
                if ($entradas_registradas >= $total_programaciones_hoy) {
                    $msg = $total_programaciones_hoy === 1 
                        ? 'Este empleado solo puede tener una entrada registrada por día (' . Carbon::parse($request->fecha)->format('d/m/Y') . ').' 
                        : "Este empleado ya ha registrado el máximo de {$total_programaciones_hoy} entradas permitidas según su programación para esta fecha.";
                    
                    Log::error("Registro Store - Rechazado: Límite de entradas alcanzado ({$entradas_registradas}/{$total_programaciones_hoy}).");

                    return response()->json([
                        'success' => false,
                        'message' => $msg
                    ], 403);
                }
            }


            $asistencia = Asistencia::create([
                'personal_id' => $request->personal_id,
                'fecha' => $request->fecha,
                'hora_entrada' => $request->hora_entrada,
                'hora_salida' => $request->hora_salida,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones
            ]);

            $asistencia->load('personal.funcion');

            return response()->json([
                'success' => true,
                'message' => 'Asistencia registrada correctamente',
                'asistencia' => $asistencia
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la asistencia: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Asistencia $asistencia)
    {
        $asistencia->load('personal.funcion');
        
        return response()->json([
            'success' => true,
            'asistencia' => $asistencia
        ]);
    }

    public function update(Request $request, Asistencia $asistencia)
    {
        $validator = Validator::make($request->all(), [
            'personal_id' => 'required|exists:personal,id',
            'fecha' => 'required|date',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i|after:hora_entrada',
            'estado' => 'required|in:presente,ausente,tardanza,permiso',
            'observaciones' => 'nullable|string|max:500'
        ], [
            'personal_id.required' => 'Debe seleccionar un empleado',
            'personal_id.exists' => 'El empleado seleccionado no existe',
            'fecha.required' => 'La fecha es obligatoria',
            'fecha.date' => 'La fecha no es válida',
            'hora_entrada.date_format' => 'El formato de hora de entrada debe ser HH:MM',
            'hora_salida.date_format' => 'El formato de hora de salida debe ser HH:MM',
            'hora_salida.after' => 'La hora de salida debe ser posterior a la hora de entrada',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado seleccionado no es válido',
            'observaciones.max' => 'Las observaciones no pueden superar los 500 caracteres'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $personal = Personal::with('funcion')->find($request->personal_id);
            $es_transportista = ($personal->funcion->nombre === 'Conductor' || $personal->funcion->nombre === 'Ayudante');
            $total_programaciones_hoy = 1;

            if ($es_transportista) {
                $programaciones_hoy = DB::table('programacion_personal')
                    ->join('programaciones', 'programacion_personal.programacion_id', '=', 'programaciones.id')
                    ->join('turnos', 'programaciones.turno_id', '=', 'turnos.id')
                    ->where('programacion_personal.personal_id', $personal->id)
                    ->whereDate('programacion_personal.fecha_dia', $request->fecha)
                    ->count(); 
                $total_programaciones_hoy = $programaciones_hoy;
            }

            // ----------------------------------------------------
            // LÓGICA DE VALIDACIÓN DE ACTUALIZACIÓN ADMINISTRATIVA/ENTRADA
            // ----------------------------------------------------
            
            // 1. Verificar otros registros existentes para ese día (excluyendo el actual)
            $otros_registros = Asistencia::where('personal_id', $request->personal_id)
                ->whereDate('fecha', $request->fecha)
                ->where('id', '!=', $asistencia->id);
                
            $otros_registros_administrativos = (clone $otros_registros)->whereIn('estado', ['permiso', 'ausente'])->exists();
            $otros_registros_con_entrada = (clone $otros_registros)->whereNotNull('hora_entrada')->exists();
            
            $es_nuevo_estado_administrativo = in_array($request->estado, ['permiso', 'ausente']);
            $registro_actual_pierde_entrada = !is_null($asistencia->hora_entrada) && !$request->filled('hora_entrada');


            // A. Bloquear si se intenta convertir a administrativo (Permiso/Ausente) y ya existe otra entrada/administrativo
            if ($es_nuevo_estado_administrativo) {
                 if ($otros_registros->count() > 0) {
                      $msg = 'Actualización rechazada: No se puede convertir este registro a un estado administrativo ("Permiso"/"Ausente") porque ya existe otro registro de asistencia para este empleado en la fecha ' . Carbon::parse($request->fecha)->format('d/m/Y') . '.';
                      Log::error("Registro Update - Rechazado: Intento de convertir a Admin cuando ya existe otro registro.");
                      return response()->json(['success' => false, 'message' => $msg], 403);
                 }
            }
            
            // B. Bloquear si el registro actual pierde su entrada Y ya existe otro registro administrativo
            // Esto asegura que si ya hay un Permiso/Ausente, el registro actual (con entrada) no se convierta a un segundo administrativo.
            if ($registro_actual_pierde_entrada && $otros_registros_administrativos) {
                 $msg = 'Actualización rechazada: Ya existe un registro administrativo para este día. El registro actual no puede ser convertido a administrativo.';
                 return response()->json(['success' => false, 'message' => $msg], 403);
            }
            
            // C. Bloquear si se añade hora_entrada cuando ya se alcanzó el límite (y el registro actual no la tenía)
            if ($request->filled('hora_entrada') && is_null($asistencia->hora_entrada)) {
                
                $entradas_registradas_sin_actual = $otros_registros_con_entrada ? $otros_registros->whereNotNull('hora_entrada')->count() : 0;
                
                if ($entradas_registradas_sin_actual >= $total_programaciones_hoy) {
                    $msg = $total_programaciones_hoy === 1 
                         ? 'Actualización rechazada: Este empleado ya tiene una entrada registrada para ese día. No se puede agregar una segunda.' 
                         : "Actualización rechazada: Ya se han registrado {$total_programaciones_hoy} entradas, que es el máximo permitido para este empleado en la fecha.";
                    
                    Log::error("Registro Update - Rechazado: Intento de agregar entrada que excede el límite.");
                    return response()->json(['success' => false, 'message' => $msg], 403);
                }
            }


            // ----------------------------------------------------
            // EJECUTAR ACTUALIZACIÓN
            // ----------------------------------------------------
            
            // Si el estado es Permiso/Ausente, forzamos hora_entrada y hora_salida a null
            $hora_entrada = $request->hora_entrada;
            $hora_salida = $request->hora_salida;
            
            if ($es_nuevo_estado_administrativo) {
                $hora_entrada = null;
                $hora_salida = null;
            }

            $asistencia->update([
                'personal_id' => $request->personal_id,
                'fecha' => $request->fecha,
                'hora_entrada' => $hora_entrada,
                'hora_salida' => $hora_salida,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones
            ]);

            $asistencia->load('personal.funcion');

            return response()->json([
                'success' => true,
                'message' => 'Asistencia actualizada correctamente',
                'asistencia' => $asistencia
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la asistencia: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Asistencia $asistencia)
    {
        try {
            $asistencia->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asistencia eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la asistencia: ' . $e->getMessage()
            ], 500);
        }
    }

    public function mostrarRegistro()
    {
        return view('asistencias.registro');
    }

    public function procesarRegistro(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|size:8|regex:/^[0-9]{8}$/',
            'clave' => 'required|string'
        ], [
            'dni.required' => 'El DNI es obligatorio',
            'dni.size' => 'El DNI debe tener exactamente 8 dígitos',
            'dni.regex' => 'El DNI debe contener solo números',
            'clave.required' => 'La contraseña es obligatoria'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $personal = Personal::with('funcion')
                             ->where('dni', $request->dni)
                             ->where('activo', true)
                             ->first();

            if (!$personal) {
                return response()->json([
                    'success' => false,
                    'message' => 'DNI no registrado o empleado inactivo'
                ], 401);
            }

            if (!$personal->verificarClave($request->clave)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta'
                ], 401);
            }

            $hoy = Carbon::today('America/Lima');
            $ahora = Carbon::now('America/Lima');

            $es_transportista = ($personal->funcion->nombre === 'Conductor' || $personal->funcion->nombre === 'Ayudante');

            // 1. Comprobar si ya existe un registro administrativo (Permiso/Ausente)
            $registro_administrativo_existente = Asistencia::where('personal_id', $personal->id)
                                                           ->whereDate('fecha', $hoy)
                                                           ->whereIn('estado', ['permiso', 'ausente'])
                                                           ->exists();

            if ($registro_administrativo_existente) {
                return response()->json([
                     'success' => false,
                     'message' => 'Ya tienes un registro administrativo (Permiso o Ausente) para hoy. No puedes registrar entrada/salida.'
                 ], 403);
            }
            
            $entradas_registradas = Asistencia::where('personal_id', $personal->id)
                                             ->whereDate('fecha', $hoy)
                                             ->whereNotNull('hora_entrada')
                                             ->count();
            
            $asistencia_pendiente = Asistencia::where('personal_id', $personal->id)
                                             ->whereDate('fecha', $hoy)
                                             ->whereNull('hora_salida')
                                             ->orderBy('hora_entrada', 'asc')
                                             ->first();

            $hora_inicio_turno = null;
            $total_programaciones_hoy = 1;

            if ($es_transportista) {
                $programaciones_hoy = DB::table('programacion_personal')
                    ->join('programaciones', 'programacion_personal.programacion_id', '=', 'programaciones.id')
                    ->join('turnos', 'programaciones.turno_id', '=', 'turnos.id')
                    ->select('turnos.hour_in', 'programacion_personal.programacion_id')
                    ->where('programacion_personal.personal_id', $personal->id)
                    ->whereDate('programacion_personal.fecha_dia', $hoy)
                    ->orderBy('turnos.hour_in', 'asc')
                    ->get();
                
                $total_programaciones_hoy = $programaciones_hoy->count();

                if ($total_programaciones_hoy === 0) {
                     return response()->json([
                         'success' => false,
                         'message' => 'No tienes una programación de transporte asignada para el día de hoy (' . $hoy->format('d/m/Y') . ').'
                     ], 403);
                }

                if (!$asistencia_pendiente && $entradas_registradas < $total_programaciones_hoy) {
                    $indice_programacion = $entradas_registradas;
                    if (isset($programaciones_hoy[$indice_programacion])) {
                        $hora_inicio_turno = $programaciones_hoy[$indice_programacion]->hour_in;
                    }
                }
            } else {
                if (!$asistencia_pendiente && $entradas_registradas === 0) {
                    $hora_inicio_turno = '08:00:00'; 
                }
            }


            if ($asistencia_pendiente) {
                // Lógica de SALIDA
                
                if ($es_transportista) {
                    
                    if ($entradas_registradas > $total_programaciones_hoy) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Error de consistencia de datos: Se detectaron más entradas (' . $entradas_registradas . ') que programaciones (' . $total_programaciones_hoy . ') para hoy. Contacte a soporte.'
                        ], 403);
                    }
                    
                    if ($entradas_registradas > 0 && $entradas_registradas <= $total_programaciones_hoy) {
                         $asistencia_pendiente->hora_salida = $ahora; 
                         $asistencia_pendiente->save();
                        
                         $mensaje_final = ($entradas_registradas === $total_programaciones_hoy) 
                            ? 'Salida registrada correctamente (Fin de la programación)'
                            : 'Salida registrada correctamente (Programación ' . $entradas_registradas . ' de ' . $total_programaciones_hoy . ' completada)';
                        
                         return response()->json([
                             'success' => true,
                             'tipo' => 'salida',
                             'message' => $mensaje_final,
                             'empleado' => $personal->nombres . ' ' . $personal->apellido_paterno,
                             'hora' => $ahora->format('H:i:s'),
                             'fecha' => $hoy->format('d/m/Y')
                         ]);
                    } else {
                         return response()->json([
                             'success' => false,
                             'message' => 'Error: Conteo de asistencias inconsistente. Revise el log de Laravel para más detalles. (Caso Salida Transportista no cubierta: E=' . $entradas_registradas . ' P=' . $total_programaciones_hoy . ')'
                         ], 403);
                    }


                } else {
                    
                    if ($entradas_registradas === 1) {
                            $asistencia_pendiente->hora_salida = $ahora;
                            $asistencia_pendiente->save();

                            return response()->json([
                                'success' => true,
                                'tipo' => 'salida',
                                'message' => 'Salida registrada correctamente (Fin de jornada)',
                                'empleado' => $personal->nombres . ' ' . $personal->apellido_paterno,
                                'hora' => $ahora->format('H:i:s'),
                                'fecha' => $hoy->format('d/m/Y')
                            ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Error: Múltiples entradas pendientes detectadas. Contacte a soporte.'
                        ], 403);
                    }
                }

            } else {
                // Lógica de ENTRADA
                
                if ($entradas_registradas >= $total_programaciones_hoy) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Has marcado todas tus entradas y salidas por hoy' 
                    ], 403);
                }
                
                $estado = 'presente';
                $mensaje_tardanza = '';
                
                if ($hora_inicio_turno) {
                    if (strlen($hora_inicio_turno) === 5) {
                        $hora_inicio_turno .= ':00';
                    }

                    $hora_programada = Carbon::createFromFormat('H:i:s', $hora_inicio_turno, 'America/Lima')->setDate($hoy->year, $hoy->month, $hoy->day);
                    
                    $hora_limite = $hora_programada->copy()->addMinutes(15);
                    
                    if ($ahora->greaterThan($hora_limite)) {
                        $estado = 'tardanza';
                        $mensaje_tardanza = "\nLlegada con TARDANZA";
                    }
                }


                $asistencia = Asistencia::create([
                    'personal_id' => $personal->id,
                    'fecha' => $hoy,
                    'hora_entrada' => $ahora, 
                    'estado' => $estado 
                ]);
                
                $mensaje_extra = '';
                if ($es_transportista && $total_programaciones_hoy > 1) {
                    $mensaje_extra = ' (Programación ' . ($entradas_registradas + 1) . ' de ' . $total_programaciones_hoy . ')';
                }

                return response()->json([
                    'success' => true,
                    'tipo' => 'entrada',
                    'message' => 'Entrada registrada correctamente.' . $mensaje_extra . $mensaje_tardanza,
                    'empleado' => $personal->nombres . ' ' . $personal->apellido_paterno,
                    'hora' => $ahora->format('H:i:s'),
                    'fecha' => $hoy->format('d/m/Y')
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el registro. Detalle técnico: ' . $e->getMessage()
            ], 500);
        }
    }
}