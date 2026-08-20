<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){
  Schema::create('courses',function(Blueprint $t){$t->id();$t->foreignId('section_id')->nullable()->constrained()->nullOnDelete();$t->string('title');$t->string('slug')->unique();$t->text('description')->nullable();$t->unsignedTinyInteger('study_year')->nullable()->index();$t->unsignedInteger('sort_order')->default(0);$t->boolean('is_active')->default(true)->index();$t->timestamps();});
  Schema::create('lessons',function(Blueprint $t){$t->id();$t->foreignId('course_id')->constrained()->cascadeOnDelete();$t->foreignId('material_id')->nullable()->constrained()->nullOnDelete();$t->foreignId('scorm_package_id')->nullable();$t->string('title');$t->text('description')->nullable();$t->string('lesson_type',30)->default('material')->index();$t->unsignedInteger('sort_order')->default(0);$t->boolean('is_required')->default(true);$t->boolean('is_active')->default(true)->index();$t->timestamps();});
  Schema::create('enrollments',function(Blueprint $t){$t->id();$t->foreignId('course_id')->constrained()->cascadeOnDelete();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->string('status',30)->default('active')->index();$t->timestamp('enrolled_at')->nullable();$t->timestamp('completed_at')->nullable();$t->unique(['course_id','user_id']);$t->timestamps();});
  Schema::create('lesson_progress',function(Blueprint $t){$t->id();$t->foreignId('lesson_id')->constrained()->cascadeOnDelete();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->string('status',30)->default('not_started')->index();$t->decimal('score',8,2)->nullable();$t->timestamp('started_at')->nullable();$t->timestamp('completed_at')->nullable();$t->unique(['lesson_id','user_id']);$t->timestamps();});
 }
 public function down(){Schema::dropIfExists('lesson_progress');Schema::dropIfExists('enrollments');Schema::dropIfExists('lessons');Schema::dropIfExists('courses');}
};
