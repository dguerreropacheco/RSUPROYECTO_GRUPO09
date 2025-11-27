<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use App\Models\Distrito;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    public function index()
    {
        $zonas = Zona::all();
        return view('zonas.index', compact('zonas'));
    }

    public function create()
    {
        $distritos = Distrito::where('activo', true)->orderBy('nombre')->get();
        $zonas = Zona::all();
        return view('zonas.create', compact('distritos', 'zonas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:zonas|max:50',
            'nombre' => 'required|max:150',
            'distrito_id' => 'required|exists:distritos,id',
            'descripcion' => 'nullable',
            'perimetro' => 'nullable|json',
            'area' => 'nullable|numeric|min:0',
            'poblacion_estimada' => 'nullable|integer|min:0',
        ]);

        // Validar que no haya superposición de zonas
        if ($request->perimetro) {
            try {
                $perimetro = json_decode($request->perimetro, true);
                
                if (isset($perimetro['geometry']['coordinates'][0])) {
                    $coordinates = $perimetro['geometry']['coordinates'][0];
                    $overlapCheck = Zona::checkPolygonOverlap($coordinates);
                    
                    if ($overlapCheck['overlap']) {
                        $zonesNames = implode(', ', array_column($overlapCheck['zones'], 'nombre'));
                        return redirect()->back()
                            ->withInput()
                            ->withErrors([
                                'perimetro' => "La zona se superpone con las siguientes zonas existentes: {$zonesNames}. Por favor, ajuste el perímetro."
                            ]);
                    }
                }
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'perimetro' => 'Error al validar el perímetro: ' . $e->getMessage()
                    ]);
            }
        }

        Zona::create($request->all());
        return redirect()->route('zonas.index')->with('success', 'Zona creada exitosamente.');
    }

    public function edit(Zona $zona)
    {
        $distritos = Distrito::where('activo', true)->orderBy('nombre')->get();
        // Obtener todas las zonas excepto la actual para mostrarlas en el mapa
        $zonas = Zona::where('id', '!=', $zona->id)->get();
        return view('zonas.edit', compact('zona', 'distritos', 'zonas'));
    }

    public function update(Request $request, Zona $zona)
    {
        $request->validate([
            'codigo' => 'required|unique:zonas,codigo,'.$zona->id.'|max:50',
            'nombre' => 'required|max:150',
            'distrito_id' => 'required|exists:distritos,id',
            'descripcion' => 'nullable',
            'perimetro' => 'nullable|json',
            'area' => 'nullable|numeric',
            'poblacion_estimada' => 'nullable|integer',
        ]);

        // Validar que no haya superposición de zonas (excluyendo la zona actual)
        if ($request->perimetro) {
            try {
                $perimetro = json_decode($request->perimetro, true);
                
                if (isset($perimetro['geometry']['coordinates'][0])) {
                    $coordinates = $perimetro['geometry']['coordinates'][0];
                    $overlapCheck = Zona::checkPolygonOverlap($coordinates, $zona->id);
                    
                    if ($overlapCheck['overlap']) {
                        $zonesNames = implode(', ', array_column($overlapCheck['zones'], 'nombre'));
                        return redirect()->back()
                            ->withInput()
                            ->withErrors([
                                'perimetro' => "La zona se superpone con las siguientes zonas existentes: {$zonesNames}. Por favor, ajuste el perímetro."
                            ]);
                    }
                }
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'perimetro' => 'Error al validar el perímetro: ' . $e->getMessage()
                    ]);
            }
        }

        $zona->update($request->all());
        return redirect()->route('zonas.index')->with('success', 'Zona actualizada exitosamente.');
    }

    public function destroy(Zona $zona)
    {
        $zona->delete();
        return redirect()->route('zonas.index')->with('success', 'Zona eliminada exitosamente.');
    }

    public function mapaGeneral()
    {
        $zonas = Zona::with('distrito')->get();
        return view('zonas.mapa', compact('zonas'));
    }

    /**
     * Verifica si un polígono se interseca con zonas existentes
     */
    public function checkOverlap(Request $request)
    {
        $request->validate([
            'perimetro' => 'required|json',
            'zona_id' => 'nullable|exists:zonas,id'
        ]);

        try {
            $perimetro = json_decode($request->perimetro, true);
            
            if (!isset($perimetro['geometry']['coordinates'][0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de perímetro inválido'
                ], 400);
            }

            $coordinates = $perimetro['geometry']['coordinates'][0];
            $excludeId = $request->zona_id;

            $result = Zona::checkPolygonOverlap($coordinates, $excludeId);

            return response()->json([
                'success' => true,
                'overlap' => $result['overlap'],
                'zones' => $result['zones'],
                'message' => $result['overlap'] 
                    ? 'La zona se superpone con otras zonas existentes' 
                    : 'No hay superposición con otras zonas'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar superposición: ' . $e->getMessage()
            ], 500);
        }
    }
}