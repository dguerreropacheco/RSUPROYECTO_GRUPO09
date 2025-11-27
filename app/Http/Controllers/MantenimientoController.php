<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\HorarioMantenimiento;
use App\Models\DiaMantenimiento;
use App\Models\Vehiculo;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MantenimientoController extends Controller
{
    // ==========================================
    // MANTENIMIENTOS (Períodos)
    // ==========================================
    
    public function index(Request $request)
    {
        // Cargar vehículos con sus relaciones
        $vehiculos = Vehiculo::with(['marca', 'modelo', 'tipoVehiculo'])
                             ->where('activo', 1)
                             ->get();
        
        $responsables = Personal::where('activo', 1)->get();
        
        // Si es petición AJAX, devolver JSON
        if ($request->ajax()) {
            $mantenimientos = Mantenimiento::with(['horarios.vehiculo.marca', 'horarios.vehiculo.modelo', 'horarios.responsable', 'horarios.dias'])
                                           ->orderBy('fecha_inicio', 'desc')
                                           ->get();
            return response()->json($mantenimientos);
        }
        
        // Si es petición normal, devolver vista
        return view('mantenimiento.index', compact('vehiculos', 'responsables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        ]);

        // Validar solapamiento
        if (Mantenimiento::validarSolapamiento($request->fecha_inicio, $request->fecha_fin)) {
            return response()->json([
                'success' => false,
                'message' => 'Las fechas se solapan con otro mantenimiento existente.'
            ], 422);
        }

        $mantenimiento = Mantenimiento::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento creado con éxito.',
            'data' => $mantenimiento->load('horarios')
        ]);
    }

    public function update(Request $request, Mantenimiento $mantenimiento)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        ]);

        // Validar solapamiento (excluyendo el actual)
        if (Mantenimiento::validarSolapamiento(
            $request->fecha_inicio, 
            $request->fecha_fin, 
            $mantenimiento->id
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Las fechas se solapan con otro mantenimiento existente.'
            ], 422);
        }

        $mantenimiento->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento actualizado con éxito.',
            'data' => $mantenimiento->fresh(['horarios'])
        ]);
    }

    public function destroy(Mantenimiento $mantenimiento)
    {
        // Validar que no tenga horarios
        if ($mantenimiento->horarios()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el mantenimiento porque tiene horarios registrados.'
            ], 422);
        }

        $mantenimiento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento eliminado con éxito.'
        ]);
    }

    // ==========================================
    // HORARIOS
    // ==========================================
    
    public function storeHorario(Request $request)
    {
        $request->validate([
            'mantenimiento_id' => 'required|exists:mantenimientos,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'responsable_id' => 'required|exists:personal,id',
            'tipo_mantenimiento' => 'required|in:Preventivo,Limpieza,Reparación',
            'dia_semana' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ], [
            'vehiculo_id.required' => 'El vehículo es obligatorio.',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe.',
            'responsable_id.required' => 'El responsable es obligatorio.',
            'responsable_id.exists' => 'El responsable seleccionado no existe.',
            'tipo_mantenimiento.required' => 'El tipo de mantenimiento es obligatorio.',
            'tipo_mantenimiento.in' => 'El tipo de mantenimiento debe ser Preventivo, Limpieza o Reparación.',
            'dia_semana.required' => 'El día de la semana es obligatorio.',
            'dia_semana.in' => 'El día de la semana no es válido.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        // Validar solapamiento
        if (HorarioMantenimiento::validarSolapamiento(
            $request->mantenimiento_id,
            $request->dia_semana,
            $request->vehiculo_id,
            $request->hora_inicio,
            $request->hora_fin
        )) {
            return response()->json([
                'success' => false,
                'message' => 'El horario se solapa con otro horario existente para el mismo vehículo.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $horario = HorarioMantenimiento::create($request->all());
            
            // Generar días automáticamente
            $cantidadDias = $horario->generarDias();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Horario creado con éxito. Se generaron {$cantidadDias} días.",
                'data' => $horario->load(['vehiculo', 'responsable', 'dias'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear horario: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el horario.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    public function updateHorario(Request $request, HorarioMantenimiento $horario)
    {
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'responsable_id' => 'required|exists:personal,id',
            'tipo_mantenimiento' => 'required|in:Preventivo,Limpieza,Reparación',
            'dia_semana' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ], [
            'vehiculo_id.required' => 'El vehículo es obligatorio.',
            'vehiculo_id.exists' => 'El vehículo seleccionado no existe.',
            'responsable_id.required' => 'El responsable es obligatorio.',
            'responsable_id.exists' => 'El responsable seleccionado no existe.',
            'tipo_mantenimiento.required' => 'El tipo de mantenimiento es obligatorio.',
            'tipo_mantenimiento.in' => 'El tipo de mantenimiento debe ser Preventivo, Limpieza o Reparación.',
            'dia_semana.required' => 'El día de la semana es obligatorio.',
            'dia_semana.in' => 'El día de la semana no es válido.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        // Validar solapamiento (excluyendo el actual)
        if (HorarioMantenimiento::validarSolapamiento(
            $horario->mantenimiento_id,
            $request->dia_semana,
            $request->vehiculo_id,
            $request->hora_inicio,
            $request->hora_fin,
            $horario->id
        )) {
            return response()->json([
                'success' => false,
                'message' => 'El horario se solapa con otro horario existente.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Verificar si cambió el día de la semana
            $cambioDeMantenimiento = $horario->dia_semana !== $request->dia_semana;
            
            $mensaje = '';
            
            if ($cambioDeMantenimiento) {
                // Si cambió el día, eliminar días antiguos y regenerar
                $horario->dias()->delete();
                $horario->update($request->all());
                $cantidadDias = $horario->generarDias();
                $mensaje = "Horario actualizado. Se regeneraron {$cantidadDias} días porque cambió el día de la semana.";
            } else {
                // Si solo cambiaron horas, vehículo o responsable, NO regenerar días
                $horario->update($request->all());
                $mensaje = "Horario actualizado exitosamente. Se conservaron los días registrados.";
            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'data' => $horario->fresh(['vehiculo', 'responsable', 'dias'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar horario: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el horario.'
            ], 500);
        }
    }

    public function destroyHorario(HorarioMantenimiento $horario)
    {
        $cantidadDias = $horario->dias()->count();
        $horario->delete(); // CASCADE eliminará los días automáticamente

        return response()->json([
            'success' => true,
            'message' => "Horario eliminado. Se eliminaron {$cantidadDias} días asociados."
        ]);
    }

    public function showDias(HorarioMantenimiento $horario)
    {
        $dias = $horario->dias()->orderBy('fecha')->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'horario' => $horario->load(['vehiculo', 'responsable', 'mantenimiento']),
                'dias' => $dias
            ]
        ]);
    }

    // ==========================================
    // DÍAS
    // ==========================================
    
    public function updateDia(Request $request, DiaMantenimiento $dia)
    {
        $request->validate([
            'observacion' => 'nullable|string',
            'realizado' => 'required|boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'realizado.required' => 'Debe indicar si el mantenimiento fue realizado o no.',
            'realizado.boolean' => 'El campo realizado debe ser verdadero o falso.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png o jpg.',
            'imagen.max' => 'La imagen no debe ser mayor a 2MB.',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('imagen');
            
            // Procesar imagen si se subió
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($dia->imagen) {
                    Storage::disk('public')->delete($dia->imagen);
                }
                
                $path = $request->file('imagen')->store('mantenimiento', 'public');
                $data['imagen'] = $path;
            }
            
            $dia->update($data);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Día actualizado con éxito.',
                'data' => $dia->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar día: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el día.'
            ], 500);
        }
    }
}






















