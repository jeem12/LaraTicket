<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('tickets', function (Blueprint $table) {
    //         $table->id();
    //         $table->timestamps();
    //     });
    // }

    public function up(): void
        {
            Schema::create('tickets', function (Blueprint $table) {
                $table->id(); // This handles the incrementing primary key
                $table->foreignId('user_id')->constrained(); // This handles the incrementing foreign key
                $table->string('subject');
                $table->text('description');
                $table->string('status')->default('open');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
