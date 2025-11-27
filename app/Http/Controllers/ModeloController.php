<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModeloController extends Controller
{
    public function index()
    {
        $modelos = Modelo::with('marca')->orderBy('nombre')->get();
        $marcas = Marca::activos()->orderBy('nombre')->get();
        return view('vehiculos.modelos.index', compact('modelos', 'marcas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'marca_id' => 'required|exists:marcas,id',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ], [
            'marca_id.required' => 'La marca es obligatoria',
            'marca_id.exists' => 'La marca seleccionada no existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $modelo = Modelo::create([
                'marca_id' => $request->marca_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->activo ?? true,
            ]);

            $modelo->load('marca');

            return response()->json([
                'success' => true,
                'message' => 'Modelo creado exitosamente',
                'data' => $modelo
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el modelo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Modelo $modelo)
    {
        $modelo->load('marca');
        return response()->json([
            'success' => true,
            'data' => $modelo
        ]);
    }

    public function update(Request $request, Modelo $modelo)
    {
        $validator = Validator::make($request->all(), [
            'marca_id' => 'required|exists:marcas,id',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ], [
            'marca_id.required' => 'La marca es obligatoria',
            'marca_id.exists' => 'La marca seleccionada no existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $modelo->update([
                'marca_id' => $request->marca_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->activo ?? $modelo->activo,
            ]);

            $modelo->load('marca');

            return response()->json([
                'success' => true,
                'message' => 'Modelo actualizado exitosamente',
                'data' => $modelo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el modelo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Modelo $modelo)
    {
        try {
            if ($modelo->vehiculos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el modelo porque tiene vehículos asociados'
                ], 409);
            }

            $modelo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Modelo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el modelo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleActivo(Modelo $modelo)
    {
        try {
            $modelo->update(['activo' => !$modelo->activo]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'activo' => $modelo->activo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get models by brand (for dependent dropdowns)
     */
    public function getPorMarca($marcaId)
    {
        try {
            $modelos = Modelo::where('marca_id', $marcaId)
                ->activos()
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $modelos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los modelos: ' . $e->getMessage()
            ], 500);
        }
    }
}