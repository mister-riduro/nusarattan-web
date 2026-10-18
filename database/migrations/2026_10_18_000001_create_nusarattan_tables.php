<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // categories
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->string('meta_title')->nullable();
            $t->string('meta_description')->nullable();
            $t->timestamps();
        });

        // products
        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->string('code')->nullable();                 // FRP-RC-01
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('material')->nullable();
            $t->text('description')->nullable();
            $t->unsignedSmallInteger('width_cm')->nullable();
            $t->unsignedSmallInteger('depth_cm')->nullable();
            $t->unsignedSmallInteger('height_cm')->nullable();
            $t->decimal('price_from', 10, 2)->nullable();   // USD, "Starts from $48"
            $t->boolean('is_active')->default(true);
            $t->boolean('is_featured')->default(false);
            $t->unsignedInteger('sort_order')->default(0);
            $t->softDeletes();
            $t->timestamps();
        });

        // product_images
        Schema::create('product_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('url', 2048);                        // URL publik Supabase / local
            $t->string('alt')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_primary')->default(false);
            $t->timestamps();
        });

        // furniture_sets
        Schema::create('furniture_sets', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->decimal('price', 10, 2)->nullable();        // harga normal (coret)
            $t->decimal('sale_price', 10, 2)->nullable();   // harga promo
            $t->string('image_url', 2048)->nullable();
            $t->text('whatsapp_message')->nullable();
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        // furniture_set_product (pivot)
        Schema::create('furniture_set_product', function (Blueprint $t) {
            $t->foreignId('furniture_set_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('group')->nullable();                // sofa | table | arm-chair
            $t->unsignedInteger('sort_order')->default(0);
            $t->primary(['furniture_set_id', 'product_id']);
        });

        // inquiries (form kontak)
        Schema::create('inquiries', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email');
            $t->string('country')->nullable();
            $t->string('business_type')->nullable();        // Dari modal catalog
            $t->text('message')->nullable();
            $t->string('type')->default('contact');         // 'contact' | 'catalog_download'
            $t->string('ip', 45)->nullable();
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
        });

        // settings (key-value)
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('inquiries');
        Schema::dropIfExists('furniture_set_product');
        Schema::dropIfExists('furniture_sets');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};

