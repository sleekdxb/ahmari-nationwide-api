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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // Identification
            $table->string('veh_id', 255)->unique();
            $table->string('stock_number', 255)->unique();
            $table->string('vin', 255)->unique();
            $table->string('admin_id', 255);
            // Vehicle information
            $table->unsignedSmallInteger('year');
            $table->string('make', 255);
            $table->string('model', 255);
            $table->string('trim', 255)->nullable();
            $table->string('condition', 255)->nullable();
            $table->string('body_type', 255)->nullable();

            // Mechanical
            $table->string('transmission', 255)->nullable();
            $table->string('fuel_type', 255)->nullable();
            $table->unsignedInteger('mileage')->nullable();
            $table->string('engine', 255)->nullable();
            $table->string('drivetrain', 255)->nullable();

            // Appearance / capacity
            $table->string('exterior_color', 255)->nullable();
            $table->string('interior_color', 255)->nullable();
            $table->integer('doors')->nullable();
            $table->integer('seats')->nullable();

            // Inventory / pricing
            $table->string('state_id', 255)->index();
            $table->string('location', 255)->nullable();
            $table->decimal('price', 12, 2)->nullable();

            // Description
            $table->text('description')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes for common filtering / listing queries
            |--------------------------------------------------------------------------
            */

            // Location / inventory
            $table->index(['state_id', 'location']);

            // Vehicle browsing
            $table->index(['make', 'model']);
            $table->index(['year', 'make', 'model']);

            // Common filters
            $table->index(['condition', 'body_type']);
            $table->index(['fuel_type', 'transmission']);
            $table->index(['drivetrain']);

            // Range filters
            $table->index(['price']);
            $table->index(['mileage']);
            $table->index(['year']);

            // Common inventory sorting/filtering
            $table->index(['state_id', 'price']);
            $table->index(['state_id', 'year']);
            $table->index(['state_id', 'make', 'model']);

            // If you use full-text vehicle searches
            $table->fullText([
                'make',
                'model',
                'trim',
                'description',
            ]);
        });

        Schema::create('vehicle_status', function (Blueprint $table) {
            $table->id();
            $table->string('state_id')->unique()->index();
            $table->string('veh_id')->index();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('vehicle_files', function (Blueprint $table) {
            $table->id();

            $table->string('file_id')->unique();

            $table->string('veh_id')->index();

            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->string('file_url', 1000)->nullable();

            $table->string('file_type', 30)->index();

            $table->string('state_id')->index();

            $table->timestamps();

            $table->index(['veh_id', 'file_type']);
            $table->index(['veh_id', 'state_id']);
        });

        Schema::create('vehicle_files_status', function (Blueprint $table) {
            $table->id();
            $table->string('file_id')->unique()->index();
            $table->string('state_id')->unique()->index();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_status');
        Schema::dropIfExists('vehicle_files');
        Schema::dropIfExists('vehicle_files_status');
    }
};
