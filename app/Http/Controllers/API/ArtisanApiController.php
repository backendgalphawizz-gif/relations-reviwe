<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ArtisanApiController extends Controller
{
    /**
     * GET /api/optimize-clear
     */
    public function optimizeClear(Request $request)
    {
        try {
            Artisan::call('optimize:clear');
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'optimize:clear ran successfully',
                'output' => trim($output),
                'status' => 200,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * GET /api/migrate
     */
    public function migrate(Request $request)
    {
        try {
            // Mark default Laravel stub migrations as ran if tables already exist (SQL-dump based DB)
            $stubMigrations = [
                '2014_10_12_000000_create_users_table',
                '2014_10_12_100000_create_password_reset_tokens_table',
                '2019_08_19_000000_create_failed_jobs_table',
                '2019_12_14_000001_create_personal_access_tokens_table',
            ];

            foreach ($stubMigrations as $migration) {
                $exists = DB::table('migrations')
                    ->where('migration', $migration)
                    ->exists();
                if (!$exists) {
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => 0,
                    ]);
                }
            }

            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'migrate ran successfully',
                'output' => trim($output),
                'status' => 200,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
