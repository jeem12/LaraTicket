<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id(); // Internal surrogate primary key for relations
            $table->string('ticket_number')->unique(); // Enterprise readable series (e.g., TCK-2026-0001)
            
            // Core Relationships
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Ticket Creator / Requester
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // Assigned Support Admin / Agent
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete(); // Target Internal Department

            // Ticket Details
            $table->string('subject');
            $table->text('description');
            
            // Classifications (Using string or enums/lookups)
            $table->string('status')->default('Open'); // Open, Pending, Closed, Resolved, Escalated
            $table->string('priority')->default('Medium'); // Low, Medium, High, Urgent
            $table->string('category')->default('General'); // Hardware, Software, Network, HR, Billing, etc.

            // Tracking / Audit Timestamps
            $table->timestamp('last_replied_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Enterprise standard for data integrity and recovery
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};