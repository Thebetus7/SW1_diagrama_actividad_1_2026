<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\PoliticaNegocio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColaboradorController extends Controller
{
    /**
     * Añade un colaborador a una política del usuario autenticado.
     */
    public function store(Request $request, PoliticaNegocio $politica_negocio)
    {
        $request->validate([
            'id_user_colab' => 'required|exists:users,id',
            'estado'        => 'required|in:leer,editar',
        ]);

        // Evitar que el creador se añada a sí mismo como colaborador
        if ($request->id_user_colab == Auth::id()) {
            return back()->withErrors(['id_user_colab' => 'No puedes añadirte a ti mismo como colaborador.']);
        }

        Colaborador::updateOrCreate(
            [
                'id_user_colab' => $request->id_user_colab,
                'id_politica'   => $politica_negocio->id,
            ],
            ['estado' => $request->estado]
        );

        return back();
    }

    /**
     * Elimina (softdelete) a un colaborador de una política.
     */
    public function destroy(PoliticaNegocio $politica_negocio, Colaborador $colaborador)
    {
        $colaborador->delete();
        return back();
    }
}
