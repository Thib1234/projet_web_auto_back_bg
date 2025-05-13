<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->integer('mileage');
            $table->decimal('price', 10, 2);
            $table->enum('fuel_type', ['essence', 'diesel', 'électrique', 'hybride']);
            $table->enum('transmission', ['manuelle', 'automatique']);
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
