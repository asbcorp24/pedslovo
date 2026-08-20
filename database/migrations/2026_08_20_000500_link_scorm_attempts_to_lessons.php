<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){Schema::table('scorm_attempts',function(Blueprint $t){$t->foreignId('lesson_id')->nullable()->after('scorm_package_id')->constrained()->nullOnDelete();});}
 public function down(){Schema::table('scorm_attempts',function(Blueprint $t){$t->dropConstrainedForeignId('lesson_id');});}
};
