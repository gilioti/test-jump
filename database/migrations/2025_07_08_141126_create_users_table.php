<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Equivalente ao INT AUTO_INCREMENT e PK
            $table->string('name');
            $table->timestamps(); // Adiciona created_at e updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};