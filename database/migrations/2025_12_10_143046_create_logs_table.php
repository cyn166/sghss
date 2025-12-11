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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // e.g., 'create', 'update', 'delete', 'login', etc.
            $table->string('table_affected')->nullable(); // e.g., 'users', etc.
            $table->text('description')->nullable(); // Descrição detalhada da ação
            $table->ipAddress('ip_address')->nullable(); // Endereço IP do usuário que realizou a ação
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('logs');
    }
};
