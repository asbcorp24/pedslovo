<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('locale', 10)->default('ru');
            $table->string('background_path')->nullable();
            $table->text('title')->nullable();
            $table->text('body_text')->nullable();
            $table->string('signer_name')->nullable();
            $table->string('signer_position')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('stamp_path')->nullable();
            $table->boolean('show_score')->default(true);
            $table->boolean('show_qr')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('certificate_template_id')->nullable()->after('certificate_enabled')->constrained('certificate_templates')->nullOnDelete();
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('certificate_template_id')->nullable()->after('user_id')->constrained('certificate_templates')->nullOnDelete();
            $table->string('verification_token', 64)->nullable()->unique()->after('number');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('certificate_template_id');
            $table->dropColumn('verification_token');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('certificate_template_id');
        });
        Schema::dropIfExists('certificate_templates');
    }
};
