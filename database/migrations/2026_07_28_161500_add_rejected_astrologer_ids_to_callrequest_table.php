<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('callrequest', function (Blueprint $table) {
            if (!Schema::hasColumn('callrequest', 'rejected_astrologer_ids')) {
                $table->json('rejected_astrologer_ids')->nullable()->after('tried_astrologer_ids');
            }
        });
    }

    public function down(): void
    {
        Schema::table('callrequest', function (Blueprint $table) {
            if (Schema::hasColumn('callrequest', 'rejected_astrologer_ids')) {
                $table->dropColumn('rejected_astrologer_ids');
            }
        });
    }
};
