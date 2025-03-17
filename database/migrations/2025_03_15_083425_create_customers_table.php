<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('nombre');
            $table->string('apellidos')->nullable();
            $table->string('empresa');
            $table->string('fiscal_code'); //NIF/CIF/RFC o similares
            $table->string('direccion');
            $table->string('ciudad');
            $table->string('codigo_postal');
            $table->string('provincia');
            $table->string('pais');
            $table->string('telefono')->nullable();
            $table->string('email');
            $table->string('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->string('stripe_customer_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
