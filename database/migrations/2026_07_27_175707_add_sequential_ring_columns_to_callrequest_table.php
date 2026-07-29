<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('callrequest', function (Blueprint $table) {
            if (!Schema::hasColumn('callrequest', 'is_sequential')) {
                $table->boolean('is_sequential')->default(false)->after('isFreeSession');
            }
            if (!Schema::hasColumn('callrequest', 'tried_astrologer_ids')) {
                $table->json('tried_astrologer_ids')->nullable()->after('is_sequential');
            }
            if (!Schema::hasColumn('callrequest', 'ring_started_at')) {
                $table->timestamp('ring_started_at')->nullable()->after('tried_astrologer_ids');
            }
            if (!Schema::hasColumn('callrequest', 'ring_timeout_seconds')) {
                $table->unsignedInteger('ring_timeout_seconds')->default(30)->after('ring_started_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('callrequest', function (Blueprint $table) {
            $columns = ['is_sequential', 'tried_astrologer_ids', 'ring_started_at', 'ring_timeout_seconds'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('callrequest', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
