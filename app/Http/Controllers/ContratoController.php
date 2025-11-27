<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Personal;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContratoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contratos = Contrato::with(['personal.funcion', 'departamento'])
                            ->orderBy('fecha_inicio', 'desc')
                            ->get();
        
        $personalDisponible = Personal::activos()
                                     ->whereDoesntHave('contratoActivo')
                                     ->with('funcion')
                                     ->orderBy('nombres')
                                     ->get();
        
        $departamentos = Departamento::activos()
                                    ->orderBy('nombre')
                                    ->get();
        
        return view('personal.contratos.index', compact('contratos', 'personalDisponible', 'departamentos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'personal_id' => 'required|exists:personal,id',
            'tipo_contrato' => 'required|in:permanente,temporal,nombrado',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'salario' => 'nullable|numeric|min:0',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'periodo_prueba' => 'nullable|integer|min:0|max:12',
            'observaciones' => 'nullable|string',
            'activo' => 'boolean'
        ], [
            'personal_id.required' => 'Debe seleccionar un empleado',
            'personal_id.exists' => 'El empleado seleccionado no existe',
            'tipo_contrato.required' => 'El tipo de contrato es obligatorio',
            'tipo_contrato.in' => 'El tipo de contrato debe ser permanente o temporal',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
            'fecha_fin.date' => 'La fecha de finalización debe ser una fecha válida',
            'fecha_fin.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio',
            'salario.numeric' => 'El salario debe ser un número',
            'salario.min' => 'El salario debe ser mayor o igual a 0',
            'departamento_id.exists' => 'El departamento seleccionado no existe',
            'periodo_prueba.integer' => 'El período de prueba debe ser un número entero',
            'periodo_prueba.min' => 'El período de prueba debe ser mayor o igual a 0',
            'periodo_prueba.max' => 'El período de prueba no puede ser mayor a 12 meses'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Validar que no tenga contrato activo si se está creando uno activo
            $tieneContratoActivo = Contrato::where('personal_id', $request->personal_id)
                                          ->where('activo', true)
                                          ->exists();
            
            if ($tieneContratoActivo && ($request->activo ?? true)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'El empleado ya tiene un contrato activo'
                ], 422);
            }

            // Si es nobmrado, fecha_fin debe ser null
            $fechaFin = $request->tipo_contrato === 'nombrado' ? null : $request->fecha_fin;

            // Si se marca como activo, desactivar otros contratos del mismo personal
            if ($request->activo ?? true) {
                Contrato::where('personal_id', $request->personal_id)
                       ->where('activo', true)
                       ->update(['activo' => false]);
            }

            // Crear el contrato
            $contrato = Contrato::create([
                'personal_id' => $request->personal_id,
                'tipo_contrato' => $request->tipo_contrato,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $fechaFin,
                'salario' => $request->salario,
                'departamento_id' => $request->departamento_id,
                'periodo_prueba' => $request->periodo_prueba,
                'observaciones' => $request->observaciones,
                'activo' => $request->activo ?? true
            ]);

            // Cargar relaciones
            $contrato->load('personal.funcion', 'departamento');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrato registrado exitosamente',
                'data' => $this->formatContratoForResponse($contrato)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear contrato: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el contrato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contrato $contrato)
    {
        $contrato->load('personal.funcion', 'departamento');
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $contrato->id,
                'personal_id' => $contrato->personal_id,
                'personal_nombre' => $contrato->personal->nombre_completo,
                'tipo_contrato' => $contrato->tipo_contrato,
                'fecha_inicio' => $contrato->fecha_inicio->format('Y-m-d'),
                'fecha_fin' => $contrato->fecha_fin ? $contrato->fecha_fin->format('Y-m-d') : null,
                'salario' => $contrato->salario,
                'departamento_id' => $contrato->departamento_id,
                'departamento_nombre' => $contrato->departamento ? $contrato->departamento->nombre : null,
                'periodo_prueba' => $contrato->periodo_prueba,
                'observaciones' => $contrato->observaciones,
                'motivo_terminacion' => $contrato->motivo_terminacion,
                'activo' => (bool)$contrato->activo,
                'esta_vigente' => $contrato->esta_vigente,
                'dias_restantes' => $contrato->dias_restantes
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contrato $contrato)
    {
        $validator = Validator::make($request->all(), [
            'personal_id' => 'required|exists:personal,id',
            'tipo_contrato' => 'required|in:permanente,temporal,nombrado',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'salario' => 'nullable|numeric|min:0',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'periodo_prueba' => 'nullable|integer|min:0|max:12',
            'observaciones' => 'nullable|string',
            'motivo_terminacion' => 'nullable|string',
            'activo' => 'boolean'
        ], [
            'personal_id.required' => 'Debe seleccionar un empleado',
            'personal_id.exists' => 'El empleado seleccionado no existe',
            'tipo_contrato.required' => 'El tipo de contrato es obligatorio',
            'tipo_contrato.in' => 'El tipo de contrato debe ser permanente o temporal',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_fin.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio',
            'salario.numeric' => 'El salario debe ser un número',
            'salario.min' => 'El salario debe ser mayor o igual a 0',
            'departamento_id.exists' => 'El departamento seleccionado no existe',
            'periodo_prueba.max' => 'El período de prueba no puede ser mayor a 12 meses'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Si se marca como activo y el personal cambió, validar
            if ($request->activo && $request->personal_id != $contrato->personal_id) {
                $tieneContratoActivo = Contrato::where('personal_id', $request->personal_id)
                                              ->where('activo', true)
                                              ->where('id', '!=', $contrato->id)
                                              ->exists();
                
                if ($tieneContratoActivo) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'El empleado seleccionado ya tiene un contrato activo'
                    ], 422);
                }
            }

            // Si se activa este contrato, desactivar otros del mismo personal
            if ($request->activo) {
                Contrato::where('personal_id', $request->personal_id)
                       ->where('activo', true)
                       ->where('id', '!=', $contrato->id)
                       ->update(['activo' => false]);
            }

            // Si es permanente, fecha_fin debe ser null
            $fechaFin = $request->tipo_contrato === 'nombrado' ? null : $request->fecha_fin;

            // Actualizar el contrato
            $contrato->update([
                'personal_id' => $request->personal_id,
                'tipo_contrato' => $request->tipo_contrato,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $fechaFin,
                'salario' => $request->salario,
                'departamento_id' => $request->departamento_id,
                'periodo_prueba' => $request->periodo_prueba,
                'observaciones' => $request->observaciones,
                'motivo_terminacion' => $request->motivo_terminacion,
                'activo' => $request->activo ?? $contrato->activo
            ]);

            // Recargar relaciones
            $contrato->load('personal.funcion', 'departamento');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrato actualizado exitosamente',
                'data' => $this->formatContratoForResponse($contrato)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar contrato: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el contrato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contrato $contrato)
    {
        try {
            // No validar vacaciones aquí, ya que Contrato usa SoftDeletes
            // y las vacaciones tienen su propia gestión
            
            $contrato->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contrato eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar contrato: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el contrato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActivo(Contrato $contrato)
    {
        try {
            DB::beginTransaction();

            $nuevoEstado = !$contrato->activo;

            // Si se está activando, desactivar otros contratos del mismo personal
            if ($nuevoEstado) {
                Contrato::where('personal_id', $contrato->personal_id)
                       ->where('activo', true)
                       ->where('id', '!=', $contrato->id)
                       ->update(['activo' => false]);
            }

            $contrato->update(['activo' => $nuevoEstado]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'activo' => (bool)$contrato->activo
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Terminar contrato anticipadamente
     */
    public function terminar(Request $request, Contrato $contrato)
    {
        $validator = Validator::make($request->all(), [
            'motivo_terminacion' => 'required|string|min:10',
            'fecha_terminacion' => 'required|date|after_or_equal:' . $contrato->fecha_inicio->format('Y-m-d') . '|before_or_equal:today'
        ], [
            'motivo_terminacion.required' => 'Debe especificar el motivo de terminación',
            'motivo_terminacion.min' => 'El motivo debe tener al menos 10 caracteres',
            'fecha_terminacion.required' => 'Debe especificar la fecha de terminación',
            'fecha_terminacion.after_or_equal' => 'La fecha de terminación no puede ser anterior a la fecha de inicio del contrato',
            'fecha_terminacion.before_or_equal' => 'La fecha de terminación no puede ser futura'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $contrato->update([
                'fecha_fin' => $request->fecha_terminacion,
                'motivo_terminacion' => $request->motivo_terminacion,
                'activo' => false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrato terminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al terminar el contrato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contratos vigentes
     */
    public function getVigentes()
    {
        try {
            $contratos = Contrato::vigentes()
                                ->with(['personal.funcion', 'departamento'])
                                ->orderBy('fecha_inicio', 'desc')
                                ->get()
                                ->map(function ($contrato) {
                                    return [
                                        'id' => $contrato->id,
                                        'personal_id' => $contrato->personal_id,
                                        'personal_nombre' => $contrato->personal->nombre_completo,
                                        'personal_dni' => $contrato->personal->dni,
                                        'tipo_contrato' => $contrato->tipo_contrato,
                                        'fecha_inicio' => $contrato->fecha_inicio->format('d/m/Y'),
                                        'fecha_fin' => $contrato->fecha_fin ? $contrato->fecha_fin->format('d/m/Y') : 'Indefinido',
                                        'dias_restantes' => $contrato->dias_restantes,
                                        'departamento' => $contrato->departamento ? $contrato->departamento->nombre : 'Sin departamento'
                                    ];
                                });

            return response()->json([
                'success' => true,
                'data' => $contratos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener contratos vigentes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contratos por vencer (próximos 30 días)
     */
    public function getPorVencer()
    {
        try {
            $fechaLimite = Carbon::today()->addDays(30);
            
            $contratos = Contrato::activos()
                                ->temporales()
                                ->whereNotNull('fecha_fin')
                                ->where('fecha_fin', '<=', $fechaLimite)
                                ->where('fecha_fin', '>=', Carbon::today())
                                ->with(['personal.funcion', 'departamento'])
                                ->orderBy('fecha_fin', 'asc')
                                ->get()
                                ->map(function ($contrato) {
                                    return [
                                        'id' => $contrato->id,
                                        'personal_nombre' => $contrato->personal->nombre_completo,
                                        'personal_dni' => $contrato->personal->dni,
                                        'fecha_inicio' => $contrato->fecha_inicio->format('d/m/Y'),
                                        'fecha_fin' => $contrato->fecha_fin->format('d/m/Y'),
                                        'dias_restantes' => $contrato->dias_restantes
                                    ];
                                });

            return response()->json([
                'success' => true,
                'data' => $contratos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener contratos por vencer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener personal disponible para contrato (sin contrato activo)
     */
    public function getPersonalDisponible()
    {
        try {
            $personal = Personal::activos()
                               ->whereDoesntHave('contratoActivo')
                               ->with('funcion')
                               ->orderBy('nombres')
                               ->get()
                               ->map(function ($p) {
                                   return [
                                       'id' => $p->id,
                                       'nombre_completo' => $p->nombre_completo,
                                       'dni' => $p->dni,
                                       'funcion' => $p->funcion ? $p->funcion->nombre : 'Sin función'
                                   ];
                               });

            return response()->json([
                'success' => true,
                'data' => $personal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener personal disponible: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * MÉTODO CRÍTICO: Formatear contrato para respuesta AJAX
     * Este método asegura que el JavaScript reciba TODOS los datos necesarios
     */
    private function formatContratoForResponse($contrato)
    {
        return [
            'id' => $contrato->id,
            'personal_id' => $contrato->personal_id,
            'tipo_contrato' => $contrato->tipo_contrato,
            'fecha_inicio' => $contrato->fecha_inicio->format('Y-m-d'),
            'fecha_fin' => $contrato->fecha_fin ? $contrato->fecha_fin->format('Y-m-d') : null,
            'salario' => $contrato->salario,
            'departamento_id' => $contrato->departamento_id,
            'periodo_prueba' => $contrato->periodo_prueba,
            'observaciones' => $contrato->observaciones,
            'motivo_terminacion' => $contrato->motivo_terminacion,
            'activo' => (bool)$contrato->activo,
            // Atributos calculados
            'esta_vigente' => $contrato->esta_vigente,
            'dias_restantes' => $contrato->dias_restantes,
            'es_permanente' => $contrato->es_permanente,
            'es_temporal' => $contrato->es_temporal,
            'salario_formateado' => $contrato->salario_formateado,
            // Relaciones
            'personal' => [
                'id' => $contrato->personal->id,
                'nombre_completo' => $contrato->personal->nombre_completo,
                'dni' => $contrato->personal->dni,
                'funcion' => $contrato->personal->funcion ? [
                    'id' => $contrato->personal->funcion->id,
                    'nombre' => $contrato->personal->funcion->nombre
                ] : null
            ],
            'departamento' => $contrato->departamento ? [
                'id' => $contrato->departamento->id,
                'nombre' => $contrato->departamento->nombre
            ] : null
        ];
    }
}
















