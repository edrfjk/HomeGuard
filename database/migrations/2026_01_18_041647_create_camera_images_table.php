<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camera_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            
            // Image data
            $table->string('image_path'); // Path to stored image
            $table->string('filename');
            $table->string('mime_type')->default('image/jpeg');
            $table->integer('file_size')->nullable(); // In bytes
            
            // Metadata
            $table->string('trigger_type')->default('auto'); // auto, manual, alert
            $table->text('caption')->nullable();
            $table->boolean('is_favorite')->default(false);
            
            $table->timestamps();
            
            $table->index(['device_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camera_images');
    }
};