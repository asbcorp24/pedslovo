<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (!Schema::hasColumn('materials', 'material_type')) {
                $table->string('material_type', 50)->default('article')->after('slug')->index();
            }
            if (!Schema::hasColumn('materials', 'author')) {
                $table->string('author')->nullable()->after('content');
            }
            if (!Schema::hasColumn('materials', 'cover')) {
                $table->string('cover')->nullable()->after('author');
            }
            if (!Schema::hasColumn('materials', 'media_url')) {
                $table->string('media_url', 2048)->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('materials', 'status')) {
                $table->string('status', 20)->default('draft')->after('media_url')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $columns = [];
            foreach (['material_type', 'author', 'cover', 'media_url', 'status'] as $column) {
                if (Schema::hasColumn('materials', $column)) {
                    $columns[] = $column;
                }
            }
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
