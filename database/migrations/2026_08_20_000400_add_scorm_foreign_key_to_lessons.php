<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){Schema::table('lessons',function(Blueprint $t){$t->foreign('scorm_package_id')->references('id')->on('scorm_packages')->nullOnDelete();});}
 public function down(){Schema::table('lessons',function(Blueprint $t){$t->dropForeign(['scorm_package_id']);});}
};
