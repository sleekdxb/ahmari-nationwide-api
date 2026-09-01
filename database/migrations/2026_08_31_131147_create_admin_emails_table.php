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
        Schema::create('admin_emails', function (Blueprint $table) {
            $table->id();

            // Admin who sent/owns this email
            $table->string('admin_id')->index();

            // Email identification
            $table->string('email_id')->nullable()->unique();


            // Email addresses
            $table->string('from_email')->index();


            // Email content
            $table->string('subject', 500)->nullable();
            $table->longText('body')->nullable();


            $table->string('email_type', 30)->nullable()->index();
            // incoming, outgoing, system, notification, etc.


            // Attachments / extra provider data
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Common queries

            $table->index(['admin_id', 'created_at']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_emails');
    }
};
