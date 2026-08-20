<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('seo_pages',function(Blueprint $t){$t->id();$t->string('name');$t->string('path')->unique();$t->string('title')->nullable();$t->text('description')->nullable();$t->text('keywords')->nullable();$t->string('og_title')->nullable();$t->text('og_description')->nullable();$t->string('og_image')->nullable();$t->string('canonical_url')->nullable();$t->string('robots',80)->default('index,follow');$t->boolean('is_active')->default(true)->index();$t->timestamps();}); }
 public function down(): void { Schema::dropIfExists('seo_pages'); }
};
