<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Personal;
use App\Models\Zona;
use App\Models\Programaciones;
use App\Models\Vacaciones;
use App\Models\VacacionesPeriodo;
use App\Models\Contrato;
use App\Models\Asistencia;
use App\Models\Turno;
use App\Models\Motivo;
use App\Models\Funcion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Para Log
use Illuminate\Support\Facades\DB;  // Para DB
use Illuminate\Support\Facades\Auth; // Para Auth


class DashboardController extends Controller
{
  public function index(Request $request)
{
    // Obtener fecha del request o usar hoy por defecto
    $fechaSeleccionada = $request->input('fecha') 
        ? Carbon::parse($request->input('fecha')) 
        : Carbon::today();
    
    // ==========================================
    // KPI 1: ESTADO DE VEHÍCULOS
    // ==========================================
    $vehiculosDisponibles = Vehiculo::where('disponible', true)->count();
    $vehiculosNoDisponibles = Vehiculo::where('disponible', false)->count();
    $totalVehiculos = Vehiculo::count();
    
    // ==========================================
    // KPI 2: ESTADO DEL PERSONAL
    // ==========================================
    $personalActivo = Personal::where('activo', true)->count();
    $personalInactivo = Personal::where('activo', false)->count();
    $totalPersonal = Personal::count();
    
    // ==========================================
    // NUEVO KPI: ESTADO DE ASISTENCIA (FECHA SELECCIONADA)
    // ==========================================
    
    // IDs del personal asignado a cualquier programación hoy (esperadas)
    $personalAsignadoHoyIds = Programaciones::whereDate('fecha_inicio', $fechaSeleccionada->format('Y-m-d'))
        ->where('status', '!=', 0) // Excluir canceladas
        ->with('personalAsignado')
        ->get()
        ->flatMap(function ($prog) {
            return $prog->personalAsignado->pluck('id');
        })
        ->unique()
        ->toArray();
        
    $asistenciasEsperadas = count($personalAsignadoHoyIds);
    
    // Asistencias efectivamente registradas (Presente)
    $asistenciasRegistradas = Asistencia::where('fecha', $fechaSeleccionada->format('Y-m-d'))
        ->where('estado', 'presente')
        ->whereIn('personal_id', $personalAsignadoHoyIds)
        ->count();
        
    $asistenciasFaltantes = $asistenciasEsperadas - $asistenciasRegistradas;


    // ==========================================
    // KPI 3: ZONAS PROGRAMADAS (FECHA SELECCIONADA)
    // ==========================================
    $zonasHoy = $this->getZonasConEstadoHoy($fechaSeleccionada);
    
    // ==========================================
    // KPI 4: PROGRAMACIONES DEL DÍA SELECCIONADO
    // ==========================================
    $programacionesHoy = Programaciones::whereDate('fecha_inicio', $fechaSeleccionada->format('Y-m-d'))
        ->with(['zona', 'turno', 'vehiculo', 'personalAsignado.funcion'])
        ->get();
    
    $programacionesTotales = $programacionesHoy->count();
    $programacionesCompletadas = $programacionesHoy->where('status', 4)->count();
    $programacionesCanceladas = $programacionesHoy->where('status', 0)->count();
    $programacionesEnProgreso = $programacionesHoy->whereIn('status', [2, 3])->count();
    
    // 🚨 NUEVA MÉTRICA: Vehículos en recorrido (Status 2)
    $vehiculosEnRecorrido = $programacionesHoy->where('status', 2)
        ->pluck('vehiculo_id')
        ->unique()
        ->count();
    
    

    // ==========================================
    // KPI 8: PERSONAL LIBRE DISPONIBLE
    // ==========================================
    $personalLibre = $this->getPersonalLibreDisponible($fechaSeleccionada);
    
    $conductoresLibres = $personalLibre->filter(function($p) {
        return $p->funcion && $p->funcion->nombre === 'Conductor';
    })->count();

    $ayudantesLibres = $personalLibre->filter(function($p) {
        return $p->funcion && $p->funcion->nombre === 'Ayudante';
    })->count();

    $totalPersonalLibre = $conductoresLibres + $ayudantesLibres;
    
    // ==========================================

    // ==========================================
    // KPI 5: PERSONAL CON VACACIONES (FECHA SELECCIONADA)
    // ==========================================
    $personalConVacacionesHoy = $this->getPersonalConVacacionesHoy($fechaSeleccionada);
    $totalEnVacaciones = count($personalConVacacionesHoy);
    
    // ==========================================
    // KPI 6: PERSONAL SIN CONTRATO VIGENTE (FECHA SELECCIONADA)
    // ==========================================
    $personalSinContratoVigente = $this->getPersonalSinContratoVigente($fechaSeleccionada);
    $totalSinContrato = count($personalSinContratoVigente);
    
    // ==========================================
    // KPI 7: DISPONIBILIDAD POR TURNO
    // ==========================================
    $disponibilidadPorTurno = $this->getDisponibilidadPorTurno($fechaSeleccionada);
    
    // ==========================================
    // KPI 8: PORCENTAJE DE COBERTURA
    // ==========================================
    $tasaCobertura = $this->calcularTasaCobertura($programacionesHoy, $totalVehiculos, $totalPersonal);
    
    // ==========================================
    // KPI 9: ALERTAS CRÍTICAS
    // ==========================================
    $alertasCriticas = $this->generarAlertasCriticas($zonasHoy, $personalConVacacionesHoy, $personalSinContratoVigente);
    
    // ==========================================
    // DATOS PARA EDICIÓN DE PROGRAMACIONES
    // ==========================================
    $turnos = Turno::where('activo', 1)->get();
    $vehiculos = Vehiculo::where('disponible', 1)->get();
    $personalData = Personal::with('funcion')->where('activo', true)->get();
    $motivos = Motivo::where('activo', 1)->get();
    $funciones = Funcion::where('activo', 1)->get();
    
    return view('dashboard', compact(
        'vehiculosDisponibles',
        'vehiculosNoDisponibles',
        'totalVehiculos',
        'personalActivo',
        'personalInactivo',
        'totalPersonal',
        'zonasHoy',
        'programacionesHoy',
        'programacionesTotales',
        'programacionesCompletadas',
        'programacionesCanceladas',
        'programacionesEnProgreso',
        'personalConVacacionesHoy',
        'totalEnVacaciones',
        'personalSinContratoVigente',
        'totalSinContrato',
        'disponibilidadPorTurno',
        'tasaCobertura',
        'alertasCriticas',
        'fechaSeleccionada',
        'turnos',
        'vehiculos',
        'personalData',
        'motivos',
        'funciones',
        'asistenciasEsperadas',
        'asistenciasRegistradas',
        'asistenciasFaltantes',
        'vehiculosEnRecorrido',
        'totalPersonalLibre',
        'conductoresLibres',
        'ayudantesLibres',
    ));
}


/**
     * Obtener el personal activo, con contrato y sin vacaciones/programación para la fecha.
     */


/**
     * Obtener el personal activo, con contrato y sin vacaciones/programación para la fecha.
     */
    private function getPersonalLibreDisponible($fecha)
    {
        // 1. Obtener IDs de todo el personal programado para la fecha (Ocupados)
        $personalOcupadoIds = Programaciones::whereDate('fecha_inicio', $fecha->format('Y-m-d'))
            ->where('status', '!=', 0) // Excluir canceladas
            ->with('personalAsignado')
            ->get()
            ->flatMap(function ($prog) {
                return $prog->personalAsignado->pluck('id');
            })
            ->unique()
            ->toArray();

        // 2. Obtener IDs de personal en vacaciones (CORRECCIÓN AQUÍ)
        $vacacionesIdsHoy = VacacionesPeriodo::whereHas('vacaciones', function ($query) {
                $query->where('estado', 'programado');
            })
            ->where('estado', 'programado')
            ->where(function ($query) use ($fecha) {
                $query->where('fecha_inicio', '<=', $fecha)
                      ->where('fecha_fin', '>=', $fecha);
            })
            ->pluck('vacaciones_id') // ✅ Se extrae el ID de la relación (vacaciones_id)
            ->unique()
            ->toArray();
            
        // Buscar los personal_id a partir de los IDs de Vacaciones
        $personalEnVacacionesIds = Vacaciones::whereIn('id', $vacacionesIdsHoy)
            ->pluck('personal_id') // ✅ Ahora sí se puede extraer el personal_id de la tabla 'vacaciones'
            ->unique()
            ->toArray();

        // 3. Obtener IDs de personal sin contrato vigente
        $personalSinContratoIds = [];
        $personalActivoIds = Personal::where('activo', 1)->pluck('id')->toArray();
        
        foreach ($personalActivoIds as $personalId) {
            $tieneContratoVigente = Contrato::where('personal_id', $personalId)
                ->where('activo', 1)
                ->where('fecha_inicio', '<=', $fecha)
                ->where(function ($q) use ($fecha) {
                    $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
                })
                ->exists();

            if (!$tieneContratoVigente) {
                $personalSinContratoIds[] = $personalId;
            }
        }

        // 4. IDs de personal que está INHABILITADO
        $personalInhabilitadoIds = array_unique(array_merge(
            $personalOcupadoIds,
            $personalEnVacacionesIds,
            $personalSinContratoIds
        ));

        // 5. Personal Libre Disponible = Activo y NO Inhabilitado
        $personalLibre = Personal::where('activo', 1)
            ->whereNotIn('id', $personalInhabilitadoIds)
            ->with('funcion')
            ->get();

        return $personalLibre;
    }
/**
     * Valida si todo el personal asignado a una programación marcó asistencia
     * y, si se cumplen otras condiciones, actualiza su estado a 'En Recorrido' (status 2).
     */
    private function validarYActualizarEstadoRecorrido($programacion, $fecha)
    {
        // El vehículo no puede ir a 'En Recorrido' si ya fue completado o cancelado.
        if ($programacion->status == 4 || $programacion->status == 0) {
            return $programacion->status;
        }

        // 1. Obtener IDs de todo el personal ASIGNADO
        $personalAsignadoIds = $programacion->personalAsignado->pluck('id')->toArray();
        $totalPersonalAsignado = count($personalAsignadoIds);

        // Si no hay personal asignado, no puede iniciar.
        if ($totalPersonalAsignado === 0) {
            return $programacion->status; 
        }

        // 2. Contar el personal asignado que marcó 'presente' hoy
        $personalPresenteCount = Asistencia::where('fecha', $fecha->format('Y-m-d'))
            ->where('estado', 'presente')
            ->whereIn('personal_id', $personalAsignadoIds)
            ->count();

        // 3. Condición para iniciar recorrido (status 2)
        // Se requiere que todo el personal haya marcado asistencia Y que el vehículo esté disponible.
        $listoParaRecorrido = ($personalPresenteCount === $totalPersonalAsignado) && 
                              $programacion->vehiculo && 
                              $programacion->vehiculo->disponible;

        if ($listoParaRecorrido) {
            // Solo actualiza si NO está ya en 'En Recorrido' (status 2) o superior (e.g. 3, Pausa)
            if ($programacion->status < 2) { 
                $programacion->status = 2; // 2 = En Recorrido
                $programacion->save();
                // Opcionalmente, registra el inicio del recorrido si es necesario.
                Log::info("🚗 Programación #{$programacion->id} (Zona: {$programacion->zona->nombre}) actualizada a 'En Recorrido' automáticamente.");
            }
            return $programacion->status;
        }

        // Si no está listo y está en estado 1 (Programado), se mantiene.
        return $programacion->status; 
    }

    /**
     * Obtener zonas programadas para hoy con su estado (puede/no puede iniciar recorrido)
     */
    private function getZonasConEstadoHoy($fecha)
    {
        $programaciones = Programaciones::whereDate('fecha_inicio', $fecha->format('Y-m-d'))
            ->where('status', '!=', 0) // Excluir canceladas
            ->with(['zona', 'turno', 'personalAsignado.funcion', 'vehiculo', 'grupo'])
            ->get();

        $zonasEstado = [];
        
        foreach ($programaciones as $prog) {
            $zonaId = $prog->zona_id;
            $prog->status = $this->validarYActualizarEstadoRecorrido($prog, $fecha);
            
            if (!isset($zonasEstado[$zonaId])) {
                $zonasEstado[$zonaId] = [
                    'zona_id' => $prog->zona->id,
                    'zona_nombre' => $prog->zona->nombre,
                    'programaciones' => [],
                    'puede_iniciar' => true,
                    'razones_bloqueo' => []
                ];
            }

            // Validar si puede iniciar recorrido
            $puedeIniciar = $this->validarProgramacionPuedaIniciar($prog, $fecha);
            
            if (!$puedeIniciar['valido']) {
                $zonasEstado[$zonaId]['puede_iniciar'] = false;
                $zonasEstado[$zonaId]['razones_bloqueo'] = array_merge(
                    $zonasEstado[$zonaId]['razones_bloqueo'],
                    $puedeIniciar['razones']
                );
            }

            $zonasEstado[$zonaId]['programaciones'][] = [
                'id' => $prog->id,
                'turno' => $prog->turno->name ?? 'N/A',
                'vehiculo' => $prog->vehiculo->placa ?? 'N/A',
                'personal_count' => $prog->personalAsignado->count(),
                'conductor' => $this->obtenerConductor($prog),
                'ayudantes' => $this->obtenerAyudantes($prog),
                'status' => $prog->status_label
            ];
        }

        return array_values($zonasEstado);
    }

    /**
     * Validar si una programación puede iniciar recorrido
     */
    private function validarProgramacionPuedaIniciar($programacion, $fecha)
    {
        $razones = [];
        $valido = true;

        // 1. Verificar si hay vehículo disponible
        if (!$programacion->vehiculo || !$programacion->vehiculo->disponible) {
            $razones[] = 'Vehículo no disponible';
            $valido = false;
        }

        // 2. Verificar personal: conductor y ayudantes
        $personalAsignado = $programacion->personalAsignado;
        
        if ($personalAsignado->isEmpty()) {
            $razones[] = 'Sin personal asignado';
            $valido = false;
        } else {
            // Verificar que haya conductor
            $conductor = $personalAsignado->filter(function($p) {
                return $p->funcion && $p->funcion->nombre === 'Conductor';
            })->first();
            if (!$conductor) {
                $razones[] = 'Sin conductor asignado';
                $valido = false;
            }

            // Verificar asistencia, vacaciones y contrato del personal
            foreach ($personalAsignado as $personal) {
                // NUEVA VALIDACIÓN: Verificar asistencia marcada para hoy
                $asistenciaHoy = Asistencia::where('personal_id', $personal->id)
                    ->where('fecha', $fecha->format('Y-m-d'))
                    ->where('estado', 'presente')
                    ->exists();

                if (!$asistenciaHoy) {
                    $razones[] = "{$personal->nombre_completo} no ha marcado asistencia";
                    $valido = false;
                }

                // Verificar vacaciones
                $enVacaciones = VacacionesPeriodo::whereHas('vacaciones', function ($query) use ($personal) {
                        $query->where('personal_id', $personal->id)
                              ->where('estado', 'programado');
                    })
                    ->where('estado', 'programado')
                    ->where(function ($query) use ($fecha) {
                        $query->where('fecha_inicio', '<=', $fecha)
                              ->where('fecha_fin', '>=', $fecha);
                    })
                    ->exists();

                if ($enVacaciones) {
                    $razones[] = "{$personal->nombre_completo} está en vacaciones";
                    $valido = false;
                }

                // Verificar contrato vigente
                $tieneContratoVigente = Contrato::where('personal_id', $personal->id)
                    ->where('activo', 1)
                    ->where('fecha_inicio', '<=', $fecha)
                    ->where(function ($q) use ($fecha) {
                        $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
                    })
                    ->exists();

                if (!$tieneContratoVigente) {
                    $razones[] = "{$personal->nombre_completo} no tiene contrato vigente";
                    $valido = false;
                }
            }
        }

        return [
            'valido' => $valido,
            'razones' => array_unique($razones)
        ];
    }

    /**
     * Obtener conductor
     */
    private function obtenerConductor($programacion)
    {
        $conductor = $programacion->personalAsignado->filter(function($p) {
            return $p->funcion && $p->funcion->nombre === 'Conductor';
        })->first();

        return $conductor ? $conductor->nombre_completo : 'Sin conductor';
    }

    /**
     * Obtener ayudantes
     */
    private function obtenerAyudantes($programacion)
    {
        $ayudantes = $programacion->personalAsignado->filter(function($p) {
            return $p->funcion && $p->funcion->nombre === 'Ayudante';
        });

        if ($ayudantes->isEmpty()) {
            return 'Sin ayudantes';
        }

        return $ayudantes->pluck('nombre_completo')->implode(', ');
    }

    /**
     * Personal con vacaciones programadas para hoy
     */
    private function getPersonalConVacacionesHoy($fecha)
    {
        // 1. Obtener los IDs de las vacaciones activas hoy
        $vacacionesIds = VacacionesPeriodo::whereHas('vacaciones', function ($query) {
                $query->where('estado', 'programado');
            })
            ->where('estado', 'programado')
            ->where(function ($query) use ($fecha) {
                $query->where('fecha_inicio', '<=', $fecha)
                      ->where('fecha_fin', '>=', $fecha);
            })
            ->pluck('vacaciones_id')
            ->unique()
            ->toArray();

        // 2. Usar los IDs para obtener el personal
        return Personal::whereHas('vacaciones', function ($query) use ($vacacionesIds) {
                $query->whereIn('id', $vacacionesIds);
            })
            ->where('activo', 1)
            ->get()
            ->toArray();
    }

    
    /**
     * Personal sin contrato vigente
     */
    private function getPersonalSinContratoVigente($fecha)
    {
        $personalSinContrato = [];
        
        $personalActivo = Personal::where('activo', 1)->get();
        
        foreach ($personalActivo as $personal) {
            $tieneContratoVigente = Contrato::where('personal_id', $personal->id)
                ->where('activo', 1)
                ->where('fecha_inicio', '<=', $fecha)
                ->where(function ($q) use ($fecha) {
                    $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
                })
                ->exists();

            if (!$tieneContratoVigente) {
                $personalSinContrato[] = $personal;
            }
        }

        return $personalSinContrato;
    }

    /**
     * Disponibilidad por turno (Mañana, Tarde)
     */
    private function getDisponibilidadPorTurno($fecha)
    {
        $programaciones = Programaciones::whereDate('fecha_inicio', $fecha->format('Y-m-d'))
            ->where('status', '!=', 0)
            ->with(['turno', 'vehiculo', 'personalAsignado.funcion'])
            ->get();

        $disponibilidad = [
            'mañana' => ['total' => 0, 'funcionales' => 0, 'tasa' => 0],
            'tarde' => ['total' => 0, 'funcionales' => 0, 'tasa' => 0]
        ];

        foreach ($programaciones as $prog) {
            $turnoNombre = strtolower($prog->turno->name ?? '');
            
            if (strpos($turnoNombre, 'mañana') !== false || strpos($turnoNombre, 'ma') !== false) {
                $turno = 'mañana';
            } else {
                $turno = 'tarde';
            }

            if (isset($disponibilidad[$turno])) {
                $disponibilidad[$turno]['total']++;
                
                if ($this->validarProgramacionPuedaIniciar($prog, $fecha)['valido']) {
                    $disponibilidad[$turno]['funcionales']++;
                }
            }
        }

        // Calcular tasas
        foreach ($disponibilidad as &$turno) {
            $turno['tasa'] = $turno['total'] > 0 
                ? round(($turno['funcionales'] / $turno['total']) * 100, 1)
                : 0;
        }

        return $disponibilidad;
    }

    /**
     * Calcular tasa de cobertura del servicio
     */
    private function calcularTasaCobertura($programacionesHoy, $totalVehiculos, $totalPersonal)
    {
        $zonasConProgramacion = $programacionesHoy->pluck('zona_id')->unique()->count();
        $vehiculosEnUso = $programacionesHoy->pluck('vehiculo_id')->unique()->count();
        $personalEnUso = $programacionesHoy->flatMap(function ($prog) {
            return $prog->personalAsignado->pluck('id');
        })->unique()->count();

        return [
            'zonas' => [
                'en_uso' => $zonasConProgramacion,
                'total' => Zona::where('activo', 1)->count(),
                'tasa' => Zona::where('activo', 1)->count() > 0 
                    ? round(($zonasConProgramacion / Zona::where('activo', 1)->count()) * 100, 1)
                    : 0
            ],
            'vehiculos' => [
                'en_uso' => $vehiculosEnUso,
                'total' => $totalVehiculos,
                'tasa' => $totalVehiculos > 0
                    ? round(($vehiculosEnUso / $totalVehiculos) * 100, 1)
                    : 0
            ],
            'personal' => [
                'en_uso' => $personalEnUso,
                'total' => $totalPersonal,
                'tasa' => $totalPersonal > 0
                    ? round(($personalEnUso / $totalPersonal) * 100, 1)
                    : 0
            ]
        ];
    }

    /**
     * Generar alertas críticas
     */
    private function generarAlertasCriticas($zonasHoy, $personalConVacaciones, $personalSinContrato)
    {
        $alertas = [];

        // Zonas sin poder iniciar recorrido
        $zonasSinRecorrido = array_filter($zonasHoy, function ($zona) {
            return !$zona['puede_iniciar'];
        });

        if (!empty($zonasSinRecorrido)) {
            $alertas[] = [
                'tipo' => 'danger',
                'icono' => 'fa-exclamation-triangle',
                'titulo' => 'Grupos incompletos',
                'mensaje' => count($zonasSinRecorrido) . ' grupo (s) no puede iniciar recorrido',
                'zonas_afectadas' => count($zonasSinRecorrido)
            ];
        }

        // Personal en vacaciones
        if (!empty($personalConVacaciones)) {
            $alertas[] = [
                'tipo' => 'warning',
                'icono' => 'fa-umbrella-beach',
                'titulo' => 'Personal en vacaciones',
                'mensaje' => count($personalConVacaciones) . ' empleado(s) en vacaciones hoy',
                'personal_afectado' => count($personalConVacaciones)
            ];
        }

        // Personal sin contrato vigente
        // if (!empty($personalSinContrato)) {
        //     $alertas[] = [
        //         'tipo' => 'warning',
        //         'icono' => 'fa-file-contract',
        //         'titulo' => 'Contratos vencidos',
        //         'mensaje' => count($personalSinContrato) . ' empleado(s) sin contrato vigente',
        //         'personal_afectado' => count($personalSinContrato)
        //     ];
        // }

        return $alertas;
    }

    /**
     * ✅ MÉTODO MEJORADO: Obtener datos de programación para edición
     * Filtra personal según asistencia y disponibilidad real
     */
    public function getProgramacionData($id)
    {
        $programacion = Programaciones::with([
            'personal.funcion',
            'turno',
            'vehiculo',
            'zona'
        ])->findOrFail($id);
        
        $fechaProgramacion = Carbon::parse($programacion->fecha_inicio);
        
        // ==========================================
        // 1. PERSONAL ACTUAL: Solo los que NO marcaron asistencia
        // ==========================================
        $personalAsignadoIds = $programacion->personal->pluck('id')->toArray();
        
        // IDs de personal asignado que NO marcó asistencia
        $personalSinAsistenciaIds = array_diff(
            $personalAsignadoIds,
            Asistencia::where('fecha', $fechaProgramacion->format('Y-m-d'))
                ->where('estado', 'presente')
                ->whereIn('personal_id', $personalAsignadoIds)
                ->pluck('personal_id')
                ->toArray()
        );
        
        // Obtener el personal completo que no marcó asistencia
        $personalActualSinAsistencia = Personal::with('funcion')
            ->whereIn('id', $personalSinAsistenciaIds)
            ->get();
        
        // ==========================================
        // 2. PERSONAL DISPONIBLE: Con asistencia y sin conflictos
        // ==========================================
        
        // Obtener IDs de personal que SÍ marcó asistencia hoy
        $personalConAsistenciaIds = Asistencia::where('fecha', $fechaProgramacion->format('Y-m-d'))
            ->where('estado', 'presente')
            ->pluck('personal_id')
            ->toArray();
        
        // Obtener IDs de personal que tiene programación en la MISMA zona Y MISMO turno
        $personalOcupadoIds = Programaciones::where('id', '!=', $id) // Excluir la programación actual
            ->whereDate('fecha_inicio', $fechaProgramacion->format('Y-m-d'))
            ->where('zona_id', $programacion->zona_id)
            ->where('turno_id', $programacion->turno_id)
            ->where('status', '!=', 0) // Excluir canceladas
            ->with('personal')
            ->get()
            ->flatMap(function($prog) {
                return $prog->personal->pluck('id');
            })
            ->unique()
            ->toArray();
        
        // Personal disponible = Con asistencia - Ocupados en misma zona/turno
        $personalDisponibleIds = array_diff($personalConAsistenciaIds, $personalOcupadoIds);
        
        // Separar por función
        $conductoresDisponibles = Personal::with('funcion')
            ->whereHas('funcion', function ($query) {
                $query->where('nombre', 'Conductor');
            })
            ->whereIn('id', $personalDisponibleIds)
            ->where('activo', true)
            ->get();
        
        $ayudantesDisponibles = Personal::with('funcion')
            ->whereHas('funcion', function ($query) {
                $query->where('nombre', 'Ayudante');
            })
            ->whereIn('id', $personalDisponibleIds)
            ->where('activo', true)
            ->get();
        
        // ==========================================
        // 3. VEHÍCULOS Y TURNOS
        // ==========================================
        $vehiculosDisponibles = Vehiculo::where('disponible', 1)->get();
        $turnos = Turno::where('activo', 1)->get();
        
        return response()->json([
            'programacion' => $programacion,
            'personal_sin_asistencia' => $personalActualSinAsistencia, // ✅ Personal asignado SIN asistencia
            'conductores' => $conductoresDisponibles,
            'ayudantes' => $ayudantesDisponibles,
            'vehiculos' => $vehiculosDisponibles,
            'turnos' => $turnos
        ]);
    }
    public function updateConCambios(Request $request, $id)
{
    try {
        DB::beginTransaction();
        
        $programacion = Programaciones::with('personalAsignado')->findOrFail($id);
        $cambios = $request->input('cambios', []);
        $personalChanges = $request->input('personal_changes', []);
        
        Log::info('🔄 Iniciando actualización de personal', [
            'programacion_id' => $id,
            'cambios' => $personalChanges
        ]);
        
        if (!empty($personalChanges)) {
            foreach ($personalChanges as $change) {
                $anteriorId = (int) ($change['anterior_id'] ?? 0);
                $nuevoId = (int) ($change['nuevo_id'] ?? 0);
                
                if (!$anteriorId || !$nuevoId) {
                    continue;
                }
                
                Log::info('📝 Procesando cambio', [
                    'anterior_id' => $anteriorId,
                    'nuevo_id' => $nuevoId
                ]);
                
                // ✅ PASO 1: Verificar si el nuevo personal YA está en la programación
                $yaExiste = DB::table('programacion_personal')
                    ->where('programacion_id', $id)
                    ->where('personal_id', $nuevoId)
                    ->exists();
                
                if ($yaExiste) {
                    Log::warning('⚠️ El nuevo personal YA está asignado - Solo eliminando el anterior');
                    
                    // Solo ELIMINAR el personal anterior
                    $deleted = DB::table('programacion_personal')
                        ->where('programacion_id', $id)
                        ->where('personal_id', $anteriorId)
                        ->delete();
                    
                    Log::info('✅ Personal anterior eliminado', ['filas' => $deleted]);
                    
                    // NO hacer nada más, el nuevo ya existe
                    continue;
                }
                
                // ✅ PASO 2: Si el nuevo NO existe, hacer DELETE + INSERT
                Log::info('🔄 Nuevo personal NO existe - Haciendo cambio');
                
                // Eliminar el anterior
                $deleted = DB::table('programacion_personal')
                    ->where('programacion_id', $id)
                    ->where('personal_id', $anteriorId)
                    ->delete();
                
                Log::info('🗑️ Anterior eliminado', ['filas' => $deleted]);
                
                // Insertar el nuevo
                if ($deleted > 0) {
                    DB::table('programacion_personal')->insert([
                        'personal_id' => $nuevoId,
                        'programacion_id' => $id,
                        'fecha_dia' => $programacion->fecha_inicio,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    Log::info('✅ Nuevo personal insertado');
                }
            }
            
            $programacion->unsetRelation('personalAsignado');
        }
        
        // Registrar cambios en la tabla 'cambios'
        if (!empty($cambios)) {
            foreach ($cambios as $cambio) {
                DB::table('cambios')->insert([
                    'programacion_id' => $id,
                    'tipo_cambio' => $cambio['tipo_cambio'] ?? 'personal',
                    'valor_anterior' => $cambio['valor_anterior'] ?? null,
                    'valor_nuevo' => $cambio['valor_nuevo'] ?? null,
                    'motivo_id' => $cambio['motivo_id'] ?? null,
                    'notas' => $cambio['notas'] ?? null,
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        DB::commit();
        
        Log::info('✅ Actualización completada exitosamente');
        
        return response()->json([
            'success' => true, 
            'message' => 'Cambios guardados correctamente'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('❌ Error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

}



