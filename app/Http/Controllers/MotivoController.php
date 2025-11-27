<?php

namespace App\Http\Controllers;

use App\Models\Motivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MotivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $motivos = Motivo::orderBy('nombre')->get();
        return view('motivos.index', compact('motivos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:motivos,nombre',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe un motivo con este nombre',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $motivo = Motivo::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->activo ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Motivo creado exitosamente',
                'data' => $motivo
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el motivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Motivo $motivo)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $motivo->id,
                'nombre' => $motivo->nombre,
                'descripcion' => $motivo->descripcion,
                'activo' => $motivo->activo,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Motivo $motivo)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:motivos,nombre,' . $motivo->id,
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe un motivo con este nombre',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $motivo->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->activo ?? $motivo->activo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Motivo actualizado exitosamente',
                'data' => $motivo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el motivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Motivo $motivo)
    {
        try {
            // Verificar si tiene cambios asociados
            if ($motivo->cambios()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el motivo porque tiene cambios asociados'
                ], 409);
            }

            $motivo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Motivo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el motivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActivo(Motivo $motivo)
    {
        try {
            $motivo->update(['activo' => !$motivo->activo]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'activo' => $motivo->activo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }
}
