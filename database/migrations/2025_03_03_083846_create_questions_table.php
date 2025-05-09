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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained();
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'short_answer', 'multiple_response', 'sequence', 'matching']);
            $table->timestamps();
        });
    }
};
