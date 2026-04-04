<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogDiagram extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'json',
    ];

    public function usuarioDiagramas()
    {
        return $this->hasMany(UsuarioDiagrama::class, 'id_log_diag');
    }
}
