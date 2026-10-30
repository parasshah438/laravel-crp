<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('permission_group_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_system')->default(false);
            $table->json('dependencies')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('permission_group_id')->references('id')->on('permission_groups')->onDelete('set null');
            $table->index(['slug', 'status']);
            $table->index(['permission_group_id', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};