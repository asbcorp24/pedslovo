<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){
  Schema::create('scorm_packages',function(Blueprint $t){$t->id();$t->string('title');$t->string('version',20)->default('1.2');$t->string('identifier')->nullable();$t->string('launch_path');$t->string('storage_path');$t->boolean('is_active')->default(true)->index();$t->timestamps();});
  Schema::create('scorm_attempts',function(Blueprint $t){$t->id();$t->foreignId('scorm_package_id')->constrained()->cascadeOnDelete();$t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();$t->string('status',40)->default('not attempted')->index();$t->decimal('score_raw',8,2)->nullable();$t->decimal('score_min',8,2)->nullable();$t->decimal('score_max',8,2)->nullable();$t->string('lesson_location')->nullable();$t->longText('suspend_data')->nullable();$t->string('session_time')->nullable();$t->string('total_time')->nullable();$t->json('cmi_data')->nullable();$t->timestamp('started_at')->nullable();$t->timestamp('completed_at')->nullable();$t->timestamps();});
 }
 public function down(){Schema::dropIfExists('scorm_attempts');Schema::dropIfExists('scorm_packages');}
};
