<?php

namespace App\Resources\Companies;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCollection extends ResourceCollection
{
    public $collects = CompanyResource::class;

    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }
}
