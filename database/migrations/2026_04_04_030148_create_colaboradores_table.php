<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colaboradores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user_colab')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_politica')->constrained('politica_negocios')->cascadeOnDelete();
            $table->enum('estado', ['leer', 'editar'])->default('leer');
            $table->timestamps();
            $table->softDeletes();

            // Un usuario solo puede ser colaborador una vez por política
            $table->unique(['id_user_colab', 'id_politica']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colaboradores');
    }
};
