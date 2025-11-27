<?php

namespace App\Http\Controllers;

use App\Models\Vacaciones;
use App\Models\VacacionesPeriodo;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VacacionesController extends Controller
{
    public function index()
    {
        $periodos = VacacionesPeriodo::with(['vacaciones.personal'])->latest()->get();
        $personal = Personal::activos()
            ->conDerechoAVacaciones()
            ->orderBy('apellido_paterno')
            ->get();

        return view('personal.vacaciones.index', compact('periodos', 'personal'));
    }

    public function show($id)
    {
        $periodo = VacacionesPeriodo::with(['vacaciones.personal'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $periodo->id,
                'fecha_inicio' => $periodo->fecha_inicio->format('Y-m-d'),
                'fecha_fin' => $periodo->fecha_fin->format('Y-m-d'),
                'dias_utilizados' => $periodo->dias_utilizados,
                'estado' => $periodo->estado,
                'observaciones' => $periodo->observaciones,
                'vacaciones' => [
                    'personal_id' => $periodo->vacaciones->personal_id
                ]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'personal_id' => 'required|exists:personal,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'dias_utilizados' => 'required|integer|min:1',
            'estado' => 'required|string',
        ]);

        $conflicto = VacacionesPeriodo::whereHas('vacaciones', function ($q) use ($request) {
            $q->where('personal_id', $request->personal_id);
        })->whereNotIn('estado', ['rechazado', 'cancelado'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
                    ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin])
                    ->orWhere(function ($q2) use ($request) {
                        $q2->where('fecha_inicio', '<=', $request->fecha_inicio)
                            ->where('fecha_fin', '>=', $request->fecha_fin);
                    });
            })
            ->exists();

        if ($conflicto) {
            return response()->json([
                'success' => false,
                'message' => 'El empleado ya tiene vacaciones solicitadas en ese rango de fechas.'
            ], 422);
        }

        DB::transaction(function () use ($request) {
            $vacaciones = Vacaciones::firstOrCreate([
                'personal_id' => $request->personal_id,
                'anio' => date('Y'),
            ]);

            $vacaciones->periodos()->create([
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'dias_utilizados' => $request->dias_utilizados,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Vacaciones registradas correctamente.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'dias_utilizados' => 'required|integer|min:1',
            'estado' => 'required|string',
        ]);

        $periodo = VacacionesPeriodo::findOrFail($id);

        $conflicto = VacacionesPeriodo::whereHas('vacaciones', function ($q) use ($periodo) {
            $q->where('personal_id', $periodo->vacaciones->personal_id);
        })->whereNotIn('estado', ['rechazado', 'cancelado'])
            ->where('id', '!=', $periodo->id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
                    ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin])
                    ->orWhere(function ($q2) use ($request) {
                        $q2->where('fecha_inicio', '<=', $request->fecha_inicio)
                            ->where('fecha_fin', '>=', $request->fecha_fin);
                    });
            })
            ->exists();

        if ($conflicto) {
            return response()->json([
                'success' => false,
                'message' => 'El empleado ya tiene vacaciones solicitadas en ese rango de fechas.'
            ], 422);
        }

        $periodo->update([
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'dias_utilizados' => $request->dias_utilizados,
            'estado' => $request->estado,
            'observaciones' => $request->observaciones,
        ]);

        return response()->json(['success' => true, 'message' => 'Vacaciones actualizadas correctamente.']);
    }

    public function destroy($id)
    {
        $periodo = VacacionesPeriodo::findOrFail($id);
        $periodo->delete();

        return response()->json(['success' => true, 'message' => 'Registro eliminado correctamente.']);
    }

    public function diasDisponiblesPorAnio($personal_id, $anio)
{
    $usados = \App\Models\VacacionesPeriodo::whereHas('vacaciones', function ($q) use ($personal_id) {
        $q->where('personal_id', $personal_id);
    })
    ->whereYear('fecha_inicio', $anio)
    ->where('estado', ['aprobado', 'completado'])
    ->sum('dias_utilizados');

    $disponibles = max(30 - $usados, 0);

    return response()->json([
        'success' => true,
        'disponibles' => $disponibles,
        'usados' => $usados
    ]);
}

}
