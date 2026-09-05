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
        Schema::create('cta_interactions', function (Blueprint $table) {
            $table->id();
            $table->string('cta_id', 225)->index();
            $table->foreignId('veh_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->string('cta_type');
            $table->timestamp('acted_at')->useCurrent();
            $table->timestamps();
            $table->index(['client_id', 'cta_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cta_interactions');
    }
};
