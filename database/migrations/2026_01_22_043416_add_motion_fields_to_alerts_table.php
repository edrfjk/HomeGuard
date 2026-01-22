<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            // Add these if they don't exist
            $table->string('type')->default('sensor_reading')->change(); // Will now support 'motion_detected'
            $table->unsignedBigInteger('camera_image_id')->nullable()->after('sensor_reading_id');
            $table->foreign('camera_image_id')->references('id')->on('camera_images')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropForeign(['camera_image_id']);
            $table->dropColumn('camera_image_id');
        });
    }
};