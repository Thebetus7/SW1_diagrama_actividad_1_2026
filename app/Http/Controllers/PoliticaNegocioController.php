<?php

namespace App\Http\Controllers;

use App\Models\PoliticaNegocio;
use App\Models\LogDiagram;
use App\Models\UsuarioDiagrama;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PoliticaNegocioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtenemos un listado general 
        $politicas = PoliticaNegocio::latest()->get();

        return Inertia::render('PoliticaNegocio/index', [
            'politicas' => $politicas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|in:borrador,publicado'
        ]);

        $politica = PoliticaNegocio::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? '',
            'estado' => $validated['estado'] ?? 'borrador'
        ]);

        // JSON basico para goJS (generado desde el modelo)
        $jsonBase = PoliticaNegocio::getBaseJsonDiagram($validated['nombre']);

        $log = LogDiagram::create([
            'json' => $jsonBase
        ]);

        UsuarioDiagrama::create([
            'id_user' => Auth::id(),
            'id_politica' => $politica->id,
            'id_log_diag' => $log->id
        ]);

        // Redirigir a edición
        return redirect()->route('politica_negocio.edit', $politica->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PoliticaNegocio $politica_negocio)
    {
        // Cargar log más reciente del diagrama asociado a esta política
        $usuarioDiagrama = UsuarioDiagrama::where('id_politica', $politica_negocio->id)->latest()->first();
        $logJson = $usuarioDiagrama ? $usuarioDiagrama->logDiagram->json : '';

        return Inertia::render('PoliticaNegocio/edit', [
            'politica' => $politica_negocio,
            'logJson' => $logJson
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PoliticaNegocio $politica_negocio)
    {
        // Para actualizaciones de nombre, descripcion o diagrama
        if ($request->has('json')) {
            $log = LogDiagram::create([
                'json' => $request->json
            ]);

            UsuarioDiagrama::create([
                'id_user' => Auth::id(),
                'id_politica' => $politica_negocio->id,
                'id_log_diag' => $log->id
            ]);
            
            return redirect()->back();
        }
        
        $politica_negocio->update($request->only(['nombre', 'descripcion', 'estado']));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PoliticaNegocio $politica_negocio)
    {
        $politica_negocio->delete();
        return redirect()->back();
    }
}
