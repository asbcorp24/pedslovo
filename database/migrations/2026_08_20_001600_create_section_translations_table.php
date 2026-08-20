<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['section_id', 'locale']);
            $table->index('locale');
        });
    }

    public function down()
    {
        Schema::dropIfExists('section_translations');
    }
};
