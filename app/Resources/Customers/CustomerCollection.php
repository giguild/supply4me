<?php

namespace App\Resources\Customers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerCollection extends ResourceCollection
{
    public $collects = CustomerResource::class;

    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }
}
