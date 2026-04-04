<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioDiagrama extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id_user',
        'id_politica',
        'id_log_diag',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function politicaNegocio()
    {
        return $this->belongsTo(PoliticaNegocio::class, 'id_politica');
    }

    public function logDiagram()
    {
        return $this->belongsTo(LogDiagram::class, 'id_log_diag');
    }
}
