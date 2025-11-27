<?php

namespace App\Http\Controllers;

use App\Models\Funcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FuncionController extends Controller
{
    public function index()
    {
        $funciones = Funcion::withCount('personal')->orderBy('nombre')->get();
        return view('personal.funciones.index', compact('funciones'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:funciones,nombre',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe una función con este nombre',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $funcion = Funcion::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'es_predefinida' => false,
                'activo' => $request->activo ?? true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Función registrada correctamente',
                'data' => $funcion
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la función: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Funcion $funcion)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $funcion->id,
                'nombre' => $funcion->nombre,
                'descripcion' => $funcion->descripcion,
                'es_predefinida' => $funcion->es_predefinida,
                'activo' => $funcion->activo,
            ]
        ]);
    }

    public function update(Request $request, Funcion $funcion)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:funciones,nombre,' . $funcion->id,
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe una función con este nombre',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $funcion->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->activo ?? $funcion->activo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Función actualizada correctamente',
                'data' => $funcion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la función: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Funcion $funcion)
    {
        try {
            if ($funcion->es_predefinida) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una función predefinida (Conductor/Ayudante)'
                ], 403);
            }

            if ($funcion->personal()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la función porque tiene ' . $funcion->personal()->count() . ' empleado(s) asignado(s)'
                ], 409);
            }

            $funcion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Función eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la función: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleActivo(Funcion $funcion)
    {
        try {
            $funcion->update(['activo' => !$funcion->activo]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'activo' => $funcion->activo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getActivas()
    {
        $funciones = Funcion::activos()
            ->select('id', 'nombre', 'es_predefinida')
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $funciones
        ]);
    }
}