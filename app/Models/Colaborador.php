<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Colaborador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'colaboradores';

    protected $fillable = [
        'id_user_colab',
        'id_politica',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user_colab');
    }

    public function politica()
    {
        return $this->belongsTo(PoliticaNegocio::class, 'id_politica');
    }
}
