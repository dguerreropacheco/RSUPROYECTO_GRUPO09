<?php

namespace App\Http\Controllers;

use App\Models\TipoVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoVehiculoController extends Controller
{
    public function index()
    {
        $tipos = TipoVehiculo::orderBy('nombre')->get();
        return view('vehiculos.tipos.index', compact('tipos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:tipos_vehiculo,nombre',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe un tipo de vehículo con este nombre',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipo = TipoVehiculo::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->activo ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de vehículo creado exitosamente',
                'data' => $tipo
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el tipo de vehículo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(TipoVehiculo $tipo)
    {
        return response()->json([
            'success' => true,
            'data' => $tipo
        ]);
    }

    public function update(Request $request, TipoVehiculo $tipo)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:tipos_vehiculo,nombre,' . $tipo->id,
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe un tipo de vehículo con este nombre',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tipo->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->activo ?? $tipo->activo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tipo de vehículo actualizado exitosamente',
                'data' => $tipo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el tipo de vehículo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(TipoVehiculo $tipo)
    {
        try {
            if ($tipo->vehiculos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el tipo porque tiene vehículos asociados'
                ], 409);
            }

            $tipo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de vehículo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el tipo de vehículo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleActivo(TipoVehiculo $tipo)
    {
        try {
            $tipo->update(['activo' => !$tipo->activo]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'activo' => $tipo->activo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }
}