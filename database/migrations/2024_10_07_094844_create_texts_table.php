<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('texts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Untitled');
            $table->string('slug', 10)->unique();
            $table->text('text');
            $table->json('tags')->nullable()->default(null);
            $table->foreignId('user_id')->nullable()->default(null)->constrained()->nullOnDelete();
            $table->boolean('is_public')->default(false);
            $table->timestamp('expiration')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('texts');
    }
};
