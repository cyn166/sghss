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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('nurse_id')->constrained('nurses')->onDelete('cascade')->nullable();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade')->nullable();
            $table->dateTime('appointment_date');
            $table->string('type')->default('presencial');
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled');
            $table->string('link_meet')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['nurse_id']);
            $table->dropForeign(['doctor_id']);
        });

        Schema::dropIfExists('appointments');
    }
};
