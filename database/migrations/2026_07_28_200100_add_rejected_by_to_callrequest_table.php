<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('callrequest', function (Blueprint $table) {
            if (!Schema::hasColumn('callrequest', 'rejected_by')) {
                // customer | timeout | advisor
                $table->string('rejected_by', 20)->nullable()->after('rejected_astrologer_ids');
            }
        });
    }

    public function down(): void
    {
        Schema::table('callrequest', function (Blueprint $table) {
            if (Schema::hasColumn('callrequest', 'rejected_by')) {
                $table->dropColumn('rejected_by');
            }
        });
    }
};
