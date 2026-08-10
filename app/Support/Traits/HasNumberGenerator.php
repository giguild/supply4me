<?php

namespace App\Support\Traits;

use Illuminate\Support\Facades\DB;

trait HasNumberGenerator
{
    public function generateSequentialNumber(string $prefix, int $companyId = 0, int $padding = 6): string
    {
        $lastNumber = DB::table('number_sequences')
            ->where('prefix', $prefix)
            ->where('company_id', $companyId)
            ->value('last_number');

        $nextNumber = ($lastNumber ?? 0) + 1;

        DB::table('number_sequences')->updateOrInsert(
            ['prefix' => $prefix, 'company_id' => $companyId],
            ['last_number' => $nextNumber, 'updated_at' => now()]
        );

        return $prefix . '-' . str_pad((string) $nextNumber, $padding, '0', STR_PAD_LEFT);
    }

    public function parseSequentialNumber(string $number): ?array
    {
        $parts = explode('-', $number);

        if (count($parts) !== 2) {
            return null;
        }

        return [
            'prefix' => $parts[0],
            'sequence' => (int) $parts[1],
        ];
    }
}
