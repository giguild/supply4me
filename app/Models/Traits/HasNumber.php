<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait HasNumber
{
    public static function bootHasNumber(): void
    {
        static::creating(function (Model $model) {
            if (is_null($model->{$model->getNumberColumn()})) {
                $model->{$model->getNumberColumn()} = $model->generateNumber();
            }
        });
    }

    abstract public function getNumberPrefix(): string;

    abstract public function getNumberColumn(): string;

    public function generateNumber(): string
    {
        $prefix = $this->getNumberPrefix();
        $column = $this->getNumberColumn();
        $companyId = $this->company_id ?? 0;

        $existing = DB::table('number_sequences')
            ->where('prefix', $prefix)
            ->where('company_id', $companyId)
            ->first();

        if ($existing) {
            $nextNumber = $existing->last_number + 1;
            DB::table('number_sequences')
                ->where('id', $existing->id)
                ->update(['last_number' => $nextNumber, 'updated_at' => now()]);
        } else {
            $nextNumber = 1;
            DB::table('number_sequences')->insert([
                'id' => \Illuminate\Support\Str::uuid(),
                'prefix' => $prefix,
                'company_id' => $companyId,
                'entity_type' => class_basename($this),
                'last_number' => $nextNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $prefix . '-' . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function parseNumber(string $number): ?int
    {
        $prefix = $this->getNumberPrefix();
        $pattern = '/^' . preg_quote($prefix) . '-(\d+)$/';

        if (preg_match($pattern, $number, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
