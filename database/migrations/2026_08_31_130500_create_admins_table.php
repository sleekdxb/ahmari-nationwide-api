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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('hashed_email');
            $table->string('state_id')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->index(['first_name', 'last_name']);
        });


        Schema::create('admin_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });


        Schema::create('admin_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('session_id')->index();
            $table->string('admin_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });


        Schema::create('admins_status', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('admin_id')->index();
            $table->string('state_id')->index();
            $table->string('state');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['admin_id', 'state_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('admin_password_reset_tokens');
        Schema::dropIfExists('admin_sessions');
        Schema::dropIfExists('admins_status');
    }
};
