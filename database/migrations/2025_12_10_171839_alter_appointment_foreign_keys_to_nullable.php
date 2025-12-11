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
        Schema::table('appointments', function (Blueprint $table) {

            $table->dropForeign(['nurse_id']);
            $table->dropForeign(['doctor_id']);


            $table->bigInteger('nurse_id')->unsigned()->nullable()->change();
            $table->bigInteger('doctor_id')->unsigned()->nullable()->change();

            $table->foreign('nurse_id')->references('id')->on('nurses')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {

            $table->dropForeign(['nurse_id']);
            $table->dropForeign(['doctor_id']);

            $table->bigInteger('nurse_id')->unsigned()->nullable(false)->change();
            $table->bigInteger('doctor_id')->unsigned()->nullable(false)->change();

            $table->foreign('nurse_id')->references('id')->on('nurses')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
        });
    }
};
