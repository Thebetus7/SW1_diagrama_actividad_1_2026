<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoliticaNegocio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    public function usuarioDiagramas()
    {
        return $this->hasMany(UsuarioDiagrama::class, 'id_politica');
    }

    /**
     * Genera el JSON base para un diagrama GoJS nuevo.
     * El Pool principal lleva el nombre de la política.
     */
    public static function getBaseJsonDiagram(string $nombre): string
    {
        return json_encode([
            'class' => 'go.GraphLinksModel',
            'nodeDataArray' => [
                ['key' => 'Pool1', 'text' => $nombre, 'isGroup' => true, 'category' => 'Pool'],
                ['key' => 'Lane1', 'text' => 'Carril 1', 'isGroup' => true, 'category' => 'Lane', 'group' => 'Pool1', 'color' => 'lightblue'],
                ['key' => 'inicio', 'text' => 'Inicio', 'group' => 'Lane1'],
                ['key' => 'actividad1', 'text' => 'Primera Actividad', 'group' => 'Lane1'],
            ],
            'linkDataArray' => [
                ['from' => 'inicio', 'to' => 'actividad1'],
            ],
        ]);
    }
}
