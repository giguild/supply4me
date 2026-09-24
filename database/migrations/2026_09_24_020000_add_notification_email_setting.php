<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $companyId = DB::table('companies')->first()?->id;
        if (!$companyId) return;

        $exists = DB::table('settings')
            ->where('company_id', $companyId)
            ->where('key', 'notification_email')
            ->exists();

        if (!$exists) {
            DB::table('settings')->insert([
                'id' => \Illuminate\Support\Str::uuid(),
                'company_id' => $companyId,
                'key' => 'notification_email',
                'value' => json_encode(''),
                'group' => 'notification',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'notification_email')->delete();
    }
};
