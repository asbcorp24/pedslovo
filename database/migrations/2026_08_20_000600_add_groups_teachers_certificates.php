<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){
  Schema::create('groups',function(Blueprint $t){$t->id();$t->string('name');$t->string('code')->nullable()->unique();$t->foreignId('curator_id')->nullable()->constrained('users')->nullOnDelete();$t->boolean('is_active')->default(true)->index();$t->timestamps();});
  Schema::create('group_user',function(Blueprint $t){$t->foreignId('group_id')->constrained()->cascadeOnDelete();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->primary(['group_id','user_id']);});
  Schema::create('course_group',function(Blueprint $t){$t->foreignId('course_id')->constrained()->cascadeOnDelete();$t->foreignId('group_id')->constrained()->cascadeOnDelete();$t->primary(['course_id','group_id']);});
  Schema::table('courses',function(Blueprint $t){$t->foreignId('instructor_id')->nullable()->after('section_id')->constrained('users')->nullOnDelete();$t->decimal('pass_score',5,2)->nullable()->after('study_year');$t->boolean('certificate_enabled')->default(false)->after('pass_score');});
  Schema::table('scorm_packages',function(Blueprint $t){$t->unsignedSmallInteger('max_attempts')->nullable()->after('version');$t->decimal('pass_score',5,2)->nullable()->after('max_attempts');});
  Schema::create('certificates',function(Blueprint $t){$t->id();$t->foreignId('course_id')->constrained()->cascadeOnDelete();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->string('number')->unique();$t->decimal('score',5,2)->nullable();$t->timestamp('issued_at');$t->timestamps();$t->unique(['course_id','user_id']);});
 }
 public function down(){
  Schema::dropIfExists('certificates');
  Schema::table('scorm_packages',function(Blueprint $t){$t->dropColumn(['max_attempts','pass_score']);});
  Schema::table('courses',function(Blueprint $t){$t->dropConstrainedForeignId('instructor_id');$t->dropColumn(['pass_score','certificate_enabled']);});
  Schema::dropIfExists('course_group');Schema::dropIfExists('group_user');Schema::dropIfExists('groups');
 }
};
