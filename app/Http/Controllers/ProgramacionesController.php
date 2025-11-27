<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use App\Models\Turno;
use App\Models\Cambio;
use App\Models\Motivo;
use App\Models\Funcion;
use App\Models\Personal;
use App\Models\Vehiculo;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\GrupoPersonal;
use App\Models\Programaciones;
use Illuminate\Support\Carbon;
use App\Models\VacacionesPeriodo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;
use App\Models\Contrato;           // ✅ AGREGAR
use App\Models\Vacaciones;

class ProgramacionesController extends Controller
{
   
    private const DIAS_MAP = [
        'Lunes' => 1, 'Martes' => 2, 'Miércoles' => 3, 'Jueves' => 4,
        'Viernes' => 5, 'Sábado' => 6, 'Domingo' => 0, // 'Domingo' es 0 en PHP dayOfWeek
    ];
    public function index()
    {
        $programaciones = Programaciones::with([
                    'grupo', 
                    'turno', 
                    'zona', 
                    'vehiculo', 
                    'personalAsignado.funcion' // ✅ Eager loading de función
                ])
                ->orderBy('fecha_inicio', 'desc')
                ->get();

        $idConductor = Funcion::where('nombre', 'Conductor')->pluck('id')->first();
        $idAyudante = Funcion::where('nombre', 'Ayudante')->pluck('id')->first();
        
        $personalNoNombradoQuery = Personal::where('activo', 1)
            ->whereDoesntHave('contratos', function ($query) {
                $query->where('activo', 1)
                    ->where('tipo_contrato', 'nombrado');
            });

        if ($idConductor) {
            $conductores = (clone $personalNoNombradoQuery)
                                ->where('funcion_id', $idConductor)
                                ->get();
        } else {
            $conductores = collect();
        }

        if ($idAyudante) {
            $ayudantes = (clone $personalNoNombradoQuery)
                                ->where('funcion_id', $idAyudante)
                                ->get();
        } else {
            $ayudantes = collect();
        }
            
        $grupos = GrupoPersonal::where('estado', 1)
                               ->with(['turno', 'zona', 'vehiculo'])
                               ->get();
        $zonas = Zona::where('activo', 1)->get();
        $turnos = Turno::all();
        $vehiculos = Vehiculo::all();
        $motivos = Motivo::activos()->get();

        return view('programacion.index', compact(
            'conductores',
            'ayudantes',
            'programaciones', 
            'grupos', 
            'zonas', 
            'turnos', 
            'vehiculos',
            'motivos'
        ));
    }

    public function getGrupoDetails(GrupoPersonal $grupoPersonal)
    {
        $grupoPersonal->load(['turno', 'zona', 'vehiculo', 'personal']); 
        
        return response()->json(['success' => true, 'data' => $grupoPersonal]);
    }

    public function store(Request $request)
{
    // 1. VALIDACIÓN DINÁMICA (Guardado Parcial o Completo)
    $rules = [
        'grupo_id' => 'required|exists:grupospersonal,id',
        'turno_id' => 'required|exists:turnos,id',
        'zona_id' => 'required|exists:zonas,id',
        'vehiculo_id' => 'required|exists:vehiculos,id',
        'conductor_id' => 'required|exists:personal,id', 
        'ayudante1_id' => 'nullable|exists:personal,id',
        'ayudante2_id' => 'nullable|exists:personal,id',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        'notes' => 'nullable|string',
        'status' => 'nullable|integer|in:0,1,2,3,4',
    ];

    // Validación condicional: 'fechas_a_guardar' (guardado parcial) O 'dias' (guardado completo)
    if ($request->has('fechas_a_guardar')) {
        $rules['fechas_a_guardar'] = 'array|min:1';
        $rules['fechas_a_guardar.*'] = 'date_format:Y-m-d';
    } else {
        $rules['dias'] = 'required|array|min:1';
        $rules['dias.*'] = 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo';
    }

    $request->validate($rules);

    // 2. PREPARACIÓN DE DATOS
    $fechaInicio = Carbon::parse($request->fecha_inicio);
    $fechaFin = Carbon::parse($request->fecha_fin);
    
    $personalIds = array_filter([
        $request->conductor_id, 
        $request->ayudante1_id, 
        $request->ayudante2_id
    ]);
    
    $dataComun = $request->except([
        'fecha_inicio', 'fecha_fin', 'dias', '_token', '_method', 
        'conductor_id', 'ayudante1_id', 'ayudante2_id', 'fechas_a_guardar'
    ]);

    // Mapeo de días de la semana
    $diasMap = [
        'Lunes' => 1, 'Martes' => 2, 'Miércoles' => 3, 'Jueves' => 4,
        'Viernes' => 5, 'Sábado' => 6, 'Domingo' => 0, 
    ];

    $registrosCreados = 0;
    $now = now();

    // 3. LÓGICA DE DÍAS A PROCESAR (Guardado Parcial vs Completo)
    $fechasAProcesar = collect();

    if ($request->has('fechas_a_guardar')) {
        // CASO A: Guardado Parcial - Usar fechas específicas enviadas
        $fechasAProcesar = collect($request->fechas_a_guardar)->map(fn($f) => Carbon::parse($f));
        
    } else {
        // CASO B: Guardado Completo - Usar rango de fechas y días de la semana
        $diasSeleccionados = $request->dias ?? []; 
        $diasNum = array_map(fn($d) => $diasMap[$d], $diasSeleccionados);

        $period = CarbonPeriod::create($fechaInicio, $fechaFin);
        
        foreach ($period as $date) {
            if (in_array($date->dayOfWeek, $diasNum)) {
                $fechasAProcesar->push($date->clone());
            }
        }
    }

    // 4. VALIDACIÓN DE FECHAS DISPONIBLES
    if ($fechasAProcesar->isEmpty()) {
        return response()->json([
            'success' => false, 
            'message' => 'No se encontraron días válidos para programar.'
        ], 400);
    }

    // 5. TRANSACCIÓN Y CREACIÓN DE REGISTROS
    DB::beginTransaction();
    try {
        foreach ($fechasAProcesar as $date) {
            $fechaDiaString = $date->toDateString();
            
            // 5.1. Crear registro principal (Programación Diaria)
            $programacionDiaria = Programaciones::create(array_merge($dataComun, [
                'fecha_inicio' => $fechaDiaString, 
                'fecha_fin' => $fechaDiaString, 
                'status' => $request->status ?? 1,
            ]));
            
            $programacionId = $programacionDiaria->id;
            
            // 5.2. Preparar asignaciones de personal (con validación adicional)
            $asignaciones = [];

            foreach ($personalIds as $personalId) {
                if ($personalId) { // Validación adicional
                    $asignaciones[] = [
                        'personal_id' => $personalId,
                        'programacion_id' => $programacionId,
                        'fecha_dia' => $fechaDiaString,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            
            if (!empty($asignaciones)) {
                DB::table('programacion_personal')->insert($asignaciones);
            }

            $registrosCreados++;
        }

        DB::commit();
        
        $message = "Programación creada con éxito. Se generaron {$registrosCreados} registros diarios.";
        return response()->json(['success' => true, 'message' => $message]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error al guardar programación: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine()); 
        
        return response()->json([
            'success' => false, 
            'message' => 'Error al guardar la programación en el servidor.',
            'error_detail' => $e->getMessage()
        ], 500);
    }
}

    /**
     * ✅ NUEVO: Obtener detalle de programación con personal y cambios
     */
    public function show(Programaciones $programacion)
    {
        $programacion->load([
            'grupo',
            'turno',
            'zona',
            'vehiculo',
            'personalAsignado',
            'cambios.motivo',
            'cambios.usuario'
        ]);
        
        return response()->json([
            'success' => true, 
            'data' => $programacion
        ]);
    }

    /**
     * ✅ NUEVO: Actualizar programación con registro de cambios
     */
    public function updateConCambios(Request $request, Programaciones $programacion)
    {
        $request->validate([
            'turno_id' => 'nullable|exists:turnos,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'personal_changes' => 'nullable|array',
            'cambios' => 'required|array|min:1',
            'cambios.*.tipo_cambio' => 'required|in:turno,vehiculo,personal',
            'cambios.*.valor_anterior' => 'required',
            'cambios.*.valor_anterior_nombre' => 'required',
            'cambios.*.valor_nuevo' => 'required',
            'cambios.*.valor_nuevo_nombre' => 'required',
            'cambios.*.motivo_id' => 'required|exists:motivos,id',
            'cambios.*.notas' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Actualizar campos de la programación
            if ($request->filled('turno_id')) {
                $programacion->turno_id = $request->turno_id;
            }
            
            if ($request->filled('vehiculo_id')) {
                $programacion->vehiculo_id = $request->vehiculo_id;
            }

            // Cambiar estado a "Reprogramada"
            $programacion->status = 4;
            $programacion->save();

            // Registrar los cambios en la tabla cambios
            foreach ($request->cambios as $cambioData) {
                Cambio::create([
                    'programacion_id' => $programacion->id,
                    'tipo_cambio' => $cambioData['tipo_cambio'],
                    'valor_anterior' => $cambioData['valor_anterior'],
                    'valor_anterior_nombre' => $cambioData['valor_anterior_nombre'],
                    'valor_nuevo' => $cambioData['valor_nuevo'],
                    'valor_nuevo_nombre' => $cambioData['valor_nuevo_nombre'],
                    'motivo_id' => $cambioData['motivo_id'],
                    'notas' => $cambioData['notas'] ?? null,
                    'user_id' => Auth::id(),
                ]);
            }

            // ✅ SOLUCIÓN MEJORADA: Actualizar personal asignado si hay cambios
            if ($request->filled('personal_changes')) {
                $personalChanges = $request->personal_changes;
                
                Log::info('🔄 Iniciando actualización de personal', [
                    'programacion_id' => $programacion->id,
                    'cambios' => $personalChanges
                ]);
                
                foreach ($personalChanges as $change) {
                    $anteriorId = (int) $change['anterior_id'];
                    $nuevoId = (int) $change['nuevo_id'];
                    
                    Log::info('📝 Procesando cambio', [
                        'anterior_id' => $anteriorId,
                        'nuevo_id' => $nuevoId
                    ]);
                    
                    // Opción 1: UPDATE directo (el que ya tienes)
                    $affected = DB::table('programacion_personal')
                        ->where('programacion_id', $programacion->id)
                        ->where('personal_id', $anteriorId)
                        ->update([
                            'personal_id' => $nuevoId,
                            'updated_at' => now(),
                        ]);
                    
                    Log::info('✅ Filas afectadas por UPDATE', ['affected' => $affected]);
                    
                    // Si el UPDATE no afectó ninguna fila, intentar con detach/attach
                    if ($affected === 0) {
                        Log::warning('⚠️ UPDATE no afectó filas, intentando detach/attach');
                        
                        // Eliminar el personal anterior
                        $programacion->personalAsignado()->detach($anteriorId);
                        
                        // Agregar el nuevo personal
                        $programacion->personalAsignado()->attach($nuevoId, [
                            'fecha_dia' => $programacion->fecha_inicio,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        Log::info('✅ Detach/Attach completado');
                    }
                }
                
                // Limpiar la caché de la relación
                $programacion->unsetRelation('personalAsignado');
            }

            DB::commit();
            
            return response()->json([
                'success' => true, 
                'message' => 'Programación actualizada con éxito.',
                'data' => $programacion->fresh(['turno', 'vehiculo', 'personalAsignado', 'cambios'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar programación: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la programación.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, Programaciones $programacion)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupospersonal,id',
            'turno_id' => 'required|exists:turnos,id',
            'zona_id' => 'required|exists:zonas,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'status' => 'required|in:0,1',
            'notes' => 'nullable|string',
        ]);

        $programacion->update($request->all());

        return response()->json(['success' => true, 'message' => 'Programación actualizada con éxito.', 'data' => $programacion]);
    }

    public function destroy(Programaciones $programacion)
    {
        $programacion->delete();
        
        return response()->json(['success' => true, 'message' => 'Programación eliminada con éxito.']);
    }


       ///FUNCIONES CREADAS
   

    public function validarVacaciones(Request $request)
    {

        $request->validate([
            'conductor_id' => 'required|integer|exists:personal,id',
            'ayudante1_id' => 'required|integer|exists:personal,id',
            'ayudante2_id' => 'required|integer|exists:personal,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        // array_unique para manejar IDs repetidos (si Ayudante 1 y 2 fueran el mismo)
        $personalIds = array_unique([
            $request->input('conductor_id'),
            $request->input('ayudante1_id'),
            $request->input('ayudante2_id'),
        ]);

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $conflictos = [];

        $periodosEnConflicto = VacacionesPeriodo::whereHas('vacaciones', function ($q) use ($personalIds) {
            $q->whereIn('personal_id', $personalIds);
        })
        ->with('vacaciones.personal')
        ->whereNotIn('estado', ['rechazado', 'cancelado'])
        ->where(function ($q) use ($fechaInicio, $fechaFin) {
            $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                    $q2->where('fecha_inicio', '<=', $fechaInicio)
                        ->where('fecha_fin', '>=', $fechaFin);
                });
        })
        ->get();

        if ($periodosEnConflicto->isNotEmpty()) {
            $rolesMap = [
                $request->conductor_id => 'Conductor',
                $request->ayudante1_id => 'Ayudante 1',
                $request->ayudante2_id => 'Ayudante 2',
            ];

            foreach ($periodosEnConflicto as $periodo) {
                $empleadoId = $periodo->vacaciones->personal_id;
                $nombreCompleto = $periodo->vacaciones->personal->nombre_completo ?? 'N/A';
                $rol = $rolesMap[$empleadoId] ?? 'Personal';

                $conflictos[] = [
                    'rol' => $rol,
                    'nombre' => $nombreCompleto,
                    'fechas' => "{$periodo->fecha_inicio->format('d/m/Y')} al {$periodo->fecha_fin->format('d/m/Y')}",
                ];
            }

            return response()->json([
                'success' => false,
                'message' => 'Conflicto de vacaciones encontrado.',
                'conflicts' => $conflictos,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Personal disponible.',
        ], 200);
    }


    public function validarDisponibilidadGeneral(Request $request)
    {
        $request->validate([
            'programacion_id' => 'nullable|integer',
            'conductor_id' => 'required|integer',
            'ayudante1_id' => 'nullable|integer',
            'ayudante2_id' => 'nullable|integer',
            'vehiculo_id' => 'required|integer',
            'grupo_id' => 'required|integer',
            'zona_id' => 'required|integer',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'dias' => 'required|array|min:1',
            'dias.*' => 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
        ]);

        $programacionId = $request->input('programacion_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $vehiculoId = $request->input('vehiculo_id');
        $grupoId = $request->input('grupo_id');
        $zonaId = $request->input('zona_id');
        $diasSeleccionados = $request->input('dias');

        $personalIds = array_unique(array_filter([
            $request->input('conductor_id'),
            $request->input('ayudante1_id'),
            $request->input('ayudante2_id'),
        ]));

        $allConflicts = [];

        // 1. Obtener el turno_id del grupo seleccionado
        $grupo = GrupoPersonal::find($grupoId);
        if (!$grupo) {
            return response()->json(['success' => false, 'message' => 'Grupo de Personal no encontrado.'], 404);
        }
        $turnoId = $grupo->turno_id;

        // 2. Validación de Conflictos Generales (Personal, Vehículo, Grupo, Zona)
        $programationConflicts = $this->checkProgramationConflicts($programacionId, $personalIds, $vehiculoId, $fechaInicio, $fechaFin, $grupoId, $zonaId);
        if (!empty($programationConflicts)) {
            $allConflicts['programaciones'] = $programationConflicts;
        }

        // 3. Validación Específica: Conflicto por la Ruta (Turno + Zona + Vehículo) y Disponibilidad de Días
        $conflictoTurnoZonaVehiculo = $this->checkSpecificConflictsAndAvailability(
            $programacionId,
            $turnoId,
            $zonaId,
            $vehiculoId,
            $fechaInicio,
            $fechaFin,
            $diasSeleccionados
        );

        $diasConflictivos = $conflictoTurnoZonaVehiculo['conflicting_dates'];
        $availableDaysSuggestion = $conflictoTurnoZonaVehiculo['available_days']; // RENOMBRAMOS para ser más claros

        if (!empty($diasConflictivos)) {
            $allConflicts['superposicion_ruta'] = [
                'message' => 'Existen programaciones para el mismo turno, zona y vehículo en las siguientes fechas:',
                'dates' => $diasConflictivos
            ];
            
            // AÑADIR LA SUGERENCIA DE DÍAS DISPONIBLES EN EL MISMO ERROR DE RUTA
             // Esto permite que el frontend use esta lista para el mensaje de sugerencia
             $allConflicts['available_days_suggestion'] = $availableDaysSuggestion; 
        }

        // Respuesta final
        if (!empty($diasConflictivos)) { // Si hay conflicto de ruta, siempre es success: false
            
            return response()->json([
                'success' => false,
                'message' => 'Conflicto de Turno, Zona, Vehículo detectado.',
                'conflicts' => $allConflicts,
                'available_days_suggestion' => $availableDaysSuggestion // Devolvemos la sugerencia aquí también para el frontend
            ], 200);

        } elseif (!empty($allConflicts)) {
             // Hay conflictos generales, pero la ruta es libre.
             return response()->json([
                'success' => false, // Podrías mantenerlo en false si el frontend requiere un flag, o true si es solo advertencia. Lo dejaremos en false para compatibilidad con tu validación de JS inicial.
                'message' => '',
                'conflicts' => $allConflicts,
                'available_days_suggestion' => $availableDaysSuggestion // Vacío si la ruta no tiene conflicto
            ], 200);

        } else {
             // Todo libre
             return response()->json([
                'success' => true,
                'message' => 'Personal, vehículo, grupo y zona disponibles.',
                'available_days_suggestion' => $availableDaysSuggestion // Todos los días seleccionados que cayeron en el rango
            ], 200);
        }
    }




    public function validarContratoVigente(Request $request)
    {
        // Validación de entradas
        $request->validate([
            'conductor_id' => 'required|integer|exists:personal,id',
            'ayudante1_id' => 'required|integer|exists:personal,id',
            'ayudante2_id' => 'required|integer|exists:personal,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        // Recolectar IDs de personal únicos y filtrar los que no son válidos (ej. 0 o null)
        $personalIds = array_unique(array_filter([
            $request->input('conductor_id'),
            $request->input('ayudante1_id'),
            $request->input('ayudante2_id'),
        ]));

        $fechaFinProgramacion = Carbon::parse($request->input('fecha_fin'));
        $conflictos = [];

        // 1. Obtener el personal con sus contratos activos
        $personalConContrato = Personal::whereIn('id', $personalIds)
            ->with(['contratos' => function ($query) {
                // Asumimos que quieres verificar el contrato ACTIVO (o el más reciente)
                $query->where('activo', 1)->latest('fecha_inicio')->limit(1);
            }])
            ->get();
        
        // Mapeo de roles para los mensajes de error
        $rolesMap = [
            $request->conductor_id => 'Conductor',
            $request->ayudante1_id => 'Ayudante 1',
            $request->ayudante2_id => 'Ayudante 2',
        ];

        foreach ($personalConContrato as $persona) {
            $contrato = $persona->contratos->first();
            $empleadoId = $persona->id;
            $nombreCompleto = $persona->nombre_completo ?? 'N/A';
            $rol = $rolesMap[$empleadoId] ?? 'Personal';

            if (!$contrato) {
                // Caso: El personal no tiene un contrato activo asociado
                $conflictos[] = [
                    'rol' => $rol,
                    'nombre' => $nombreCompleto,
                    'detalle' => 'No tiene un contrato activo registrado.',
                ];
                continue;
            }

            $fechaFinContrato = $contrato->fecha_fin ? Carbon::parse($contrato->fecha_fin) : null;

            // 2. Verificar vigencia
            // Si la fecha de fin de contrato es conocida y es ANTERIOR al final de la programación, hay conflicto.
            if ($fechaFinContrato && $fechaFinContrato->lt($fechaFinProgramacion)) {
                $conflictos[] = [
                    'rol' => $rol,
                    'nombre' => $nombreCompleto,
                    'detalle' => "Contrato vigente hasta el {$fechaFinContrato->format('d/m/Y')}.",
                ];
            }
        }

        if (!empty($conflictos)) {
            return response()->json([
                'success' => false,
                'message' => 'Contrato no vigente encontrado.',
                'conflicts' => $conflictos,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contratos vigentes para el periodo.',
        ], 200);
    }
    


    private function checkSpecificConflictsAndAvailability($programacionId, $turnoId, $zonaId, $vehiculoId, $fechaInicio, $fechaFin, $diasSeleccionados)
    {
        $conflictingDates = [];
        $availableDays = [];

        // Mapeo de días a formato numérico (0=Domingo, 1=Lunes...)
        $diasNum = array_map(fn($d) => self::DIAS_MAP[$d], $diasSeleccionados);

        $period = CarbonPeriod::create($fechaInicio, $fechaFin);

        foreach ($period as $date) {
            if (!in_array($date->dayOfWeek, $diasNum)) {
                continue;
            }

            $dateString = $date->toDateString();
            $dayName = array_search($date->dayOfWeek, self::DIAS_MAP);

            // Conflicto: Otra programación activa con la misma Ruta (Turno+Zona+Vehiculo)
            $conflict = Programaciones::where('id', '!=', $programacionId)
                ->where('status', 1)
                ->where('turno_id', $turnoId)
                ->where('zona_id', $zonaId)
                ->where('vehiculo_id', $vehiculoId)
                ->where(function ($query) use ($dateString) {
                    $query->where('fecha_inicio', '<=', $dateString)
                        ->where('fecha_fin', '>=', $dateString);
                })
                ->exists();

            if ($conflict) {
                $conflictingDates[] = $dayName . ' (' . $date->format('Y-m-d') . ')';
            } else {
                // Agregar el día disponible en el formato que espera el frontend
                $availableDays[] = $dayName . ' (' . $date->format('Y-m-d') . ')'; 
            }
        }

        return [
            'conflicting_dates' => $conflictingDates,
            'available_days' => $availableDays, 
        ];
    }

 
    
    private function checkProgramationConflicts($programacionId, $personalIds, $vehiculoId, $fechaInicio, $fechaFin, $grupoId, $zonaId)
    {
        $conflicts = [];

        // La consulta busca cualquier programación ACTIVA que se solape en el tiempo Y
        // que comparta alguno de los recursos (Personal, Vehículo, Grupo, Zona).
        $programacionesConflictivas = Programaciones::where('id', '!=', $programacionId)
            ->where('status', 1)
            ->where(function ($query) use ($vehiculoId, $personalIds, $grupoId, $zonaId, $fechaInicio, $fechaFin, $programacionId) {

                // Lógica de Solape Temporal (aplicada a ambos criterios)
                $timeOverlap = function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                        ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                        ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                            $q2->where('fecha_inicio', '<', $fechaInicio)
                                ->where('fecha_fin', '>', $fechaFin);
                        });
                };

                // Criterio A: Conflicto con Vehículo, Grupo o Zona (en el rango de fechas)
                $query->where(function ($q) use ($vehiculoId, $grupoId, $zonaId, $timeOverlap) {
                    $q->where(function ($qInner) use ($vehiculoId, $grupoId, $zonaId) {
                        $qInner->where('vehiculo_id', $vehiculoId)
                            ->orWhere('grupo_id', $grupoId)
                            ->orWhere('zona_id', $zonaId);
                    })->where($timeOverlap);
                });

                // Criterio B: Conflicto con el Personal (en el rango de fechas)
                $query->orWhere(function ($q) use ($personalIds, $timeOverlap) {
                    // Conflicto de Personal en tabla pivote
                    $q->whereHas('personal', function ($qPersonal) use ($personalIds) {
                        $qPersonal->whereIn('personal_id', $personalIds);
                    })->where($timeOverlap);
                });

            })
            ->with(['vehiculo', 'grupo', 'zona', 'personal'])
            ->get();

        foreach ($programacionesConflictivas as $p) {
            $conflictDetails = [];

            // 1. Conflictos fijos (Vehículo, Grupo, Zona)
            if ($p->vehiculo_id == $vehiculoId) {
                $conflictDetails[] = 'Vehículo (' . ($p->vehiculo->codigo ?? 'N/A') . ')';
            }
            if ($p->grupo_id == $grupoId) {
                $conflictDetails[] = 'Grupo (' . ($p->grupo->nombre ?? 'N/A') . ')';
            }
            if ($p->zona_id == $zonaId) {
                $conflictDetails[] = 'Zona (' . ($p->zona->nombre ?? 'N/A') . ')';
            }

            // 2. Conflicto de Personal
            $personalEnConflicto = $p->personal->filter(function ($personal) use ($personalIds) {
                return in_array($personal->id, $personalIds);
            });

            if ($personalEnConflicto->isNotEmpty()) {
                $nombresPersonal = $personalEnConflicto->pluck('nombre_completo')->unique()->implode(', ');
                $conflictDetails[] = 'Personal (' . $nombresPersonal . ')';
            }

            if (!empty($conflictDetails)) {
                $conflicts[] = [
                    'type' => 'Programación Existente',
                    'id' => $p->id,
                    'details' => implode(', ', array_unique($conflictDetails)),
                    'fecha_inicio' => Carbon::parse($p->fecha_inicio)->format('d/m/Y'),
                    'fecha_fin' => Carbon::parse($p->fecha_fin)->format('d/m/Y'),
                ];
            }
        }

        return $conflicts;
    }

    // ========================================
    // MÉTODOS PARA PROGRAMACIÓN MASIVA
    // ========================================

    public function indexMasiva()
    {
        $idConductor = Funcion::where('nombre', 'Conductor')->pluck('id')->first();
        $idAyudante = Funcion::where('nombre', 'Ayudante')->pluck('id')->first();
        
        $personalNoNombradoQuery = Personal::where('activo', 1)
            ->whereDoesntHave('contratos', function ($query) {
                $query->where('activo', 1)
                    ->where('tipo_contrato', 'nombrado');
            });

        if ($idConductor) {
            $conductores = (clone $personalNoNombradoQuery)
                                ->where('funcion_id', $idConductor)
                                ->get();
        } else {
            $conductores = collect();
        }

        if ($idAyudante) {
            $ayudantes = (clone $personalNoNombradoQuery)
                                ->where('funcion_id', $idAyudante)
                                ->get();
        } else {
            $ayudantes = collect();
        }

        $grupos = GrupoPersonal::where('estado', 1)
                               ->with(['turno', 'zona', 'vehiculo.marca', 'personal'])
                               ->get();
        $zonas = Zona::where('activo', 1)->get();
        $turnos = Turno::all();
        $vehiculos = Vehiculo::where('activo', 1)->with('marca')->get();

        return view('programacion.masiva', compact(
            'conductores',
            'ayudantes',
            'grupos',
            'zonas',
            'turnos',
            'vehiculos'
        ));
    }

    public function validarDisponibilidadVehiculo(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'required|integer|exists:vehiculos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $vehiculoId = $request->input('vehiculo_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Verificar si el vehículo está disponible
        $vehiculo = Vehiculo::find($vehiculoId);
        if (!$vehiculo || !$vehiculo->disponible) {
            return response()->json([
                'success' => false,
                'message' => 'El vehículo no está disponible.',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vehículo disponible.',
        ], 200);
    }

    /**
     * Validar programación masiva (SOLO VALIDAR, NO GUARDAR)
     */
    public function validarProgramacionMasiva(Request $request)
    {
        $request->validate([
            'turno_id' => 'required|exists:turnos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'dias' => 'required|array|min:1', 
            'dias.*' => 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'programaciones' => 'required|array|min:1',
            'programaciones.*.grupo_id' => 'nullable|exists:grupospersonal,id',
            'programaciones.*.zona_id' => 'required|exists:zonas,id',
            'programaciones.*.vehiculo_id' => 'required|exists:vehiculos,id',
            'programaciones.*.conductor_id' => 'required|exists:personal,id',
            'programaciones.*.ayudante1_id' => 'nullable|exists:personal,id',
            'programaciones.*.ayudante2_id' => 'nullable|exists:personal,id',
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        $diasSeleccionados = $request->dias;
        $turnoId = $request->turno_id;

        $diasMap = [
            'Lunes' => 1, 'Martes' => 2, 'Miércoles' => 3, 'Jueves' => 4,
            'Viernes' => 5, 'Sábado' => 6, 'Domingo' => 0, 
        ];
        $diasNum = array_map(fn($d) => $diasMap[$d], $diasSeleccionados);

        $period = CarbonPeriod::create($fechaInicio, $fechaFin);
        $errores = [];
        $exitosas = [];

        try {
            foreach ($request->programaciones as $index => $progData) {
                $grupoId = $progData['grupo_id'] ?? null;
                $zonaId = $progData['zona_id'];
                $grupoNombre = '';
                
                if ($grupoId) {
                    $grupo = GrupoPersonal::find($grupoId);
                    $grupoNombre = $grupo ? $grupo->nombre : "Programación " . ($index + 1);
                } else {
                    $zona = Zona::find($zonaId);
                    $turno = Turno::find($turnoId);
                    $grupoNombre = ($zona ? $zona->nombre : "Zona") . " - " . ($turno ? $turno->name : "Turno");
                }

                $erroresProg = [];

                $personalIds = array_filter([
                    $progData['conductor_id'], 
                    $progData['ayudante1_id'] ?? null, 
                    $progData['ayudante2_id'] ?? null
                ]);

                // Validación 1: Verificar contratos vigentes
                foreach ($personalIds as $personalId) {
                    $personal = Personal::find($personalId);
                    $tieneContratoVigente = Contrato::where('personal_id', $personalId)
                        ->where('activo', 1)
                        ->where('fecha_inicio', '<=', $fechaFin)
                        ->where(function($q) use ($fechaInicio) {
                            $q->whereNull('fecha_fin')
                              ->orWhere('fecha_fin', '>=', $fechaInicio);
                        })
                        ->exists();

                    if (!$tieneContratoVigente) {
                        $erroresProg[] = "El personal {$personal->nombre_completo} no tiene contrato vigente para el período seleccionado.";
                    }
                }

                // Validación 2: Verificar vacaciones
                $periodosVacaciones = VacacionesPeriodo::whereHas('vacaciones', function ($q) use ($personalIds) {
                    $q->whereIn('personal_id', $personalIds);
                })
                ->with('vacaciones.personal')
                ->whereNotIn('estado', ['rechazado', 'cancelado'])
                ->where(function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                        ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                        ->orWhere(function ($q2) use ($fechaInicio, $fechaFin) {
                            $q2->where('fecha_inicio', '<=', $fechaInicio)
                                ->where('fecha_fin', '>=', $fechaFin);
                        });
                })
                ->get();

                if ($periodosVacaciones->isNotEmpty()) {
                    foreach ($periodosVacaciones as $periodo) {
                        $nombrePersonal = $periodo->vacaciones->personal->nombre_completo ?? 'N/A';
                        $erroresProg[] = "El personal {$nombrePersonal} tiene vacaciones del {$periodo->fecha_inicio->format('d/m/Y')} al {$periodo->fecha_fin->format('d/m/Y')}.";
                    }
                }

                // Validación 3: Verificar programaciones duplicadas para cada día
                foreach ($period as $date) {
                    if (in_array($date->dayOfWeek, $diasNum)) {
                        $fechaDia = $date->toDateString();

                        // Verificar duplicados por día específico
                        $existente = Programaciones::where('fecha_inicio', $fechaDia)
                            ->where('fecha_fin', $fechaDia)
                            ->where('turno_id', $turnoId)
                            ->where(function ($q) use ($progData, $personalIds, $zonaId) {
                                $q->where('vehiculo_id', $progData['vehiculo_id'])
                                  ->orWhere('zona_id', $zonaId)
                                  ->orWhereHas('personalAsignado', function ($q2) use ($personalIds) {
                                      $q2->whereIn('personal_id', $personalIds);
                                  });
                            })
                            ->exists();

                        if ($existente) {
                            $erroresProg[] = "Ya existe una programación para el día {$date->format('d/m/Y')} con el mismo turno, vehículo, zona o personal.";
                            break; // Dejar de verificar más días para esta programación
                        }
                    }
                }

                // Registrar errores o éxito
                if (!empty($erroresProg)) {
                    $errores[] = [
                        'grupo' => $grupoNombre,
                        'errores' => $erroresProg
                    ];
                } else {
                    $exitosas[] = [
                        'grupo' => $grupoNombre,
                        'registros' => 0 // En validación no se cuentan registros
                    ];
                }
            }

            // Preparar respuesta
            if (empty($errores)) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Validación exitosa. No se encontraron errores.',
                    'exitosas' => $exitosas
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Se encontraron errores en la validación.',
                    'errores' => $errores,
                    'exitosas' => $exitosas
                ], 200);
            }

        } catch (\Exception $e) {
            Log::error("Error al validar programación masiva: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error al validar la programación masiva en el servidor.',
                'error_detail' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => basename($e->getFile())
            ], 500);
        }
    }

    public function storeMasiva(Request $request)
    {
        $request->validate([
            'turno_id' => 'required|exists:turnos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'dias' => 'required|array|min:1', 
            'dias.*' => 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'programaciones' => 'required|array|min:1',
            'programaciones.*.grupo_id' => 'nullable|exists:grupospersonal,id',
            'programaciones.*.zona_id' => 'required|exists:zonas,id',
            'programaciones.*.vehiculo_id' => 'required|exists:vehiculos,id',
            'programaciones.*.conductor_id' => 'required|exists:personal,id',
            'programaciones.*.ayudante1_id' => 'nullable|exists:personal,id',
            'programaciones.*.ayudante2_id' => 'nullable|exists:personal,id',
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = Carbon::parse($request->fecha_fin);
        $diasSeleccionados = $request->dias;
        $turnoId = $request->turno_id;

        $diasMap = [
            'Lunes' => 1, 'Martes' => 2, 'Miércoles' => 3, 'Jueves' => 4,
            'Viernes' => 5, 'Sábado' => 6, 'Domingo' => 0, 
        ];
        $diasNum = array_map(fn($d) => $diasMap[$d], $diasSeleccionados);

        $period = CarbonPeriod::create($fechaInicio, $fechaFin);
        $exitosas = [];

        DB::beginTransaction();
        
        try {
            foreach ($request->programaciones as $index => $progData) {
                // Obtener grupo o crear referencia temporal
                $grupoId = $progData['grupo_id'] ?? null;
                $zonaId = $progData['zona_id'];
                $grupoNombre = '';
                
                if ($grupoId) {
                    $grupo = GrupoPersonal::find($grupoId);
                    $grupoNombre = $grupo ? $grupo->nombre : "Programación " . ($index + 1);
                } else {
                    $zona = Zona::find($zonaId);
                    $turno = Turno::find($turnoId);
                    $grupoNombre = ($zona ? $zona->nombre : "Zona") . " - " . ($turno ? $turno->name : "Turno");
                    
                    // Crear grupo temporal
                    try {
                        $grupoTemp = GrupoPersonal::create([
                            'nombre' => "Temp-" . $grupoNombre . "-" . time() . "-" . $index,
                            'zona_id' => $zonaId,
                            'turno_id' => $turnoId,
                            'vehiculo_id' => $progData['vehiculo_id'],
                            'dias' => implode(',', $diasSeleccionados),
                            'estado' => 1,
                        ]);
                        $grupoId = $grupoTemp->id;
                    } catch (\Exception $e) {
                        Log::error("Error creando grupo temporal: " . $e->getMessage());
                        // Si falla crear el grupo, continuar sin grupo
                        $grupoId = null;
                    }
                }

                $personalIds = array_filter([
                    $progData['conductor_id'], 
                    $progData['ayudante1_id'] ?? null, 
                    $progData['ayudante2_id'] ?? null
                ]);

                // NO VALIDAR - Solo guardar directamente
                $registrosCreados = 0;
                $now = now();

                // Asegurar que tenemos un grupo válido antes de crear programaciones
                if (!$grupoId) {
                    $zona = Zona::find($zonaId);
                    $turno = Turno::find($turnoId);
                    $nombreGrupoTemp = "Temp-" . ($zona ? $zona->nombre : "Zona") . "-" . ($turno ? $turno->name : "Turno") . "-" . time() . "-" . $index;
                    
                    $grupoTemp = GrupoPersonal::create([
                        'nombre' => $nombreGrupoTemp,
                        'zona_id' => $zonaId,
                        'turno_id' => $turnoId,
                        'vehiculo_id' => $progData['vehiculo_id'],
                        'dias' => implode(',', $diasSeleccionados),
                        'estado' => 1,
                    ]);
                    $grupoId = $grupoTemp->id;
                }

                foreach ($period as $date) {
                    if (in_array($date->dayOfWeek, $diasNum)) {
                        $programacionDiaria = Programaciones::create([
                            'grupo_id' => $grupoId,
                            'turno_id' => $turnoId,
                            'zona_id' => $zonaId,
                            'vehiculo_id' => $progData['vehiculo_id'],
                            'fecha_inicio' => $date->toDateString(), 
                            'fecha_fin' => $date->toDateString(), 
                            'status' => 1,
                            'notes' => $progData['notes'] ?? null,
                        ]);
                        
                        $asignaciones = [];
                        foreach ($personalIds as $personalId) {
                            $asignaciones[] = [
                                'personal_id' => $personalId,
                                'programacion_id' => $programacionDiaria->id,
                                'fecha_dia' => $date->toDateString(),
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                        
                        if (!empty($asignaciones)) {
                            DB::table('programacion_personal')->insert($asignaciones);
                        }

                        $registrosCreados++;
                    }
                }

                $exitosas[] = [
                    'grupo' => $grupoNombre,
                    'registros' => $registrosCreados
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Todas las programaciones se crearon exitosamente.',
                'exitosas' => $exitosas
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al guardar programación masiva: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error al guardar la programación masiva en el servidor.',
                'error_detail' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => basename($e->getFile())
            ], 500);
        }
    }

    /**
     * Validar disponibilidad del personal (contratos, vacaciones y programaciones existentes)
     */
    public function validarDisponibilidadPersonal(Request $request)
    {
        $request->validate([
            'personal_ids' => 'required|array|min:1',
            'personal_ids.*' => 'exists:personal,id',
            'grupos_data' => 'required|array|min:1',
            'turno_id' => 'required|exists:turnos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $personalIds = $request->personal_ids;
        $gruposData = $request->grupos_data;
        $turnoId = $request->turno_id;
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $errores = [
            'sin_contrato' => [],
            'con_vacaciones' => [],
            'con_programaciones' => []
        ];

        // Mapear personal_ids a grupo_index
        // Necesitamos obtener qué grupo contiene cada personal
        $personalAGrupoMap = [];
        
        // Obtener los grupos y su personal
        $gruposPersonal = GrupoPersonal::whereIn('id', array_column($gruposData, 'grupo_id'))
            ->with('personal')
            ->get();
        
        foreach ($gruposPersonal as $grupo) {
            foreach ($grupo->personal as $personal) {
                if (in_array($personal->id, $personalIds)) {
                    if (!isset($personalAGrupoMap[$personal->id])) {
                        $personalAGrupoMap[$personal->id] = [];
                    }
                    // Obtener el índice del grupo en gruposData
                    $grupoIndex = array_search($grupo->id, array_column($gruposData, 'grupo_id'));
                    $personalAGrupoMap[$personal->id][] = $grupoIndex !== false ? $grupoIndex : 0;
                }
            }
        }

        // Validar contratos y vacaciones del personal
        foreach ($personalIds as $personalId) {
            $personal = Personal::find($personalId);
            
            if (!$personal) {
                continue;
            }

            // Obtener índice del grupo para este personal
            $gruposIndexes = $personalAGrupoMap[$personalId] ?? [0];

            // Validar que tenga contrato vigente
            // Un contrato es válido si:
            // 1. Está activo
            // 2. Ya ha iniciado (fecha_inicio <= fecha_programacion)
            // 3. No ha terminado o es indefinido (fecha_fin IS NULL o fecha_fin >= fecha_programacion)
            $tieneContratoVigente = Contrato::where('personal_id', $personalId)
                ->where('activo', 1)
                ->where('fecha_inicio', '<=', $fechaInicio) // Ya debe haber iniciado
                ->where(function ($query) use ($fechaFin) {
                    // Fecha fin NULL (indefinido/permanente) o fecha_fin >= fecha_fin de programación
                    $query->whereNull('fecha_fin')
                          ->orWhere('fecha_fin', '>=', $fechaFin);
                })
                ->exists();

            if (!$tieneContratoVigente) {
                foreach ($gruposIndexes as $groupIndex) {
                    $errores['sin_contrato'][] = [
                        'id' => $personal->id,
                        'nombre' => $personal->nombre_completo,
                        'grupo_index' => $groupIndex
                    ];
                }
            }

            // Validar que no tenga vacaciones en el rango de fechas
            $vacaciones = VacacionesPeriodo::whereHas('vacaciones', function($query) use ($personalId) {
                    $query->where('personal_id', $personalId);
                })
                ->where('estado', 'Aprobado')
                ->where(function ($query) use ($fechaInicio, $fechaFin) {
                    $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                          ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                          ->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
                              $q->where('fecha_inicio', '<=', $fechaInicio)
                                ->where('fecha_fin', '>=', $fechaFin);
                          });
                })
                ->get();

            foreach ($vacaciones as $vacacion) {
                foreach ($gruposIndexes as $groupIndex) {
                    $errores['con_vacaciones'][] = [
                        'id' => $personal->id,
                        'nombre' => $personal->nombre_completo,
                        'fecha_inicio' => $vacacion->fecha_inicio->format('d/m/Y'),
                        'fecha_fin' => $vacacion->fecha_fin->format('d/m/Y'),
                        'grupo_index' => $groupIndex
                    ];
                }
            }
        }

        // Validar que no existan programaciones para los grupos en el turno y fechas seleccionadas
        foreach ($gruposData as $grupoIndex => $grupoInfo) {
            $grupoId = $grupoInfo['grupo_id'] ?? null;
            
            if (!$grupoId) {
                continue;
            }

            $grupo = GrupoPersonal::find($grupoId);
            if (!$grupo) {
                continue;
            }

            // Buscar programaciones existentes para este grupo en el rango de fechas y turno
            $programacionesExistentes = Programaciones::where('grupo_id', $grupoId)
                ->where('turno_id', $turnoId)
                ->where(function ($query) use ($fechaInicio, $fechaFin) {
                    $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                          ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                          ->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
                              $q->where('fecha_inicio', '<=', $fechaInicio)
                                ->where('fecha_fin', '>=', $fechaFin);
                          });
                })
                ->with('turno')
                ->get();

            if ($programacionesExistentes->isNotEmpty()) {
                foreach ($programacionesExistentes as $prog) {
                    $errores['con_programaciones'][] = [
                        'grupo_id' => $grupoId,
                        'grupo_nombre' => $grupo->nombre,
                        'fecha_inicio' => $prog->fecha_inicio,
                        'fecha_fin' => $prog->fecha_fin,
                        'turno' => $prog->turno ? $prog->turno->name : 'N/A',
                        'grupo_index' => $grupoIndex
                    ];
                }
            }
        }

        // Verificar si hay errores
        $hayErrores = !empty($errores['sin_contrato']) || 
                      !empty($errores['con_vacaciones']) || 
                      !empty($errores['con_programaciones']);

        if ($hayErrores) {
            return response()->json([
                'success' => false,
                'message' => 'Se encontraron errores de validación',
                'errores' => $errores
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Validación exitosa. No se encontraron errores.'
        ]);
    }

    /**
     * ✅ DEBUG: Mostrar programaciones que coinciden con criterios de búsqueda
     */
    public function debugBusqueda(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'zona_id' => 'nullable|integer|exists:zonas,id',
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
        $zonaId = $request->zona_id && $request->zona_id !== '' ? (int)$request->zona_id : null;

        // Mostrar todas las programaciones en el rango (sin filtro de status)
        $programacionesTodas = Programaciones::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->with('zona', 'turno', 'vehiculo')
            ->orderBy('fecha_inicio')
            ->get();

        // Mostrar solo las activas (status = 1)
        $programacionesActivas = Programaciones::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->where('status', 1)
            ->with('zona', 'turno', 'vehiculo')
            ->orderBy('fecha_inicio')
            ->get();

        // Si se especifica zona, filtrar también
        $programacionesConZona = null;
        if ($zonaId) {
            $programacionesConZona = Programaciones::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->where('status', 1)
                ->where('zona_id', $zonaId)
                ->with('zona', 'turno', 'vehiculo')
                ->orderBy('fecha_inicio')
                ->get();
        }

        return response()->json([
            'debug' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'zona_id' => $zonaId,
            ],
            'total_todas' => $programacionesTodas->count(),
            'programaciones_todas' => $programacionesTodas->map(function($p) {
                return [
                    'id' => $p->id,
                    'fecha_inicio' => $p->fecha_inicio,
                    'status' => $p->status,
                    'zona' => $p->zona?->nombre,
                    'turno' => $p->turno?->name,
                    'vehiculo' => $p->vehiculo?->codigo,
                ];
            }),
            'total_activas' => $programacionesActivas->count(),
            'programaciones_activas' => $programacionesActivas->map(function($p) {
                return [
                    'id' => $p->id,
                    'fecha_inicio' => $p->fecha_inicio,
                    'status' => $p->status,
                    'zona' => $p->zona?->nombre,
                    'turno' => $p->turno?->name,
                    'vehiculo' => $p->vehiculo?->codigo,
                ];
            }),
            'total_con_zona' => $programacionesConZona?->count(),
            'programaciones_con_zona' => $programacionesConZona?->map(function($p) {
                return [
                    'id' => $p->id,
                    'fecha_inicio' => $p->fecha_inicio,
                    'status' => $p->status,
                    'zona' => $p->zona?->nombre,
                    'turno' => $p->turno?->name,
                    'vehiculo' => $p->vehiculo?->codigo,
                ];
            }),
        ]);
    }

    /**
     * ✅ NUEVO: Actualizar programaciones de forma masiva
     * Modifica múltiples programaciones según los criterios de búsqueda
     * y registra los cambios en el historial
     */
    public function updateMasiva(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'zona_id' => 'nullable|integer|exists:zonas,id',
            'tipo_cambio' => 'required|in:conductor,ocupante,turno,vehiculo',
            'motivo_id' => 'required|integer|exists:motivos,id',
            'notas' => 'nullable|string',
            // Validación de campos según tipo de cambio (solo nuevo valor)
            'conductor_nuevo' => 'nullable|integer|exists:personal,id',
            'ocupante_nuevo' => 'nullable|integer|exists:personal,id',
            'turno_nuevo' => 'nullable|integer|exists:turnos,id',
            'vehiculo_nuevo' => 'nullable|integer|exists:vehiculos,id',
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
        $tipoCambio = $request->tipo_cambio;
        $motivoId = $request->motivo_id;
        $notas = $request->notas;
        // Convertir zona_id a null si es vacío o 0
        $zonaId = $request->zona_id && $request->zona_id !== '' ? (int)$request->zona_id : null;

        DB::beginTransaction();
        try {
            // Construir query base para obtener programaciones que cumplan los criterios
            // Buscar en todos los status excepto 0 (Cancelada)
            $query = Programaciones::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->whereIn('status', [1, 2, 3, 4]); // Programada, Iniciada, Completada, Reprogramada

            // Filtrar por zona si se especifica
            if ($zonaId) {
                $query->where('zona_id', $zonaId);
            }

            // Logging para debugging
            Log::info('Búsqueda de programaciones masiva', [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'zona_id' => $zonaId,
                'tipo_cambio' => $tipoCambio
            ]);

            // Buscar programaciones según tipo de cambio y obtener datos actuales
            $programaciones = null;

            if ($tipoCambio === 'conductor') {
                $conductorNuevo = $request->conductor_nuevo;
                
                // Obtener programaciones que tengan algún conductor
                $programaciones = $query->whereHas('personalAsignado', function($q) {
                    $q->whereHas('funcion', function($q2) {
                        $q2->where('nombre', 'Conductor');
                    });
                })->with('personalAsignado.funcion', 'turno', 'vehiculo')->get();

            } elseif ($tipoCambio === 'ocupante') {
                $ocupanteNuevo = $request->ocupante_nuevo;
                
                // Obtener programaciones que tengan algún ocupante
                $programaciones = $query->whereHas('personalAsignado', function($q) {
                    $q->whereHas('funcion', function($q2) {
                        $q2->where('nombre', 'Ayudante');
                    });
                })->with('personalAsignado.funcion', 'turno', 'vehiculo')->get();

            } elseif ($tipoCambio === 'turno') {
                $turnoNuevo = $request->turno_nuevo;
                
                // Obtener todas las programaciones del rango
                $programaciones = $query->with('personalAsignado.funcion', 'turno', 'vehiculo')->get();

            } elseif ($tipoCambio === 'vehiculo') {
                $vehiculoNuevo = $request->vehiculo_nuevo;
                
                // Obtener todas las programaciones del rango
                $programaciones = $query->with('personalAsignado.funcion', 'turno', 'vehiculo')->get();
            }

            Log::info('Programaciones encontradas en búsqueda masiva', [
                'cantidad' => $programaciones?->count() ?? 0,
                'tipo_cambio' => $tipoCambio
            ]);

            if ($programaciones->isEmpty()) {
                DB::rollBack();
                // Verificar cuántas programaciones hay sin filtro de status para debugging
                $totalSinFiltro = Programaciones::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])->count();
                $statusesCancelados = Programaciones::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])->where('status', 0)->count();
                Log::warning('No se encontraron programaciones activas', [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'zona_id' => $zonaId,
                    'total_sin_filtro_status' => $totalSinFiltro,
                    'status_canceladas' => $statusesCancelados
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron programaciones que cumplan con los criterios. Verifica: 1) El rango de fechas, 2) La zona seleccionada, 3) Que no todas las programaciones estén canceladas.',
                    'debug_info' => [
                        'total_en_rango' => $totalSinFiltro,
                        'canceladas' => $statusesCancelados
                    ]
                ], 404);
            }

            // ✅ VALIDAR vacaciones y contrato vigente para todas las programaciones a modificar
            $validarVacaciones = in_array($tipoCambio, ['conductor', 'ocupante']);
            $validarContrato = in_array($tipoCambio, ['conductor', 'ocupante']);

            $conflictosPersonal = [];

            if ($validarVacaciones || $validarContrato) {
                $personalNuevoId = null;
                $rolPersona = '';

                if ($tipoCambio === 'conductor') {
                    $personalNuevoId = $request->conductor_nuevo;
                    $rolPersona = 'Conductor';
                } elseif ($tipoCambio === 'ocupante') {
                    $personalNuevoId = $request->ocupante_nuevo;
                    $rolPersona = 'Ocupante';
                }

                foreach ($programaciones as $prog) {
                    // Validar vacaciones
                    if ($validarVacaciones && $personalNuevoId) {
                        $conflictosVacaciones = VacacionesPeriodo::whereHas('vacaciones', function ($q) use ($personalNuevoId) {
                            $q->where('personal_id', $personalNuevoId);
                        })->with('vacaciones.personal')
                          ->whereNotIn('estado', ['Rechazado', 'Cancelado'])
                          ->where(function ($q) use ($prog) {
                              $q->whereBetween('fecha_inicio', [$prog->fecha_inicio, $prog->fecha_fin])
                                ->orWhereBetween('fecha_fin', [$prog->fecha_inicio, $prog->fecha_fin])
                                ->orWhere(function ($q2) use ($prog) {
                                    $q2->where('fecha_inicio', '<=', $prog->fecha_inicio)
                                      ->where('fecha_fin', '>=', $prog->fecha_fin);
                                });
                          })->get();

                        if ($conflictosVacaciones->isNotEmpty()) {
                            $conflictosPersonal[] = [
                                'programacion_id' => $prog->id,
                                'fecha' => $prog->fecha_inicio->format('Y-m-d'),
                                'motivo' => "El $rolPersona tiene vacaciones programadas"
                            ];
                        }
                    }

                    // Validar contrato vigente
                    if ($validarContrato && $personalNuevoId) {
                        $personal = Personal::find($personalNuevoId);
                        $contratoVigente = $personal->contratos()
                            ->where('activo', 1)
                            ->where('fecha_inicio', '<=', $prog->fecha_inicio)
                            ->where('fecha_fin', '>=', $prog->fecha_fin)
                            ->exists();

                        if (!$contratoVigente) {
                            $conflictosPersonal[] = [
                                'programacion_id' => $prog->id,
                                'fecha' => $prog->fecha_inicio->format('Y-m-d'),
                                'motivo' => "El $rolPersona no tiene contrato vigente en este período"
                            ];
                        }
                    }
                }

                if (!empty($conflictosPersonal)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Se encontraron conflictos de validación de personal.',
                        'conflictos' => $conflictosPersonal,
                        'tipo' => 'conflictos_personal'
                    ], 422);
                }
            }

            // ✅ Proceder con las actualizaciones y registro de cambios
            $programacionesActualizadas = 0;
            $cambiosRegistrados = 0;

            foreach ($programaciones as $prog) {
                $cambiosData = [];

                if ($tipoCambio === 'conductor') {
                    $conductorNuevoObj = Personal::find($request->conductor_nuevo);
                    
                    // Obtener el conductor actual de esta programación específica
                    $conductorActual = $prog->personalAsignado()
                        ->whereHas('funcion', function($q) {
                            $q->where('nombre', 'Conductor');
                        })->first();

                    if (!$conductorActual) {
                        continue; // Saltar si no tiene conductor
                    }

                    // Validar que no sea el mismo conductor
                    if ($conductorActual->id == $request->conductor_nuevo) {
                        continue; // Saltar si es el mismo
                    }

                    $prog->update(['status' => 4]); // Cambiar a "Reprogramada"
                    
                    // Actualizar personal en la tabla pivote
                    DB::table('programacion_personal')
                        ->where('programacion_id', $prog->id)
                        ->where('personal_id', $conductorActual->id)
                        ->update(['personal_id' => $request->conductor_nuevo, 'updated_at' => now()]);

                    $cambiosData = [
                        'tipo_cambio' => 'personal',
                        'valor_anterior' => $conductorActual->id,
                        'valor_anterior_nombre' => $conductorActual->nombre_completo ?? 'N/A',
                        'valor_nuevo' => $request->conductor_nuevo,
                        'valor_nuevo_nombre' => $conductorNuevoObj->nombre_completo ?? 'N/A',
                    ];

                } elseif ($tipoCambio === 'ocupante') {
                    $ocupanteNuevoObj = Personal::find($request->ocupante_nuevo);
                    
                    // Obtener el ocupante actual de esta programación específica
                    $ocupanteActual = $prog->personalAsignado()
                        ->whereHas('funcion', function($q) {
                            $q->where('nombre', 'Ayudante');
                        })->first();

                    if (!$ocupanteActual) {
                        continue; // Saltar si no tiene ocupante
                    }

                    // Validar que no sea el mismo ocupante
                    if ($ocupanteActual->id == $request->ocupante_nuevo) {
                        continue; // Saltar si es el mismo
                    }

                    $prog->update(['status' => 4]);
                    
                    DB::table('programacion_personal')
                        ->where('programacion_id', $prog->id)
                        ->where('personal_id', $ocupanteActual->id)
                        ->update(['personal_id' => $request->ocupante_nuevo, 'updated_at' => now()]);

                    $cambiosData = [
                        'tipo_cambio' => 'personal',
                        'valor_anterior' => $ocupanteActual->id,
                        'valor_anterior_nombre' => $ocupanteActual->nombre_completo ?? 'N/A',
                        'valor_nuevo' => $request->ocupante_nuevo,
                        'valor_nuevo_nombre' => $ocupanteNuevoObj->nombre_completo ?? 'N/A',
                    ];

                } elseif ($tipoCambio === 'turno') {
                    $turnoNuevoObj = Turno::find($request->turno_nuevo);

                    // Validar que no sea el mismo turno
                    if ($prog->turno_id == $request->turno_nuevo) {
                        continue; // Saltar si es el mismo
                    }

                    $turnoActualNombre = $prog->turno->name ?? 'N/A';

                    $prog->update([
                        'turno_id' => $request->turno_nuevo,
                        'status' => 4
                    ]);

                    $cambiosData = [
                        'tipo_cambio' => 'turno',
                        'valor_anterior' => $prog->turno_id,
                        'valor_anterior_nombre' => $turnoActualNombre,
                        'valor_nuevo' => $request->turno_nuevo,
                        'valor_nuevo_nombre' => $turnoNuevoObj->name ?? 'N/A',
                    ];

                } elseif ($tipoCambio === 'vehiculo') {
                    $vehiculoNuevoObj = Vehiculo::find($request->vehiculo_nuevo);

                    // Validar que no sea el mismo vehículo
                    if ($prog->vehiculo_id == $request->vehiculo_nuevo) {
                        continue; // Saltar si es el mismo
                    }

                    $vehiculoActualNombre = $prog->vehiculo->codigo ?? 'N/A';

                    $prog->update([
                        'vehiculo_id' => $request->vehiculo_nuevo,
                        'status' => 4
                    ]);

                    $cambiosData = [
                        'tipo_cambio' => 'vehiculo',
                        'valor_anterior' => $prog->vehiculo_id,
                        'valor_anterior_nombre' => $vehiculoActualNombre,
                        'valor_nuevo' => $request->vehiculo_nuevo,
                        'valor_nuevo_nombre' => $vehiculoNuevoObj->codigo ?? 'N/A',
                    ];
                }

                // Registrar el cambio en la tabla de cambios
                if (!empty($cambiosData)) {
                    Cambio::create([
                        'programacion_id' => $prog->id,
                        'tipo_cambio' => $cambiosData['tipo_cambio'],
                        'valor_anterior' => $cambiosData['valor_anterior'],
                        'valor_anterior_nombre' => $cambiosData['valor_anterior_nombre'],
                        'valor_nuevo' => $cambiosData['valor_nuevo'],
                        'valor_nuevo_nombre' => $cambiosData['valor_nuevo_nombre'],
                        'motivo_id' => $motivoId,
                        'notas' => $notas,
                        'user_id' => Auth::id(),
                    ]);
                    
                    $cambiosRegistrados++;
                    $programacionesActualizadas++;
                }
            }

            DB::commit();

            // Construir mensaje de éxito con detalles
            $mensaje = '';
            if ($programacionesActualizadas > 0) {
                $mensaje = "✅ Se actualizaron $programacionesActualizadas programaciones y se registraron $cambiosRegistrados cambios.";
            } else {
                $mensaje = "ℹ️ No se realizaron cambios. Todas las programaciones ya tenían asignado el " . ($tipoCambio === 'conductor' ? 'conductor' : ($tipoCambio === 'ocupante' ? 'ocupante' : ($tipoCambio === 'turno' ? 'turno' : 'vehículo'))) . " seleccionado.";
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'datos' => [
                    'programaciones_encontradas' => $programaciones->count(),
                    'programaciones_actualizadas' => $programacionesActualizadas,
                    'cambios_registrados' => $cambiosRegistrados,
                    'programaciones_saltadas' => $programaciones->count() - $programacionesActualizadas
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en updateMasiva:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la modificación masiva.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function indexCambios()
{
    $zonas = Zona::where('activo', 1)->get();
    $turnos = Turno::where('activo', 1)->get();
    $vehiculos = Vehiculo::where('activo', 1)->get();
    
    // ✅ CORRECTO: funcion (singular)
    $conductores = Personal::whereHas('funcion', function($q) {
        $q->where('nombre', 'Conductor');
    })->where('activo', 1)->get();
    
    // ✅ CORRECTO: funcion (singular)
    $ayudantes = Personal::whereHas('funcion', function($q) {
        $q->where('nombre', 'Ayudante');
    })->where('activo', 1)->get();
    
    $motivos = Motivo::where('activo', 1)->get();
    
    return view('cambios.modificacion-masiva', compact(
        'zonas',
        'turnos',
        'vehiculos',
        'conductores',
        'ayudantes',
        'motivos'
    ));
}
}