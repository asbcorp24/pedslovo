<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('material_section', 'created_at') || !Schema::hasColumn('material_section', 'updated_at')) {
            Schema::table('material_section', function (Blueprint $table) {
                if (!Schema::hasColumn('material_section', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('material_section', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('material_section', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('material_section', 'created_at')) {
                $drop[] = 'created_at';
            }
            if (Schema::hasColumn('material_section', 'updated_at')) {
                $drop[] = 'updated_at';
            }
            if ($drop) {
                $table->dropColumn($drop);
            }
        });
    }
};
