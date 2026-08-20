<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lesson_scorm_package', function (Blueprint $table) {
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scorm_package_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->primary(['lesson_id','scorm_package_id']);
        });

        // Переносим старую одиночную связь в новую таблицу.
        if (Schema::hasColumn('lessons','scorm_package_id')) {
            DB::table('lessons')
                ->whereNotNull('scorm_package_id')
                ->orderBy('id')
                ->chunkById(200, function ($lessons) {
                    $now = now();
                    foreach ($lessons as $lesson) {
                        DB::table('lesson_scorm_package')->updateOrInsert(
                            ['lesson_id'=>$lesson->id,'scorm_package_id'=>$lesson->scorm_package_id],
                            ['sort_order'=>0,'created_at'=>$now,'updated_at'=>$now]
                        );
                    }
                });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lesson_scorm_package');
    }
};
