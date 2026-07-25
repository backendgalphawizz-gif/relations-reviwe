<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_login_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId');
            $table->string('deviceId')->nullable();
            $table->string('appId', 20)->nullable();
            $table->string('fcmToken', 500)->nullable();
            $table->string('deviceManufacturer')->nullable();
            $table->string('deviceModel')->nullable();
            $table->string('appVersion')->nullable();
            $table->string('deviceLocation')->nullable();
            $table->string('ipAddress', 45)->nullable();
            $table->string('userAgent', 500)->nullable();
            $table->enum('status', ['active', 'logout', 'forced_logout'])->default('active');
            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->timestamps();

            $table->index('userId');
            $table->index('status');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_login_histories');
    }
};
