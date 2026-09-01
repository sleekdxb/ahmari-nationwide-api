<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();

            // Admin receiving the notification
            $table->string('admin_id')->index();

            // Notification information
            $table->string('notification_id')->unique();
            $table->string('type', 50)->index();

            // Display content
            $table->string('title', 255);
            $table->text('message');

            // Notification state
            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('read_at')->nullable();

            // Priority
            $table->string('priority', 20)->default('normal')->index();
            // low, normal, high, urgent



            // Additional notification data
            $table->json('data')->nullable();

            $table->timestamps();

            // Common queries
            $table->index(['admin_id', 'is_read']);
            $table->index(['admin_id', 'created_at']);
            $table->index(['admin_id', 'type']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
