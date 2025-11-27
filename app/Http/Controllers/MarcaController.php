<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $marcas = Marca::orderBy('nombre')->get();
        return view('vehiculos.marcas.index', compact('marcas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:marcas,nombre',
            'descripcion' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe una marca con este nombre',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'logo.image' => 'El logo debe ser una imagen',
            'logo.mimes' => 'El logo debe ser de tipo: jpeg, png, jpg, svg',
            'logo.max' => 'El logo no debe superar los 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->activo ?? true,
            ];

            // Procesar el logo si existe
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $path = $logo->store('marcas', 'public');
                $data['logo'] = $path;
            }

            $marca = Marca::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Marca creada exitosamente',
                'data' => $marca
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la marca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Marca $marca)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $marca->id,
                'nombre' => $marca->nombre,
                'descripcion' => $marca->descripcion,
                'logo' => $marca->logo,
                'logo_url' => $marca->logo_url,
                'activo' => $marca->activo,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marca $marca)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:marcas,nombre,' . $marca->id,
            'descripcion' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Ya existe una marca con este nombre',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'logo.image' => 'El logo debe ser una imagen',
            'logo.mimes' => 'El logo debe ser de tipo: jpeg, png, jpg, svg',
            'logo.max' => 'El logo no debe superar los 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activo' => $request->activo ?? $marca->activo,
            ];

            // Procesar el logo si existe
            if ($request->hasFile('logo')) {
                // Eliminar el logo anterior si existe
                if ($marca->logo && Storage::disk('public')->exists($marca->logo)) {
                    Storage::disk('public')->delete($marca->logo);
                }

                $logo = $request->file('logo');
                $path = $logo->store('marcas', 'public');
                $data['logo'] = $path;
            }

            $marca->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Marca actualizada exitosamente',
                'data' => $marca
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la marca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marca $marca)
    {
        try {
            // Verificar si tiene modelos asociados
            if ($marca->modelos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la marca porque tiene modelos asociados'
                ], 409);
            }

            $marca->delete();

            return response()->json([
                'success' => true,
                'message' => 'Marca eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la marca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActivo(Marca $marca)
    {
        try {
            $marca->update(['activo' => !$marca->activo]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'activo' => $marca->activo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete logo
     */
    public function deleteLogo(Marca $marca)
    {
        try {
            if ($marca->logo && Storage::disk('public')->exists($marca->logo)) {
                Storage::disk('public')->delete($marca->logo);
                $marca->update(['logo' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Logo eliminado exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'La marca no tiene logo'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el logo: ' . $e->getMessage()
            ], 500);
        }
    }
}