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
        Schema::create('master_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creativity_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('master_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description');
            $table->date('class_date')->index();
            $table->time('class_time')->index();
            $table->unsignedSmallInteger('max_participants');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['master_id', 'class_date', 'class_time']);
            $table->index(['creativity_type_id', 'class_date', 'class_time'], 'type_date_time_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_classes');
    }
};
