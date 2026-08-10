<?php

namespace App\Resources\Companies;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanySettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'currency' => $this->when(isset($this->currency), $this->currency),
            'timezone' => $this->when(isset($this->timezone), $this->timezone),
            'date_format' => $this->when(isset($this->date_format), $this->date_format),
            'tax_rate' => $this->when(isset($this->tax_rate), (float) $this->tax_rate),
            'low_stock_threshold' => $this->when(isset($this->low_stock_threshold), $this->low_stock_threshold),
            'auto_generate_invoice' => $this->when(isset($this->auto_generate_invoice), $this->auto_generate_invoice),
            'payment_terms_days' => $this->when(isset($this->payment_terms_days), $this->payment_terms_days),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
