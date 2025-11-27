<?php

namespace App\Http\Controllers;

use App\Models\GrupoPersonal;
use App\Models\Zona;
use App\Models\Turno;
use App\Models\Personal;
use App\Models\Vehiculo;
use App\Models\Funcion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GruposPersonalController extends Controller
{
    public function index() {
        $idConductor = Funcion::where('nombre', 'Conductor')->pluck('id')->first();
        $idAyudante = Funcion::where('nombre', 'Ayudante')->pluck('id')->first();
        
        $personalNoNombradoQuery = Personal::where('activo', 1)
            ->whereDoesntHave('contratos', function ($query) {
                $query->where('activo', 1)
                    ->where('tipo_contrato', 'nombrado');
            });

        if ($idConductor) {
            $conductores = (clone $personalNoNombradoQuery)
                                ->where('funcion_id', $idConductor)
                                ->get();
        } else {
            $conductores = collect();
        }

        if ($idAyudante) {
            $ayudantes = (clone $personalNoNombradoQuery)
                                ->where('funcion_id', $idAyudante)
                                ->get();
        } else {
            $ayudantes = collect();
        }
            
        $grupos = GrupoPersonal::with(['zona', 'turno', 'vehiculo'])->get();
        $zonas = Zona::where('activo', 1)->get();
        $turnos = Turno::all();
        $vehiculos = Vehiculo::where('disponible', 1)->get();
        
        $personal = $personalNoNombradoQuery->get();

        return view('grupospersonal.index', compact('conductores','ayudantes','grupos', 'zonas', 'turnos', 'vehiculos','personal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'zona_id' => 'required|exists:zonas,id',
            'turno_id' => 'required|exists:turnos,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'dias' => 'required|string',
            'estado' => 'required|in:0,1',
            'conductor_id' => 'nullable|exists:personal,id',
            'ayudante1_id' => 'nullable|exists:personal,id',
            'ayudante2_id' => 'nullable|exists:personal,id',
        ]);
        
        $grupo = GrupoPersonal::create($request->only([
            'nombre', 'zona_id', 'turno_id', 'vehiculo_id', 'dias', 'estado'
        ]));

        $miembrosIds = [
            $request->input('conductor_id'),
            $request->input('ayudante1_id'),
            $request->input('ayudante2_id'),
        ];

        $miembrosValidos = array_filter($miembrosIds); 

        if (!empty($miembrosValidos)) {
            $idsParaAdjuntar = [];
            $now = Carbon::now();

            foreach (array_unique($miembrosValidos) as $personalId) {
                $idsParaAdjuntar[$personalId] = [
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            
            $grupo->personal()->attach($idsParaAdjuntar);
        }
        
        return response()->json(['success' => true, 'message' => 'Grupo creado con éxito.']);
    }
    
    
    public function update(Request $request, GrupoPersonal $grupoPersonal)
    {
        $request->validate([
            'nombre' => 'required|string', 
            'zona_id' => 'required|exists:zonas,id',
            'turno_id' => 'required|exists:turnos,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'dias' => 'required|string',
            'estado' => 'required|in:0,1',
            'conductor_id' => 'nullable|exists:personal,id',
            'ayudante1_id' => 'nullable|exists:personal,id',
            'ayudante2_id' => 'nullable|exists:personal,id',
        ]);
        
        $grupoPersonal->update($request->only([
            'nombre', 'zona_id', 'turno_id', 'vehiculo_id', 'dias', 'estado'
        ]));

        $miembrosIds = [
            $request->input('conductor_id'),
            $request->input('ayudante1_id'),
            $request->input('ayudante2_id'),
        ];
        
        $miembrosValidos = array_filter($miembrosIds);

        $uniqueMiembros = [];
        foreach ($miembrosValidos as $id) {
            if (!in_array($id, $uniqueMiembros) && $id !== null) { 
                $uniqueMiembros[] = $id;
            }
        }
        
        $grupoPersonal->personal()->detach(); 

        if (!empty($uniqueMiembros)) {
            $idsParaAdjuntar = [];
            $now = Carbon::now();

            foreach ($uniqueMiembros as $personalId) {
                $idsParaAdjuntar[$personalId] = [
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            
            $grupoPersonal->personal()->attach($idsParaAdjuntar);
        }

        return response()->json(['success' => true, 'message' => 'Grupo actualizado con éxito.']);
    }
    
    public function show(GrupoPersonal $grupoPersonal)
    {
        $grupo = GrupoPersonal::with('personal')->find($grupoPersonal->id);
        
        if (!$grupo) {
            return response()->json(['success' => false, 'message' => 'Grupo no encontrado.'], 404);
        }
        
        return response()->json(['success' => true, 'data' => $grupo]);
    }

    public function destroy(GrupoPersonal $grupoPersonal)
    {
        $grupoPersonal->delete();
        return response()->json(['success' => true, 'message' => 'Grupo eliminado con éxito.']);
    }
}