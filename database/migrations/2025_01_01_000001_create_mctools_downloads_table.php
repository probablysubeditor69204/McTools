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
        Schema::create('mctools_downloads', function (Blueprint $table) {
            $table->id();
            $table->string('item_id');
            $table->string('item_name');
            $table->string('version_id')->nullable();
            $table->string('version_name')->nullable();
            $table->string('provider'); // modrinth or curseforge
            $table->string('category'); // Mods, Plugins, etc.
            $table->bigInteger('file_size')->default(0); // in bytes
            $table->unsignedBigInteger('server_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mctools_downloads');
    }
};
