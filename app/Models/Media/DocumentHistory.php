<?php

namespace App\Models\Media;

use App\Models\Traits\HasUuid;
use Database\Factories\DocumentHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentHistory extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'company_id',
        'media_id',
        'action',
        'details',
        'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Companies\Company::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(\Spatie\MediaLibrary\MediaCollections\Models\Media::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Core\User::class, 'performed_by');
    }
}
