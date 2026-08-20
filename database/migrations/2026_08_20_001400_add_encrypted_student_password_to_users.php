<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users','student_password_secret')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('student_password_secret')->nullable()->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users','student_password_secret')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('student_password_secret');
            });
        }
    }
};
