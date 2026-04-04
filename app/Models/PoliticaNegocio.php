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
}
