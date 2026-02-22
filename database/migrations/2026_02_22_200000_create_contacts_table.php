<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('cpf', 11);
            $table->string('phone');
            $table->string('cep', 9);
            $table->string('street');
            $table->string('number');
            $table->string('district');
            $table->string('city');
            $table->string('state', 2);
            $table->string('complement')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'cpf']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
