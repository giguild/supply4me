<?php

namespace App\Actions\Suppliers;

use App\Models\Suppliers\Supplier;

class UpdateSupplierAction
{
    public function execute(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier->fresh();
    }
}
