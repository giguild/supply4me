<?php

namespace App\Models\Settings;

use App\Models\Traits\HasCompany;
use App\Models\Traits\HasUuid;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasCompany, HasFactory, HasUuid;

    protected $fillable = [
        'company_id',
        'group',
        'key',
        'value',
        'type',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Companies\Company::class);
    }
}
