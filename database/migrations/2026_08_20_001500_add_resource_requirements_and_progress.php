<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->boolean('material_required')->default(false)->after('material_id');
        });

        Schema::table('lesson_files', function (Blueprint $table) {
            $table->boolean('is_required')->default(true)->after('is_primary')->index();
        });

        Schema::table('lesson_links', function (Blueprint $table) {
            $table->boolean('is_required')->default(true)->after('sort_order')->index();
        });

        Schema::table('lesson_scorm_package', function (Blueprint $table) {
            $table->boolean('is_required')->default(true)->after('sort_order')->index();
        });

        Schema::create('lesson_resource_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('resource_type', 30);
            $table->unsignedBigInteger('resource_id');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['lesson_id','user_id','resource_type','resource_id'], 'lesson_resource_progress_unique');
            $table->index(['user_id','lesson_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_resource_progress');

        Schema::table('lesson_scorm_package', function (Blueprint $table) {
            $table->dropColumn('is_required');
        });
        Schema::table('lesson_links', function (Blueprint $table) {
            $table->dropColumn('is_required');
        });
        Schema::table('lesson_files', function (Blueprint $table) {
            $table->dropColumn('is_required');
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('material_required');
        });
    }
};
