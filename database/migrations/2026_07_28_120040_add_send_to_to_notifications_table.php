<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'send_to')) {
                $table->string('send_to', 50)->nullable()->after('image');
            }
            if (!Schema::hasColumn('notifications', 'send_to_user_ids')) {
                $table->text('send_to_user_ids')->nullable()->after('send_to');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'send_to_user_ids')) {
                $table->dropColumn('send_to_user_ids');
            }
            if (Schema::hasColumn('notifications', 'send_to')) {
                $table->dropColumn('send_to');
            }
        });
    }
};
