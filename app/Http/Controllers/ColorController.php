<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{
    public function index()
    {
        $colores = Color::orderBy('nombre')->get();
        return view('vehiculos.colores.index', compact('colores'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:colores,nombre',
            'codigo_rgb' => 'required|regex:/^#[0-9A-Fa-f]{6}$/|unique:colores,codigo_rgb',
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe un color con este nombre',
            'nombre.max' => 'El nombre no puede exceder 50 caracteres',
            'codigo_rgb.required' => 'El código RGB es obligatorio',
            'codigo_rgb.regex' => 'El código RGB debe tener el formato #FFFFFF',
            'codigo_rgb.unique' => 'Ya existe un color con este código RGB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $color = Color::create([
                'nombre' => $request->nombre,
                'codigo_rgb' => strtoupper($request->codigo_rgb),
                'activo' => $request->activo ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Color creado exitosamente',
                'data' => $color
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el color: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Color $color)
    {
        return response()->json([
            'success' => true,
            'data' => $color
        ]);
    }

    public function update(Request $request, Color $color)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:colores,nombre,' . $color->id,
            'codigo_rgb' => 'required|regex:/^#[0-9A-Fa-f]{6}$/|unique:colores,codigo_rgb,' . $color->id,
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe un color con este nombre',
            'nombre.max' => 'El nombre no puede exceder 50 caracteres',
            'codigo_rgb.required' => 'El código RGB es obligatorio',
            'codigo_rgb.regex' => 'El código RGB debe tener el formato #FFFFFF',
            'codigo_rgb.unique' => 'Ya existe un color con este código RGB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $color->update([
                'nombre' => $request->nombre,
                'codigo_rgb' => strtoupper($request->codigo_rgb),
                'activo' => $request->activo ?? $color->activo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Color actualizado exitosamente',
                'data' => $color
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el color: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Color $color)
    {
        try {
            if ($color->vehiculos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el color porque tiene vehículos asociados'
                ], 409);
            }

            $color->delete();

            return response()->json([
                'success' => true,
                'message' => 'Color eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el color: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleActivo(Color $color)
    {
        try {
            $color->update(['activo' => !$color->activo]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'activo' => $color->activo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }
}