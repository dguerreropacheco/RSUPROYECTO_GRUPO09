<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TurnosController extends Controller
{
    /**
     * Muestra la lista de turnos y el formulario modal.
     */
    public function index()
    {
        $turnos = Turno::all();
        return view('turnos.index', compact('turnos'));
    }

    /**
     * Almacena un nuevo turno o actualiza uno existente.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'hour_in' => 'required',
        'hour_out' => 'required',
        'description' => 'nullable|string',
    ]);

    // Convertir a Carbon para comparar - manejo más flexible de formatos
    try {
        // Carbon::parse maneja múltiples formatos automáticamente
        $horaEntrada = Carbon::parse($request->hour_in);
        $horaSalida = Carbon::parse($request->hour_out);
        
        // Extraer solo hora y minuto para comparación
        $horaEntradaStr = $horaEntrada->format('H:i');
        $horaSalidaStr = $horaSalida->format('H:i');
        
        // Comparar las horas como strings
        if ($horaSalidaStr <= $horaEntradaStr) {
            return response()->json([
                'success' => false,
                'message' => 'La hora de salida debe ser posterior a la hora de entrada.'
            ], 422);
        }
        
        // Actualizar el request con el formato correcto para guardar
        $request->merge([
            'hour_in' => $horaEntradaStr,
            'hour_out' => $horaSalidaStr
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Formato de hora inválido: ' . $e->getMessage()
        ], 422);
    }

    $id = $request->input('turno_id');

    if ($id) {
        // Actualizar
        $turno = Turno::findOrFail($id);
        $turno->update($request->only(['name', 'hour_in', 'hour_out', 'description']));
        $message = 'Turno actualizado con éxito.';
    } else {
        // Crear nuevo
        Turno::create($request->only(['name', 'hour_in', 'hour_out', 'description']));
        $message = 'Turno creado con éxito.';
    }

    return response()->json(['success' => true, 'message' => $message]);
}

    /**
     * Obtiene los datos de un turno para edición.
     */
    public function show(Turno $turno)
    {
        return response()->json(['success' => true, 'data' => $turno]);
    }

    /**
     * Elimina un turno.
     */
    public function destroy(Turno $turno)
    {
        $turno->delete();
        return response()->json(['success' => true, 'message' => 'Turno eliminado con éxito.']);
    }
}