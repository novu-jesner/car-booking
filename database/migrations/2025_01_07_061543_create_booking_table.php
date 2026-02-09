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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('img_attachment')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('purpose'); 
            $table->dateTime('from_date'); 
            $table->dateTime('to_date'); 
            $table->string('destination'); 

            // Foreign key for driver
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');

            // Additional fields
            $table->foreignId('car_id')->nullable()->constrained('cars')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'done'])->default('pending');
            $table->text('remarks')->nullable();
            $table->string('odo_meter')->nullable();

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
